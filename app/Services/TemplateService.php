<?php

namespace App\Services;

use App\Services\Interfaces\TemplateServiceInterface;
use Illuminate\Support\Facades\Auth;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\Interfaces\RoleRepositoryInterface;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\DB;

class TemplateService implements TemplateServiceInterface
{
    use ApiResponse;
    protected $userRepository;
    protected $roleRepository;

    public function __construct(
        UserRepositoryInterface $userRepository,
        RoleRepositoryInterface $roleRepository,
    ) {
        $this->userRepository = $userRepository;
        $this->roleRepository = $roleRepository;
    }
    public function registerProcessing($username, $password)
    {
        $user = $this->userRepository->getUserByName($username);
        if ($user) {
            return $this->responseFail(__('validation.unique'));
        }
        DB::beginTransaction();
        try {
            $user = $this->userRepository->createUser(
                    $username,
                    $password
                );
            if (!$user) {
                return $this->responseFail(__('messages.userCreate-F'));
            }
            $role = $this->roleRepository->getRoleByName('user');
            $user->roles()->attach($role->id);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->responseFail(__('messages.errorAddingUser'), 500);
        }
        return $this->responseSuccess(
            [
                'user' => $user,
            ],
            __('messages.tempCreate-T')
        );
    }
    public function loginProcessing($username, $password)
    {
        if (Auth::attempt(['username' => $username, 'password' => $password])) {
            $user = $this->userRepository->findLoggedUser();
            try {
                $token = $user->createToken('auth_token')->plainTextToken;
            } catch (\Exception $e) {
                return $this->responseFail($e->getMessage());
            }
            return $this->responseSuccess(
                [
                    'status' => 'success',
                    'access_token' => $token,
                    'token_type' => 'Bearer',
                ],
                __('messages.login-T')
            );
        } else {
            return $this->responseFail(__('messages.login-F'));
        }
    }
    public function editName($lastname, $firstname)
    {
        $user = $this->userRepository->findLoggedUser();
        if ($user) {
            try {
                $user->update([
                    'lastname' => $lastname,
                    'firstname' => $firstname,
                    'name' => $lastname . ' '. $firstname,
                ]);
                return $this->responseSuccess([
                    'user'=> $user,
                ],__('messages.editName-T'));
            } catch (\Exception $e) {
                return $this->responseFail(__('messages.editName-F'));
            }
        }
    }
    public function viewProfile(){
        $user = $this->userRepository->findLoggedUser();
        return $this->responseSuccess([
            'user'=> $user,
        ]);
    }
    public function uploadCoverphoto($request){
        $user = $this->userRepository->findLoggedUser();
        if ($request->hasFile('image')) {
            try {
                $image = $request->file('image');
                $imageName = '/images/' . time() . '.' . $image->getClientOriginalExtension();

                $oldImage = $user->coverphoto;
                if ($oldImage && $oldImage != '/images/default-coverphoto.png') {
                    $oldImagePath = public_path() . '/' . $oldImage;
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }

                $image->move(public_path('images'), $imageName);
                $user->update([
                    'coverphoto' => $imageName,
                ]);
                return $this->responseSuccess(__('messages.avaEdit-T'));
            } catch (\Exception $e) {
                return $this->responseFail(__('messages.avaEdit-F'));
            }
        }
        return $this->responseFail(__('messages.avaEdit-F'));
    }
    public function uploadAvatar($request){
        $user = $this->userRepository->findLoggedUser();
        
        if ($request->hasFile('image')) {
            try {
                $image = $request->file('image');
                $imageName = '/images/' . time() . '.' . $image->getClientOriginalExtension();
                
                $oldImage = $user->ava;
                if ($oldImage && $oldImage != '/images/default-ava.png') {
                    $oldImagePath = public_path() . '/' . $oldImage;
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }

                $image->move(public_path('images'), $imageName);
                $user->update([
                    'ava' => $imageName,
                ]);
                return $this->responseSuccess(__('messages.avaEdit-T'));
            } catch (\Exception $e) {
                return $this->responseFail(__('messages.avaEdit-F'));
            }
        }
        else
            
        return $this->responseFail(__('messages.avaEdit-F'));
    }
}
