<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Casts\MyDateTime;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    const ADMIN = 'admin';
    const USER = 'user';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'sex',
        'dob',
        'height',
        'weight',
        'runGoal',
        'email',
        'password',
        'access_token',
        'remember_token',
        'running',
        'base64_encoded_image',
        'runCounter',
        'runTotalKm',
        'runningPercentage'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'access_token',
        'created_at',
        'updated_at',
        'email_verified_at'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => MyDateTime::class,
    ];

    public static function HashPass(string $pasword)
    {
        return md5($pasword . env('PASS_HASH'));
    }

    public static function update_run_stats($id)
    {
        $user = User::find($id);
        if (empty($user)) {
            return;
        }

        $query =  Run::where('user_id', $id);
        $runCounter = $query->count();
        $runTotalKm = $query->sum('distance');
        $runningPercentage = $user->runGoal == 0
            ? 0
            : min(100, round($runTotalKm * 100 / $user->runGoal, 2));

        $user->update([
            'runCounter' => $runCounter, 
            'runTotalKm' => $runTotalKm,
            'runningPercentage' => round($runningPercentage, 2)
        ]);
    }
}
