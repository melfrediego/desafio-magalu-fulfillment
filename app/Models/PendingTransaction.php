<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PendingTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_id',
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
}
