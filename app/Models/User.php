<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nombres',
        'cargo',
        'user',
        'rol',
        'estado',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // Se dirige ha eventos
    public function evento()
    {
        return $this->hasMany('App\Models\Evento'); // se dirige hacia Eventos
    }

    // Se dirige ha puntos
    public function punto()
    {
        return $this->hasMany('App\Models\Punto'); // se dirige hacia Puntos
    }

    // Se dirige ha agendas
    public function agenda()
    {
        return $this->hasMany('App\Models\Agenda'); // se dirige hacia Agenda
    }

    // Se dirige ha agendas
    public function ingreso()
    {
        return $this->hasMany('App\Models\Ingreso'); // se dirige hacia Ingreso
    }

    // Se dirige ha agendas
    public function fotografia()
    {
        return $this->hasMany('App\Models\Fotografia'); // se dirige hacia fotografia
    }
}
