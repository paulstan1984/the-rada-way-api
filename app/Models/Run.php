<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Run extends Model
{
    use HasFactory;

    var $fillable = ['user_id', 'startTime', 'endTime', 'distance', 'avgSpeed', 'locations'];
    var $hidden = ['created_at', 'updated_at'];
}
