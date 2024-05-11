<?php

namespace Tests\Traits\Entity;

use App\Domain\Entities\Account;
use App\Domain\Entities\Transaction;
use App\Domain\Enum\TransactionStatus;
use App\Domain\Enum\TransactionType;
use App\Domain\ValueObjects\Uuid;
use App\Models\Account as ModelsAccount;
use App\Models\Transaction as ModelsTransaction;

trait CreateTransactionByTest
{
    public function createTransactionEntity()
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

    public function createTransactionEntityMock()
    {
        $payer = Account::create(fake()->name(), '563.657.910-11', fake()->email(), 30);
        $payee = Account::create(fake()->name(), '424.559.250-80', fake()->email(), 20);
        return  $this->createStub(Transaction::class);
        // Configure the stub.
        // $stub->method('doSomething')
        //      ->willReturn('foo');
        // return $this->getMockBuilder(Transaction::class)
        //     ->setConstructorArgs([
        //         "id" => Uuid::random(),
        //         "transactionType" => TransactionType::TRANSFER,
        //         "payerId" => $payer->id,
        //         "payeeId" => $payee->id,
        //         "value" => 10,
        //     ])
        //     ->getMock();
    }

    public function createTransactionDb($transactionType = TransactionType::TRANSFER->value, $value = 10, $transactionStatus = TransactionStatus::PROCESSING->value)
    {
        $payer = ModelsAccount::factory()->create(["id" => Uuid::random(), "name" => fake()->name(), "cpf_cnpj" => '352.318.010-46', "email" => fake()->email(), "balance" => 68]);
        $payee = ModelsAccount::factory()->create(["id" => Uuid::random(), "name" => fake()->name(), "cpf_cnpj" => '368.708.660-74', "email" => fake()->email(), "balance" => 20]);
        return ModelsTransaction::factory()->create([
            "id" => Uuid::random(),
            "transaction_type" => $transactionType,
            "payer_id" => $payer->id,
            "payee_id" => $payee->id,
            "value" => $value,
            "transaction_status" => $transactionStatus
        ]);
    }
}
