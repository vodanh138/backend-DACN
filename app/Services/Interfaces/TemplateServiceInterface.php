<?php

namespace App\Services\Interfaces;

interface TemplateServiceInterface
{
    public function registerProcessing($username, $password);
    public function loginProcessing($username, $password);
    public function editName($lastname, $firstname);
}
