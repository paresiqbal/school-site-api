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
     * Relationship to agendas
     */
    public function agendas() // Pluralized to reflect the relationship
    {
        return $this->hasMany(Agenda::class);
    }
}
