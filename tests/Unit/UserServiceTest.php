<?php

namespace Tests\Unit;

use Tests\TestCase;

use Mockery;
use App\Services\UserService;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\Interfaces\RoleRepositoryInterface;
use App\Repositories\Interfaces\PostRepositoryInterface;
use App\Repositories\Interfaces\LikeRepositoryInterface;
use App\Repositories\Interfaces\CommentRepositoryInterface;
use App\Repositories\Interfaces\FollowRepositoryInterface;

class UserServiceTest extends TestCase
{
    protected $userService;
    protected $userRepo;
    protected $roleRepo;
    protected $postRepo;
    protected $likeRepo;
    protected $commentRepo;
    protected $followRepo;

    protected function setUp(): void
    {
        parent::setUp();

        // Khởi tạo mock các repository
        $this->userRepo = Mockery::mock(UserRepositoryInterface::class);
        $this->roleRepo = Mockery::mock(RoleRepositoryInterface::class);
        $this->postRepo = Mockery::mock(PostRepositoryInterface::class);
        $this->likeRepo = Mockery::mock(LikeRepositoryInterface::class);
        $this->commentRepo = Mockery::mock(CommentRepositoryInterface::class);
        $this->followRepo = Mockery::mock(FollowRepositoryInterface::class);

        // Inject mock vào UserService
        $this->userService = new UserService(
            $this->userRepo,
            $this->roleRepo,
            $this->postRepo,
            $this->likeRepo,
            $this->commentRepo,
            $this->followRepo
        );
    }

    public function test_view_profile_success()
    {
        $user = (object)[
            'id' => 1,
            'name' => 'Test User',
            'ava' => 'test.jpg'
        ];

        $post = (object)[
            'id' => 10,
            'content' => 'Hello world',
            'user' => $user,
            'image' => 'img.png',
            'created_at' => now()
        ];

        $this->userRepo->shouldReceive('findLoggedUser')->andReturn($user)->twice();
        $this->followRepo->shouldReceive('totalFollow')->with(1)->andReturn(5);
        $this->followRepo->shouldReceive('totalFollower')->with(1)->andReturn(10);
        $this->postRepo->shouldReceive('getPostsBelongTo')->with(1)->andReturn(collect([$post]));
        $this->likeRepo->shouldReceive('findLike')->with(10, 1, null)->andReturn(null);
        $this->likeRepo->shouldReceive('totalLike')->with(10, null)->andReturn(3);
        $this->commentRepo->shouldReceive('totalComment')->with(10)->andReturn(7);

        $response = $this->userService->viewProfile();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertArrayHasKey('data', $response->getData(true));
    }

    public function test_view_profile_fail_when_user_not_logged_in()
    {
        $this->userRepo->shouldReceive('findLoggedUser')->andReturn(null);

        $response = $this->userService->viewProfile();

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals(__('messages.myProfile-F'), $response->getData(true)['message']);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}


