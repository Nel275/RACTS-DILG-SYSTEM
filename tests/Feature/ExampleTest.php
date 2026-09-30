<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * This is Laravel's own default placeholder test, generated automatically
 * when the project was first created in Phase 1. Its original assertion
 * (that "/" returns a plain 200 response) stopped being true the moment
 * Phase 1 made "/" redirect to either /login or /dashboard - so it's
 * updated here to check the behavior RACTS actually has, instead of being
 * deleted outright.
 */
class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_root_url_redirects_a_guest_to_login(): void
    {
        $response = $this->get('/');

        $response->assertRedirect('/login');
    }
}
