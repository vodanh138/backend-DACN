<?php

namespace App\Services;

use App\Repositories\Interfaces\CommentRepositoryInterface;
use App\Repositories\Interfaces\FollowRepositoryInterface;
use App\Repositories\Interfaces\LikeRepositoryInterface;
use App\Repositories\Interfaces\PostRepositoryInterface;
use App\Services\Interfaces\PostServiceInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Traits\ApiResponse;
use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Upload\UploadApi;
use Cloudinary\Exception\CloudinaryApiException;

class PostService implements PostServiceInterface
{
    use ApiResponse;
    protected $userRepository;
    protected $postRepository;
    protected $likeRepository;
    protected $commentRepository;
    protected $followRepository;

    public function __construct(
        UserRepositoryInterface $userRepository,
        PostRepositoryInterface $postRepository,
        LikeRepositoryInterface $likeRepository,
        CommentRepositoryInterface $commentRepository,
        FollowRepositoryInterface $followRepository,
    ) {
        $this->userRepository = $userRepository;
        $this->likeRepository = $likeRepository;
        $this->postRepository = $postRepository;
        $this->commentRepository = $commentRepository;
        $this->followRepository = $followRepository;
    }

    //Post
    public function getPost()
    {
        try {
            $user = $this->userRepository->findLoggedUser();
            $posts = $this->postRepository->getHomePage($user->id);
            $posts = $posts->map(function ($post) {
                $user = $this->userRepository->findLoggedUser();
                $isLiked = false;
                if ($user) {
                    $like = $this->likeRepository->findLike(
                        post_id: $post->id,
                        user_id: $user->id,
                        comment_id: null
                    );
                    $likes = $this->likeRepository->totalLike($post->id, null);
                    $comments = $this->commentRepository->totalComment($post->id);
                    $isLiked = $like ? true : false;
                }
                return [
                    'id' => $post->id,
                    'content' => $post->content,
                    'user_id' => $post->user->id ?? 'Unknown',
                    'user_name' => $post->user->name ?? 'Unknown',
                    'user_ava' => $post->user->ava ?? 'Unknown',
                    'image' => $post->image,
                    'created_at' => $post->created_at,
                    'isLiked' => $isLiked,
                    'likes' => $likes,
                    'comments' => $comments,
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

            if (!$user) {
                return $this->responseFail(__('messages.createPost-F'));
            }

            $imageUrl = null;

            if ($request->hasFile('image')) {
                Configuration::instance([
                    'cloud' => [
                        'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                        'api_key' => env('CLOUDINARY_API_KEY'),
                        'api_secret' => env('CLOUDINARY_API_SECRET'),
                    ],
                    'url' => [
                        'secure' => true
                    ]
                ]);

                try {
                    // Generate timestamp and signature
                    $timestamp = time();
                    $signature = sha1('timestamp=' . $timestamp . env('CLOUDINARY_API_SECRET'));

                    $uploadedFile = (new UploadApi())->upload(
                        $request->file('image')->getRealPath(),
                        [
                            'timestamp' => $timestamp,
                            'signature' => $signature
                        ]
                    );
                    $imageUrl = basename($uploadedFile['public_id']) . '.' . $uploadedFile['format'];

                    if (!$imageUrl) {
                        throw new \Exception("Cloudinary image upload failed.");
                    }

                } catch (CloudinaryApiException $e) {
                    return $this->responseFail(__('messages.createPost-F') . ' Error: ' . $e->getMessage());
                } catch (\Exception $e) {
                    return $this->responseFail(__('messages.createPost-F') . ' Error: ' . $e->getMessage());
                }
            }

            $post = $this->postRepository->createPost(
                $request->content,
                $imageUrl,
                $user->id
            );

            if (!$post) {
                return $this->responseFail(__('messages.createPost-F'));
            }

            return $this->responseSuccess(
                [
                    'posts' => [
                        'id' => $post->id,
                        'content' => $post->content,
                        'user_name' => $post->user->name ?? 'Unknown',
                        'user_ava' => $post->user->ava ?? 'Unknown',
                        'image' => $post->image,
                        'created_at' => $post->created_at,
                        'likes' => 0,
                        'comments' => 0,
                    ]
                ],
                __('messages.createPost-T')
            );

        } catch (\Exception $e) {
            return $this->responseFail(__('messages.createPost-F') . ' Error: ' . $e->getMessage());
        }
    }
}
