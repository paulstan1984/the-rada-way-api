<?php

namespace App\Services;

use App\Models\Message;
use Illuminate\Support\Facades\DB;

class MessagesRepository
{

    public function create($item)
    {
        return Message::create($item);
    }

    public function search($user1_id, $user2_id)
    {
        $query = Message::query()
            ->whereRaw('((sender_id = ' . $user1_id . ' and receiver_id = ' . $user2_id . ')
            or (sender_id = ' . $user2_id . ' and receiver_id = ' . $user1_id . '))');

        return $query;
    }

    public function getMessages($user_id, $friend_id, $type, $lastId)
    {
        $query = $this->search($user_id, $friend_id);
        $query->orderBy('id');

        if ($type == 'newer' && !empty($lastId) && $lastId != 0) {
            $query = $query->where('id', '>', $lastId);
        }

        if ($type == 'older' && !empty($lastId) && $lastId != 0) {
            $query = $query->where('id', '<', $lastId);
        }

        return $query;
    }

    public function markAllRead($user_id, $friend_id)
    {
        Message::query()
            ->where('sender_id', '=', $friend_id)
            ->where('receiver_id', '=', $user_id)
            ->update(['read' => 1]);
    }

    public function search_my_last_messages($user_id)
    {
        $last_received_messages = DB::table('messages')
            ->where('receiver_id', $user_id)
            ->select(DB::raw('max(id) as id'))
            ->groupBy('sender_id');

        $last_sent_messages = DB::table('messages')
            ->where('sender_id', $user_id)
            ->select(DB::raw('max(id) as id'))
            ->groupBy('receiver_id');

        $mesages = $last_sent_messages
            ->union($last_received_messages)
            ->pluck('id');

        $query = Message::query()
            ->whereIn('id', $mesages);

        return $query;
    }

    public function count_unread_messages($user_id) {
        return Message
            ::where('receiver_id', $user_id)
            ->where('read', 0)
            ->count();
    }

    public function update(Message $item, $data)
    {
        return $item->update($data);
    }

    public function delete(Message $item)
    {
        $item->delete();
    }
}
