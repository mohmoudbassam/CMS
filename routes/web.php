<?php

use App\Jobs\UploadVideoToYoutube;

Route::get('test', function () {
	$videos = [];
	foreach (\App\Models\Short::query()->whereNotNull('video_path')->whereNull('youtube_video_id')->get() as $short) {
		$videos[] = new UploadVideoToYoutube($short);
	}
	Bus::chain($videos)->dispatch();
});