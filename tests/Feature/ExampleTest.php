<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200)->assertSee("Let's get started", false)->assertSee('Browse MotoMarket');
    }

    public function test_marketplace_homepage_is_available_at_its_own_route(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Find the Motorcycle', false);
    }
}
