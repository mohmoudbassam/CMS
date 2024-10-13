<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	/**
	 * Run the migrations.
	 */
	public function up(): void
	{
		Schema::create('shorts', function (Blueprint $table) {
			$table->id();
			$table->foreignId('interest_id')->constrained();
			$table->string('title')->nullable();
			$table->text('description')->nullable();
			$table->text('tags')->nullable();
			$table->text('script')->nullable();
			$table->json('content')->nullable();
			$table->string('image')->nullable();
			$table->string('audio')->nullable();
			$table->text('audio_path')->nullable();
			$table->text('subtitle_path')->nullable();
			$table->double('audio_duration')->nullable();
			$table->double('video_duration')->nullable();
			$table->double('video_image_duration')->nullable();
			$table->string('video_path')->nullable();
			$table->string('video_without_sound_path')->nullable();
			$table->string('text')->nullable();
			$table->string('type')->nullable();
			$table->string('status')->nullable();
			$table->string('youtube_video_id')->nullable();
			$table->datetime('published_at')->nullable();
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('table_shorts');
	}
};
