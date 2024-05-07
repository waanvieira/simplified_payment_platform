<?php

namespace Tests\Unit\UseCase;

use App\Domain\Entities\NewsLetter;
use App\Domain\Repositories\NewsletterEntityRepositoryInterface;
use App\Domain\Repositories\UserEntityRepositoryInterface;
use App\Domain\ValueObjects\Uuid;
use App\Models\User;
use App\UseCases\DTO\NewsLetter\NewsLetterUpdateInputDto;
use App\UseCases\DTO\NewsLetter\NewsLetterUpdateOutputDto;
use App\UseCases\NewsLetter\NewsLetterUpdateUseCase;
use DateTime;
use Mockery;
use PHPUnit\Framework\TestCase as FrameworkTestCase;
use stdClass;

//Testes unitarios
class NewsLetterUpdateUseCaseTest extends FrameworkTestCase
{
    public function testUpdateNewsLetter()
    {
        $id = Uuid::random();
        $name = 'usuario teste2';
        $description = 'description';
        $email = 'email@dev.com.br';
        $modelEntity = NewsLetter::restore($id, $name, $description, date('Y-m-d H:m:s'));
        // $modelEntity = NewsLetter::restore($id, $name, $description);
        // $modelEntity = Mockery::mock('alias:'.NewsLetter::class);
        // $modelEntity->shouldReceive('create')->andReturn(
        $userData = [
            'name' => $name,
            'email' => $email,
            'is_admin' => true,
        ];

        $modelUser = (new User($userData));
        $repositoyMock = Mockery::mock(stdClass::class, NewsletterEntityRepositoryInterface::class);
        $repositoyMock->shouldReceive('update')->andReturn($modelEntity);
        $repositoyMock->shouldReceive('findById')->andReturn($modelEntity);

        $userRespositoty = Mockery::mock(stdClass::class, UserEntityRepositoryInterface::class);
        $userRespositoty->shouldReceive('findByEmail')->andReturn($modelUser);

        $useCase = new NewsLetterUpdateUseCase($repositoyMock, $userRespositoty);
        $mockUpdateDto = Mockery::mock(NewsLetterUpdateInputDto::class, [$id, $name, $description, $email]);

        $userResponse =  $useCase->execute($mockUpdateDto);
        $this->assertInstanceOf(NewsLetterUpdateOutputDto::class, $userResponse);
        $this->assertEquals($modelEntity->id, $userResponse->id);
        $this->assertEquals($name, $userResponse->name);
        $this->assertEquals($description, $userResponse->description);
        Mockery::close();
    }

    public function testUpdateNewsLetterSpie()
    {
        $id = Uuid::random();
        $name = 'usuario teste2';
        $description = 'description';
        $email = 'email@dev.com.br';
        $modelEntity = NewsLetter::restore($id, $name, $description);
        $userData = [
            'name' => $name,
            'email' => $email,
            'is_admin' => true,
        ];
        $modelUser = (new User($userData));
        $repositoyMock = Mockery::mock(stdClass::class, NewsletterEntityRepositoryInterface::class);
        $repositoyMock->shouldReceive('update')->andReturn($modelEntity);
        $repositoyMock->shouldReceive('findById')->andReturn($modelEntity);

        $userRespositoty = Mockery::mock(stdClass::class, UserEntityRepositoryInterface::class);
        $userRespositoty->shouldReceive('findByEmail')->andReturn($modelUser);

        $useCase = new NewsLetterUpdateUseCase($repositoyMock, $userRespositoty);
        $mockUpdateDto = Mockery::mock(NewsLetterUpdateInputDto::class, [$id, $name, $description, $email]);
        $useCase->execute($mockUpdateDto);
        $userRespositoty->shouldHaveReceived('findByEmail');
        $repositoyMock->shouldHaveReceived('findById');
        $repositoyMock->shouldHaveReceived('update');
        $this->assertTrue(true);
        Mockery::close();
    }
}
