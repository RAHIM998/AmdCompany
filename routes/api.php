<?php

use App\Http\Controllers\API\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//Route de connexion
Route::post('login', [UserController::class, 'login'])->name('login');

//Route pour créer des utilisateurs
Route::post('user', [UserController::class, 'store']);

Route::middleware('auth:api')->group(function () {
    // Routes des utilisateurs protégées
    Route::get('user', [UserController::class, 'index']);
    Route::get('user/{id}', [UserController::class, 'show']);
    Route::put('user/{id}', [UserController::class, 'update']);
    Route::delete('user/{id}', [UserController::class, 'destroy']);

    //Routes des catégory
    Route::apiResource('category', \App\Http\Controllers\API\CategorieController::class);
});


