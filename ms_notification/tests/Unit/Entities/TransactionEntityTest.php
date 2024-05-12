<?php

namespace Tests\Unit\Models;

use App\Domain\Entities\Account;
use App\Domain\Entities\Transaction;
use App\Domain\Enum\TransactionStatus;
use App\Domain\Enum\TransactionType;
use App\Exceptions\EntityValidationException;
use App\Exceptions\InvalidArgumentException;
use DateTime;
use Exception;
use Tests\TestCase;

class TransactionEntityTest extends TestCase
{
    public function testAttributes()
    {
        $payer = Account::create('user payer', '563.657.910-11', 'tes@dev.com.br', 30);
        $payee = Account::create('user payee', '424.559.250-80', 'tes2@dev.com.br', 20);
        $transaction = Transaction::create(
            transactionType: TransactionType::TRANSFER,
            payerId: $payer->id,
            payeeId: $payee->id,
            value: 10,
        );

        $this->assertNotNull($transaction->id);
        $this->assertEquals('TRANSFER', $transaction->transactionType->value);
        $this->assertEquals($payer->id, $transaction->payerId());
        $this->assertEquals($payee->id, $transaction->payeeId());
        $this->assertEquals(10, $transaction->value);
        $this->assertEquals('PROCESSING', $transaction->transactionStatus->value);
    }

    public function testTransactionAproved()
    {
        $transaction = $this->createTransactionEntity();
        $transaction->paymentAproved();
        $this->assertEquals(TransactionStatus::APROVED, $transaction->transactionStatus);
    }

    public function testTransactionAlrightAproved()
    {
        try {
            $transaction = $this->createTransactionEntity();
            $transaction->paymentAproved();
            $transaction->paymentAproved();
            $this->assertTrue(false);
        } catch (Exception $e) {
            $this->assertInstanceOf(EntityValidationException::class, $e);
            $this->assertEquals("payment already approved", $e->getMessage());
        }
    }

    public function testTransactioReproved()
    {
        $transaction = $this->createTransactionEntity();
        $transaction->paymentReproved();
        $this->assertEquals(TransactionStatus::ERROR, $transaction->transactionStatus);
    }

    public function testTransactionAlrightReproved()
    {
        try {
            $transaction = $this->createTransactionEntity();
            $transaction->paymentAproved();
            $transaction->paymentReproved();
            $this->assertTrue(false);
        } catch (Exception $e) {
            $this->assertInstanceOf(EntityValidationException::class, $e);
            $this->assertEquals("payment already approved", $e->getMessage());
        }
    }

    public function testTransactionAlrightConfirmationAt()
    {
            $transaction = $this->createTransactionEntity();
            $transactionRestore = Transaction::restore(
                id: $transaction->id,
                transactionType: $transaction->transactionType,
                payerId: $transaction->payerId,
                payeeId: $transaction->payeeId,
                value: $transaction->value,
                transactionStatus: TransactionStatus::APROVED,
                confirmationAt: new DateTime()
            );

            $this->assertEquals(TransactionStatus::PROCESSING, $transactionRestore->transactionStatus);
            $transactionRestore->paymentAproved();
            $this->assertEquals(TransactionStatus::APROVED, $transactionRestore->transactionStatus);
            $this->assertEquals(date('Y-m-d H:i:s'), $transactionRestore->confirmationAt());
    }

    private function createTransactionEntity()
    {
        $payer = Account::create(fake()->name(), '563.657.910-11', fake()->email(), 30);
        $payee = Account::create(fake()->name(), '424.559.250-80', fake()->email(), 20);
        return Transaction::create(
            transactionType: TransactionType::TRANSFER,
            payerId: $payer->id,
            payeeId: $payee->id,
            value: 10,
        );
    }
}
