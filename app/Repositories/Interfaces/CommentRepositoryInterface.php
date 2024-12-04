<?php

namespace App\Repositories\Interfaces;

interface CommentRepositoryInterface extends RepositoryInterface
{
    public function createComment(
        $content,
        $image,
        $user_id,
        $post_id
    );
    public function getComment(
        $post_id
    );
    public function totalComment(
        $post_id
    );
}
