<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class RepairJob extends Model
{
    use HasUuids, HasFactory, SoftDeletes;

    protected $guarded = [];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->ticket_number)) {
                $model->ticket_number = 'REP-' . strtoupper(Str::random(8));
            }
        });
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function parts(): HasMany
    {
        return $this->hasMany(RepairPart::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'received' => 'Received',
            'diagnosed' => 'Diagnosed',
            'in_progress' => 'In Progress',
            'waiting_parts' => 'Waiting for Parts',
            'completed' => 'Completed',
            'delivered' => 'Delivered',
            'cancelled' => 'Cancelled',
            default => ucfirst($this->status),
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            'received' => 'bg-info',
            'diagnosed' => 'bg-warning text-dark',
            'in_progress' => 'bg-primary',
            'waiting_parts' => 'bg-secondary',
            'completed' => 'bg-success',
            'delivered' => 'bg-dark',
            'cancelled' => 'bg-danger',
            default => 'bg-secondary',
        };
    }

    public function getPartsTotalAttribute(): float
    {
        return (float) $this->parts->sum('selling_price');
    }

    public function getBalanceAttribute(): float
    {
        return (float) $this->final_cost - (float) $this->deposit_amount;
    }
}
