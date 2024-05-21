<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Task;
use Illuminate\Support\Facades\View;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TaskTest extends TestCase
{
    use RefreshDatabase, WithFaker;

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
        Task::factory(10)->create();

        $this->assertEquals(10, Task::all()->count());
    }

    public function test_it_can_show_all_tasks(): void
    {
        $tasks = Task::factory(10)->create([
            "title" => $this->faker->name(2),
        ]);

        $view = View::make('index', ['tasks' => $tasks]);

        $content = $view->render();

        foreach ($tasks as $task) {
            $this->assertStringContainsString($task->title, $content);
        }
    }

    public function test_it_can_show_all_tasks_completed(): void
    {
        $tasks = Task::factory(10)->create([
            "title" => $this->faker->name(2),
        ]);

        $view = View::make('index', ['tasks' => $tasks->where('completed', true)]);

        $content = $view->render();

        foreach ($tasks as $task) {
            $this->assertStringContainsString($task->title, $content);
        }
    }

    public function test_it_can_show_one_single_task(): void
    {
        $task = Task::factory()->create([
            'title' => 'Laravel',
            'description' => 'Laravel 10'
        ]);

        $view = View::make('show', ['task' => $task]);

        $content = $view->render();

        $this->assertStringContainsString($task->title, $content);
        $this->assertStringContainsString($task->description, $content);
    }

    public function test_it_can_show_create_template()
    {
        $this->assertStringContainsString('<input type="text" name="title" id="title">', View::make('create')->render());
    }

}
