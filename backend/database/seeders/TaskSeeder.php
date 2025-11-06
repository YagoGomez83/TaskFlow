<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        $projects = Project::all();
        $users = User::where('role', 'member')->get();
        $admin = User::where('role', 'admin')->first();

        $taskTemplates = [
            ['title' => 'Setup project repository', 'priority' => 'high', 'status' => 'done'],
            ['title' => 'Create wireframes', 'priority' => 'medium', 'status' => 'done'],
            ['title' => 'Design database schema', 'priority' => 'high', 'status' => 'in_progress'],
            ['title' => 'Implement authentication', 'priority' => 'urgent', 'status' => 'in_progress'],
            ['title' => 'Build API endpoints', 'priority' => 'high', 'status' => 'todo'],
            ['title' => 'Create UI components', 'priority' => 'medium', 'status' => 'todo'],
            ['title' => 'Write unit tests', 'priority' => 'medium', 'status' => 'todo'],
            ['title' => 'Deploy to staging', 'priority' => 'low', 'status' => 'todo'],
        ];

        foreach ($projects as $project) {
            foreach ($taskTemplates as $index => $template) {
                Task::create([
                    'title' => $template['title'],
                    'description' => "Task description for {$template['title']}",
                    'status' => $template['status'],
                    'priority' => $template['priority'],
                    'project_id' => $project->id,
                    'assigned_to' => $users->random()->id,
                    'created_by' => $admin->id,
                    'due_date' => now()->addDays(rand(1, 30)),
                    'position' => $index,
                ]);
            }
        }
    }
}
