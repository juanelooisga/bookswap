<?php

declare(strict_types=1);

use Juanc\Bookswap\App;
use Juanc\Bookswap\Exceptions\ExceptionHandler;

$root = dirname(__DIR__);

require $root . '/vendor/autoload.php';

$handler = new ExceptionHandler();
$handler->register();

try {
    $app = App::create($root);
    $app->run();
} catch (Throwable $e) {
    $handler->render($e);
}
