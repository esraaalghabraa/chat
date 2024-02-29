<?php

namespace App\Http\Controllers;

use App\Events\NewMessageSent;
use App\Http\Requests\GetMessageRequest;
use App\Http\Requests\StoreMessageRequest;
use App\Models\Chat;
use App\Models\ChatMessage;
use App\Models\User;

class ChatMessageController extends Controller
{
    public function index(GetMessageRequest $request){
        $data = $request->validated();
        $chatId = $data['chat_id'];
        $currentPage = $data['page'];
        $pageSize = isset($data['page_size']) ? $data['page_size'] : 15;
        $messsages = ChatMessage::where('chat_id',$chatId)
            ->with('user')
            ->latest('created_at')
            ->simplePaginate(
                $pageSize,
                ['*'],
                $currentPage
            );
        return $this->success($messsages->getCollection());
    }

    public function store(StoreMessageRequest $request){
        $data = $request->validated();
        $data['user_id']=auth()->user()->id;

        $chatMessage = ChatMessage::create($data);
        $chatMessage->load('user');

        $this->sendNotificationToOther($chatMessage);

        return $this->success($chatMessage,'Message has been sent successfuly');
    }

    private function sendNotificationToOther(ChatMessage $chatMessage){
        broadcast(new NewMessageSent($chatMessage))->toOthers();
        $user = auth()->user();
        $userId = $user->id;
        $chat=Chat::where('id',$chatMessage->chat_id)
            ->with(['participants'=>function($query) use ($userId){
                $query->where('user_id','!=',$userId);
            }])
            ->first();

        if (count($chat->participants)>0)
        {
            $otherUserId = $chat->participants[0]->user_id;
            $otherUser = User::where('id',$otherUserId)->first();
            $otherUser->sendNewMessageNotification([
                'messageData'=>[
                    'senderName'=>$user->username,
                    'message'=>$chatMessage->message,
                    'chatId'=>$chatMessage->chat_id
                ]
            ]);
        }
    }
}
