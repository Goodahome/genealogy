<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(config('activitylog.table_name'), function (Blueprint $table) {
            if (! Schema::hasColumn(config('activitylog.table_name'), 'team_id')) {
                $table->foreignId('team_id')->nullable()->after('batch_uuid')->constrained()->onDelete('cascade');
            }
        });
    }

    public function down(): void
    {
        Schema::table(config('activitylog.table_name'), function (Blueprint $table) {
            if (Schema::hasColumn(config('activitylog.table_name'), 'team_id')) {
                $table->dropForeign(['team_id']);
                $table->dropColumn('team_id');
            }
        });
    }
};
