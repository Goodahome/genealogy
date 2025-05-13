<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('people', function (Blueprint $table) {
            // 农历出生日期字段
            $table->integer('dob_lunar_year')->nullable()->after('is_lunar_dob');
            $table->integer('dob_lunar_month')->nullable()->after('dob_lunar_year');
            $table->integer('dob_lunar_day')->nullable()->after('dob_lunar_month');
            $table->boolean('dob_lunar_leap')->default(false)->after('dob_lunar_day');
            
            // 农历死亡日期字段
            $table->integer('dod_lunar_year')->nullable()->after('is_lunar_dod');
            $table->integer('dod_lunar_month')->nullable()->after('dod_lunar_year');
            $table->integer('dod_lunar_day')->nullable()->after('dod_lunar_month');
            $table->boolean('dod_lunar_leap')->default(false)->after('dod_lunar_day');
        });
    }

    public function down(): void
    {
        Schema::table('people', function (Blueprint $table) {
            $table->dropColumn([
                'dob_lunar_year', 'dob_lunar_month', 'dob_lunar_day', 'dob_lunar_leap',
                'dod_lunar_year', 'dod_lunar_month', 'dod_lunar_day', 'dod_lunar_leap'
            ]);
        });
    }
};