param(
    [string] $BaseUrl = 'http://localhost:8080'
)
$ErrorActionPreference = 'Stop'
$base = [Uri] ($BaseUrl.TrimEnd('/') + '/')
if ($base.Scheme -notin @('http', 'https')) { throw 'Use an HTTP or HTTPS base URL.' }
$checks = 0

function Get-Response([string] $Path, [string] $Method = 'GET', [hashtable] $Body = $null) {
    $arguments = @{
        Uri = [Uri]::new($base, $Path)
        Method = $Method
        UseBasicParsing = $true
        TimeoutSec = 30
        MaximumRedirection = 0
    }
    if ($null -ne $Body) { $arguments.Body = $Body }
    try {
        $response = Invoke-WebRequest @arguments
        return @{ Status = [int] $response.StatusCode; Content = [string] $response.Content }
    } catch {
        if ($null -eq $_.Exception.Response) { throw }
        return @{ Status = [int] $_.Exception.Response.StatusCode; Content = '' }
    }
}

function Assert-Status([string] $Path, [int[]] $Expected, [string] $Method = 'GET', [hashtable] $Body = $null) {
    $response = Get-Response $Path $Method $Body
    if ($response.Status -notin $Expected) {
        throw "$Method $Path returned $($response.Status), expected $($Expected -join ' or ')."
    }
    $script:checks++
    return $response
}

$home = Assert-Status '' @(200)
if ($home.Content -notmatch 'id="home-title"') { throw 'The root URL did not render the portfolio.' }
$checks++

# Follow the actual rendered assets and project links, then project screenshots
# and CSS font URLs. No valid form submission occurs, so this cannot send email.
$pending = [System.Collections.Generic.Queue[Uri]]::new()
$seen = [System.Collections.Generic.HashSet[string]]::new()
function Queue-References([string] $Content, [Uri] $DocumentUrl, [bool] $IsCss = $false) {
    $pattern = if ($IsCss) { 'url\(["'']?([^\s)"'']+)' } else { '(?:src|href)="([^"]+)"' }
    foreach ($match in [regex]::Matches($Content, $pattern)) {
        $reference = [System.Net.WebUtility]::HtmlDecode($match.Groups[1].Value)
        $target = [Uri]::new($DocumentUrl, $reference)
        if ($target.Authority -ne $base.Authority -or $target.Scheme -ne $base.Scheme) { continue }
        $isAsset = $target.AbsolutePath.StartsWith($base.AbsolutePath + 'assets/')
        $isProject = $target.AbsolutePath -eq ($base.AbsolutePath + 'project.php') -and $target.Query.StartsWith('?project=')
        if (($isAsset -or $isProject) -and $seen.Add($target.AbsoluteUri)) { $pending.Enqueue($target) }
    }
}
Queue-References $home.Content $base
while ($pending.Count -gt 0) {
    $target = $pending.Dequeue()
    $response = Assert-Status $target.AbsoluteUri @(200)
    if ($target.AbsolutePath.EndsWith('.php')) { Queue-References $response.Content $target }
    elseif ($target.AbsolutePath.EndsWith('.css')) { Queue-References $response.Content $target $true }
}
$null = Assert-Status 'index.php#about' @(200)
$null = Assert-Status 'project.php?project=not-a-project' @(404)
$null = Assert-Status 'handlers/contactHandler.php' @(405)
$null = Assert-Status 'handlers/contactHandler.php' @(403) 'POST' @{ name = 'Deployment check' }

foreach ($path in @('config/appConfig.php', 'data/projectData.php', 'includes/header.php', 'pages/about.php')) {
    $null = Assert-Status $path @(403)
}
foreach ($path in @('.git/config', '.env', 'tools/verifyPortfolio.php', 'docs/contactSetup.md', 'vendor/phpmailer/README.md', 'Dockerfile', 'render.yaml', 'docker/start-portfolio.sh')) {
    $null = Assert-Status $path @(403, 404)
}
Write-Output "PASS: $checks deployment HTTP checks; rendered assets, project links, contact rejection, and private paths. No email sent."
