<?php

declare(strict_types=1);

use Juanc\Bookswap\App;
use Juanc\Bookswap\Exceptions\ExceptionHandler;

$root = dirname(__DIR__);

header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: same-origin');
if (parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) === '/') {
    header('Location: /frontend.html', true, 302);
    exit;
}
if ((int) ($_SERVER['CONTENT_LENGTH'] ?? 0) > 32768) {
    http_response_code(413);
    header('Content-Type: application/json; charset=utf-8');
    echo '{"success":false,"message":"La solicitud es demasiado grande."}';
    exit;
}

require $root . '/vendor/autoload.php';

$handler = new ExceptionHandler();
$handler->register();

try {
    $app = App::create($root);
    $app->run();
} catch (Throwable $e) {
    $handler->render($e);
}
