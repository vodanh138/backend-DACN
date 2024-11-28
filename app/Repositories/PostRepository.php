<?php

namespace App\Repositories;

use App\Models\Post;
use App\Repositories\Interfaces\PostRepositoryInterface;

class PostRepository extends BaseRepository implements PostRepositoryInterface
{
    public function model(): string
    {
        return Post::class;
    }
    public function getHomePage()
    {
        return $this->model
            ->with('user:id,name,ava')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
    }
    public function createPost(
        $content,
        $image,
        $user_id
    ) {
        return $this->model->create(
            [
                'content' => $content,
                'user_id' => $user_id,
                'image' => $image,
                'view' => 0,
            ]
        );
    }
    public function findPost($post_id)
    {
        return $this->model
            ->where("id", $post_id)
            ->first();
    }
}
