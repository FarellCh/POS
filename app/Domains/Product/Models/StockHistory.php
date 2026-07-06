<?php

namespace App\Domains\Product\Models;

use App\Account\Account\Models\User as ModelsUser;
use App\Domains\Account\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockHistory extends Model
{
    // Kita menonaktifkan updated_at karena tabel ini hanya bersifat log/insert-only
    const UPDATED_AT = null; 

    protected $fillable = [
        'product_id',
        'user_id',
        'type',
        'quantity',
        'reference'
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(ModelsUser::class, 'user_id');
    }
}