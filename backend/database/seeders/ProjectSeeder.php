<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@taskflow.com')->first();
        $manager = User::where('email', 'john@taskflow.com')->first();

        $projects = [
            [
                'name' => 'Website Redesign',
                'description' => 'Complete redesign of company website with modern UI/UX',
                'color' => '#3B82F6',
                'owner_id' => $admin->id,
                'status' => 'active',
            ],
            [
                'name' => 'Mobile App Development',
                'description' => 'Native mobile application for iOS and Android',
                'color' => '#10B981',
                'owner_id' => $manager->id,
                'status' => 'active',
            ],
            [
                'name' => 'API Integration',
                'description' => 'Integrate third-party APIs for payment and notifications',
                'color' => '#F59E0B',
                'owner_id' => $admin->id,
                'status' => 'active',
            ],
        ];

        foreach ($projects as $projectData) {
            $project = Project::create($projectData);

            // Add members to project
            $members = User::where('role', 'member')->get();
            foreach ($members as $member) {
                $project->addMember($member, 'member');
            }

            // Add owner as admin
            $project->addMember($project->owner, 'admin');
        }
    }
}
