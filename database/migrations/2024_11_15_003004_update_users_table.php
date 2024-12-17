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
        Schema::table('users', function (Blueprint $table) {
            $table->string('image')->nullable(); // إضافة عمود الصورة
            $table->enum('role', ['admin','reader'])->default('reader'); // إضافة عمود الدور
            $table->enum('gender', ['male', 'female']); // إضافة عمود الجندر
            $table->string('username')->unique(); // إضافة عمود اليوزرنيم
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn('image');
        $table->dropColumn('role');
        $table->dropColumn('gender');
        $table->dropColumn('username');
    });
    }
};

