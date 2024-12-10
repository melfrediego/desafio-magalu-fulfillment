<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;

    /**
     * Os atributos podem ser adicionados em massa.
     */
    protected $fillable = ['agency', 'number', 'balance', 'credit_limit', 'user_id', 'bank_id'];

    /**
     * Relacionamento: Um registro pertence a um usuário.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relacionamento: Um registro pertence a um banco.
     */
    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }

    /**
     * Relacionamento: Um registro tem várias transações associadas.
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
