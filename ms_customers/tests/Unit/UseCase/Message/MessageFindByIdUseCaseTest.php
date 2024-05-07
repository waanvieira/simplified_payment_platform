<?php

namespace Tests\Unit\UseCase;

use App\Domain\Entities\Message;
use App\Domain\Repositories\MessageEntityRepositoryInterface;
use App\UseCases\DTO\Message\MessageUpdateOutputDto;
use App\UseCases\Message\MessageDeleteUseCase;
use App\UseCases\Message\MessageFindByIDUseCase;
use Mockery;
use PHPUnit\Framework\TestCase as FrameworkTestCase;
use stdClass;

class MessageFindByIdUseCaseTest extends FrameworkTestCase
{
    public function testFindByIdMessage()
    {
        $title = 'title message';
        $message = 'message update test';
        $newsLetterId = '123';
        $messageEntity = Message::create($title, $message, $newsLetterId);
        $repositoyMock = Mockery::mock(stdClass::class, MessageEntityRepositoryInterface::class);
        $repositoyMock->shouldReceive('findById')->andReturn($messageEntity);

        $useCase = new MessageFindByIDUseCase($repositoyMock);
        $messageResponse =  $useCase->execute($messageEntity->id);
        $this->assertInstanceOf(MessageUpdateOutputDto::class, $messageResponse);
        $this->assertNotNull($messageEntity->id);
        $this->assertEquals($title, $messageResponse->title);
        $this->assertEquals($message, $messageResponse->message);
        $this->assertEquals($newsLetterId, $messageEntity->newsLetterId);
        Mockery::close();
    }

    public function testCreateMessageSpie()
    {
        $title = 'title message';
        $message = 'message update test';
        $newsLetterId = '123';
        $messageEntity = Message::create($title, $message, $newsLetterId);
        $repositoyMock = Mockery::mock(stdClass::class, MessageEntityRepositoryInterface::class);
        $repositoyMock->shouldReceive('findById')->andReturn($messageEntity);
        $useCase = new MessageFindByIDUseCase($repositoyMock);
        $useCase->execute($messageEntity->id);
        $repositoyMock->shouldHaveReceived('findById');
        $this->assertTrue(true);
        Mockery::close();
    }
}
