<?php

namespace App\Services;

use App\Repositories\Interfaces\CommentRepositoryInterface;
use App\Repositories\Interfaces\LikeRepositoryInterface;
use App\Services\Interfaces\CommentServiceInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Traits\ApiResponse;

class CommentService implements CommentServiceInterface
{
    use ApiResponse;
    protected $userRepository;
    protected $commentRepository;
    protected $likeRepository;

    public function __construct(
        UserRepositoryInterface $userRepository,
        CommentRepositoryInterface $commentRepository,
        LikeRepositoryInterface $likeRepository,
    ) {
        $this->userRepository = $userRepository;
        $this->likeRepository = $likeRepository;
        $this->commentRepository = $commentRepository;
    }

    //Comment
    public function comment($request, $post_id)
    {
        try {
            $user = $this->userRepository->findLoggedUser();
            if ($user) {
                $imageName = null;
                if ($request->hasFile('image')) {
                    $image = $request->file('image');
                    $imageName = '/images/' . time() . '.' . $image->getClientOriginalExtension();
                    $image->move(public_path('images'), $imageName);
                }
                $comment = $this->commentRepository->createComment(
                    $request->content,
                    $imageName,
                    $user->id,
                    $post_id
                );
                if (!$comment) {
                    return $this->responseFail(__('messages.createComment-F'));
                }
                return $this->responseSuccess(
                    [
                        'comments' => [
                            'id' => $comment->id,
                            'content' => $comment->content,
                            'user_name' => $comment->user->name ?? 'Unknown',
                            'user_ava' => $comment->user->ava ?? 'Unknown',
                            'image' => $comment->image,
                            'created_at' => $comment->created_at,
                            'likes' => 0,
                        ]
                    ],
                    __('messages.createPost-T')
                );
            }
            return $this->responseFail(__('messages.createComment-F'));
        } catch (\Exception $e) {
            return $this->responseFail(__('messages.createComment-F'));
        }
    }
    public function getComment($post_id)
    {
        try {
            $comments = $this->commentRepository->getComment($post_id);
            $comments = $comments->map(function ($comment) {
                $user = $this->userRepository->findLoggedUser();
                $isLiked = false;
                if ($user) {
                    $like = $this->likeRepository->findLike(
                        post_id: null,
                        user_id: $user->id,
                        comment_id: $comment->id
                    );
                    $likes = $this->likeRepository->totalLike(null,$comment->id);
                    $isLiked = $like ? true : false;
                }
                return [
                    'id' => $comment->id,
                    'content' => $comment->content,
                    'user_name' => $comment->user->name ?? 'Unknown',
                    'user_ava' => $comment->user->ava ?? 'Unknown',
                    'image' => $comment->image,
                    'created_at' => $comment->created_at,
                    'isLiked' => $isLiked,
                    'likes' => $likes,
                ];
            });

            return $this->responseSuccess([
                'comments' => $comments,
            ], __('messages.getComment-T'));
        } catch (\Exception $e) {
            return $this->responseFail(__('messages.getComment-F'));
        }
    }
}
