<?php

namespace Tests\Feature;

use App\Models\Board;
use App\Models\Column;
use App\Models\Comment;
use App\Models\Task;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $user;
    protected User $teamMember;
    protected User $outsider;
    protected Team $team;
    protected Board $board;
    protected Column $column;
    protected Task $task;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->teamMember = User::factory()->create();
        $this->outsider = User::factory()->create();

        $this->team = Team::factory()->create(['owner_id' => $this->user->id]);
        $this->team->members()->attach($this->user->id, ['role' => 'owner']);
        $this->team->members()->attach($this->teamMember->id, ['role' => 'member']);

        $this->board = Board::factory()->create(['team_id' => $this->team->id]);
        $this->column = Column::factory()->create(['board_id' => $this->board->id]);
        $this->task = Task::factory()->create([
            'column_id' => $this->column->id,
            'created_by' => $this->user->id,
        ]);
    }

    public function test_team_member_can_create_comment_on_task(): void
    {
        $commentData = [
            'content' => 'This is a great task!',
        ];

        $response = $this->actingAs($this->user)
            ->postJson("/api/tasks/{$this->task->id}/comments", $commentData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'content',
                'task_id',
                'user_id',
                'created_at',
                'updated_at',
            ])
            ->assertJson([
                'content' => 'This is a great task!',
                'task_id' => $this->task->id,
                'user_id' => $this->user->id,
            ]);

        $this->assertDatabaseHas('comments', [
            'content' => 'This is a great task!',
            'task_id' => $this->task->id,
            'user_id' => $this->user->id,
        ]);
    }

    public function test_unauthenticated_user_cannot_create_comment(): void
    {
        $commentData = [
            'content' => 'This is a comment',
        ];

        $response = $this->postJson("/api/tasks/{$this->task->id}/comments", $commentData);

        $response->assertStatus(401);
    }

    public function test_comment_creation_requires_content(): void
    {
        $commentData = [];

        $response = $this->actingAs($this->user)
            ->postJson("/api/tasks/{$this->task->id}/comments", $commentData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['content']);
    }

    public function test_comment_content_cannot_be_empty(): void
    {
        $commentData = [
            'content' => '',
        ];

        $response = $this->actingAs($this->user)
            ->postJson("/api/tasks/{$this->task->id}/comments", $commentData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['content']);
    }

    public function test_non_team_member_cannot_comment_on_task(): void
    {
        $commentData = [
            'content' => 'Unauthorized comment',
        ];

        $response = $this->actingAs($this->outsider)
            ->postJson("/api/tasks/{$this->task->id}/comments", $commentData);

        $response->assertStatus(403);

        $this->assertDatabaseMissing('comments', [
            'content' => 'Unauthorized comment',
            'task_id' => $this->task->id,
        ]);
    }

    public function test_user_can_delete_own_comment(): void
    {
        $comment = Comment::factory()->create([
            'task_id' => $this->task->id,
            'user_id' => $this->user->id,
            'content' => 'My comment',
        ]);

        $response = $this->actingAs($this->user)
            ->deleteJson("/api/comments/{$comment->id}");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Comment deleted successfully',
            ]);

        $this->assertSoftDeleted('comments', [
            'id' => $comment->id,
        ]);
    }

    public function test_user_cannot_delete_other_users_comment(): void
    {
        $comment = Comment::factory()->create([
            'task_id' => $this->task->id,
            'user_id' => $this->teamMember->id,
            'content' => 'Team member comment',
        ]);

        $response = $this->actingAs($this->user)
            ->deleteJson("/api/comments/{$comment->id}");

        $response->assertStatus(403);

        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'deleted_at' => null,
        ]);
    }

    public function test_unauthenticated_user_cannot_delete_comment(): void
    {
        $comment = Comment::factory()->create([
            'task_id' => $this->task->id,
            'user_id' => $this->user->id,
        ]);

        $response = $this->deleteJson("/api/comments/{$comment->id}");

        $response->assertStatus(401);
    }

    public function test_user_can_update_own_comment(): void
    {
        $comment = Comment::factory()->create([
            'task_id' => $this->task->id,
            'user_id' => $this->user->id,
            'content' => 'Original comment',
        ]);

        $updateData = [
            'content' => 'Updated comment',
        ];

        $response = $this->actingAs($this->user)
            ->putJson("/api/comments/{$comment->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'content' => 'Updated comment',
            ]);

        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'content' => 'Updated comment',
        ]);
    }

    public function test_user_cannot_update_other_users_comment(): void
    {
        $comment = Comment::factory()->create([
            'task_id' => $this->task->id,
            'user_id' => $this->teamMember->id,
            'content' => 'Original comment',
        ]);

        $updateData = [
            'content' => 'Hacked comment',
        ];

        $response = $this->actingAs($this->user)
            ->putJson("/api/comments/{$comment->id}", $updateData);

        $response->assertStatus(403);

        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'content' => 'Original comment',
        ]);
    }

    public function test_team_member_can_view_task_comments(): void
    {
        $comment1 = Comment::factory()->create([
            'task_id' => $this->task->id,
            'user_id' => $this->user->id,
            'content' => 'First comment',
        ]);

        $comment2 = Comment::factory()->create([
            'task_id' => $this->task->id,
            'user_id' => $this->teamMember->id,
            'content' => 'Second comment',
        ]);

        $response = $this->actingAs($this->teamMember)
            ->getJson("/api/tasks/{$this->task->id}/comments");

        $response->assertStatus(200)
            ->assertJsonFragment(['content' => 'First comment'])
            ->assertJsonFragment(['content' => 'Second comment']);
    }

    public function test_non_team_member_cannot_view_task_comments(): void
    {
        $comment = Comment::factory()->create([
            'task_id' => $this->task->id,
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->outsider)
            ->getJson("/api/tasks/{$this->task->id}/comments");

        $response->assertStatus(403);
    }

    public function test_comments_are_returned_with_user_information(): void
    {
        $comment = Comment::factory()->create([
            'task_id' => $this->task->id,
            'user_id' => $this->user->id,
            'content' => 'Test comment',
        ]);

        $response = $this->actingAs($this->user)
            ->getJson("/api/tasks/{$this->task->id}/comments");

        $response->assertStatus(200)
            ->assertJsonStructure([
                '*' => [
                    'id',
                    'content',
                    'task_id',
                    'user_id',
                    'created_at',
                    'updated_at',
                    'user' => [
                        'id',
                        'name',
                        'email',
                    ],
                ],
            ]);
    }

    public function test_comments_are_ordered_by_creation_date(): void
    {
        $comment1 = Comment::factory()->create([
            'task_id' => $this->task->id,
            'user_id' => $this->user->id,
            'content' => 'Older comment',
            'created_at' => now()->subHours(2),
        ]);

        $comment2 = Comment::factory()->create([
            'task_id' => $this->task->id,
            'user_id' => $this->user->id,
            'content' => 'Newer comment',
            'created_at' => now()->subHour(),
        ]);

        $response = $this->actingAs($this->user)
            ->getJson("/api/tasks/{$this->task->id}/comments");

        $response->assertStatus(200);

        $comments = $response->json();
        $this->assertEquals('Older comment', $comments[0]['content']);
        $this->assertEquals('Newer comment', $comments[1]['content']);
    }

    public function test_deleting_task_soft_deletes_comments(): void
    {
        $comment = Comment::factory()->create([
            'task_id' => $this->task->id,
            'user_id' => $this->user->id,
        ]);

        $this->actingAs($this->user)
            ->deleteJson("/api/tasks/{$this->task->id}");

        $this->assertSoftDeleted('tasks', [
            'id' => $this->task->id,
        ]);

        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'task_id' => $this->task->id,
        ]);
    }

    public function test_comment_content_has_maximum_length(): void
    {
        $commentData = [
            'content' => str_repeat('a', 10001),
        ];

        $response = $this->actingAs($this->user)
            ->postJson("/api/tasks/{$this->task->id}/comments", $commentData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['content']);
    }

    public function test_user_can_view_single_comment(): void
    {
        $comment = Comment::factory()->create([
            'task_id' => $this->task->id,
            'user_id' => $this->user->id,
            'content' => 'Test comment',
        ]);

        $response = $this->actingAs($this->user)
            ->getJson("/api/comments/{$comment->id}");

        $response->assertStatus(200)
            ->assertJson([
                'id' => $comment->id,
                'content' => 'Test comment',
            ]);
    }

    public function test_non_team_member_cannot_view_comment(): void
    {
        $comment = Comment::factory()->create([
            'task_id' => $this->task->id,
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->outsider)
            ->getJson("/api/comments/{$comment->id}");

        $response->assertStatus(403);
    }

    public function test_team_owner_can_delete_any_comment(): void
    {
        $comment = Comment::factory()->create([
            'task_id' => $this->task->id,
            'user_id' => $this->teamMember->id,
            'content' => 'Member comment',
        ]);

        $response = $this->actingAs($this->user)
            ->deleteJson("/api/comments/{$comment->id}");

        $response->assertStatus(200);

        $this->assertSoftDeleted('comments', [
            'id' => $comment->id,
        ]);
    }

    public function test_comment_includes_timestamps(): void
    {
        $comment = Comment::factory()->create([
            'task_id' => $this->task->id,
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)
            ->getJson("/api/comments/{$comment->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'created_at',
                'updated_at',
            ]);
    }
}
