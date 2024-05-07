<?php

namespace Tests\Unit\UseCase;

use App\Domain\Entities\NewsLetter;
use App\Domain\Repositories\NewsletterEntityRepositoryInterface;
use App\UseCases\DTO\NewsLetter\NewsLetterUpdateOutputDto;
use App\UseCases\NewsLetter\NewsLetterFindByIdUseCase;
use Mockery;
use PHPUnit\Framework\TestCase as FrameworkTestCase;
use Ramsey\Uuid\Uuid;
use stdClass;

class NewsLetterFindByIdUseCaseTest extends FrameworkTestCase
{
    public function testFindByIdUseCase()
    {
        $id = Uuid::uuid4()->toString();
        $modelEntity = NewsLetter::restore($id, 'name', 'description', date('Y-m-d h:i:s'));
        $repositoyMock = Mockery::mock(stdClass::class, NewsletterEntityRepositoryInterface::class);
        $repositoyMock->shouldReceive('findById')->andReturn($modelEntity);
        $useCase = new NewsLetterFindByIdUseCase($repositoyMock);
        $newsLetterResponse =  $useCase->execute($modelEntity->id);
        $this->assertInstanceOf(NewsLetterUpdateOutputDto::class, $newsLetterResponse);
        $this->assertEquals($modelEntity->id, $newsLetterResponse->id);
        $this->assertEquals($modelEntity->name, $newsLetterResponse->name);
        $this->assertEquals($modelEntity->description, $newsLetterResponse->description);
        $this->assertEquals($modelEntity->createdAt, $newsLetterResponse->created_at);
        // Spie
        $repositoyMock->shouldHaveReceived('findById');
        $this->assertTrue(true);
        Mockery::close();
    }

    public function newsLetter()
    {
        return NewsLetter::create('newletter', 'description');
    }
}
