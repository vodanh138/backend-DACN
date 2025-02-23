<?php

namespace App\Services;

use App\Repositories\Interfaces\ChatRepositoryInterface;
use App\Services\Interfaces\ChatServiceInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Http;

class ChatService implements ChatServiceInterface
{
    use ApiResponse;
    protected $userRepository;
    protected $ChatRepository;
    protected $likeRepository;

    public function __construct(
        UserRepositoryInterface $userRepository,
        ChatRepositoryInterface $ChatRepository,
    ) {
        $this->userRepository = $userRepository;
        $this->ChatRepository = $ChatRepository;
    }

    //Chat
    public function chatBot($request)
    {
        try {
            $user = $this->userRepository->findLoggedUser();
            if ($user) {
                $apiKey = env('AI_API_KEY');
                $userMessage = $request->input('message');
                
                $response = Http::post("https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key=$apiKey", [
                    "contents" => [
                        ["parts" => [["text" => $userMessage]]]
                    ]
                ]);

                $responseData = $response->json();
                $aiMessage = __('messages.AI-not-response');

                if (!empty($responseData['candidates'][0]['content']['parts'][0]['text'])) {
                    $aiMessage = $responseData['candidates'][0]['content']['parts'][0]['text'];
                } else
                    return $this->responseFail(__('messages.AI-not-response'));

                /*
                $Chat = $this->ChatRepository->createMessage(
                    $user->id,
                    $userMessage
                );
                $aiMessageEntry = $this->ChatRepository->createMessage(
                    'bot',
                    $aiMessage
                );
                */

                return $this->responseSuccess([
                    'user_message' => $userMessage,
                    'ai_message' => $aiMessage,
                ]);
            }
            return $this->responseFail(__('messages.sendMessage-F'));
        } catch (\Exception $e) {
            return $this->responseFail(__('messages.sendMessage-F'));
        }
    }
}
