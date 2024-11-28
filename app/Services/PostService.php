<?php

namespace App\Services;

use App\Repositories\Interfaces\PostRepositoryInterface;
use App\Services\Interfaces\PostServiceInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Traits\ApiResponse;

class PostService implements PostServiceInterface
{
    use ApiResponse;
    protected $userRepository;
    protected $postRepository;

    public function __construct(
        UserRepositoryInterface $userRepository,
        PostRepositoryInterface $postRepository,
    ) {
        $this->userRepository = $userRepository;
        $this->postRepository = $postRepository;
    }

    //Post
    public function getPost()
    {
        try {
            $posts = $this->postRepository->getHomePage();
            $posts = $posts->map(function ($post) {
                return [
                    'content' => $post->content,
                    'user_name' => $post->user->name ?? 'Unknown',
                    'user_ava' => $post->user->ava ?? 'Unknown',
                    'image' => $post->image,
                    'created_at' => $post->created_at,
                ];
            });

            return $this->responseSuccess([
                'posts' => $posts,
            ], __('messages.getPost-T'));
        } catch (\Exception $e) {
            return $this->responseFail(__('messages.getPost-F'));
        }
    }
    public function upPost($request)
    {
        try {
            $user = $this->userRepository->findLoggedUser();
            if ($request->hasFile('image') && $user) {
                $image = $request->file('image');
                $imageName = '/images/' . time() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('images'), $imageName);
                $post = $this->postRepository->createPost(
                    $request->content,
                    $imageName,
                    $user->id
                );
                if (!$post) {
                    return $this->responseFail(__('messages.createPost-F'));
                }
                return $this->responseSuccess(
                    [
                        'posts' => [
                            'content' => $post->content,
                            'user_name' => $post->user->name ?? 'Unknown',
                            'user_ava' => $post->user->ava ?? 'Unknown',
                            'image' => $post->image,
                            'created_at' => $post->created_at,
                        ]
                    ],
                    __('messages.createPost-T')
                );
            }
            return $this->responseFail(__('messages.createPost-F'));
        } catch (\Exception $e) {
            return $this->responseFail(__('messages.createPost-F'));
        }
    }
}
