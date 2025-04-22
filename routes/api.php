<?php

use App\Http\Controllers\Chatcontroller;
use App\Http\Controllers\Commentcontroller;
use App\Http\Controllers\Followcontroller;
use App\Http\Controllers\Likecontroller;
use App\Http\Controllers\Postcontroller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\usercontroller;
Route::get('/not-authorized', [usercontroller::class, 'notLoggedIn'])->name('Not-Loggedin');
Route::post('/login', [usercontroller::class, 'loginProcessing'])->name('login');
Route::post('/register', [usercontroller::class, 'registerProcessing']);

Route::middleware('auth:sanctum')->group(function () {
    //Post
    Route::get('/post', [Postcontroller::class, 'getPost']);//View Post
    Route::post('/post', [PostController::class, 'upPost']);//Create Post

    //Like
    Route::post('/post/{post_id}/like', [Likecontroller::class, 'postLike']);//Like
    Route::delete('/post/{post_id}/like', [LikeController::class, 'postUnlike']);//Unlike

    Route::post('/comment/{comment_id}/like', [Likecontroller::class, 'commentLike']);//Like
    Route::delete('/comment/{comment_id}/like', [LikeController::class, 'commentUnlike']);//Unlike

    //Profile
    Route::get('/profile', [usercontroller::class, 'viewProfile']);//View my profile
    Route::put('/profile', [usercontroller::class, 'editName']);//Edit name
    Route::post('/upload/coverphoto', [usercontroller::class, 'uploadCoverphoto']);//Edit Coverphoto
    Route::post('/upload/avatar', [usercontroller::class, 'uploadAvatar']);//Edit Avatar
    Route::get('/profile/{user_id}', [usercontroller::class, 'viewFriendProfile']);//View friend profile

    //Comment
    Route::post('/comment/{post_id}', [Commentcontroller::class, 'comment']);//Create Comment
    Route::get('/comment/{post_id}', [Commentcontroller::class, 'getComment']);//Get Comment

    //Search
    Route::get('/search', [usercontroller::class, 'search']);//Search 

    //Follow
    Route::post('/follow/{user_id}', [Followcontroller::class, 'follow']);//Follow
    Route::delete('/follow/{user_id}', [Followcontroller::class, 'unfollow']);//Unfollow

    //Chat
    Route::post('/chatbot', [Chatcontroller::class, 'chatbot']);//Chat with AI
    //Route::get('/chatbot', [Chatcontroller::class, 'getchatbot']);//Load messages with AI

    Route::post('/logout', [usercontroller::class, 'logout'])->name('logout');
});