<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DjController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/', function () {
    return view('welcome');
});

//login画面表示
Route::get('/Dj/login', [DjController::class, 'login']);
//login画面のpost処理
Route::post('/Dj/login', [DjController::class, 'login_post']);

//home_user画面表示
Route::get('/Dj/home_user', [DjController::class, 'home_user']);


//問い合わせ一覧画面表示
Route::get('/Dj/inquiry_list', [DjController::class, 'inquiry_list']);
//問い合わせ一覧のpost処理
Route::post('/Dj/inquiry_list', [DjController::class, 'inquiry_post']);

//打ち合わせ画面表示
Route::get('/Dj/meeting', [DjController::class, 'meeting']);
//スケジュール入力画面のpost処理
Route::post('/Dj/meeting', [DjController::class, 'meeting_post']);

//スケジュール入力画面表示
Route::get('/Dj/schedule_input', [DjController::class, 'schedule_input']);
//スケジュール入力画面のpost処理
Route::post('/Dj/schedule_input', [DjController::class, 'schedule_post']);

//スケジュール表示画面表示
Route::get('/Dj/schedule', [DjController::class, 'schedule']);
