<?php

namespace App\Http\Controllers;

use App\Services\Interfaces\FollowServiceInterface;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class Followcontroller extends Controller
{
    use ApiResponse;

    protected $FollowService;
    public function __construct(FollowServiceInterface $FollowService)
    {
        $this->FollowService = $FollowService;
    }
    public function follow($id)
    {
        return $this->FollowService->follow($id);
    }
    public function unfollow($id)
    {
        return $this->FollowService->unfollow($id);
    }
}
