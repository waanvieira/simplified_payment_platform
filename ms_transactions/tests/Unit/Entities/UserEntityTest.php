<?php

namespace Tests\Unit\Models;

use App\Domain\Entities\User;
use App\Exceptions\InvalidArgumentException;
use Exception;
use Tests\TestCase;

class UserEntityTest extends TestCase
{

    public function testAttributes()
    {
        $user = User::create(
            name: 'name',
            email: 'email@dev.com',
            cpfCnpj: '616.177.000-88',
            password: '1234'
        );

        $this->assertNotNull($user->id);
        $this->assertEquals('name', $user->name);
        $this->assertEquals('email@dev.com', $user->email);
        $this->assertEquals('616.177.000-88', $user->cpfCnpj);
        $this->assertEquals(false, $user->shopkeeper);
        $this->assertEquals(date('Y-m-d H:m'), date('Y-m-d H:m', strtotime($user->createdAt())));
    }

    public function testIsShopkeeper()
    {
        $user = User::create(
            name: 'name',
            email: 'email@dev.com',
            cpfCnpj: '34.212.980/0001-08',
            password: '1234'
        );

        $this->assertTrue($user->shopkeeper);
    }

    public function testInvalidEmail()
    {
        try {
            User::create(
                name: 'name',
                email: 'email',
                cpfCnpj: '34.212.980/0001-08',
                password: '1234'
            );
            $this->assertTrue(false);
        } catch (Exception $e) {
            $this->assertTrue($e instanceof InvalidArgumentException);
            $this->assertEquals("Email address is invalid.", $e->getMessage());
        }
    }

    public function testInvalidCpf()
    {
        try {
            User::create(
                name: 'name',
                email: 'email@dev.com.br',
                cpfCnpj: '111.111.111-11',
                password: '1234'
            );
            $this->assertTrue(false);
        } catch (Exception $e) {
            $this->assertTrue($e instanceof InvalidArgumentException);
            $this->assertEquals("Invalid CPF", $e->getMessage());
        }
    }

    public function testInvalidCnpj()
    {
        try {
            User::create(
                name: 'name',
                email: 'email@dev.com.br',
                cpfCnpj: '11.111.111/1111-11',
                password: '1234'
            );
            $this->assertTrue(false);
        } catch (Exception $e) {
            $this->assertTrue($e instanceof InvalidArgumentException);
            $this->assertEquals("Invalid CNPJ", $e->getMessage());
        }
    }
}
