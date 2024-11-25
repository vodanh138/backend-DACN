<?php

namespace App\Repositories\Interfaces;

interface PostRepositoryInterface extends RepositoryInterface
{
    public function getHomePage();
    public function createPost(
        $title,
        $content,
        $image,
        $user_id
    );
}
