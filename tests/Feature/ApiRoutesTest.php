<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

class ApiRoutesTest extends TestCase
{
    use RefreshDatabase;
    use WithoutMiddleware;

    public function test_dashboard_api_route_is_available(): void
    {
        $response = $this->getJson('/api/dashboard');

        $response->assertOk();
        $response->assertJsonStructure([
            'success',
            'data' => [
                'products',
                'totalProducts',
                'totalStock',
                'lowStock',
                'outOfStock',
                'healthyStock',
            ],
        ]);
    }
}
