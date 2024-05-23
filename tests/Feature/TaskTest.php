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

    public function test_it_should_redirect_after_create_task(): void
    {
        $task = [
            'title' => $this->faker->name(2),
            'description' => $this->faker->sentence(2),
            'long_description' => $this->faker->sentence(7),
        ];

        $response = $this->post(route('tasks.store', $task));

        $this->assertDatabaseHas('tasks', $task);

        $response->assertSessionHas('success', 'Task created successfully!');

        $task = Task::latest()->first();

        $response->assertRedirect(route('tasks.show', ['task' => $task]));

        $response = $this->get(route('tasks.show', ['task' => $task]));

        $response->assertSee('Task created successfully!');
    }

    public function test_it_can_update_task(): void
    {
        $task = Task::factory()->create();

        $updatedTitle = $this->faker->name;
        $updatedDescription = $this->faker->paragraph(2);
        $updatedLongDescription = $this->faker->paragraph(7);

        $reponse = $this->put(
            route('tasks.update', ['task' => $task]),
            [
                'title' => $updatedTitle,
                'description' => $updatedDescription,
                'long_description' => $updatedLongDescription,
            ]
        );

        $reponse->assertRedirect(route('tasks.show', ['task' => $task]));

        $reponse = $this->get(route('tasks.show', ['task' => $task]));

        $reponse->assertSee('Task updated successfully!');

        $task = $task->fresh();

        $this->assertEquals($updatedTitle, $task->title);
        $this->assertEquals($updatedDescription, $task->description);
        $this->assertEquals($updatedLongDescription, $task->long_description);
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

        $this->assertStringContainsString('Test test', View::make('create')->render());

        $this->assertStringContainsString('Long description', View::make('create')->render());
    }
}
