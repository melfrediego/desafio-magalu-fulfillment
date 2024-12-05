<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    use HasFactory;
    protected $fillable = ['code', 'name'];

    /**
     * Relacionamento: Um banco pode ter várias contas.
     */
    public function accounts()
    {
        return $this->hasMany(Account::class);
    }
}
