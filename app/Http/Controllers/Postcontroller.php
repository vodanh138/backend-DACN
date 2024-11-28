<?php

namespace App\Http\Controllers;

use App\Services\Interfaces\PostServiceInterface;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class Postcontroller extends Controller
{
    use ApiResponse;

    protected $PostService;
    public function __construct(PostServiceInterface $PostService)
    {
        $this->PostService = $PostService;
    }

    //Post
    public function getPost()
    {
        return $this->PostService->getPost();
    }
    public function upPost(Request $request)
    {
        return $this->PostService->upPost($request);
    }
}