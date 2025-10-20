<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskPriority extends Model
{
    protected $fillable = [
        'slug',
        'name',
        'color',
        'order',
    ];

    public static function getDefaultPriorities(): array
    {
        return [
            ['slug' => 'low', 'name' => 'Low', 'color' => 'green', 'order' => 1],
            ['slug' => 'medium', 'name' => 'Medium', 'color' => 'yellow', 'order' => 2],
            ['slug' => 'high', 'name' => 'High', 'color' => 'orange', 'order' => 3],
            ['slug' => 'critical', 'name' => 'Critical', 'color' => 'red', 'order' => 4],
        ];
    }
}
