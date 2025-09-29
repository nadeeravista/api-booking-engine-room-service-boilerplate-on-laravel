<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('room_prices', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('gen_random_uuid()'));
            $table->uuid('room_rate_plan_id');
            $table->date('date');
            $table->decimal('price', 10, 2);
            $table->string('currency', 3)->default('USD');
            $table->boolean('is_available')->default(true);
            $table->timestamps();

            // Unique constraint and indexes
            $table->unique(['room_rate_plan_id', 'date']);
            $table->index('date');
            $table->index('room_rate_plan_id');
            $table->index(['date', 'is_available']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('room_prices');
    }
};
