<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Model
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $fillable = [
        'username',
        'password',
    ];

    /**
     * Relationship to news
     */
    public function news()
    {
        return $this->hasMany(News::class);
    }

    /**
     * Relationship to announcements
     */
    public function announcements()
    {
        return $this->hasMany(Announcement::class);
    }

    /**
     * Relationship to agendas
     */
    public function agendas()
    {
        return $this->hasMany(Agenda::class);
    }

    /**
     * Relationship to achievements
     */
    public function achievements()
    {
        return $this->hasMany(Achievement::class);
    }
}
