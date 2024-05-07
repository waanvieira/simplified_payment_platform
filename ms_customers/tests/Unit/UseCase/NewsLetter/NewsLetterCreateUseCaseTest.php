<?php

namespace Tests\Unit\UseCase;

use App\Domain\Entities\NewsLetter;
use App\Domain\Repositories\NewsletterEntityRepositoryInterface;
use App\Domain\Repositories\UserEntityRepositoryInterface;
use App\Models\User;
use App\UseCases\DTO\NewsLetter\NewsLetterCreateInputDto;
use App\UseCases\DTO\NewsLetter\NewsLetterCreateOutputDto;
use App\UseCases\NewsLetter\NewsLetterCreateUseCase;
use Mockery;
use PHPUnit\Framework\TestCase as FrameworkTestCase;
use stdClass;

//Testes unitarios
class NewsLetterCreateUseCaseTest extends FrameworkTestCase
{
    public function testCreateNewsLetter()
    {
        $name = 'usuario teste2';
        $description = 'email@dev.com.br';
        $email = 'email@dev.com.br';
        $modelEntity = NewsLetter::create($name, $description);

        $userData = [
            'name' => $name,
            'email' => $email,
            'is_admin' => true,
        ];

        $modelUser = (new User($userData));
        $repositoyMock = Mockery::mock(stdClass::class, NewsletterEntityRepositoryInterface::class);
        $repositoyMock->shouldReceive('insert')->andReturn($modelEntity);

        $userRespositoty = Mockery::mock(stdClass::class, UserEntityRepositoryInterface::class);
        $userRespositoty->shouldReceive('findByEmail')->andReturn($modelUser);

        $useCase = new NewsLetterCreateUseCase($repositoyMock, $userRespositoty);
        $mockInputDto = Mockery::mock(NewsLetterCreateInputDto::class, [$name, $description, $email]);
        $userResponse =  $useCase->execute($mockInputDto);
        $this->assertInstanceOf(NewsLetterCreateOutputDto::class, $userResponse);
        $this->assertEquals($modelEntity->id, $userResponse->id);
        $this->assertEquals($name, $userResponse->name);
        $this->assertEquals($description, $userResponse->description);
        $this->assertNotNull($userResponse->created_at);
        Mockery::close();
    }

    public function testCreatenNewsLetterSpie()
    {
        $name = 'usuario teste2';
        $description = 'email@dev.com.br';
        $email = 'email@dev.com.br';
        $modelEntity = NewsLetter::create($name, $description);
        $userData = [
            'name' => $name,
            'email' => $email,
            'is_admin' => true,
        ];
        $modelUser = (new User($userData));
        $repositoyMock = Mockery::mock(stdClass::class, NewsletterEntityRepositoryInterface::class);
        $repositoyMock->shouldReceive('insert')->andReturn($modelEntity);

        $userRespositoty = Mockery::mock(stdClass::class, UserEntityRepositoryInterface::class);
        $userRespositoty->shouldReceive('findByEmail')->andReturn($modelUser);

        $useCase = new NewsLetterCreateUseCase($repositoyMock, $userRespositoty);
        $mockInputDto = Mockery::mock(NewsLetterCreateInputDto::class, [$name, $description, $email]);

        $userResponse =  $useCase->execute($mockInputDto);
        $this->assertInstanceOf(NewsLetterCreateOutputDto::class, $userResponse);
        $this->assertEquals($modelEntity->id, $userResponse->id);
        $this->assertEquals($name, $userResponse->name);
        $this->assertEquals($description, $userResponse->description);
        $userResponse =  $useCase->execute($mockInputDto);
        $userRespositoty->shouldHaveReceived('findByEmail');
        $repositoyMock->shouldHaveReceived('insert');
        Mockery::close();
    }
}
