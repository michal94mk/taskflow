<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Task;
use App\Models\TaskPriority;
use App\Models\TaskStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected TaskStatus $status;
    protected TaskPriority $priority;
    protected Project $project;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        
        $this->status = TaskStatus::create([
            'name' => 'To Do',
            'slug' => 'todo',
            'color' => '#gray',
            'order' => 1,
        ]);

        $this->priority = TaskPriority::create([
            'name' => 'Medium',
            'slug' => 'medium',
            'color' => '#blue',
            'order' => 1,
        ]);

        $this->project = Project::create([
            'user_id' => $this->user->id,
            'name' => 'Test Project',
            'status' => 'active',
        ]);
    }

    public function test_guests_cannot_search()
    {
        $response = $this->getJson(route('search', ['q' => 'test']));
        $response->assertUnauthorized();
    }

    public function test_empty_query_returns_empty_results()
    {
        $this->actingAs($this->user);

        $response = $this->getJson(route('search', ['q' => '']));
        
        $response->assertOk()
            ->assertJson([
                'tasks' => [],
                'projects' => [],
            ]);
    }

    public function test_can_search_tasks_by_title()
    {
        $this->actingAs($this->user);

        Task::create([
            'user_id' => $this->user->id,
            'project_id' => $this->project->id,
            'title' => 'Fix login bug',
            'task_status_id' => $this->status->id,
            'task_priority_id' => $this->priority->id,
        ]);

        Task::create([
            'user_id' => $this->user->id,
            'project_id' => $this->project->id,
            'title' => 'Update documentation',
            'task_status_id' => $this->status->id,
            'task_priority_id' => $this->priority->id,
        ]);

        $response = $this->getJson(route('search', ['q' => 'login']));

        $response->assertOk()
            ->assertJsonPath('tasks.0.title', 'Fix login bug')
            ->assertJsonCount(1, 'tasks');
    }

    public function test_can_search_tasks_by_description()
    {
        $this->actingAs($this->user);

        Task::create([
            'user_id' => $this->user->id,
            'project_id' => $this->project->id,
            'title' => 'Task 1',
            'description' => 'This task involves authentication system',
            'task_status_id' => $this->status->id,
            'task_priority_id' => $this->priority->id,
        ]);

        $response = $this->getJson(route('search', ['q' => 'authentication']));

        $response->assertOk()
            ->assertJsonPath('tasks.0.title', 'Task 1')
            ->assertJsonCount(1, 'tasks');
    }

    public function test_can_search_projects()
    {
        $this->actingAs($this->user);

        Project::create([
            'user_id' => $this->user->id,
            'name' => 'Website Redesign',
            'status' => 'active',
        ]);

        Project::create([
            'user_id' => $this->user->id,
            'name' => 'Mobile App',
            'status' => 'active',
        ]);

        $response = $this->getJson(route('search', ['q' => 'Website']));

        $response->assertOk()
            ->assertJsonPath('projects.0.title', 'Website Redesign')
            ->assertJsonCount(1, 'projects');
    }

    public function test_search_only_returns_current_users_data()
    {
        $otherUser = User::factory()->create();

        // Other user's task
        $otherProject = Project::create([
            'user_id' => $otherUser->id,
            'name' => 'Other Project',
            'status' => 'active',
        ]);

        Task::create([
            'user_id' => $otherUser->id,
            'project_id' => $otherProject->id,
            'title' => 'Other user task',
            'task_status_id' => $this->status->id,
            'task_priority_id' => $this->priority->id,
        ]);

        // Current user's task
        Task::create([
            'user_id' => $this->user->id,
            'project_id' => $this->project->id,
            'title' => 'My task',
            'task_status_id' => $this->status->id,
            'task_priority_id' => $this->priority->id,
        ]);

        $this->actingAs($this->user);

        $response = $this->getJson(route('search', ['q' => 'task']));

        $response->assertOk()
            ->assertJsonCount(1, 'tasks')
            ->assertJsonPath('tasks.0.title', 'My task');
    }

    public function test_search_limits_results_to_5_per_category()
    {
        $this->actingAs($this->user);

        // Create 10 tasks
        for ($i = 1; $i <= 10; $i++) {
            Task::create([
                'user_id' => $this->user->id,
                'project_id' => $this->project->id,
                'title' => "Test task $i",
                'task_status_id' => $this->status->id,
                'task_priority_id' => $this->priority->id,
            ]);
        }

        $response = $this->getJson(route('search', ['q' => 'Test']));

        $response->assertOk()
            ->assertJsonCount(5, 'tasks'); // Should limit to 5
    }

    public function test_search_sanitizes_wildcard_characters()
    {
        $this->actingAs($this->user);

        Task::create([
            'user_id' => $this->user->id,
            'project_id' => $this->project->id,
            'title' => 'Test 100% completion',
            'task_status_id' => $this->status->id,
            'task_priority_id' => $this->priority->id,
        ]);

        // Search with % should be escaped and not match everything
        $response = $this->getJson(route('search', ['q' => '%']));

        $response->assertOk()
            ->assertJsonCount(1, 'tasks');
    }

    public function test_search_validates_query_length()
    {
        $this->actingAs($this->user);

        $longQuery = str_repeat('a', 256); // Exceeds max length

        $response = $this->getJson(route('search', ['q' => $longQuery]));

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['q']);
    }
}

