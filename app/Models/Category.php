<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Casts\MyDateTime;

class Category extends Model
{
    use HasFactory;

    var $fillable = ['name', 'imagelink', 'position'];
    var $hidden = ['created_at', 'updated_at'];

    protected $casts = [
        'created_at' => MyDateTime::class,
        'updated_at' => MyDateTime::class,
    ];

}
