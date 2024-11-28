<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\NameRequest;
use App\Http\Requests\PhotoRequest;
use App\Services\Interfaces\UserServiceInterface;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class Usercontroller extends Controller
{
    use ApiResponse;

    protected $UserService;
    public function __construct(UserServiceInterface $UserService)
    {
        $this->UserService = $UserService;
    }
    public function notLoggedIn()
    {
        return $this->responseFail(__('messages.unauthor'));
    }
    public function registerProcessing(LoginRequest $request)
    {
        return $this->UserService->registerProcessing($request->username, $request->password);
    }
    public function loginProcessing(LoginRequest $request)
    {
        return $this->UserService->loginProcessing($request->username, $request->password);
    }
    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();
            return $this->responseSuccess([], __('messages.logout-T'));
        } catch (\Exception $e) {
            return $this->responseFail($e->getMessage(), 500);
        }
    }

    //Profile
    public function uploadCoverphoto(PhotoRequest $request)
    {
        return $this->UserService->uploadCoverphoto($request);
    }
    public function uploadAvatar(PhotoRequest $request)
    {
        return $this->UserService->uploadAvatar($request);
    }
    public function editName(NameRequest $request)
    {
        return $this->UserService->editName($request->lastname, $request->firstname);
    }
    public function viewProfile()
    {
        return $this->UserService->viewProfile();
    }
}
