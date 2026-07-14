<?php

namespace App\Domains\Transaction\Models;

use App\Domains\Product\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransactionDetail extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'transaction_id',
        'product_id',
        'quantity',
        'cost_price_at_transaction',
        'selling_price_at_transaction',
        'subtotal'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'cost_price_at_transaction' => 'decimal:2',
        'selling_price_at_transaction' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class, 'transaction_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
