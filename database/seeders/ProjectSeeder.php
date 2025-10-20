<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = \App\Models\User::first();

        if (! $user) {
            $user = \App\Models\User::factory()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);
        }

        $projects = [
            [
                'name' => 'TaskFlow Development',
                'description' => 'Main project for TaskFlow application development',
                'status' => 'active',
                'start_date' => now()->subDays(30),
                'end_date' => now()->addDays(60),
                'user_id' => $user->id,
            ],
            [
                'name' => 'Website Redesign',
                'description' => 'Redesign company website with modern UI/UX',
                'status' => 'active',
                'start_date' => now()->subDays(15),
                'end_date' => now()->addDays(30),
                'user_id' => $user->id,
            ],
            [
                'name' => 'Mobile App',
                'description' => 'Develop mobile application for iOS and Android',
                'status' => 'on_hold',
                'start_date' => now()->subDays(10),
                'end_date' => now()->addDays(90),
                'user_id' => $user->id,
            ],
        ];

        foreach ($projects as $project) {
            \App\Models\Project::create($project);
        }
    }
}
