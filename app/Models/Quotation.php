<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Quotation extends Model
{
    use HasUuids, HasFactory, SoftDeletes;

    protected $guarded = [];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->quotation_number)) {
                $model->quotation_number = 'QUO-' . strtoupper(Str::random(8));
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

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function convertedSale(): BelongsTo
    {
        return $this->belongsTo(Sale::class, 'converted_sale_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(QuotationItem::class);
    }

    public function calculateTotals(): void
    {
        $this->subtotal = $this->items->sum('subtotal');
        $this->total = $this->subtotal - $this->discount_amount + $this->tax_amount;
        $this->save();
    }
}
