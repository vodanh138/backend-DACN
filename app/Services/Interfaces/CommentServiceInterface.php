<?php

namespace App\Services\Interfaces;

interface CommentServiceInterface
{
    public function comment($request, $post_id);
    public function getComment($post_id);
    
}
