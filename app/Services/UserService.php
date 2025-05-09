<?php

namespace App\Services;

use App\Repositories\Interfaces\CommentRepositoryInterface;
use App\Repositories\Interfaces\FollowRepositoryInterface;
use App\Repositories\Interfaces\LikeRepositoryInterface;
use App\Repositories\Interfaces\PostRepositoryInterface;
use App\Services\Interfaces\UserServiceInterface;
use Illuminate\Support\Facades\Auth;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\Interfaces\RoleRepositoryInterface;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\DB;
use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Upload\UploadApi;

class UserService implements UserServiceInterface
{
    use ApiResponse;
    protected $userRepository;
    protected $roleRepository;
    protected $postRepository;
    protected $likeRepository;
    protected $commentRepository;
    protected $followRepository;
    public function __construct(
        UserRepositoryInterface $userRepository,
        RoleRepositoryInterface $roleRepository,
        PostRepositoryInterface $postRepository,
        LikeRepositoryInterface $likeRepository,
        CommentRepositoryInterface $commentRepository,
        FollowRepositoryInterface $followRepository,
    ) {
        $this->userRepository = $userRepository;
        $this->likeRepository = $likeRepository;
        $this->roleRepository = $roleRepository;
        $this->postRepository = $postRepository;
        $this->commentRepository = $commentRepository;
        $this->followRepository = $followRepository;
    }
    public function registerProcessing($username, $password)
    {
        $user = $this->userRepository->getUserByName($username);
        if ($user) {
            return $this->responseFail(__('validation.unique', ['attribute' => 'username']));
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
            [],
            __('messages.userCreate-T')
        );
    }
    public function loginProcessing($username, $password)
    {
        if (Auth::attempt(['username' => $username, 'password' => $password])) {
            $user = $this->userRepository->findLoggedUser();
            if ($user) {
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
            }
        } else {
            return $this->responseFail(__('messages.login-F'));
        }
    }

    //Profile
    public function uploadCoverphoto($request)
    {
        $user = $this->userRepository->findLoggedUser();
        if ($request->hasFile('image') && $user) {
            try {
                Configuration::instance([
                    'cloud' => [
                        'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                        'api_key' => env('CLOUDINARY_API_KEY'),
                        'api_secret' => env('CLOUDINARY_API_SECRET'),
                    ],
                    'url' => ['secure' => true],
                ]);
                $uploadedFile = (new UploadApi())->upload(
                    $request->file('image')->getRealPath(),
                );
                $filename = basename($uploadedFile['public_id']) . '.' . $uploadedFile['format'];
                $user->update([
                    'coverphoto' => $filename,
                ]);
                return $this->responseSuccess(['coverphoto' => $filename], __('messages.coverEdit-T'));
            } catch (\Exception $e) {
                return $this->responseFail(__('messages.coverEdit-F') . ' Error: ' . $e->getMessage());
            }
        }
        return $this->responseFail(__('messages.coverEdit-F'));
    }
    public function uploadAvatar($request)
    {
        $user = $this->userRepository->findLoggedUser();
        if ($request->hasFile('image') && $user) {
            try {
                Configuration::instance([
                    'cloud' => [
                        'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                        'api_key' => env('CLOUDINARY_API_KEY'),
                        'api_secret' => env('CLOUDINARY_API_SECRET'),
                    ],
                    'url' => ['secure' => true],
                ]);
                $uploadedFile = (new UploadApi())->upload(
                    $request->file('image')->getRealPath(),
                );
                $filename = basename($uploadedFile['public_id']) . '.' . $uploadedFile['format'];
                if ($user->ava && !str_contains($user->ava, 'default-ava')) {
                    $publicId = pathinfo($user->ava, PATHINFO_FILENAME);
                    (new UploadApi())->destroy("avatars/$publicId");
                }
                $user->update([
                    'ava' => $filename,
                ]);
                return $this->responseSuccess(['ava' => $filename], __('messages.avaEdit-T'));
            } catch (\Exception $e) {
                return $this->responseFail(__('messages.avaEdit-F') . ' Error: ' . $e->getMessage());
            }
        }

        return $this->responseFail(__('messages.avaEdit-F'));
    }

    public function editName($lastname, $firstname)
    {
        $user = $this->userRepository->findLoggedUser();
        if ($user) {
            try {
                $user->update([
                    'lastname' => $lastname,
                    'firstname' => $firstname,
                    'name' => $lastname . ' ' . $firstname,
                ]);
                return $this->responseSuccess([], __('messages.editName-T'));
            } catch (\Exception $e) {
                return $this->responseFail(__('messages.editName-F'));
            }
        }
    }
    public function viewProfile()
    {
        $user = $this->userRepository->findLoggedUser();
        if ($user) {
            $following = $this->followRepository->totalFollow($user->id);
            $follower = $this->followRepository->totalFollower($user->id);
            $posts = $this->postRepository->getPostsBelongTo($user->id);
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
                'user' => $user,
                'posts' => $posts,
                'following' => $following,
                'follower' => $follower,
            ]);
        }
        return $this->responseFail(__('messages.myProfile-F'));
    }
    public function search($request)
    {
        $user = $this->userRepository->findLoggedUser();
        if ($user) {
            $users = $this->userRepository->getUsersHaveName($request);
            $posts = $this->postRepository->getPostsHaveContent($request);
            if (!$users && $posts)
                return $this->responseSuccess([], __('messages.search-F'));
            return $this->responseSuccess([
                'users' => $users,
                'posts' => $posts,
            ]);
        }
        return $this->responseFail(__('messages.search-F'));
    }
    public function viewFriendProfile($user_id)
    {
        $user = $this->userRepository->findLoggedUser();
        if ($user) {
            $following = $this->followRepository->totalFollow($user_id);
            $follower = $this->followRepository->totalFollower($user_id);
            $friend = $this->userRepository->getUserById($user_id);
            $follow = $this->followRepository->findFollow($user->id, $friend->id);
            $isFollow = $follow ? true : false;
            $posts = $this->postRepository->getPostsBelongTo($friend->id);
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
                'name' => $friend->name,
                'ava' => $friend->ava,
                'coverphoto' => $friend->coverphoto,
                'posts' => $posts,
                'isFollow' => $isFollow,
                'following' => $following,
                'follower' => $follower,
            ]);
        }
        return $this->responseFail(__('messages.friendProfile-F'));

    }
}
