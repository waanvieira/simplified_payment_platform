<?php

namespace Tests\Unit\UseCase;

use App\Domain\Entities\Message;
use App\Domain\Entities\NewsLetter;
use App\Domain\Repositories\MessageEntityRepositoryInterface;
use App\Domain\Repositories\NewsletterEntityRepositoryInterface;
use App\UseCases\DTO\Message\MessageUpdateInputDto;
use App\UseCases\DTO\Message\MessageUpdateOutputDto;
use App\UseCases\Message\MessageUpdateUseCase;
use Mockery;
use PHPUnit\Framework\TestCase as FrameworkTestCase;
use stdClass;

//Testes unitarios
class MessageUpdateUseCaseTest extends FrameworkTestCase
{
    public function testCreateMessage()
   {
        $newsLetter = NewsLetter::create('ne', 'te');
        $title = 'title message';
        $message = 'message update test';
        $messageEntity = Message::create($title, $message, $newsLetter->id);
        $repositoyMock = Mockery::mock(stdClass::class, MessageEntityRepositoryInterface::class);
        $repositoyMock->shouldReceive('update')->andReturn($messageEntity);

        $newsLetterRepository = Mockery::mock(stdClass::class, NewsletterEntityRepositoryInterface::class);
        $newsLetterRepository->shouldReceive('findById')->andReturn($newsLetter);

        $useCase = new MessageUpdateUseCase($repositoyMock, $newsLetterRepository);
        $mockUpdateDto = Mockery::mock(MessageUpdateInputDto::class, [$messageEntity->id, $title, $message, $newsLetter->id]);

        $messageResponse =  $useCase->execute($mockUpdateDto);
        $this->assertInstanceOf(MessageUpdateOutputDto::class, $messageResponse);
        $this->assertNotNull($messageEntity->id);
        $this->assertEquals($title, $messageResponse->title);
        $this->assertEquals($message, $messageResponse->message);
        $this->assertEquals($newsLetter->id, $messageEntity->newsLetterId);
        Mockery::close();
    }

    public function testCreateMessageSpie()
    {
        $newsLetter = NewsLetter::create('ne', 'te');
        $title = 'title message';
        $message = 'message update test';
        $messageEntity = Message::create($title, $message, $newsLetter->id);
        $repositoyMock = Mockery::mock(stdClass::class, MessageEntityRepositoryInterface::class);
        $repositoyMock->shouldReceive('update')->andReturn($messageEntity);

        $newsLetterRepository = Mockery::mock(stdClass::class, NewsletterEntityRepositoryInterface::class);
        $newsLetterRepository->shouldReceive('findById')->andReturn($newsLetter);

        $useCase = new MessageUpdateUseCase($repositoyMock, $newsLetterRepository);
        $mockUpdateDto = Mockery::mock(MessageUpdateInputDto::class, [$messageEntity->id, $title, $message, $newsLetter->id]);
        $useCase->execute($mockUpdateDto);
        $newsLetterRepository->shouldHaveReceived('findById');
        $repositoyMock->shouldHaveReceived('update');
        $this->assertTrue(true);
        Mockery::close();
    }
}
