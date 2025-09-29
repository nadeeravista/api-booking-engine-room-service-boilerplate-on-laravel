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
            $table->string('thumbnail_url')->nullable()->change();
            $table->string('processing_status')->default('pending')->after('upload_status');
            $table->boolean('is_moderated')->default(false)->after('is_primary');
            $table->timestamp('moderated_at')->nullable()->after('is_moderated');
            $table->json('moderation_result')->nullable()->after('moderated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('room_photos', function (Blueprint $table) {
            $table->dropColumn(['processing_status', 'is_moderated', 'moderated_at', 'moderation_result']);
        });
    }
};
