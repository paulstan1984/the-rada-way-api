<?php

namespace App\Services;

use App\Models\Message;

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

    public function update(Message $item, $data)
    {
        return $item->update($data);
    }

    public function delete(Message $item)
    {
        $item->delete();
    }
}
