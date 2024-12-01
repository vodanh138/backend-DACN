<?php

namespace App\Services;

use App\Repositories\Interfaces\LikeRepositoryInterface;
use App\Services\Interfaces\LikeServiceInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Traits\ApiResponse;

class LikeService implements LikeServiceInterface
{
    use ApiResponse;
    protected $userRepository;
    protected $likeRepository;

    public function __construct(
        UserRepositoryInterface $userRepository,
        LikeRepositoryInterface $likeRepository,
    ) {
        $this->userRepository = $userRepository;
        $this->likeRepository = $likeRepository;
    }

    //Like
    public function like($post_id, $comment_id)
    {
        try {
            $user = $this->userRepository->findLoggedUser();
            if ($user) {
                $like = $this->likeRepository->findLike(
                    post_id: $post_id,
                    user_id: $user->id,
                    comment_id: $comment_id
                );
                if (!$like) {
                    $newLike = $this->likeRepository->createLike(
                        post_id: $post_id,
                        user_id: $user->id,
                        comment_id: $comment_id
                    );
                    if (!$newLike) {
                        return $this->responseFail(__('messages.like-F'));
                    }
                    return $this->responseSuccess([],__('messages.like-T'));
                }
            }
            return $this->responseFail(message: __(key: 'messages.like-F'));
        } catch (\Exception $e) {
            return $this->responseFail(__('messages.like-F'));
        }
    }
    public function unlike($post_id, $comment_id)
    {
        try {
            $user = $this->userRepository->findLoggedUser();
            if ($user) {
                $like = $this->likeRepository->findLike(
                    post_id: $post_id,
                    user_id: $user->id,
                    comment_id: $comment_id
                );
                if ($like) {
                    $like->delete();
                    return $this->responseSuccess([],__('messages.unlike-T'));
                }
            }
            return $this->responseFail(message: __(key: 'messages.unlike-F'));
        } catch (\Exception $e) {
            return $this->responseFail(__('messages.unlike-F'));
        }
    }
}
