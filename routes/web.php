<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'app'     => 'Task Manager API',
        'version' => '1.0.0',
        'status'  => 'running',
        'endpoints' => [
            'create task'   => 'POST   /api/tasks',
            'list tasks'    => 'GET    /api/tasks',
            'update status' => 'PATCH  /api/tasks/{id}/status',
            'delete task'   => 'DELETE /api/tasks/{id}',
            'daily report'  => 'GET    /api/tasks/report?date=YYYY-MM-DD',
        ],
    ], 200, [], JSON_UNESCAPED_SLASHES);
});