<?php

use App\Http\Controllers\ItemController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RecommendationController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SelectedOutfitController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WeatherController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/impressum', function () {
    return view('impressum');
})->name('impressum');

Route::get('/onion', [RecommendationController::class, 'index']);
Route::get('/', [RecommendationController::class, 'index'])->name('onion.home');

Route::get('/weather/city', [WeatherController::class, 'weatherByCity']);
Route::get('/weather/location', [WeatherController::class, 'weatherByLocation']);

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () { });

Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'index']);
    Route::get('/review', [ReviewController::class, 'index'])->name('outfit.review');

    /*Route::post('/save-outfit', function (Request $request) {
        return response()->json([
            'message' => 'Outfit-IDs empfangen!',
            'daten' => $request->all(),
        ]);
    })->name('outfit.save');*/

    Route::post('/save-outfit', [SelectedOutfitController::class, 'storeOutfit']) ->name('outfit.save');

    Route::post('/save-item', [ItemController::class, 'store']);

    Route::get('/settings', function () {
        return view('settings');
    })->name('settings');
});

Route::resource('items', ItemController::class)->middleware('auth');
Route::resource('tags', TagController::class)->except(['show'])->middleware('auth');

Route::put('/user/offset', [UserController::class, 'updateOffset'])->name('user.updateOffset');
Route::post('/selected-outfits/add/{item}', [SelectedOutfitController::class, 'addItem'])->name('selected-outfits.add')->middleware('auth');
Route::put('/selected-outfits/{selectedOutfit}/save-review', [SelectedOutfitController::class, 'saveReview'])->name('selected-outfits.save-review')->middleware('auth');

Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
