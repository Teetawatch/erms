<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TimeEntry extends Model
{
    use HasFactory;

    protected $fillable = ['task_id', 'user_id', 'hours', 'description', 'date_worked', 'start_time', 'end_time', 'is_overtime'];

    protected function casts(): array
    {
        return [
            'date_worked' => 'date',
            'hours' => 'decimal:2',
            'is_overtime' => 'boolean',
        ];
    }

    public function getCalculatedHoursAttribute(): float
    {
        if ($this->start_time && $this->end_time) {
            $start = \Carbon\Carbon::createFromTimeString($this->start_time);
            $end = \Carbon\Carbon::createFromTimeString($this->end_time);
            if ($end->lessThan($start)) {
                $end->addDay();
            }
            return round($start->diffInMinutes($end) / 60, 2);
        }
        return (float) $this->hours;
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
