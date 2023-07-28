<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Casts\MyDateTime;

class Run extends Model
{
    use HasFactory;

    var $fillable = ['user_id', 'startTime', 'endTime', 'distance', 'avgSpeed', 'running', 'locations'];
    var $hidden = ['created_at', 'updated_at'];

    protected $casts = [
        'created_at' => MyDateTime::class,
        'updated_at' => MyDateTime::class,
    ];

    public static function update_run_stats($id)
    {
        $run = Run::find($id);
        if (empty($run)) {
            return;
        }

        $query =  Location::where('run_id', $id)->where('speed', '>', 0);

        $distance = round(floatval($query->sum('distance')) / 1000, 2);

        $speedSum = $query->sum('speed');
        $count = $query->count();
        $avgSpeed = ($speedSum / max(1, $count)) * 3.6; // convert from m/s to km/h

        $run->update(['distance' => round($distance, 2), 'avgSpeed' => round($avgSpeed, 2)]);
    }
}
