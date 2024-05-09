<?php

namespace Tests\Unit\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Tests\TestCase;
use Tests\Traits\TestModels;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class UserTest extends TestCase
{
    use TestModels;

    private $model;

    protected function setUp(): void
    {
        parent::setUp();
        $this->model = new User();
    }

    public function model(): Model
    {
        return $this->model;
    }

    public function filleableAtributes(): array
    {
        return [
            'id',
            'name',
            'cpf_cnpj',
            'email',
            'password',
            'shopkeeper',
            'created_at',
        ];
    }

    public function casts(): array
    {
        return [
            'id' => 'string',
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'shopkeeper' => 'boolean',
            'deleted_at' => 'datetime'
        ];
    }

    public function primaryKeyName(): string
    {
        return 'id';
    }

    public function constantes(): array
    {
        return [
        ];
    }

    public function relations(): array
    {
        $this->assertTrue(true);
        return [];
    }

    public function traitsNeed(): array
    {
        return [
            // HasApiTokens::class,
            HasFactory::class,
            Notifiable::class,
            \Illuminate\Database\Eloquent\SoftDeletes::class
        ];
    }
}
