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
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
    }
    public function createPost(
        $title,
        $content,
        $image,
        $user_id
    ) {
        return $this->model->create(
            [
                'title' => $title,
                'content' => $content,
                'user_id' => $user_id,
                'image' => $image,
                'view' => 0,
            ]
        );
    }
}
