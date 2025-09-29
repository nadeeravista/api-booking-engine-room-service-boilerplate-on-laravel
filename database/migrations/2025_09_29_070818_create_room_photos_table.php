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
        Schema::create('room_photos', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('gen_random_uuid()'));
            $table->uuid('room_id');
            $table->string('filename'); // Original filename
            $table->string('s3_key'); // S3 object key/path
            $table->string('s3_bucket'); // S3 bucket name
            $table->string('s3_region', 20)->default('us-east-1'); // S3 region
            $table->string('mime_type', 100); // image/jpeg, image/png, etc.
            $table->bigInteger('file_size'); // File size in bytes
            $table->string('url'); // Full S3 URL
            $table->string('thumbnail_url')->nullable(); // Thumbnail URL if available
            $table->integer('width')->nullable(); // Image width
            $table->integer('height')->nullable(); // Image height
            $table->integer('sort_order')->default(0); // Display order
            $table->boolean('is_primary')->default(false); // Primary photo flag
            $table->boolean('is_active')->default(true);
            $table->json('metadata')->nullable(); // Additional metadata (EXIF, etc.)
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('room_id')->references('id')->on('rooms')->onDelete('cascade');

            // Indexes
            $table->index('room_id');
            $table->index('is_active');
            $table->index('is_primary');
            $table->index(['room_id', 'is_active']);
            $table->index(['room_id', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('room_photos');
    }
};
