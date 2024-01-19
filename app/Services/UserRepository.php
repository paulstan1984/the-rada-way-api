<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;

class UserRepository
{
    public function login($data)
    {
        $pass = User::HashPass($data['password']);
        $item = User::where('email', $data['email'])->where('password', $pass)->first();
        if (!empty($item)) {
            $item->access_token = md5(date('Y-m-d H:i:s') . $pass . env('PASS_HASH'));
            $item->update();
            return $item->access_token;
        }

        return null;
    }

    public function logout($token)
    {
        $item = User::where('access_token', $token)->first();
        if (!empty($item)) {
            $item->access_token = '';
            $item->update();
            return true;
        }

        return false;
    }

    public function getUserByToken(string $token)
    {
        return User::where('access_token', $token)->first();
    }

    public function getUserByRememberToken(string $token)
    {
        return User::where('remember_token', $token)->first();
    }

    public function getUserByEmail(string $email)
    {
        return User::where('email', $email)->first();
    }

    public function getUserByEmailAndRememberToken($item)
    {
        return User
            ::where('email', $item['email'])
            ->where('remember_token', $item['remember_token'])
            ->first();
    }

    public function create($item)
    {
        return User::create($item);
    }

    public function search(string $keyword = null, Builder $last_user_messages = null)
    {
        $query = User::query();

        if (!empty($keyword)) {
            $query = $query->where('name', 'like', '%' . $keyword . '%');
        }

        if ($last_user_messages != null) {
            $query = $query
                ->leftJoinSub($last_user_messages, 'last_user_messages', function (JoinClause $join) {
                    $join->on('users.id', '=', 'last_user_messages.receiver_id');
                    $join->orOn('users.id', '=', 'last_user_messages.sender_id');
                })
                ->select('users.*', 
                    'last_user_messages.created_at as last_message_date',
                    'last_user_messages.id as last_message_id',
                    'last_user_messages.read as last_message_read',
                    'last_user_messages.text as last_message',
                    'last_user_messages.receiver_id as receiver_id',
                    'last_user_messages.sender_id as sender_id'
                );
        }

        return $query;
    }

    public function update(User $item, $data)
    {
        return $item->update($data);
    }

    public function delete(User $item)
    {
        $item->delete();
    }

    public function generateRememberToken()
    {
        $length = 6;
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function updateUserStats($userId) {
        User::update_run_stats($userId);
    }
}
