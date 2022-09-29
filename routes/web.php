<?php

use App\Http\Controllers\CompanyController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

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

Route::prefix('admin')->group(function () {
    Route::get( '/user', [UserController::class, 'index'])->name('user');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::group(['middleware' => ['auth', 'verified']], function () {

    /** Company Routes */
    Route::get( '/company', [CompanyController::class, 'index'])->name('company');
    Route::get( 'company/create',[CompanyController::class, 'create'])->name('company-new');
    Route::post( 'company/save', [CompanyController::class, 'store'])->name('company-save');
    Route::get( 'company/edit/{id}', [CompanyController::class,'edit'])->name('company-edit');
    Route::put( 'company/update/{id}', [CompanyController::class,'update'])->name('company-update');
    Route::delete( 'company/delete/{id}', [CompanyController::class,'destroy'])->name('company-delete');

    /** User Routes */
    Route::get( 'user/edit/', [UserController::class,'index'])->name('user-edit');
    Route::put( 'user/update/{id}', [UserController::class,'update'])->name('user-update');
    // Route::prefix('admin')->group(function () {
    //     Route::get( '/user', [CompanyController::class, 'create'])->name('company');
    // });
    
});

require __DIR__.'/auth.php';