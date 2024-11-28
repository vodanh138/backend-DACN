<?php

namespace App\Providers;

use App\Repositories\BaseRepository;
use App\Repositories\Interfaces\LikeRepositoryInterface;
use App\Repositories\Interfaces\PostRepositoryInterface;
use App\Repositories\Interfaces\RepositoryInterface;
use App\Repositories\Interfaces\RoleRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\LikeRepository;
use App\Repositories\PostRepository;
use App\Repositories\RoleRepository;
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
        $this->app->singleton(UserRepositoryInterface::class, UserRepository::class);
        $this->app->singleton(RoleRepositoryInterface::class, RoleRepository::class);
        $this->app->singleton(PostRepositoryInterface::class, PostRepository::class);
        $this->app->singleton(LikeRepositoryInterface::class, LikeRepository::class);
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
