<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TaskStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = \App\Models\TaskStatus::getDefaultStatuses();

        foreach ($statuses as $status) {
            \App\Models\TaskStatus::create($status);
        }
    }
}
