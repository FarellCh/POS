<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('payment_methods', 'account_number')) {
            Schema::table('payment_methods', function (Blueprint $table) {
                $table->string('account_number', 100)->nullable()->after('label');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('payment_methods', 'account_number')) {
            Schema::table('payment_methods', function (Blueprint $table) {
                $table->dropColumn('account_number');
            });
        }
    }
};
