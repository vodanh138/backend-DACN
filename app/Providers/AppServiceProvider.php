<?php

namespace App\Providers;

use App\Repositories\BaseRepository;
use App\Repositories\ChatRepository;
use App\Repositories\CommentRepository;
use App\Repositories\FollowRepository;
use App\Repositories\Interfaces\ChatRepositoryInterface;
use App\Repositories\Interfaces\CommentRepositoryInterface;
use App\Repositories\Interfaces\FollowRepositoryInterface;
use App\Repositories\Interfaces\LikeRepositoryInterface;
use App\Repositories\Interfaces\PostRepositoryInterface;
use App\Repositories\Interfaces\RepositoryInterface;
use App\Repositories\Interfaces\RoleRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\LikeRepository;
use App\Repositories\PostRepository;
use App\Repositories\RoleRepository;
use App\Services\ChatService;
use App\Services\CommentService;
use App\Services\FollowService;
use App\Services\Interfaces\ChatServiceInterface;
use App\Services\Interfaces\CommentServiceInterface;
use App\Services\Interfaces\FollowServiceInterface;
use App\Services\Interfaces\LikeServiceInterface;
use App\Services\Interfaces\PostServiceInterface;
use App\Services\LikeService;
use App\Services\PostService;
use Illuminate\Support\ServiceProvider;
use App\Repositories\UserRepository;
use App\Services\Interfaces\UserServiceInterface;
use App\Services\UserService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
        $this->app->singleton(UserServiceInterface::class, UserService::class);
        $this->app->singleton(LikeServiceInterface::class, LikeService::class);
        $this->app->singleton(PostServiceInterface::class, PostService::class);
        $this->app->singleton(CommentServiceInterface::class, CommentService::class);
        $this->app->singleton(FollowServiceInterface::class, FollowService::class);
        $this->app->singleton(ChatServiceInterface::class, ChatService::class);
        
        $this->app->singleton(UserRepositoryInterface::class, UserRepository::class);
        $this->app->singleton(RoleRepositoryInterface::class, RoleRepository::class);
        $this->app->singleton(PostRepositoryInterface::class, PostRepository::class);
        $this->app->singleton(LikeRepositoryInterface::class, LikeRepository::class);
        $this->app->singleton(CommentRepositoryInterface::class, CommentRepository::class);
        $this->app->singleton(FollowRepositoryInterface::class, FollowRepository::class);
        $this->app->singleton(ChatRepositoryInterface::class, ChatRepository::class);
        $this->app->singleton(RepositoryInterface::class, BaseRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
