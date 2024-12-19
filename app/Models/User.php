<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Exceptions\BusinessValidationException;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Os atributos podem ser adicionados em massa.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_whatsapp',
        'birth_date',
        'is_client',
        'cpf_cnpj',
        'person_type',
    ];

    /**
     * Os atributos ocultos para arrays.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Os atributos que devem ser convertidos para tipos nativos.
     */
    protected $casts = [
        'birth_date' => 'date',
        'is_client' => 'boolean',
    ];


}
