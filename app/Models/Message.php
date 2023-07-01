<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Casts\MyDateTime;

class Message extends Model
{
    use HasFactory;

    protected $casts = [
        'created_at' => MyDateTime::class,
        'updated_at' => MyDateTime::class,
    ];

    var $fillable = ['sender_id', 'receiver_id', 'text', 'read'];
    var $appends = ['receiver_name', 'sender_name'];
    var $hidden = ['receiver', 'sender'];

    public function receiver()
    {
        return $this->belongsTo(User::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class);
    }

    #region appenders
    public function getReceiverNameAttribute()
    {
        return $this->receiver?->name;
    }

    public function getSenderNameAttribute()
    {
        return $this->sender?->name;
    }
    #endregion
}
