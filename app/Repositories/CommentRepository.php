<?php

namespace App\Repositories;

use App\Models\Comment;
use App\Repositories\Interfaces\CommentRepositoryInterface;

class CommentRepository extends BaseRepository implements CommentRepositoryInterface
{
    public function model(): string
    {
        return Comment::class;
    }
    public function createComment(
        $content,
        $image,
        $user_id,
        $post_id
    ) {
        return $this->model->create(
            [
                'post_id' => $post_id,
                'content' => $content,
                'user_id' => $user_id,
                'image' => $image,
            ]
        );
    }
    public function getComment(
        $post_id
    ) {
        return $this->model
        ->where("post_id", $post_id)
        ->get();
    }
}
