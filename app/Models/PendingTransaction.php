<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PendingTransaction extends Model
{
    use HasFactory;

    /**
     * Os atributos podem ser adicionados em massa.
     */
    protected $fillable = [
        'account_id',
        'target_account_id',
        'transaction_id',
        'type',
        'amount',
        'description',
        'processed',
    ];

    /**
     * Relacionamento: Uma transação pendente pertence a uma conta.
     */
    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * Retorna o status da transação com base no campo 'processed'.
     *
     * @return string
     */
    public function getStatusAttribute(): string
    {
        return $this->processed ? 'processed' : 'pending';
    }
}
