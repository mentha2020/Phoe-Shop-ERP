<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class JournalEntry extends Model
{
    use HasUuids, HasFactory, SoftDeletes;

    protected $guarded = [];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->entry_number)) {
                $model->entry_number = 'JE-' . strtoupper(Str::random(8));
            }
        });
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(JournalEntryItem::class);
    }

    public function isBalanced(): bool
    {
        return abs($this->total_debit - $this->total_credit) < 0.01;
    }

    public function calculateTotals(): void
    {
        $this->total_debit = $this->items->sum('debit');
        $this->total_credit = $this->items->sum('credit');
        $this->save();
    }
}
