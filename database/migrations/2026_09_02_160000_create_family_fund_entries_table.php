<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('family_fund_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('person_id')->nullable()->constrained('people')->nullOnDelete();
            $table->string('type', 20); // income | expense
            $table->string('category', 50);
            $table->decimal('amount', 12, 2);
            $table->date('occurred_on');
            $table->string('title');
            $table->string('contributor')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index(['team_id', 'occurred_on']);
            $table->index(['team_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('family_fund_entries');
    }
};
