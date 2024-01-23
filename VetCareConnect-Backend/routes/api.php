<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MainController;
use App\Http\Controllers\AuthController;
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });


Route::get('/vet-all', [MainController::class, 'getAllVet']);

Route::get('/cure-types-all', [MainController::class, 'getCureTypes']);


Route::post('/register',[AuthController::class,'register']);
Route::post('/login',[AuthController::class,'login']);




Route::post('/search-vets', [MainController::class, 'searchVets']);
//Route::post('/logout',[AuthController::class,'logout']);

Route::middleware('auth:sanctum')->group(function(){
    Route::post('/logout',[AuthController::class,'logout']);

    //owner
    Route::get('/user-data', [MainController::class, 'getUserData']);

    Route::get('/pets', [MainController::class, 'getPets']);
    Route::get('/delete-pet/{id}', [MainController::class, 'deletePet']);
    Route::get('/delete-appointment/{id}', [MainController::class, 'deleteAppointment']);
    Route::get('/owner-appointments', [MainController::class, 'getOwnerAppointments']);
    Route::get('/free-appointments/{id}/{date}', [MainController::class, 'getFreeAppointments']);
    Route::post('/new-pet',[MainController::class,'addNewPet']);


    //Route::middleware('auth:vet')->group(function(){
        Route::get('/bearer-test',[MainController::class,'bearerTest']);
    //});
    //vet
    //Route::get('/vet-data/{id}', [MainController::class, 'getVetData']);





});


