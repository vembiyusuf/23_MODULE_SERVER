<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Administrator extends Model
{
    use HasFactory, Notifiable;


    protected $fillable = [
        'username',
        'password',
        'last_login_at',
    ];
}
