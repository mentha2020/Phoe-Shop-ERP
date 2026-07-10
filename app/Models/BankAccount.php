<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BankAccount extends Model
{
    use HasUuids, HasFactory, SoftDeletes;

    protected $guarded = [];

    public function journalEntryItems(): HasMany
    {
        return $this->hasMany(JournalEntryItem::class);
    }

    public function getIsDefaultAttribute(): bool
    {
        return $this->attributes['is_default'] ?? false;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }
}
