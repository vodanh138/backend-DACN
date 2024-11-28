<?php

namespace App\Repositories\Interfaces;

interface PostRepositoryInterface extends RepositoryInterface
{
    public function getHomePage();
    public function createPost(
        $content,
        $image,
        $user_id
    );
    public function findPost($post_id);
}
