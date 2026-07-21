<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('phone', 30)->nullable();
            $table->string('email', 150)->nullable();
            $table->text('address')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('stock_histories', function (Blueprint $table) {
            if (! Schema::hasColumn('stock_histories', 'supplier_id')) {
                $table->foreignId('supplier_id')->nullable()->after('user_id')->constrained('suppliers')->nullOnDelete();
            }

            if (! Schema::hasColumn('stock_histories', 'before_stock')) {
                $table->integer('before_stock')->default(0)->after('quantity');
            }

            if (! Schema::hasColumn('stock_histories', 'after_stock')) {
                $table->integer('after_stock')->default(0)->after('before_stock');
            }

            if (! Schema::hasColumn('stock_histories', 'unit_cost')) {
                $table->decimal('unit_cost', 12, 2)->nullable()->after('after_stock');
            }

            if (! Schema::hasColumn('stock_histories', 'total_cost')) {
                $table->decimal('total_cost', 12, 2)->nullable()->after('unit_cost');
            }

            if (! Schema::hasColumn('stock_histories', 'reference_number')) {
                $table->string('reference_number', 100)->nullable()->after('total_cost');
            }

            if (! Schema::hasColumn('stock_histories', 'notes')) {
                $table->text('notes')->nullable()->after('reference_number');
            }
        });
    }

    public function down(): void
    {
        Schema::table('stock_histories', function (Blueprint $table) {
            if (Schema::hasColumn('stock_histories', 'notes')) {
                $table->dropColumn('notes');
            }

            if (Schema::hasColumn('stock_histories', 'reference_number')) {
                $table->dropColumn('reference_number');
            }

            if (Schema::hasColumn('stock_histories', 'total_cost')) {
                $table->dropColumn('total_cost');
            }

            if (Schema::hasColumn('stock_histories', 'unit_cost')) {
                $table->dropColumn('unit_cost');
            }

            if (Schema::hasColumn('stock_histories', 'after_stock')) {
                $table->dropColumn('after_stock');
            }

            if (Schema::hasColumn('stock_histories', 'before_stock')) {
                $table->dropColumn('before_stock');
            }

            if (Schema::hasColumn('stock_histories', 'supplier_id')) {
                $table->dropConstrainedForeignId('supplier_id');
            }
        });

        Schema::dropIfExists('suppliers');
    }
};
