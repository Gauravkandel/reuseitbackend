<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RecommendationController;
use App\Http\Controllers\SellProductController;
use App\Http\Controllers\verificationController;
use App\Http\Controllers\ViewProductController;
use App\Models\category;
use Illuminate\Support\Facades\Route;

//recommendation
Route::post('/recommend', [RecommendationController::class, 'recommend']);
Route::post('/get_recommend', [RecommendationController::class, 'getrecommended']);

//for viewing products
Route::controller(ViewProductController::class)->group(function () {
    Route::get('/getIndivProduct/{id}', 'getIndivProduct');
    Route::get('/getdat', 'fetchalldata');
    Route::get('/filter', 'filter');
});

//for posting products
Route::controller(SellProductController::class)->group(function () {
    $category = category::all();
    foreach ($category as $cat) {
        Route::post('/' . $cat->function_name, $cat->function_name);
    }
    Route::post('/sellproducts', 'insertProducts');
    Route::get('/getCategory', 'getCategory');
    Route::post('/makeCategory', 'makeCategory');
    Route::get('/getindivcategory/{id}', 'getIndivCategory');
    // Route::post('/homeappliances', 'HomeAppliances');
    // Route::post('/electronics', 'Electronics');
    // Route::post('/furnitures', 'Furnitures');
    // Route::post('/clothings', 'Clothing');
    // Route::post('/sports', 'Sports');
    // Route::post('/books', 'Books');
    // Route::post('/antiques', 'Antiques');
    // Route::post('/cars', 'Cars');
    // Route::post('/motorcycles', 'Motorcycle');
    // Route::post('/scooters', 'Scooter');
    // Route::post('/bicycles', 'Bicycle');
    // Route::post('/toys', 'Toys');
    // Route::post('/music', 'Music');
});

//authentication
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::group(['middleware' => 'api'], function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::post('/me', [AuthController::class, 'me']);
    Route::get('/activeuser', [AuthController::class, 'ActiveUser']);
    Route::get('/notactiveuser', [AuthController::class, 'NotActiveUser']);

    //For dashboard
    $category = category::all();
    foreach ($category as $cat) {
        Route::post('update/' . $cat->function_name, [DashboardController::class, $cat->function_name]);
    }
    Route::get('/myproducts', [DashboardController::class, 'myProducts']);
    Route::get('/deleteads/{id}', [DashboardController::class, 'deleteAds']);
    Route::post('/status', [DashboardController::class, 'Soldout']);
    Route::get('/editProducts/{id}', [DashboardController::class, 'EditUserProducts']);
    Route::post('/UpdateProducts', [DashboardController::class, 'UpdateProducts']);
    //For chat application
    Route::post('/messages', [ChatController::class, 'message']);
    Route::get('/getmsgcount', [ChatController::class, 'getMessageCount']);
    Route::post('/typing', [chatController::class, 'typing']);
    Route::post('/notTyping', [chatController::class, 'notTyping']);
    //phone verification
    Route::post('/sendotp', [verificationController::class, 'SendSms']);
    Route::Post('/verifyotp', [verificationController::class, 'VerifyOtp']);
    Route::get('/getUsers', [ChatController::class, 'getUsers']);
    Route::get('/user', [ChatController::class, 'InitUser']);
    Route::get('/userdetails/{id}', [ChatController::class, 'ChatProfile']);
});
Route::get('/messages/{senderId}/{receiverId}', [ChatController::class, 'getMessages']);
