<?php

use App\Http\Middleware\AuthResource;
use App\Http\Middleware\PlateMiddleware;
use App\Http\Middleware\RestaurantMiddleware;
use App\Http\Middleware\UserControllMiddleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;


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

Route::get('/', 'PageController@home')->name('home');
Route::get('restaurants/{id}', 'PageController@home')->name('restaurant');

Route::get('/cart', 'PageController@showCart')->name('cart');
Route::get('/confirm', 'PageController@confirm')->name('confirm');

Route::any('checkout/pay', 'CheckoutController@pay' )->name('checkout.pay');



// Route::get('/checkout', 'CheckoutController@generate')->name('checkout');
// Route::post('/pay', 'CheckoutController@checkout')->name('checkout');
Auth::routes();

//rotta da vedere se tenere o cancellare, e la rotta che prima riconduceva dopo il login e la registrazione
Route::get('/user', 'HomeController@user')->name('user');
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {

    Route::resource('restaurant', RestaurantController::class)->middleware(RestaurantMiddleware::class);
    Route::resource('plate', PlateController::class)->middleware(PlateMiddleware::class);

    Route::get('ordini', 'HomeController@ordini')->name('ordini');
    Route::get('ordini/{id}', 'HomeController@showOrdini')->name('ordini.show');


    Route::get('/statistiche', 'HomeController@statistiche')->name('statistiche');
});


Route::post('/pay', 'CheckoutController@confirmedPay')->name('confirmed.pay');

/* ROTTA PER CONTROLLARE CHE IL FORM FUNZIONI CORRETTAMENTE */
Route::any('/controlloForm', 'HomeController@ControlloPost')->name('controllo.post'); 
    
