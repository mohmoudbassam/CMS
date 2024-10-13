<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class ConvertImageToVideo
{
    public function __construct()
    {

    }

    public function convertImageToVideo($image): string
    {
        $image = Storage::disk('public')->path($image);
        //generate random video name

        $videoPath = storage_path('app/public/'.time().'.mp4');
        exec("ffmpeg -y -loop 1 -i $image  -c:v libx264 -t 15 -pix_fmt yuv420p -vf scale=320:240 $videoPath");
        return $videoPath;

    }
}
