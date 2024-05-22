<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Task;
use Illuminate\Support\Facades\View;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TaskTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    public function test_it_can_create_1000_tasks(): void
    {
        Task::factory(1000)->create();

        $this->assertEquals(1000, Task::all()->count());
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

    public function test_it_can_show_one_task(): void
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

    public function test_it_can_show_create_task_template(): void
    {
        $this->assertStringContainsString('<input type="text" name="title" id="title">', View::make('create')->render());
    }

    public function test_it_should_redirect_after_create_task(): void
    {
        $task = new Task;
        $task->title = 'Test';
        $task->description = 'Test test';
        $task->long_description = 'Long description';

        $response = $this->post('/tasks', $task->getAttributes());

        $this->assertDatabaseHas('tasks', $task->getAttributes());

        $newModel = Task::latest()->first();

        $response->assertRedirect(route('tasks.show', ['id' => $newModel->id]));
    }

    public function test_it_should_redirect_on_the_same_page_if_errors(): void
    {
        $task = new Task;
        $task->description = 'Test test';
        $task->long_description = 'Long description';

        $response = $this->post('/tasks', $task->getAttributes());

        $this->assertDatabaseMissing('tasks', $task->getAttributes());

        $response->assertRedirect();

        $response->assertSessionHasErrors('title');

    }

}
