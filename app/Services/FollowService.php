<?php

namespace App\Services;

use App\Repositories\Interfaces\FollowRepositoryInterface;
use App\Services\Interfaces\FollowServiceInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Traits\ApiResponse;

class FollowService implements FollowServiceInterface
{
    use ApiResponse;
    protected $userRepository;
    protected $followRepository;

    public function __construct(
        UserRepositoryInterface $userRepository,
        FollowRepositoryInterface $followRepository,
    ) {
        $this->userRepository = $userRepository;
        $this->followRepository = $followRepository;
    }
    public function follow($id)
    {
        try {
            $user = $this->userRepository->findLoggedUser();
            if ($user) {
                $follow = $this->followRepository->findFollow($user->id, $id);
                $friend = $this->userRepository->getUserById($id);
                if (!$follow) {
                    $newFollow = $this->followRepository->createFollow($user->id, $id);
                    if (!$newFollow) {
                        return $this->responseFail(__('messages.follow-F'));
                    }
                    return $this->responseSuccess([], __('messages.follow-T', ['att' => $friend->name]));
                }
            }
            return $this->responseFail(__('messages.follow-F'));
        } catch (\Exception $e) {
            return $this->responseFail(__('messages.follow-F'));
        }
    }
    public function unfollow($id)
    {
        try {
            $user = $this->userRepository->findLoggedUser();
            if ($user) {
                $follow = $this->followRepository->findFollow($user->id, $id);
                $friend = $this->userRepository->getUserById($id);
                if ($follow) {
                    $follow->delete();
                    return $this->responseSuccess([], __('messages.unfollow-T', ['att' => $friend->name]));
                }
            }
            return $this->responseFail(__('messages.unfollow-F'));
        } catch (\Exception $e) {
            return $this->responseFail(__('messages.unfollow-F'));
        }
    }
}
