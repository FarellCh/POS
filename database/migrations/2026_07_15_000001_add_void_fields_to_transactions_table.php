<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            if (! Schema::hasColumn('transactions', 'is_voided')) {
                $table->boolean('is_voided')->default(false)->after('payment_method');
            }

            if (! Schema::hasColumn('transactions', 'voided_at')) {
                $table->timestamp('voided_at')->nullable()->after('is_voided');
            }

            if (! Schema::hasColumn('transactions', 'voided_by')) {
                $table->foreignId('voided_by')->nullable()->after('voided_at')->constrained('users')->nullOnDelete();
            }

            if (! Schema::hasColumn('transactions', 'void_reason')) {
                $table->text('void_reason')->nullable()->after('voided_by');
            }
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            if (Schema::hasColumn('transactions', 'void_reason')) {
                $table->dropColumn('void_reason');
            }

            if (Schema::hasColumn('transactions', 'voided_by')) {
                $table->dropConstrainedForeignId('voided_by');
            }

            if (Schema::hasColumn('transactions', 'voided_at')) {
                $table->dropColumn('voided_at');
            }

            if (Schema::hasColumn('transactions', 'is_voided')) {
                $table->dropColumn('is_voided');
            }
        });
    }
};
