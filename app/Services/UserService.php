<?php

namespace App\Services;

use App\Repositories\Interfaces\CommentRepositoryInterface;
use App\Repositories\Interfaces\LikeRepositoryInterface;
use App\Repositories\Interfaces\PostRepositoryInterface;
use App\Services\Interfaces\UserServiceInterface;
use Illuminate\Support\Facades\Auth;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\Interfaces\RoleRepositoryInterface;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\DB;

class UserService implements UserServiceInterface
{
    use ApiResponse;
    protected $userRepository;
    protected $roleRepository;
    protected $postRepository;
    protected $likeRepository;
    protected $commentRepository;
    public function __construct(
        UserRepositoryInterface $userRepository,
        RoleRepositoryInterface $roleRepository,
        PostRepositoryInterface $postRepository,
        LikeRepositoryInterface $likeRepository,
        CommentRepositoryInterface $commentRepository,
    ) {
        $this->userRepository = $userRepository;
        $this->likeRepository = $likeRepository;
        $this->roleRepository = $roleRepository;
        $this->postRepository = $postRepository;
        $this->commentRepository = $commentRepository;
    }
    public function registerProcessing($username, $password)
    {
        $user = $this->userRepository->getUserByName($username);
        if ($user) {
            return $this->responseFail(__('validation.unique',['attribute' => 'username']));
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
                return $this->responseSuccess([], __('messages.avaEdit-T'));
            } catch (\Exception $e) {
                return $this->responseFail(__('messages.avaEdit-F'));
            }
        }
        return $this->responseFail(__('messages.avaEdit-F'));
    }
    public function uploadAvatar($request)
    {
        $user = $this->userRepository->findLoggedUser();

        if ($request->hasFile('image') && $user) {
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
                return $this->responseSuccess([], __('messages.coverEdit-T'));
            } catch (\Exception $e) {
                return $this->responseFail(__('messages.coverEdit-F'));
            }
        } else

            return $this->responseFail(__('messages.coverEdit-F'));
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
                return $this->responseSuccess([
                    'user' => $user,
                ], __('messages.editName-T'));
            } catch (\Exception $e) {
                return $this->responseFail(__('messages.editName-F'));
            }
        }
    }
    public function viewProfile()
    {
        $user = $this->userRepository->findLoggedUser();
        if ($user) {
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
            $friend = $this->userRepository->getUserById($user_id);
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
            ]);
        }
        return $this->responseFail(__('messages.friendProfile-F'));

    }
}
