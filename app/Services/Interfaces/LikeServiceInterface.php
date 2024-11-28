<?php

namespace App\Services\Interfaces;

interface LikeServiceInterface
{
    public function like($post_id, $comment_id);
    public function unlike($post_id, $comment_id);
}
