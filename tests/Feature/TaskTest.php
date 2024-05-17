<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Task;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TaskTest extends TestCase
{
    use RefreshDatabase, WithFaker;
    /**
     * A basic feature test example.
     */
    public function test_it_can_create_task(): void
    {
        $faker = $this->faker();

        $task = Task::create([
            "title" => $faker->name,
            "description" => $faker->sentence(2),
            "long_description" => $faker->sentence(5),
            "completed" => true
        ]);

        $this->assertDatabaseHas(
            "tasks",
            [
                "title" => $task->title,
                "description" => $task->description,
                "long_description" => $task->long_description,
                "completed" => true
            ]
        );
    }

    public function test_it_can_create_10_tasks(): void
    {
        for ($i = 0; $i < 10; $i++) {
            $faker = $this->faker();
            Task::create([
                "title" => $faker->name,
                "description" => $faker->sentence(2),
                "long_description" => $faker->sentence(5),
                "completed" => true
            ]);
        }

        $this->assertEquals(10, Task::all()->count());
    }
}
