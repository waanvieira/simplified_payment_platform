<?php

namespace Tests\Unit\UseCase\Users;

use App\Domain\Entities\User;
use App\Domain\Repositories\UserEntityRepositoryInterface;
use App\Services\RabbitMQ\RabbitInterface;
use App\UseCases\DTO\User\UserCreateInputDto;
use App\UseCases\DTO\User\UserCreateOutputDto;
use App\UseCases\User\UserCreateUseCase;
use Mockery;
use PHPUnit\Framework\TestCase as FrameworkTestCase;
use stdClass;

//Testes unitarios
class UserCreateUseCaseTest extends FrameworkTestCase
{
    public function testCreateNewUser()
    {
        $modelEntity = $this->createUser();
        $repositoyMock = Mockery::mock(stdClass::class, UserEntityRepositoryInterface::class);
        $repositoyMock->shouldReceive('findByEmail')->andReturn(null);
        $repositoyMock->shouldReceive('findByCpfCnpj')->andReturn(null);
        $repositoyMock->shouldReceive('insert')->andReturn($modelEntity);
        $rabbitInterface = Mockery::mock(stdClass::class, RabbitInterface::class);
        $rabbitInterface->shouldReceive('producer')->andReturn(true);
        $useCase = new UserCreateUseCase($repositoyMock, $rabbitInterface);
        $mockInputDto = Mockery::mock(UserCreateInputDto::class, [$modelEntity->name, $modelEntity->cpfCnpj, $modelEntity->email, $modelEntity->password]);
        $userResponse =  $useCase->execute($mockInputDto);
        $this->assertInstanceOf(UserCreateOutputDto::class, $userResponse);
        $this->assertEquals($modelEntity->id(), $userResponse->id);
        $this->assertEquals($modelEntity->name, $userResponse->name);
        $this->assertEquals($modelEntity->email, $userResponse->email);
        $this->assertFalse($userResponse->shopkeeper);
        Mockery::close();
    }

    public function testCreateNewUserSpie()
    {
        $modelEntity = $modelEntity = $this->createUser();
        $repositoySpy = Mockery::spy(stdClass::class, UserEntityRepositoryInterface::class);
        $repositoySpy->shouldReceive('findByEmail')->andReturn(null);
        $repositoySpy->shouldReceive('findByCpfCnpj')->andReturn(null);
        $repositoySpy->shouldReceive('insert')->andReturn($modelEntity);
        $rabbitInterface = Mockery::mock(stdClass::class, RabbitInterface::class);
        $rabbitInterface->shouldReceive('producer')->andReturn(true);
        $useCase = new UserCreateUseCase($repositoySpy, $rabbitInterface);
        $mockInputDto = Mockery::mock(UserCreateInputDto::class, [$modelEntity->name, $modelEntity->cpfCnpj, $modelEntity->email, $modelEntity->password]);
        $useCase->execute($mockInputDto);
        $repositoySpy->shouldHaveReceived('findByEmail');
        $repositoySpy->shouldHaveReceived('findByCpfCnpj');
        $res = $repositoySpy->shouldHaveReceived('insert');
        $this->assertNotNull($res);
        Mockery::close();
    }

    private function createUser()
    {
        $name = 'usuario teste';
        $email = 'email@dev.com.br';
        $pass = '*****';
        $cpfCnpj = '616.177.000-88';
        return User::create($name, $cpfCnpj, $email, $pass);
    }
}
