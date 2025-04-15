<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'created_by',
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function versions()
    {
        return $this->hasMany(GameVersion::class);
    }

    public function latestVersion()
    {
        return $this->hasOne(GameVersion::class)->latestOfMany();
    }

    public function scores()
    {
        return $this->hasManyThrough(Score::class, GameVersion::class);
    }
}
