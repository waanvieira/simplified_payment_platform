<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;

    protected $table = 'accounts';

    public $incrementing = false;

    protected $fillable = [
        'id',
        'name',
        'cpf_cnpj',
        'email',
        'password',
        'shopkeeper',
        'balance',
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
}
