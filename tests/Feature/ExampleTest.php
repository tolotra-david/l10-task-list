<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{

    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {

        $response = $this->get("/tasks/1");

        dd($response);

        $response->assertStatus(200);

        $response->assertSee('One single task');
    }

    /**
     * Second test
     */
    public function test_the_application_entry_point_redirect_to_tasks_index(): void
    {
        $response = $this->get('/tasks');

        $response->assertStatus(302);

        $response->assertRedirect(route('tasks.index'));

    }

}
