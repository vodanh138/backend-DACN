<?php

namespace App\Repositories;

use App\Models\Chat;
use App\Repositories\Interfaces\ChatRepositoryInterface;

class ChatRepository extends BaseRepository implements ChatRepositoryInterface
{
    public function model(): string
    {
        return Chat::class;
    }
    public function createMessage(
        $sender,
        $message
    ) {
        return $this->model->create(
            [
                'sender' => $sender,
                'message' => $message,
            ]
        );
    }
}
