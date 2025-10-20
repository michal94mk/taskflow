<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskStatus extends Model
{
    protected $fillable = [
        'slug',
        'name',
        'color',
        'order',
    ];

    public static function getDefaultStatuses(): array
    {
        return [
            ['slug' => 'to_do', 'name' => 'To Do', 'color' => 'blue', 'order' => 1],
            ['slug' => 'in_progress', 'name' => 'In Progress', 'color' => 'yellow', 'order' => 2],
            ['slug' => 'completed', 'name' => 'Completed', 'color' => 'green', 'order' => 3],
            ['slug' => 'cancelled', 'name' => 'Cancelled', 'color' => 'red', 'order' => 4],
        ];
    }
}
