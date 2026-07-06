<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 100)->unique();
            $table->timestamps();
        });


        Schema::create('products' , function (Blueprint $table){
            $table->id();
            //jika kategori di hapus, produk tetap ada tapi kategori null
            $table->foreignId('category_id')->nullable()->constrained('categories');
            $table->string('sku', 50)->unique(); //-> code barcode barang
            $table->string('nama', 150);
            $table->decimal('cost_price' , 12, 2);
            $table->decimal('selling_price' , 12, 2);
            $table->integer('stock')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        // Constraint PostgreSQL agar stok tidak bisa di-update menjadi minus (< 0)
        DB::statement('ALTER table products ADDc CONSTRAINT chk_stock_positive CHECK (stock >= 0)');

       
        Schema::create('stock_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('type', 20); // 'in', 'out', 'adjustment'
            $table->integer('quantity');
            $table->string('reference')->nullable(); // Contoh: "Penjualan INV-xxx"
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_histories');
        Schema::dropIfExists('products');
        Schema::dropIfExists('categories');
    }
};
