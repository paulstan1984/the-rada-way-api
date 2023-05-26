<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Run extends Model
{
    use HasFactory;

    var $fillable = ['user_id', 'startTime', 'endTime', 'distance', 'avgSpeed', 'locations'];
    var $hidden = ['created_at', 'updated_at'];

    #region accesors
    protected function locations(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => json_decode($value),
            set: fn ($value) => json_encode($value)
        );
    }
    #endregion
}
