<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('room_photos', function (Blueprint $table) {
            $table->string('upload_status')->default('pending')->after('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('room_photos', function (Blueprint $table) {
            $table->dropColumn('upload_status');
        });
    }
};
