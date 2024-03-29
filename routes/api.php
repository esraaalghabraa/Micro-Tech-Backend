<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\PlatformController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TechnologyController;
use App\Http\Controllers\ToolController;
use App\Http\Controllers\UserAuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WorkTypeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::controller(AdminAuthController::class)
    ->prefix('auth')
    ->group(function () {
        Route::post('login', 'login');
        Route::post('verify_code', 'verifyCode');
        Route::get('logout', 'logout');
        Route::post('reset_password', 'resetPassword');
        Route::get('refresh_token', 'refreshToken');
    });

Route::middleware(['auth:sanctum', 'abilities:admin,access'])->group(function (){
    Route::controller(WorkTypeController::class)->prefix('work_types')->group(function (){
        Route::post('create','create');
        Route::post('edit','edit');
        Route::delete('delete','delete');
        Route::get('index','index');
    });
    Route::controller(MemberController::class)->prefix('member')->group(function (){
        Route::post('create','create');
        Route::post('edit','edit');
        Route::delete('delete','delete');
        Route::get('index','index');
    });
    Route::controller(TechnologyController::class)->prefix('technology')->group(function (){
        Route::post('create','create');
        Route::post('edit','edit');
        Route::delete('delete','delete');
        Route::get('index','index');
    });
    Route::controller(PlatformController::class)->prefix('platform')->group(function (){
        Route::post('create','create');
        Route::post('edit','edit');
        Route::delete('delete','delete');
        Route::get('index','index');
    });
    Route::controller(ToolController::class)->prefix('tool')->group(function (){
        Route::post('create','create');
        Route::post('edit','edit');
        Route::delete('delete','delete');
        Route::get('index','index');
    });
    Route::controller(ProjectController::class)->prefix('project')->group(function (){
        Route::post('create','create');
        Route::post('edit','edit');
        Route::delete('delete','delete');
        Route::get('index','index');
        Route::patch('activate','activate');
        Route::patch('special','special');

        Route::post('create_fast','createFast');
        Route::post('add_images','addImages');
        Route::post('edit_images','editImages');
        Route::post('add_features','addFeatures');
        Route::post('edit_features','editFeatures');
        Route::get("get_groups",'getGroups');
    });
});
Route::prefix('user')
    ->group(function () {
        Route::controller(UserAuthController::class)
            ->group(function () {
                Route::prefix('auth')->group(function (){
                    Route::post('register', 'register');
                    Route::post('login', 'login');
                    Route::get('refresh_token', 'refreshToken');
                    Route::get('logout', 'logout');
                    //TODO Forget password
                });
                Route::post('send_message','sendMessage');
                Route::get('get_home_projects','getHomeProjects');
                Route::get('get_projects','getProjects');

            });
        Route::controller(UserController::class)
            ->group(function () {
                Route::post('change_like','changeLike');
                Route::post('add_comment','addComment');
                Route::post('edit_comment','editComment');
                Route::post('delete_comment','deleteComment');
            });

    });
