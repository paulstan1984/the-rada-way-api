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
            ->whereRaw('(sender_id = ' . $user1_id . ' and receiver_id = ' . $user2_id . ')')
            ->orWhereRaw('(sender_id = ' . $user2_id . ' and receiver_id = ' . $user1_id . ')');

        return $query;
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

    public function update(Message $item, $data)
    {
        return $item->update($data);
    }

    public function delete(Message $item)
    {
        $item->delete();
    }
}
