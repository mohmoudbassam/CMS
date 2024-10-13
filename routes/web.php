<?php

use App\Http\Controllers\AuthController;

//use App\Services\OpenAiImageService;
//use App\Services\SuggestionService;
//use FFMpeg\FFMpeg;
//use FFMpeg\Filters\Video\VideoFilters;
use App\Services\OpenAiImageService;
use App\Services\SuggestionService;
use FFMpeg\FFMpeg;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

//use Krisciunas\OpenAi\Api\GenerateImageCommand;
//use Krisciunas\OpenAi\Api\ImagePrompt;

//use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;
use FFMpeg\Format\Video\X264;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Symfony\Component\DomCrawler\Crawler;

Route::get('/', function () {
   $apiKey='88865ac7c92af178e40e5cb4165c725ae6aff21d1e371ff1038b279689336200';


//        $ffmpeg = FFMpeg::create(
//
//                [
//            'ffmpeg.binaries'  => '/opt/homebrew/bin/ffmpeg',
//            'ffprobe.binaries' => '/opt/homebrew/bin/ffprobe',
//            'timeout'          => 3600, // The timeout for the underlying process
//            'ffmpeg.threads'   => 12,   // The number of threads that FFMpeg should use
//        ]
//    );
//        $ffmpeg->open(storage_path('app/public/' . 'news.png'))
//        ->filters()
//        ->synchronize()
//
//            ->save($format, $videoPath);

//    $ffmpeg->open(storage_path('app/public/' . 'news.png'))
//       ->filters()
//        ->synchronize()
//        ->save(new X264(), $videoPath);


});
//
//Route::get('/', function () {
//    return inertia('app');
//});
//Route::get('login',[AuthController::class,'login'])->name('login');
//Route::post('login',[AuthController::class,'loginAction']);
//Route::get('home',[AuthController::class,'home'])->name('home');
//
