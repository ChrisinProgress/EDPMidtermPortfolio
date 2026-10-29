<?php
declare(strict_types=1);

// Container-local GET only: this never submits the contact form or sends email.
$port = filter_var(getenv('PORT') ?: '10000', FILTER_VALIDATE_INT, [
    'options' => ['min_range' => 1, 'max_range' => 65535],
]);
if ($port === false) {
    exit(1);
}
$context = stream_context_create(['http' => ['timeout' => 3, 'ignore_errors' => true]]);
$page = @file_get_contents('http://127.0.0.1:' . $port . '/', false, $context);
$status = $http_response_header[0] ?? '';
exit(is_string($page) && str_contains($status, ' 200 ') && str_contains($page, 'id="home-title"') ? 0 : 1);
