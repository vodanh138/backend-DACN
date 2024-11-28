<?php

namespace App\Services\Interfaces;

interface UserServiceInterface
{
    public function registerProcessing($username, $password);
    public function loginProcessing($username, $password);
    public function editName($lastname, $firstname);
    public function viewProfile();
    public function uploadCoverphoto($request);
    public function uploadAvatar($request);
}
