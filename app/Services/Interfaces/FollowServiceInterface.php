<?php

namespace App\Services\Interfaces;

interface FollowServiceInterface
{
    public function follow($id);
    public function unfollow($id);
}
