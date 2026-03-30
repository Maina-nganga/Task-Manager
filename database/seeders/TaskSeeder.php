<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        $today     = now()->toDateString();
        $tomorrow  = now()->addDay()->toDateString();
        $nextWeek  = now()->addWeek()->toDateString();

        DB::table('tasks')->insert([
            [
                'title'      => 'Fix critical production bug',
                'due_date'   => $today,
                'priority'   => 'high',
                'status'     => 'in_progress',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title'      => 'Deploy security patch',
                'due_date'   => $today,
                'priority'   => 'high',
                'status'     => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title'      => 'Review authentication flow',
                'due_date'   => $tomorrow,
                'priority'   => 'high',
                'status'     => 'done',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title'      => 'Write unit tests for API',
                'due_date'   => $tomorrow,
                'priority'   => 'medium',
                'status'     => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title'      => 'Update project documentation',
                'due_date'   => $nextWeek,
                'priority'   => 'medium',
                'status'     => 'done',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title'      => 'Refactor legacy helper functions',
                'due_date'   => $nextWeek,
                'priority'   => 'low',
                'status'     => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title'      => 'Clean up unused CSS',
                'due_date'   => $nextWeek,
                'priority'   => 'low',
                'status'     => 'done',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}