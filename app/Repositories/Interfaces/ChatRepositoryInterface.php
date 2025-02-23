<?php

namespace App\Repositories\Interfaces;

interface ChatRepositoryInterface extends RepositoryInterface
{
    public function createMessage(
        $sender,
        $message
    );
}
