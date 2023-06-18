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

    public function search($user1_id = null, $user2_id = null)
    {
        $query = Message::query();

        $user_ids = array();

        if (!empty($user1_id)) {
            $user_ids[] = $user1_id;
        }
        if (!empty($user2_id)) {
            $user_ids[] = $user2_id;
        }

        if (count($user_ids) > 0) {
            $query = $query->whereIn('receiver_id', $user_ids);
        }

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
            ->addSelect(DB::raw('*, IF(receiver_id = ' . $user_id . ', 1, 0) as received'));

        $query = $query->whereIn('id', $mesages)
            ->orderBy('received', 'desc')
            ->orderBy('read', 'desc')
            ->orderby('created_at', 'desc');

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
