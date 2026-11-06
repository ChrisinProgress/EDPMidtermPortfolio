<?php
declare(strict_types=1);
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
$root = dirname(__DIR__);
chdir($root);
session_save_path(sys_get_temp_dir());
$checks = 0;
$failures = [];
$check = static function (bool $ok, string $message) use (&$checks, &$failures): void { $checks++; if (!$ok) $failures[] = $message; };
function renderPolicyTestPage(string $path, array $query = []): string {
    $_GET = $query;
    ob_start();
    require $path;
    return ob_get_clean();
}
$documents = [];
$previous = libxml_use_internal_errors(true);
foreach (['index.php','privacy.php','terms.php','licenses.php','legal.php','project.php'] as $page) {
    $html = renderPolicyTestPage($page, $page === 'project.php' ? ['project'=>'vision-aid'] : []);
    $doc = new DOMDocument(); $doc->loadHTML('<?xml encoding="UTF-8">' . $html); libxml_clear_errors();
    $documents[$page] = [$doc, new DOMXPath($doc), $html];
}
foreach ($documents as $page => [$doc,$xpath,$html]) {
    $ids = [];
    foreach ($xpath->query('//*[@id]') as $node) { $id=$node->getAttribute('id'); $check(!isset($ids[$id]), $page . ': duplicate id ' . $id); $ids[$id]=true; }
    $check($xpath->query('//main[@id="main-content"]')->length === 1, $page . ': main landmark missing.');
    foreach ($xpath->query('//a[@href]') as $a) {
        $href = html_entity_decode($a->getAttribute('href'));
        if (preg_match('~^https?://~',$href)) {
            $check($a->getAttribute('target') === '_blank', $page . ': external link must open in new tab: ' . $href);
            $check(str_contains($a->getAttribute('rel'),'noopener') && str_contains($a->getAttribute('rel'),'noreferrer'), $page . ': unsafe external link ' . $href);
        } elseif (!preg_match('~^[a-z]+:~i',$href)) {
            $parts = parse_url($href); $path=$parts['path'] ?? ''; $fragment=$parts['fragment'] ?? '';
            $target=$path !== '' ? $path : $page;
            $check(is_file($root . '/' . $target), $page . ': local link missing ' . $href);
            if ($fragment !== '' && isset($documents[$target])) $check($documents[$target][1]->query('//*[@id="' . $fragment . '"]')->length === 1, $page . ': target fragment missing ' . $href);
        }
    }
}
foreach (['privacy.php'=>'Privacy Policy','terms.php'=>'Terms of Service','licenses.php'=>'Licenses & Attribution'] as $page=>$title) {
    [$doc,$xpath,$html]=$documents[$page];
    $check($xpath->query('//main//h1[@data-type-heading]')->length === 1,$page . ': one typing title expected.');
    $check(str_contains($html,'datetime="2026-11-06"'),$page . ': updated date missing.');
    $check($xpath->query('//nav[@aria-label="Legal pages"]//a[@aria-current="page"]')->length === 1,$page . ': current page link missing.');
    $check(str_contains($html,'Back to Portfolio'),$page . ': return navigation missing.');
}
$privacy=$documents['privacy.php'][2];
foreach (['https://privacy.gov.ph/','https://privacy.gov.ph/data-subject-rights/','https://privacy.gov.ph/contactus/','https://privacy.gov.ph/implementing-rules-regulations-data-privacy-act-2012/','GDPR','CCPA','CPRA','parent or guardian','local storage'] as $value) $check(str_contains($privacy,$value),'Privacy content missing ' . $value);
$check(!str_contains($privacy,'privacy-cookies'),'Privacy page should describe the deployed contact flow and theme storage.');
$check($documents['terms.php'][1]->query('//section[@id="terms-contact"]//a[@href="privacy.php"]')->length===1,'Contact form terms must link to the Privacy Policy.');

$checkContactDisclosure = static function (string $html, bool $usesBrevo) use ($check): void {
    if ($usesBrevo) {
        $check(str_contains($html,'https://www.brevo.com/legal/privacypolicy/'),'Brevo notice is missing the provider’s privacy link.');
        $check(str_contains($html,'transactional email retention guidance'),'Brevo notice is missing retention guidance.');
        $check(str_contains($html,'first-party session cookie'),'Brevo notice is missing the contact session cookie.');
        $check(!str_contains($html,'FormSubmit'),'Brevo notice still refers to FormSubmit.');
    } else {
        $check(str_contains($html,'FormSubmit'),'Fallback notice is missing FormSubmit.');
        $check(!str_contains($html,'Brevo') && !str_contains($html,'first-party session cookie')
            && !str_contains($html,'cjcPortfolioSession'),'Fallback notice should omit Brevo and its contact session.');
    }
};
$checkContactDisclosure($privacy, contactUsesBrevo());
$fixture = [
    'BREVO_API_KEY' => 'legal-check-placeholder',
    'BREVO_SENDER_EMAIL' => 'sender@example.com',
    'BREVO_SENDER_NAME' => 'Legal test sender',
    'CONTACT_OWNER_EMAIL' => 'owner@example.com',
];
$savedContactEnvironment = [];
foreach ($fixture as $name => $value) $savedContactEnvironment[$name] = getenv($name);
try {
    putenv('BREVO_API_KEY');
    $checkContactDisclosure(renderPolicyTestPage('privacy.php'), false);
    foreach ($fixture as $name => $value) putenv($name . '=' . $value);
    $checkContactDisclosure(renderPolicyTestPage('privacy.php'), true);
} finally {
    foreach ($savedContactEnvironment as $name => $value) putenv($value === false ? $name : $name . '=' . $value);
}
$home=$documents['index.php'][2]; $homeXPath=$documents['index.php'][1];
$check(!str_contains($home,'The Form of Beauty'),'Removed presentation still displayed.');
$check(!str_contains($home,'Community Engagement'),'Removed expertise still displayed.');
$check($homeXPath->query('//*[@data-social-slot]//nav[@data-social-dock]')->length===1,'Socials must start inside the introduction.');
$check($homeXPath->query('//*[@data-type-heading]')->length===3,'Only About, Gallery, and Contact should type on home.');
$check($homeXPath->query('//input[@name="privacyConsent" and @type="checkbox" and @required]')->length===1,'Inquiry consent checkbox must be required.');
$tools=require $root . '/data/toolkitData.php'; $arduino=array_values(array_filter($tools,fn($tool)=>$tool['name']==='Arduino IDE'));
$check(count($arduino)===1 && $arduino[0]['group']==='workspace','Arduino IDE must be in Workspace once.');
$projects=require $root . '/data/projectData.php'; $check($projects[0]['technologies']===['Arduino IDE'],'Vision Aid must show only the Arduino IDE technology.');
$check(is_file($root . '/' . ($arduino[0]['icon'] ?? 'missing')),'Arduino logo missing.');
$check($documents['licenses.php'][1]->query('//table//thead//th')->length===5,'Attribution table needs five fields.');
foreach (['privacy.php','terms.php','licenses.php','legal.php'] as $page) {
    $check(str_contains(file_get_contents($root . '/Dockerfile'),'privacy.php terms.php licenses.php legal.php'),'Policy entry points missing from Docker copy.');
    $check(str_contains(file_get_contents($root . '/.dockerignore'),'!' . $page),'Policy entry point excluded from Docker context: ' . $page);
}
libxml_use_internal_errors($previous);
if ($failures) { fwrite(STDERR,"FAIL:\n- " . implode("\n- ",$failures) . "\n"); exit(1); }
echo 'PASS: ' . $checks . " legal, navigation, asset, content, and deployment checks. No messages sent.\n";
