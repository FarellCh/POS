<?php

namespace App\Domains\Product\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = ['name', 'slug'];

    /**
     * Relasi: Satu kategori memiliki banyak produk.
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'category_id');
    }
}