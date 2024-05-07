<?php

namespace Tests\Unit\UseCase;

use App\Domain\Entities\Message;
use App\Domain\Entities\NewsLetter;
use App\Domain\Repositories\MessageEntityRepositoryInterface;
use App\Domain\Repositories\NewsletterEntityRepositoryInterface;
use App\UseCases\DTO\Message\MessageCreateInputDto;
use App\UseCases\DTO\Message\MessageCreateOutputDto;
use App\UseCases\Message\MessageCreateUseCase;
use Mockery;
use PHPUnit\Framework\TestCase as FrameworkTestCase;
use stdClass;

//Testes unitarios
class MessageCreateUseCaseTest extends FrameworkTestCase
{
    public function testCreateMessage()
   {
        $newsLetter = NewsLetter::create('ne', 'te');
        $title = 'usuario teste2';
        $message = 'message create test';
        $modelEntity = Message::create($title, $message, $newsLetter->id);
        $repositoyMock = Mockery::mock(stdClass::class, MessageEntityRepositoryInterface::class);
        $repositoyMock->shouldReceive('insert')->andReturn($modelEntity);

        $newsLetterRepository = Mockery::mock(stdClass::class, NewsletterEntityRepositoryInterface::class);
        $newsLetterRepository->shouldReceive('findById')->andReturn($newsLetter);

        $useCase = new MessageCreateUseCase($repositoyMock, $newsLetterRepository);
        $mockInputDto = Mockery::mock(MessageCreateInputDto::class, [$title, $message, $newsLetter->id]);

        $messageResponse =  $useCase->execute($mockInputDto);
        $this->assertInstanceOf(MessageCreateOutputDto::class, $messageResponse);
        $this->assertNotNull($modelEntity->id);
        $this->assertEquals($title, $messageResponse->title);
        $this->assertEquals($message, $messageResponse->message);
        $this->assertEquals($newsLetter->id, $modelEntity->newsLetterId);
        Mockery::close();
    }

    public function testCreateMessageSpie()
    {
        $newsLetter = NewsLetter::create('ne', 'te');
        $title = 'usuario teste2';
        $message = 'message create test';
        $modelEntity = Message::create($title, $message, $newsLetter->id);
        $repositoyMock = Mockery::mock(stdClass::class, MessageEntityRepositoryInterface::class);
        $repositoyMock->shouldReceive('insert')->andReturn($modelEntity);

        $newsLetterRepository = Mockery::mock(stdClass::class, NewsletterEntityRepositoryInterface::class);
        $newsLetterRepository->shouldReceive('findById')->andReturn($newsLetter);

        $useCase = new MessageCreateUseCase($repositoyMock, $newsLetterRepository);
        $mockInputDto = Mockery::mock(MessageCreateInputDto::class, [$title, $message, $newsLetter->id]);

        $useCase->execute($mockInputDto);
        $newsLetterRepository->shouldHaveReceived('findById');
        $repositoyMock->shouldHaveReceived('insert');
        $this->assertTrue(true);
        Mockery::close();
    }
}
