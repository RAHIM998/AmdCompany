<?php

use App\Http\Controllers\API\CommandeController;
use App\Http\Controllers\API\ProduitController;
use App\Http\Controllers\API\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    //Route de connexion
    Route::post('login', [UserController::class, 'login'])->name('login');

    //Route pour créer des utilisateurs
    Route::post('user', [UserController::class, 'store']);

});

Route::middleware('auth:api')->group(function () {
    // Routes des utilisateurs protégées
    Route::get('user', [UserController::class, 'index']);
    Route::get('user/{id}', [UserController::class, 'show']);
    Route::put('user/{id}', [UserController::class, 'update']);
    Route::delete('user/{id}', [UserController::class, 'destroy']);
    Route::post('logout', [UserController::class, 'logout'])->name('logout');

    //Routes des catégory
    Route::apiResource('category', \App\Http\Controllers\API\CategorieController::class);

    //Routes pour les produits
    Route::apiResource('produit', \App\Http\Controllers\API\ProduitController::class);

    //Routes des commandes
    Route::apiResource('commande', CommandeController::class);

});

//Route de recherche
Route::get('search', [ProduitController::class, 'search'])->name('search');

