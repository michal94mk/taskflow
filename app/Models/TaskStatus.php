<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

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

    /**
     * Get all statuses with caching.
     */
    public static function getCached(): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember('task_statuses', 3600, function () {
            return static::orderBy('id')->get(['id', 'name', 'slug', 'color', 'order']);
        });
    }

    /**
     * Clear the cache when status is updated or created.
     */
    protected static function booted(): void
    {
        static::saved(function () {
            Cache::forget('task_statuses');
        });

        static::deleted(function () {
            Cache::forget('task_statuses');
        });
    }
}
