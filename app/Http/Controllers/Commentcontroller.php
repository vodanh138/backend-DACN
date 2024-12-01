<?php

namespace App\Http\Controllers;

use App\Services\Interfaces\CommentServiceInterface;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class Commentcontroller extends Controller
{
    use ApiResponse;

    protected $commentService;
    public function __construct(CommentServiceInterface $commentService)
    {
        $this->commentService = $commentService;
    }

    //Comment
    public function comment(Request $request, $post_id)
    {
        return $this->commentService->comment($request, $post_id);
    }
    public function getComment($post_id)
    {
        return $this->commentService->getComment( $post_id);
    }
    
}