<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Team;
use App\Models\Board;
use App\Models\Column;
use App\Models\Task;
use App\Models\Tag;
use App\Models\Comment;
use App\Models\Attachment;
use App\Models\ActivityLog;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Clear existing data (optional - comment out in production)
        $this->command->info('Clearing existing data...');

        // Create test users
        $this->command->info('Creating users...');
        $users = $this->createUsers();

        // Create test teams
        $this->command->info('Creating teams...');
        $teams = $this->createTeams($users);

        // Create test boards
        $this->command->info('Creating boards...');
        $boards = $this->createBoards($teams, $users);

        // Create columns for boards
        $this->command->info('Creating columns...');
        $columns = $this->createColumns($boards);

        // Create tags
        $this->command->info('Creating tags...');
        $tags = $this->createTags();

        // Create test tasks
        $this->command->info('Creating tasks...');
        $tasks = $this->createTasks($columns, $users, $tags);

        // Create comments
        $this->command->info('Creating comments...');
        $this->createComments($tasks, $users);

        // Create attachments
        $this->command->info('Creating attachments...');
        $this->createAttachments($tasks, $users);

        // Create activity logs
        $this->command->info('Creating activity logs...');
        $this->createActivityLogs($users, $tasks);

        $this->command->info('Database seeding completed successfully!');
    }

    /**
     * Create test users
     */
    private function createUsers(): array
    {
        $users = [];

        // Create admin user
        $users[] = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'bio' => 'System Administrator',
            'last_active_at' => now(),
        ]);

        // Create team members
        $names = [
            'John Doe',
            'Jane Smith',
            'Bob Johnson',
            'Alice Williams',
            'Charlie Brown',
            'Diana Prince',
            'Eve Martinez',
            'Frank Garcia',
            'Grace Lee',
            'Henry Wilson'
        ];

        foreach ($names as $index => $name) {
            $email = strtolower(str_replace(' ', '.', $name)) . '@example.com';

            $users[] = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make('password'),
                'bio' => 'Team member - ' . $name,
                'last_active_at' => rand(0, 1) ? now()->subDays(rand(0, 7)) : now(),
            ]);
        }

        return $users;
    }

    /**
     * Create test teams
     */
    private function createTeams(array $users): array
    {
        $teams = [];

        // Team 1: Development Team
        $team1 = Team::create([
            'owner_id' => $users[0]->id,
            'name' => 'Development Team',
            'description' => 'Main development team working on core features',
            'slug' => 'development-team-' . Str::random(8),
        ]);

        // Add members to team 1
        foreach (array_slice($users, 1, 6) as $user) {
            $team1->addMember($user, rand(0, 2) === 0 ? 'admin' : 'member');
        }

        $teams[] = $team1;

        // Team 2: Marketing Team
        $team2 = Team::create([
            'owner_id' => $users[1]->id,
            'name' => 'Marketing Team',
            'description' => 'Marketing and outreach initiatives',
            'slug' => 'marketing-team-' . Str::random(8),
        ]);

        // Add members to team 2
        foreach (array_slice($users, 5, 5) as $user) {
            $team2->addMember($user, rand(0, 3) === 0 ? 'admin' : 'member');
        }

        $teams[] = $team2;

        // Team 3: Design Team
        $team3 = Team::create([
            'owner_id' => $users[2]->id,
            'name' => 'Design Team',
            'description' => 'UI/UX design and branding',
            'slug' => 'design-team-' . Str::random(8),
        ]);

        // Add members to team 3
        foreach (array_slice($users, 3, 4) as $user) {
            $team3->addMember($user, 'member');
        }

        $teams[] = $team3;

        return $teams;
    }

    /**
     * Create test boards
     */
    private function createBoards(array $teams, array $users): array
    {
        $boards = [];
        $colors = ['#3B82F6', '#EF4444', '#10B981', '#F59E0B', '#8B5CF6', '#EC4899'];

        // Boards for each team
        $boardTemplates = [
            ['name' => 'Sprint Planning', 'description' => 'Current sprint tasks and backlog'],
            ['name' => 'Bug Tracking', 'description' => 'Track and resolve bugs'],
            ['name' => 'Feature Development', 'description' => 'New feature development'],
            ['name' => 'Campaign Management', 'description' => 'Marketing campaigns'],
            ['name' => 'Content Calendar', 'description' => 'Content planning and publishing'],
            ['name' => 'Design Projects', 'description' => 'Design work and mockups'],
        ];

        $position = 0;
        foreach ($teams as $teamIndex => $team) {
            for ($i = 0; $i < 2; $i++) {
                $template = $boardTemplates[$teamIndex * 2 + $i];

                $boards[] = Board::create([
                    'name' => $template['name'],
                    'description' => $template['description'],
                    'color' => $colors[array_rand($colors)],
                    'position' => $position++,
                    'team_id' => $team->id,
                    'created_by' => $team->owner_id,
                ]);
            }
        }

        // Personal boards
        for ($i = 0; $i < 3; $i++) {
            $boards[] = Board::create([
                'name' => 'Personal Board ' . ($i + 1),
                'description' => 'Personal task management',
                'color' => $colors[array_rand($colors)],
                'position' => $position++,
                'team_id' => null,
                'created_by' => $users[rand(0, 3)]->id,
            ]);
        }

        return $boards;
    }

    /**
     * Create columns for boards
     */
    private function createColumns(array $boards): array
    {
        $columns = [];
        $columnTemplates = [
            ['name' => 'Backlog', 'color' => '#6B7280'],
            ['name' => 'To Do', 'color' => '#3B82F6'],
            ['name' => 'In Progress', 'color' => '#F59E0B'],
            ['name' => 'Review', 'color' => '#8B5CF6'],
            ['name' => 'Done', 'color' => '#10B981'],
        ];

        foreach ($boards as $board) {
            foreach ($columnTemplates as $index => $template) {
                $columns[] = Column::create([
                    'board_id' => $board->id,
                    'name' => $template['name'],
                    'position' => $index,
                    'task_limit' => rand(0, 1) ? null : rand(5, 15),
                    'color' => $template['color'],
                ]);
            }
        }

        return $columns;
    }

    /**
     * Create tags
     */
    private function createTags(): array
    {
        $tags = [];
        $tagNames = [
            'Bug',
            'Feature',
            'Enhancement',
            'Documentation',
            'Urgent',
            'Frontend',
            'Backend',
            'Database',
            'API',
            'Testing',
            'Design',
            'Marketing',
            'Research',
            'Blocked',
            'Ready',
        ];

        $colors = ['#EF4444', '#F59E0B', '#10B981', '#3B82F6', '#8B5CF6', '#EC4899', '#6B7280'];

        foreach ($tagNames as $name) {
            $tags[] = Tag::create([
                'name' => $name,
                'color' => $colors[array_rand($colors)],
            ]);
        }

        return $tags;
    }

    /**
     * Create test tasks
     */
    private function createTasks(array $columns, array $users, array $tags): array
    {
        $tasks = [];
        $priorities = ['high', 'medium', 'low', null];

        $taskTemplates = [
            'Fix login authentication bug',
            'Implement user profile page',
            'Add email notifications',
            'Optimize database queries',
            'Create landing page design',
            'Write API documentation',
            'Set up CI/CD pipeline',
            'Add search functionality',
            'Implement dark mode',
            'Create mobile responsive layout',
            'Add social media sharing',
            'Implement two-factor authentication',
            'Create admin dashboard',
            'Add export to PDF feature',
            'Improve error handling',
            'Set up monitoring and logging',
            'Create user onboarding flow',
            'Add analytics tracking',
            'Implement caching strategy',
            'Create backup system',
        ];

        foreach ($columns as $column) {
            $taskCount = rand(2, 8);

            for ($i = 0; $i < $taskCount; $i++) {
                $title = $taskTemplates[array_rand($taskTemplates)];
                $priority = $priorities[array_rand($priorities)];

                // Determine due date
                $dueDate = null;
                if (rand(0, 2) > 0) {
                    if ($column->name === 'Done') {
                        // Completed tasks had due dates in the past
                        $dueDate = now()->subDays(rand(1, 60));
                    } elseif (rand(0, 3) === 0) {
                        // Some tasks are overdue
                        $dueDate = now()->subDays(rand(1, 14));
                    } else {
                        // Future due dates
                        $dueDate = now()->addDays(rand(1, 90));
                    }
                }

                $assignedTo = rand(0, 4) > 0 ? $users[rand(1, count($users) - 1)]->id : null;

                $task = Task::create([
                    'column_id' => $column->id,
                    'title' => $title,
                    'description' => 'Description for: ' . $title . "\n\nThis task includes:\n- Implementation details\n- Testing requirements\n- Documentation updates",
                    'position' => $i,
                    'priority' => $priority,
                    'due_date' => $dueDate,
                    'assigned_to' => $assignedTo,
                    'created_by' => $users[rand(0, 3)]->id,
                ]);

                // Attach random tags
                $tagCount = rand(0, 3);
                if ($tagCount > 0) {
                    $randomTags = array_rand($tags, min($tagCount, count($tags)));
                    if (!is_array($randomTags)) {
                        $randomTags = [$randomTags];
                    }

                    foreach ($randomTags as $tagIndex) {
                        $task->tags()->attach($tags[$tagIndex]->id);
                    }
                }

                $tasks[] = $task;
            }
        }

        return $tasks;
    }

    /**
     * Create comments
     */
    private function createComments(array $tasks, array $users): void
    {
        $commentTemplates = [
            'Great progress on this!',
            'I have a question about the implementation.',
            'Can we schedule a review for this?',
            'This looks good to me.',
            'I found an edge case we need to handle.',
            'Updated the requirements based on feedback.',
            'Ready for testing.',
            'Blocked by another task.',
            'Need more information to proceed.',
            'This is completed and tested.',
        ];

        foreach ($tasks as $task) {
            $commentCount = rand(0, 5);

            for ($i = 0; $i < $commentCount; $i++) {
                Comment::create([
                    'task_id' => $task->id,
                    'user_id' => $users[rand(0, count($users) - 1)]->id,
                    'content' => $commentTemplates[array_rand($commentTemplates)],
                    'created_at' => now()->subDays(rand(0, 30)),
                ]);
            }
        }
    }

    /**
     * Create attachments
     */
    private function createAttachments(array $tasks, array $users): void
    {
        $attachmentTemplates = [
            ['name' => 'screenshot.png', 'type' => 'image/png', 'size' => rand(100000, 5000000)],
            ['name' => 'document.pdf', 'type' => 'application/pdf', 'size' => rand(50000, 2000000)],
            ['name' => 'design-mockup.fig', 'type' => 'application/octet-stream', 'size' => rand(1000000, 10000000)],
            ['name' => 'requirements.docx', 'type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'size' => rand(20000, 500000)],
            ['name' => 'data-export.csv', 'type' => 'text/csv', 'size' => rand(10000, 1000000)],
        ];

        foreach ($tasks as $task) {
            if (rand(0, 3) === 0) { // 25% of tasks have attachments
                $attachmentCount = rand(1, 3);

                for ($i = 0; $i < $attachmentCount; $i++) {
                    $template = $attachmentTemplates[array_rand($attachmentTemplates)];

                    Attachment::create([
                        'task_id' => $task->id,
                        'user_id' => $users[rand(0, count($users) - 1)]->id,
                        'filename' => $template['name'],
                        'file_path' => 'attachments/' . Str::random(40) . '/' . $template['name'],
                        'file_size' => $template['size'],
                        'mime_type' => $template['type'],
                        'created_at' => now()->subDays(rand(0, 30)),
                    ]);
                }
            }
        }
    }

    /**
     * Create activity logs
     */
    private function createActivityLogs(array $users, array $tasks): void
    {
        $actions = [
            'created',
            'updated',
            'deleted',
            'assigned',
            'completed',
            'commented',
            'attached',
            'moved',
        ];

        foreach ($tasks as $task) {
            $activityCount = rand(1, 5);

            for ($i = 0; $i < $activityCount; $i++) {
                $action = $actions[array_rand($actions)];

                ActivityLog::create([
                    'user_id' => $users[rand(0, count($users) - 1)]->id,
                    'action' => $action,
                    'model_type' => 'App\\Models\\Task',
                    'model_id' => $task->id,
                    'changes' => [
                        'field' => 'status',
                        'old' => 'To Do',
                        'new' => 'In Progress',
                    ],
                    'description' => $action . ' task: ' . $task->title,
                    'created_at' => now()->subDays(rand(0, 30)),
                ]);
            }
        }
    }
}
