<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Task extends Model
{
    use HasFactory;

    protected $table = 'tasks';

    protected $fillable = [
        'title',
        'due_date',
        'priority',
        'status',
    ];

    protected $casts = [
        'due_date' => 'date:Y-m-d',
    ];

    public const PRIORITY_ORDER = [
        'high'   => 3,
        'medium' => 2,
        'low'    => 1,
    ];

    public const NEXT_STATUS = [
        'pending'     => 'in_progress',
        'in_progress' => 'done',
    ];

    public function canTransitionTo(string $newStatus): bool
    {
        $allowed = self::NEXT_STATUS[$this->status] ?? null;
        return $allowed === $newStatus;
    }

    public function isDeletable(): bool
    {
        return $this->status === 'done';
    }

    public function nextAllowedStatus(): ?string
    {
        return self::NEXT_STATUS[$this->status] ?? null;
    }
}