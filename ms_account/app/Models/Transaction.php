<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $fillable = [
        'id',
        'transaction_type',
        'payer_id',
        'payee_id',
        'value',
        'transaction_status',
        'confirmation_at',
        'created_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'id' => 'string',
        'password' => 'hashed',
        'shopkeeper' => 'boolean',
    ];

    public function payer()
    {
        return $this->hasOne(Account::class, 'id', 'payer_id');
    }

    public function payee()
    {
        return $this->hasOne(Account::class, 'id', 'payee_id');
    }
}
