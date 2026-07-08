<?php

namespace App\Domains\Product\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'sku',
        'name',
        'cost_price',
        'selling_price',
        'stock',
        'is_active'
    ];

    protected $casts = [
        'cost_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'stock' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Relasi: Produk memiliki satu kategori.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * Relasi: Produk memiliki banyak catatan riwayat perubahan stok.
     */
    public function stockHistories(): HasMany
    {
        return $this->hasMany(StockHistory::class, 'product_id');
    }
}
