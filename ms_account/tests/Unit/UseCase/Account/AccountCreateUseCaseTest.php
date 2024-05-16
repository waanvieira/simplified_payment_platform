<?php

namespace Tests\Unit\UseCase\Account;

use App\Domain\Entities\Account;
use App\Domain\Repositories\AccountEntityRepositoryInterface;
use App\Services\RabbitMQ\RabbitInterface;
use App\UseCases\Account\AccountCreateUseCase;
use App\UseCases\DTO\Account\AccountCreateInputDto;
use App\UseCases\DTO\Account\AccountCreateOutputDto;
use Mockery;
use PHPUnit\Framework\TestCase as FrameworkTestCase;
use stdClass;

class AccountCreateUseCaseTest extends FrameworkTestCase
{
    public function testCreateNewAccount()
    {
        $modelEntity = $this->createAccount();
        $repositoyMock = Mockery::mock(stdClass::class, AccountEntityRepositoryInterface::class);
        $repositoyMock->shouldReceive('findByEmail')->andReturn(null);
        $repositoyMock->shouldReceive('findByCpfCnpj')->andReturn(null);
        $repositoyMock->shouldReceive('insert')->andReturn($modelEntity);
        $rabbitInterface = Mockery::mock(stdClass::class, RabbitInterface::class);
        $rabbitInterface->shouldReceive('producer')->andReturn(true);
        $useCase = new AccountCreateUseCase($repositoyMock, $rabbitInterface);
        $mockInputDto = Mockery::mock(AccountCreateInputDto::class, [$modelEntity->name, $modelEntity->cpfCnpj, $modelEntity->email, $modelEntity->password]);
        $accountResponse = $useCase->execute($mockInputDto);
        $this->assertInstanceOf(AccountCreateOutputDto::class, $accountResponse);
        $this->assertEquals($modelEntity->id(), $accountResponse->id);
        $this->assertEquals($modelEntity->name, $accountResponse->name);
        $this->assertEquals($modelEntity->email, $accountResponse->email);
        $this->assertEquals(0, $accountResponse->balance);
        $this->assertFalse($accountResponse->shopkeeper);
        Mockery::close();
    }

    public function testCreateNewAccountSpie()
    {
        $modelEntity = $modelEntity = $this->createAccount();
        $repositoySpy = Mockery::spy(stdClass::class, AccountEntityRepositoryInterface::class);
        $repositoySpy->shouldReceive('findByEmail')->andReturn(null);
        $repositoySpy->shouldReceive('findByCpfCnpj')->andReturn(null);
        $repositoySpy->shouldReceive('insert')->andReturn($modelEntity);
        $rabbitInterface = Mockery::mock(stdClass::class, RabbitInterface::class);
        $rabbitInterface->shouldReceive('producer')->andReturn(true);
        $useCase = new AccountCreateUseCase($repositoySpy, $rabbitInterface);
        $mockInputDto = Mockery::mock(AccountCreateInputDto::class, [$modelEntity->name, $modelEntity->cpfCnpj, $modelEntity->email, $modelEntity->password]);
        $useCase->execute($mockInputDto);
        $repositoySpy->shouldHaveReceived('findByEmail');
        $repositoySpy->shouldHaveReceived('findByCpfCnpj');
        $res = $repositoySpy->shouldHaveReceived('insert');
        $this->assertNotNull($res);
        Mockery::close();
    }

    private function createAccount()
    {
        $name = 'usuario teste';
        $email = 'email@dev.com.br';
        $pass = '*****';
        $cpfCnpj = '616.177.000-88';

        return Account::create($name, $cpfCnpj, $email, $pass);
    }
}
