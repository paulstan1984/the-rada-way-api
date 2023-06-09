<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    var $fillable = ['run_id', 'lat', 'lng', 'speed', 'distance'];

    public $timestamps= false;
}
