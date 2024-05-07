<?php

namespace Tests\Unit\UseCase;

use App\Domain\Entities\Message;
use App\Domain\Repositories\MessageEntityRepositoryInterface;
use App\UseCases\Message\MessageDeleteUseCase;
use Mockery;
use PHPUnit\Framework\TestCase as FrameworkTestCase;
use stdClass;

class MessageDeleteUseCaseTest extends FrameworkTestCase
{
    public function testDeleteMessage()
    {
        $title = 'title message';
        $message = 'message update test';
        $newsLetterId = '123';
        $messageEntity = Message::create($title, $message, $newsLetterId);
        $repositoyMock = Mockery::mock(stdClass::class, MessageEntityRepositoryInterface::class);
        $repositoyMock->shouldReceive('delete')->andReturn(true);

        $useCase = new MessageDeleteUseCase($repositoyMock);
        $messageResponse =  $useCase->execute($messageEntity->id);
        $this->assertTrue($messageResponse);
        Mockery::close();
    }

    public function testCreateMessageSpie()
    {
        $title = 'title message';
        $message = 'message update test';
        $newsLetterId = '123';
        $messageEntity = Message::create($title, $message, $newsLetterId);
        $repositoyMock = Mockery::mock(stdClass::class, MessageEntityRepositoryInterface::class);
        $repositoyMock->shouldReceive('delete')->andReturn(true);

        $useCase = new MessageDeleteUseCase($repositoyMock);
        $messageResponse =  $useCase->execute($messageEntity->id);
        $this->assertTrue($messageResponse);
        $repositoyMock->shouldHaveReceived('delete');
        $this->assertTrue(true);
        Mockery::close();
    }
}
