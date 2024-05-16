<?php

namespace Tests\Unit\Models;

use App\Domain\Entities\Account;
use App\Exceptions\EntityValidationException;
use App\Exceptions\InvalidArgumentException;
use Exception;
use Tests\TestCase;

class AccountEntityTest extends TestCase
{

    public function testAttributes()
    {
        $account = Account::create(
            name: 'name',
            email: 'email@dev.com',
            cpfCnpj: '616.177.000-88',
            password: '1234',
        );

        $this->assertNotNull($account->id);
        $this->assertEquals('name', $account->name);
        $this->assertEquals('email@dev.com', $account->email);
        $this->assertEquals('616.177.000-88', $account->cpfCnpj);
        $this->assertEquals(false, $account->shopkeeper);
        $this->assertEquals(date('Y-m-d H:m'), date('Y-m-d H:m', strtotime($account->createdAt())));
        $this->assertEquals(0, $account->balance);
    }

    public function testIsShopkeeper()
    {
        $account = Account::create(
            name: 'name',
            email: 'email@dev.com',
            cpfCnpj: '34.212.980/0001-08',
            password: '1234'
        );

        $this->assertTrue($account->shopkeeper);
    }

    public function testInvalidEmail()
    {
        try {
            Account::create(
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
            Account::create(
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
            Account::create(
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

    public function testMakeTransfer()
    {
        $account = Account::create(
            name: 'name',
            email: 'email@dev.com',
            cpfCnpj: '616.177.000-88',
            password: '1234',
            balance: 100
        );

        $account->makeTransfer(10);
        $this->assertEquals(90, $account->balance);

        $account = Account::create(
            name: 'name',
            email: 'email@dev.com',
            cpfCnpj: '616.177.000-88',
            password: '1234',
            balance: 21.5
        );

        $account->makeTransfer(13.2);
        $this->assertEquals(8.3, $account->balance);

        try {

            $account = Account::create(
                name: 'name',
                email: 'email@dev.com',
                cpfCnpj: '616.177.000-88',
                password: '1234',
                balance: -21.5
            );

            $account->makeTransfer(13.2);
            $this->assertTrue(true);
        } catch (Exception $e) {
            $this->assertTrue($e instanceof EntityValidationException);
            $this->assertEquals("balance unavailable to carry out transaction", $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function testReceiverTransfer()
    {
        $account = Account::create(
            name: 'name',
            email: 'email@dev.com',
            cpfCnpj: '616.177.000-88',
            password: '1234',
            balance: 10
        );

        $account->receiveTransfer(35);
        $this->assertEquals(45, $account->balance);
    }

    public function transferError()
    {
        $account = Account::create(
            name: 'name',
            email: 'email@dev.com',
            cpfCnpj: '616.177.000-88',
            password: '1234',
            balance: 10
        );

        $account->receiveTransfer(35);
        $account->transferReprovedEstimateValue(35);
        $this->assertEquals(10, $account->balance);
    }

    public function testMakeTransferBalanceZero()
    {
        try {
            $account = Account::create(
                name: 'name',
                email: 'email@dev.com',
                cpfCnpj: '616.177.000-88',
                password: '1234'
            );

            $account->makeTransfer(10.0);
            $this->assertTrue(false);
        } catch (Exception $e) {
            $this->assertTrue($e instanceof EntityValidationException);
            $this->assertEquals("balance unavailable to carry out transaction", $e->getMessage());
        }
    }

    public function testMakeTransferByShopKeeperType()
    {
        try {
            $account = Account::create(
                name: 'name',
                email: 'email@dev.com',
                cpfCnpj: '34.212.980/0001-08',
                password: '1234',
                balance: 100
            );

            $account->makeTransfer(10.0);
            $this->assertTrue(false);
        } catch (Exception $e) {
            $this->assertTrue($e instanceof EntityValidationException);
            $this->assertEquals("Shopkeeper can't make transfer", $e->getMessage());
        }
    }

    // public function testMakeTransferDecimalNumbers()
    // {
    //     $account = Account::create(
    //         name: 'name',
    //         email: 'email@dev.com',
    //         cpfCnpj: '616.177.000-88',
    //         password: '1234',
    //         balance: 1.32578978979
    //     );

    //     $account->makeTransfer(1.24578978979);
    //     $this->assertEquals(0.08, $account->balance);

    //     $account = Account::create(
    //         name: 'name',
    //         email: 'email@dev.com',
    //         cpfCnpj: '616.177.000-88',
    //         password: '1234',
    //         balance: 92.001
    //     );

    //     $account->makeTransfer(91.199);
    //     $this->assertEquals(0.802, $account->balance);
    // }
}
