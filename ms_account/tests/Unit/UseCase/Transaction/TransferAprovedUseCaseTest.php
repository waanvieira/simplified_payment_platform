<?php

namespace Tests\Unit\UseCase\Transaction;

use App\Domain\Enum\TransactionStatus;
use App\Domain\ValueObjects\Uuid;
use App\Exceptions\NotFoundException;
use App\Models\Account as ModelsAccount;
use App\Models\Transaction as ModelsTransaction;
use App\Repositories\Eloquent\AccountEloquentRepository;
use App\Repositories\Eloquent\TransactionEloquentRepository;
use App\Services\RabbitMQ\AMQPService;
use App\Services\RabbitMQ\RabbitInterface;
use App\UseCases\DTO\Transaction\TransactionCreateInputDto;
use App\UseCases\DTO\Transaction\TransferAprovedInputDto;
use App\UseCases\Transaction\TransferAprovedUseCase;
use Exception;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Mockery;
use stdClass;
use Tests\TestCase;
use Tests\Traits\Entity\CreateTransactionByTest;

class TransferAprovedUseCaseTest extends TestCase
{
    use CreateTransactionByTest;
    use DatabaseTransactions;

    public function testExecuteSuccess()
    {
        $response = $this->createExecute();
        $transactionDb = $this->createTransactionDb();
        $mockInputDto = Mockery::mock(TransferAprovedInputDto::class, [$transactionDb->id, $transactionDb->payer_id, $transactionDb->payee_id, 12, TransactionStatus::APROVED->value]);
        $response->execute($mockInputDto);
        $accountDb = ModelsAccount::find($transactionDb->payee_id);
        $this->assertEquals(32, $accountDb->balance);
        $transactionDb = ModelsTransaction::find($transactionDb->id);
        $this->assertEquals(TransactionStatus::APROVED->value, $transactionDb->transaction_status);
        $this->assertEquals(date('Y-m-d H:i'), date('Y-m-d H:i', strtotime($transactionDb->confirmation_at)));
        Mockery::close();
    }

    // public function testExecuteNotFoundException()
    // {
    //     try {
    //         $response = $this->createExecute();
    //         $transactionFake = UUid::random();
    //         $mockInputDto = Mockery::mock(TransferAprovedInputDto::class, [$transactionFake, Uuid::random(), Uuid::random(), 12, TransactionStatus::APROVED->value]);
    //         $response->execute($mockInputDto);
    //         $this->assertFalse(true);
    //     } catch (Exception $e) {
    //         // $this->isInstanceOf(NotFoundException::class, $e);
    //         // $this->assertEquals("Register $transactionFake Not Found", $e->getMessage());
    //     }
    // }

    private function createExecute()
    {
        $model = new ModelsTransaction();
        $repositoyMock = new TransactionEloquentRepository($model);
        $rabbitInterface = Mockery::mock(stdClass::class, RabbitInterface::class);
        // $rabbitInterface = new AMQPService();
        $rabbitInterface->shouldReceive('producer')->andReturn(true);
        $modelAccount = new ModelsAccount();
        $repositoyMockAccount = new AccountEloquentRepository($modelAccount);
        return new TransferAprovedUseCase($repositoyMock, $repositoyMockAccount, $rabbitInterface);
    }

}
