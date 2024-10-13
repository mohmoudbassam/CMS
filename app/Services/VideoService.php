<?php

namespace App\Services;


use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class VideoService
{

    public function generateVideoFromImages($images, $video_image_duration): string
    {

        $main_video_path = storage_path('app/public/' . time() . '.mp4');
        $videos = [];
        foreach ($images as $image) {
            $video = storage_path('app/public/videos/' . time() . '.mp4');
            $image = Storage::disk('public')->path($image);

            exec("ffmpeg -y -loop 1 -i $image  -c:v libx264 -t $video_image_duration -pix_fmt yuv420p -vf scale=320:240 $video");
            $videos[] = $video;
        }

        $this->mergeVideos($videos, $main_video_path);
        return $main_video_path;
    }

    public function mergeVideos($videos, $main_video_path): string
    {
        $fpm_videos = [];
        $cmd = "ffmpeg  ";
        foreach ($videos as $video) {
            $fpm_videos[] = "-i $video";
        }
        $fpm_videos = implode(' ', $fpm_videos);
        $cmd .= "$fpm_videos -filter_complex ";
        //example command
        //$pr_command = "ffmpeg -i in1.mp4 -i in2.mp4 -i in3.mp4 -i in4.mp4 -i in5.mp4 \
        //-filter_complex "[0:v]scale=640:480,setsar=1[v0];[1:v]scale=640:480,setsar=1[v1];[2:v]scale=640:480,setsar=1[v2];[3:v]scale=640:480,setsar=1[v3];[4:v]scale=640:480,setsar=1[v4];[v0][v1][v2][v3][v4]concat=n=5:v=1[outv]" \
        //-map "[outv]" merged_output.mp4";
        $cmd .= '"';
        foreach ($videos as $key => $video) {
            $cmd .= "[$key:v]scale=640:480,setsar=1[v$key];";
        }
        foreach ($videos as $key => $video) {
            $cmd .= "[v$key]";
        }

        $cmd .= "concat=n=" . count($videos) . ':v=1[outv]"' . ' -map' . ' "[outv]' . '" ' . $main_video_path;

        exec($cmd);
        return $main_video_path;
    }

    public function mergeVideoWithAudio($video_path, $audio_path): string
    {
        $output_path = storage_path('app/public/' . time() . '.mp4');
        $cmd = "ffmpeg -y -i $video_path  -i $audio_path  \
    -c:v copy \
    -map 0:v -map 1:a \
    -y $output_path";
        exec($cmd);
        return $output_path;
    }

    public function makeVideoSilent($video_path): string
    {
        $output_path = storage_path('app/public/videos' . time() . '.mp4');
        $cmd = "ffmpeg -i $video_path -c copy -an $output_path";
        exec($cmd);
        return $output_path;
    }

    public function CutVideo($video_path, $start_time=0, $end_time=60): string //
    {
        $output_path = storage_path('app/public/videos' . time() . '.mp4');
        $cmd = "ffmpeg -i $video_path -ss $start_time -t $end_time  -async 1 $output_path";
        exec($cmd);
        return $output_path;
    }

    public function getRandomVideo($folder_path): string
    {
        $files = File::files($folder_path);
        $random_video = $files[array_rand($files)];
        return $random_video->getFilename();
    }


}
