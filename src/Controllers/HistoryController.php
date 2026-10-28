<?php

declare(strict_types=1);

namespace Juanc\Bookswap\Controllers;

use Juanc\Bookswap\Http\Request;
use Juanc\Bookswap\Repositories\HistoryRepository;

final class HistoryController extends BaseController
{
    private HistoryRepository $history;

    public function __construct()
    {
        $this->history = new HistoryRepository();
    }

    public function index(Request $request): void
    {
        $this->json(['history' => $this->history->all()]);
    }
}
