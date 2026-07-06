<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Header Transaksi
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number', 50)->unique(); // INV-YYYYMMDD-XXXX
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); // Kasir
            $table->decimal('total_cost', 12, 2);   // Total modal untuk hitung profit
            $table->decimal('total_amount', 12, 2); // Total harga kotor
            $table->decimal('discount_amount', 12, 2)->default(0.00);
            $table->decimal('grand_total', 12, 2);  // Total bersih setelah diskon
            $table->decimal('paid_amount', 12, 2);  // Nominal dibayar
            $table->decimal('change_amount', 12, 2); // Uang kembalian
            $table->string('payment_method', 50);   // 'cash', 'qris', 'transfer'
            $table->timestamp('created_at')->useCurrent();
        });

        // 2. Detail Item yang Dibeli
        Schema::create('transaction_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained('transactions')->onDelete('cascade');
            $table->foreignId('product_id')->nullable()->constrained('products')->onDelete('set null');
            $table->integer('quantity');
            $table->decimal('cost_price_at_transaction', 12, 2);  // Harga modal saat dibeli (dikunci)
            $table->decimal('selling_price_at_transaction', 12, 2); // Harga jual saat dibeli (dikunci)
            $table->decimal('subtotal', 12, 2);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaction_details');
        Schema::dropIfExists('transactions');
    }
};