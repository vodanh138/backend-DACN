<?php

namespace App\Repositories;

use App\Models\Post;
use App\Repositories\Interfaces\PostRepositoryInterface;
use Carbon\Carbon;

class PostRepository extends BaseRepository implements PostRepositoryInterface
{
    public function model(): string
    {
        return Post::class;
    }
    public function getHomePage($user_id)
    {
        $oneWeekAgo = Carbon::now()->subWeek();
        return $this->model::where(function ($query) use ($user_id) {
            $query->whereIn('user_id', function ($subquery) use ($user_id) {
                $subquery->select('follow')
                    ->from('follows')
                    ->where('user_id', $user_id);
            })
                ->orWhere('user_id', $user_id);
        })
            ->with('user:id,name,ava')
            ->where('created_at', '>=', $oneWeekAgo)
            ->orderBy('created_at', 'desc')
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
    public function getPostsHaveContent($content)
    {
        return $this->model->where('content', 'like', "%{$content}%")->get();
    }
    public function getPostsBelongTo($id)
    {
        return $this->model->where('user_id', $id)->orderBy('created_at', 'desc')->get();
    }
}
