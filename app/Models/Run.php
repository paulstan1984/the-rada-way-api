<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Run extends Model
{
    use HasFactory;

    var $fillable = ['name', 'description', 'weekly_timetable', 'schedule_type', 'schedule_units', 'open', 'service_provider_id'];
    var $hidden = ['created_at', 'updated_at', 'service_provider'];
}
