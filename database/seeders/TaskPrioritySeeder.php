<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TaskPrioritySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $priorities = \App\Models\TaskPriority::getDefaultPriorities();

        foreach ($priorities as $priority) {
            \App\Models\TaskPriority::create($priority);
        }
    }
}
