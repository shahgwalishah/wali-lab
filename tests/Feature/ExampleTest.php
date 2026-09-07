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
    public function test_guest_is_redirected_from_the_application_to_login(): void
    {
        $response = $this->get('/');

        $response->assertRedirect('/dashboard');
    }
}
