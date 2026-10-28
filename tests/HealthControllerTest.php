<?php

declare(strict_types=1);

namespace Juanc\Bookswap\Tests;

use Juanc\Bookswap\Controllers\HealthController;
use Juanc\Bookswap\Http\Request;
use Juanc\Bookswap\Utils\JsonResponse;
use PHPUnit\Framework\TestCase;

final class HealthControllerTest extends TestCase
{
    protected function setUp(): void
    {
        JsonResponse::$haltAfterSend = false;
    }

    protected function tearDown(): void
    {
        JsonResponse::$haltAfterSend = true;
    }

    public function testHealthReturnsServiceName(): void
    {
        $controller = new HealthController();
        ob_start();
        $controller->index(new Request('GET', '/health', [], []));
        $output = ob_get_clean();

        $this->assertIsString($output);
        $json = json_decode($output, true);
        $this->assertTrue($json['success']);
        $this->assertSame('BookSwap', $json['data']['service']);
        $this->assertSame('ok', $json['data']['status']);
        $this->assertSame('connected', $json['data']['database']);
    }
}
