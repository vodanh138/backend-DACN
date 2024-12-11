<?php

namespace App\Repositories\Interfaces;

interface PostRepositoryInterface extends RepositoryInterface
{
    public function getHomePage($user_id);
    public function createPost(
        $content,
        $image,
        $user_id
    );
    public function findPost($post_id);
    public function getPostsHaveContent($content);
    public function getPostsBelongTo($id);
}
