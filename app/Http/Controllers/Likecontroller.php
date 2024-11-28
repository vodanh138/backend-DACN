<?php

namespace App\Http\Controllers;


use App\Services\Interfaces\LikeServiceInterface;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class Likecontroller extends Controller
{
    use ApiResponse;

    protected $LikeService;
    public function __construct(LikeServiceInterface $LikeService)
    {
        $this->LikeService = $LikeService;
    }

    //Like
    public function postLike($post_id)
    {
        return $this->LikeService->like($post_id,null);
    }
    public function postUnlike($post_id)
    {
        return $this->LikeService->unlike($post_id,null);
    }

    public function commentLike($comment_id)
    {
        return $this->LikeService->like(null,$comment_id);
    }
    public function commentUnlike($comment_id)
    {
        return $this->LikeService->unlike(null,$comment_id);
    }
}