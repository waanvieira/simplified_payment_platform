<?php

namespace Tests\Unit\UseCase\Transaction;

use App\Domain\Entities\Account;
use App\Domain\Entities\Transaction;
use App\Domain\Enum\TransactionStatus;
use App\Domain\Enum\TransactionType;
use App\Domain\Repositories\AccountEntityRepositoryInterface;
use App\Domain\Repositories\TransactionEntityRepositoryInterface;
use App\Domain\ValueObjects\Uuid;
use App\Exceptions\NotFoundException;
use App\Models\Account as ModelsAccount;
use App\Models\Transaction as ModelsTransaction;
use App\Repositories\Eloquent\AccountEloquentRepository;
use App\Repositories\Eloquent\TransactionEloquentRepository;
use App\Services\RabbitMQ\RabbitInterface;
use App\UseCases\DTO\Transaction\TransactionCreateInputDto;
use App\UseCases\DTO\Transaction\TransactionCreateOutputDto;
use App\UseCases\Transaction\TransferCreateUseCase;
use Exception;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Mockery;
use stdClass;
use Tests\TestCase;
use Tests\Traits\Entity\CreateTransactionByTest;

class TransferCreateUseCaseTest extends TestCase
{
    use CreateTransactionByTest;
    use DatabaseTransactions;

    public function testCreateNewTransfer()
    {
        $payer = ModelsAccount::factory()->create(["id" => Uuid::random(), "name" => fake()->name(), "cpf_cnpj"=> '352.318.010-46', "email" => fake()->email(), "balance" => 68]);
        $payee = ModelsAccount::factory()->create(["id" => Uuid::random(), "name" => fake()->name(), "cpf_cnpj"=> '368.708.660-74', "email" => fake()->email(), "balance" => 20]);
        $model = new ModelsTransaction();
        $repositoyMock = new TransactionEloquentRepository($model);
        $rabbitInterface = Mockery::mock(stdClass::class, RabbitInterface::class);
        $rabbitInterface->shouldReceive('producer')->andReturn(true);
        $modelAccount = new ModelsAccount();
        $repositoyMockAccount = new AccountEloquentRepository($modelAccount);
        $useCase = new TransferCreateUseCase($repositoyMock, $repositoyMockAccount, $rabbitInterface);
        $mockInputDto = Mockery::mock(TransactionCreateInputDto::class, [$payer->id, $payee->id, 10]);
        $transactionResponse = $useCase->execute($mockInputDto);
        $this->assertInstanceOf(TransactionCreateOutputDto::class, $transactionResponse);
        $this->assertNotNull($transactionResponse->id);
        $this->assertEquals(TransactionType::TRANSFER->value, $transactionResponse->transaction_type);
        $this->assertEquals($payer->id, $transactionResponse->payer_id);
        $this->assertEquals($payee->id, $transactionResponse->payee_id);
        $this->assertEquals(10, $transactionResponse->value);
        $this->assertEquals(TransactionStatus::PROCESSING->value, $transactionResponse->transaction_status);
        $this->assertEquals(date('Y-m-d H:i:s'), $transactionResponse->created_at);
        $accountDb = ModelsAccount::find($payer->id);
        $this->assertEquals(58, $accountDb->balance);
        Mockery::close();
    }

    public function testExecuteNotFoundException()
    {
        try {
            $payerId = UUid::random();
            $response = $this->createExecute();
            $mockInputDto = Mockery::mock(TransactionCreateInputDto::class, [$payerId, Uuid::random(), 10]);
            $response->execute($mockInputDto);
            $this->assertTrue(false);
        } catch (Exception $e) {
            $this->isInstanceOf(NotFoundException::class, $e);
            $this->assertEquals("Register $payerId Not Found", $e->getMessage());
        }
    }

    private function createExecute()
    {
        $model = new ModelsTransaction();
        $repositoyMock = new TransactionEloquentRepository($model);
        $rabbitInterface = Mockery::mock(stdClass::class, RabbitInterface::class);
        $rabbitInterface->shouldReceive('producer')->andReturn(true);
        $modelAccount = new ModelsAccount();
        $repositoyMockAccount = new AccountEloquentRepository($modelAccount);
        return new TransferCreateUseCase($repositoyMock, $repositoyMockAccount, $rabbitInterface);
    }

}
