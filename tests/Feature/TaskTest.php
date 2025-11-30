<?php

namespace Tests\Feature;

use App\Models\Board;
use App\Models\Column;
use App\Models\Tag;
use App\Models\Task;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $user;
    protected User $otherUser;
    protected Team $team;
    protected Board $board;
    protected Column $column;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->otherUser = User::factory()->create();

        $this->team = Team::factory()->create(['owner_id' => $this->user->id]);
        $this->team->members()->attach($this->user->id, ['role' => 'owner']);

        $this->board = Board::factory()->create(['team_id' => $this->team->id]);
        $this->column = Column::factory()->create(['board_id' => $this->board->id]);
    }

    public function test_authenticated_user_can_create_task(): void
    {
        $taskData = [
            'title' => 'New Task',
            'description' => 'Task description',
            'column_id' => $this->column->id,
            'priority' => 'high',
            'due_date' => now()->addDays(7)->format('Y-m-d'),
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/tasks', $taskData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'title',
                'description',
                'column_id',
                'priority',
                'due_date',
                'created_at',
                'updated_at',
            ]);

        $this->assertDatabaseHas('tasks', [
            'title' => 'New Task',
            'description' => 'Task description',
            'column_id' => $this->column->id,
            'priority' => 'high',
        ]);
    }

    public function test_unauthenticated_user_cannot_create_task(): void
    {
        $taskData = [
            'title' => 'New Task',
            'column_id' => $this->column->id,
        ];

        $response = $this->postJson('/api/tasks', $taskData);

        $response->assertStatus(401);
    }

    public function test_task_creation_requires_title(): void
    {
        $taskData = [
            'description' => 'Task description',
            'column_id' => $this->column->id,
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/tasks', $taskData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title']);
    }

    public function test_task_creation_requires_valid_column(): void
    {
        $taskData = [
            'title' => 'New Task',
            'column_id' => 99999,
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/tasks', $taskData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['column_id']);
    }

    public function test_authenticated_user_can_update_own_team_task(): void
    {
        $task = Task::factory()->create([
            'column_id' => $this->column->id,
            'created_by' => $this->user->id,
        ]);

        $updateData = [
            'title' => 'Updated Task Title',
            'description' => 'Updated description',
            'priority' => 'urgent',
        ];

        $response = $this->actingAs($this->user)
            ->putJson("/api/tasks/{$task->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'title' => 'Updated Task Title',
                'description' => 'Updated description',
                'priority' => 'urgent',
            ]);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Updated Task Title',
            'description' => 'Updated description',
            'priority' => 'urgent',
        ]);
    }

    public function test_user_cannot_update_task_from_other_team(): void
    {
        $otherTeam = Team::factory()->create(['owner_id' => $this->otherUser->id]);
        $otherBoard = Board::factory()->create(['team_id' => $otherTeam->id]);
        $otherColumn = Column::factory()->create(['board_id' => $otherBoard->id]);

        $task = Task::factory()->create([
            'column_id' => $otherColumn->id,
            'created_by' => $this->otherUser->id,
        ]);

        $updateData = [
            'title' => 'Hacked Title',
        ];

        $response = $this->actingAs($this->user)
            ->putJson("/api/tasks/{$task->id}", $updateData);

        $response->assertStatus(403);
    }

    public function test_authenticated_user_can_delete_own_task(): void
    {
        $task = Task::factory()->create([
            'column_id' => $this->column->id,
            'created_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)
            ->deleteJson("/api/tasks/{$task->id}");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Task deleted successfully',
            ]);

        $this->assertSoftDeleted('tasks', [
            'id' => $task->id,
        ]);
    }

    public function test_user_cannot_delete_task_from_other_team(): void
    {
        $otherTeam = Team::factory()->create(['owner_id' => $this->otherUser->id]);
        $otherBoard = Board::factory()->create(['team_id' => $otherTeam->id]);
        $otherColumn = Column::factory()->create(['board_id' => $otherBoard->id]);

        $task = Task::factory()->create([
            'column_id' => $otherColumn->id,
            'created_by' => $this->otherUser->id,
        ]);

        $response = $this->actingAs($this->user)
            ->deleteJson("/api/tasks/{$task->id}");

        $response->assertStatus(403);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'deleted_at' => null,
        ]);
    }

    public function test_team_member_can_view_team_tasks(): void
    {
        $task = Task::factory()->create([
            'column_id' => $this->column->id,
            'created_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/tasks');

        $response->assertStatus(200)
            ->assertJsonFragment([
                'id' => $task->id,
                'title' => $task->title,
            ]);
    }

    public function test_user_can_move_task_to_different_column(): void
    {
        $task = Task::factory()->create([
            'column_id' => $this->column->id,
            'created_by' => $this->user->id,
            'position' => 0,
        ]);

        $newColumn = Column::factory()->create(['board_id' => $this->board->id]);

        $response = $this->actingAs($this->user)
            ->postJson("/api/tasks/{$task->id}/move", [
                'column_id' => $newColumn->id,
                'position' => 0,
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'column_id' => $newColumn->id,
                'position' => 0,
            ]);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'column_id' => $newColumn->id,
            'position' => 0,
        ]);
    }

    public function test_user_cannot_move_task_to_column_in_different_board(): void
    {
        $task = Task::factory()->create([
            'column_id' => $this->column->id,
            'created_by' => $this->user->id,
        ]);

        $otherBoard = Board::factory()->create(['team_id' => $this->team->id]);
        $otherColumn = Column::factory()->create(['board_id' => $otherBoard->id]);

        $response = $this->actingAs($this->user)
            ->postJson("/api/tasks/{$task->id}/move", [
                'column_id' => $otherColumn->id,
                'position' => 0,
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['column_id']);
    }

    public function test_task_can_have_tags_attached(): void
    {
        $task = Task::factory()->create([
            'column_id' => $this->column->id,
            'created_by' => $this->user->id,
        ]);

        $tag1 = Tag::factory()->create(['team_id' => $this->team->id]);
        $tag2 = Tag::factory()->create(['team_id' => $this->team->id]);

        $response = $this->actingAs($this->user)
            ->putJson("/api/tasks/{$task->id}", [
                'title' => $task->title,
                'tags' => [$tag1->id, $tag2->id],
            ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('task_tag', [
            'task_id' => $task->id,
            'tag_id' => $tag1->id,
        ]);

        $this->assertDatabaseHas('task_tag', [
            'task_id' => $task->id,
            'tag_id' => $tag2->id,
        ]);
    }

    public function test_task_priority_must_be_valid_value(): void
    {
        $taskData = [
            'title' => 'New Task',
            'column_id' => $this->column->id,
            'priority' => 'invalid_priority',
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/tasks', $taskData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['priority']);
    }

    public function test_task_due_date_must_be_valid_date(): void
    {
        $taskData = [
            'title' => 'New Task',
            'column_id' => $this->column->id,
            'due_date' => 'not-a-date',
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/tasks', $taskData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['due_date']);
    }

    public function test_user_can_reorder_tasks_within_column(): void
    {
        $task1 = Task::factory()->create([
            'column_id' => $this->column->id,
            'created_by' => $this->user->id,
            'position' => 0,
        ]);

        $task2 = Task::factory()->create([
            'column_id' => $this->column->id,
            'created_by' => $this->user->id,
            'position' => 1,
        ]);

        $response = $this->actingAs($this->user)
            ->postJson("/api/tasks/{$task1->id}/reorder", [
                'position' => 1,
            ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('tasks', [
            'id' => $task1->id,
            'position' => 1,
        ]);
    }

    public function test_task_list_can_be_filtered_by_priority(): void
    {
        Task::factory()->create([
            'column_id' => $this->column->id,
            'created_by' => $this->user->id,
            'priority' => 'high',
            'title' => 'High Priority Task',
        ]);

        Task::factory()->create([
            'column_id' => $this->column->id,
            'created_by' => $this->user->id,
            'priority' => 'low',
            'title' => 'Low Priority Task',
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/tasks?priority=high');

        $response->assertStatus(200)
            ->assertJsonFragment(['title' => 'High Priority Task'])
            ->assertJsonMissing(['title' => 'Low Priority Task']);
    }

    public function test_task_list_can_be_searched(): void
    {
        Task::factory()->create([
            'column_id' => $this->column->id,
            'created_by' => $this->user->id,
            'title' => 'Implement authentication',
        ]);

        Task::factory()->create([
            'column_id' => $this->column->id,
            'created_by' => $this->user->id,
            'title' => 'Fix bug in dashboard',
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/tasks?search=authentication');

        $response->assertStatus(200)
            ->assertJsonFragment(['title' => 'Implement authentication'])
            ->assertJsonMissing(['title' => 'Fix bug in dashboard']);
    }

    public function test_only_team_members_can_access_team_tasks(): void
    {
        $task = Task::factory()->create([
            'column_id' => $this->column->id,
            'created_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->otherUser)
            ->getJson("/api/tasks/{$task->id}");

        $response->assertStatus(403);
    }
}
