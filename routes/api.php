<?php

use App\Http\Controllers\MemberController;
use App\Http\Controllers\PlatformController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TechnologyController;
use App\Http\Controllers\ToolController;
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

    Route::post('create_fast','createFast');
    Route::post('add_images','addImages');
    Route::post('edit_images','editImages');
    Route::post('add_features','addFeatures');
    Route::post('edit_features','editFeatures');
    Route::get("get_groups",'getGroups');
});

Route::get('test',function(){
    return 'test';
});
