<?php

namespace App\Services\Interfaces;

interface TemplateServiceInterface
{
    public function registerProcessing($username, $password);
    public function loginProcessing($username, $password);
    public function editName($lastname, $firstname);
    public function viewProfile();
    public function getPost();
    public function upPost($title, $content, $image);
    public function uploadCoverphoto($request);
    public function uploadAvatar($request);
}
