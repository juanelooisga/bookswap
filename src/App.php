<?php

declare(strict_types=1);

namespace Juanc\Bookswap;

use Juanc\Bookswap\Config\Database;
use Juanc\Bookswap\Controllers\BooksController;
use Juanc\Bookswap\Controllers\ExchangesController;
use Juanc\Bookswap\Controllers\HealthController;
use Juanc\Bookswap\Controllers\HistoryController;
use Juanc\Bookswap\Controllers\MessagesController;
use Juanc\Bookswap\Controllers\RatingsController;
use Juanc\Bookswap\Controllers\UsersController;
use Juanc\Bookswap\Http\Request;
use Juanc\Bookswap\Http\Router;
use Juanc\Bookswap\Utils\Env;

final class App
{
    private string $root;
    private Router $router;

    private function __construct(string $root, Router $router)
    {
        $this->root = $root;
        $this->router = $router;
    }

    public static function create(string $root): self
    {
        Env::load($root);

        $router = new Router(Env::get('API_PREFIX', '/api'));
        self::registerRoutes($router);

        return new self($root, $router);
    }

    private static function registerRoutes(Router $router): void
    {
        $health = new HealthController();
        $users = new UsersController();
        $books = new BooksController();
        $exchanges = new ExchangesController();
        $messages = new MessagesController();
        $history = new HistoryController();
        $ratings = new RatingsController();

        $router->get('/health', [$health, 'index']);
        $router->post('/users', [$users, 'store']);
        $router->get('/users', [$users, 'index']);
        $router->get('/users/{id}/notifications', [$users, 'notifications']);
        $router->get('/users/{id}/ratings', [$users, 'ratings']);
        $router->get('/users/{id}', [$users, 'show']);
        $router->post('/books', [$books, 'store']);
        $router->get('/books', [$books, 'index']);
        $router->get('/books/search', [$books, 'search']);
        $router->get('/books/{id}', [$books, 'show']);
        $router->delete('/books/{id}', [$books, 'destroy']);
        $router->post('/exchanges', [$exchanges, 'store']);
        $router->get('/exchanges', [$exchanges, 'index']);
        $router->put('/exchanges/{id}/accept', [$exchanges, 'accept']);
        $router->put('/exchanges/{id}/reject', [$exchanges, 'reject']);
        $router->get('/exchanges/{id}/messages', [$exchanges, 'messages']);
        $router->post('/messages', [$messages, 'store']);
        $router->get('/history', [$history, 'index']);
        $router->post('/ratings', [$ratings, 'store']);
    }

    public function run(): void
    {
        $request = Request::fromGlobals();
        $this->router->dispatch($request);
    }

    public function root(): string
    {
        return $this->root;
    }

    public static function database(): \PDO
    {
        return Database::connection();
    }
}
