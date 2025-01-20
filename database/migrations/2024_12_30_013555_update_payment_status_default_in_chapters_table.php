<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('chapters', function (Blueprint $table) {
            $table->enum('payment_status', ['paid', 'notpaid'])->default('notpaid')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    //rollback ترجع للقديم
    public function down(): void
    {
        Schema::table('chapters', function (Blueprint $table) {
            $table->enum('payment_status', ['paid', 'notpaid'])->default('paid')->change();

        });
    }
};
