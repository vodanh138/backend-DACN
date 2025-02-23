<?php

namespace App\Http\Controllers;

use App\Services\Interfaces\ChatServiceInterface;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class Chatcontroller extends Controller
{
    use ApiResponse;

    protected $ChatService;
    public function __construct(ChatServiceInterface $ChatService)
    {
        $this->ChatService = $ChatService;
    }

    //Chat
    public function chatBot(Request $request)
    {
        return $this->ChatService->chatBot($request);
    }
}