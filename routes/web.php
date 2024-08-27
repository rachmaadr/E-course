<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CourseVideoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FrontController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SubscribeTransactionController;
use App\Http\Controllers\TeacherController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [FrontController::class, 'index'])
->name('front.index');

Route::get('/details/{course:slug}',[FrontController::class, 'details'])
->name('front.detail');

Route::get('/category/{course:slug}', [FrontController::class, 'category'])
->name('front.category');

Route::get('/pricing', [FrontController::class, 'pricing'])
->name('front.pricing');



Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])
    ->name('profile.edit');
    
    Route::patch('/profile', [ProfileController::class, 'update'])
    ->name('profile.update');
    
    Route::delete('/profile', [ProfileController::class, 'destroy'])
    ->name('profile.destroy');

    Route::get('/checkout', [FrontController::class, 'checkout'])
    ->name('front.checkout')
    ->middleware('role:student');
    
    Route::post('/checkout/store', [FrontController::class, 'checkout_store'])
    ->name('front.checkout.store')
    ->middleware('role:student');
    
    Route::get('/learning/{course}/{courseVideoid}', [FrontController::class, 'learning'])
    ->name('front.learning')
    ->middleware('role:student|teacher');
    

    Route::prefix('admin')->name('admin.')->group(function(){
        Route::resource('categories', CategoryController::class)
        ->middleware('role:owner');
        
        Route::resource('teachers', TeacherController::class)
        ->middleware('role:owner');
        
        Route::resource('courses', CourseController::class)
        ->middleware('role:owner|teacher');
        
        Route::resource('subcription_transactions', SubscribeTransactionController::class)
        ->middleware('role:owner');

        Route::get('add/video/{course:id}', [CourseVideoController::class, 'create'])
        ->middleware('role:teacher|owner')
        ->name('course.add_video');
        
        Route::post('add/video/save/{course:id}', [CourseVideoController::class, 'store'])
        ->middleware('role:teacher|owner')
        ->name('course.add_video.save');

        Route::resource('course_videos', CourseVideoController::class)
        ->middleware('role:owner|teacher');
    });

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

require __DIR__.'/auth.php';
