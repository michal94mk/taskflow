<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

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

    /**
     * Get all priorities with caching.
     */
    public static function getCached(): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember('task_priorities', 3600, function () {
            return static::orderBy('id')->get(['id', 'name', 'slug', 'color', 'order']);
        });
    }

    /**
     * Clear the cache when priority is updated or created.
     */
    protected static function booted(): void
    {
        static::saved(function () {
            Cache::forget('task_priorities');
        });

        static::deleted(function () {
            Cache::forget('task_priorities');
        });
    }
}
