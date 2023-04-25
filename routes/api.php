<?php

use App\Http\Controllers\API\BookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\RegisterController;
use App\Http\Controllers\API\TestController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\ArtistController;
use App\Http\Controllers\API\AlbumController;
use App\Http\Controllers\API\GenreController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
app/Http/Controllers/API/UserController.php| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::controller(RegisterController::class)->group(function(){
    Route::post("register", "register");
    Route::post("login", "login");
    Route::post("logout", "logout");
    Route::post("forgot_password", "forgotPassword");
    Route::get("password_reset", "passwordReset");
});


Route::middleware('auth:sanctum')->group( function () {
    Route::controller(UserController::class)->group(function(){
        Route::get('user', 'getUser');
        Route::post('user/upload_avatar', 'uploadAvatar');
        Route::delete('user/remove_avatar','removeAvatar');
        Route::post('user/send_verification_email','sendVerificationEmail');
        Route::post('user/change_email', 'changeEmail');
    });
    
    Route::resource('books', BookController::class);
    
    Route::controller(BookController::class)->group(function(){
        Route::post('books/{id}/checkout', 'checkoutBook');
        Route::put('books/{id}/return', 'returnBook');
        Route::post('books/{id}/update_image', 'uploadBookImage');
    });
    

    // New routes for Final Project
    Route::resource('artists', ArtistController::class);
    Route::controller(ArtistController::class)->group(function(){
        Route::get('artists/{id}/withAlbums', 'artistsWithAlbums');

        Route::post('artists/{id}/update_image', 'uploadArtistImage');
        Route::delete('artists/{id}/remove_image', 'removeArtistImage');
        
        Route::get('/artists/{id}/genres', 'getArtistGenres');
        Route::post('/artists/{id}/genres/{genre_id}', 'addArtistGenre');
        Route::delete('/artists/{id}/genres/{genre_id}', 'removeArtistGenre');
        
        Route::get('/artists/{id}/albums', 'getArtistAlbums');
        Route::post('/artists/summary', 'sendSummaryEmail');

    });
    
    Route::resource('albums', AlbumController::class);
    Route::controller(AlbumController::class)->group(function(){
        Route::post('albums/{id}/update_image', 'uploadAlbumImage');
        Route::delete('albums/{id}/remove_image', 'removeAlbumImage');
   
    });
    
    Route::resource('genres', GenreController::class);
    Route::controller(GenreController::class)->group(function(){
        Route::get('/genres/{id}/artists', 'getGenreArtists');
    });
    
    Route::controller(TestController::class)->group(function(){
        Route::get('', 'index');
    });
});



