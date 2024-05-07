<?php

namespace Tests\Unit\UseCase;

use App\Domain\Entities\NewsLetter;
use App\Domain\Repositories\NewsletterEntityRepositoryInterface;
use App\UseCases\NewsLetter\NewsLetterDeleteUseCase;
use Mockery;
use PHPUnit\Framework\TestCase as FrameworkTestCase;
use stdClass;

//Testes unitarios
class NewsLetterDeleteUseCaseTest extends FrameworkTestCase
{
    public function testDelete()
    {
        $modelEntity = $this->newsLetter();
        $repositoyMock = Mockery::mock(stdClass::class, NewsletterEntityRepositoryInterface::class);
        $repositoyMock->shouldReceive('delete')->andReturn(true);
        $useCase = new NewsLetterDeleteUseCase($repositoyMock);
        $userResponse =  $useCase->execute($modelEntity->id);
        $this->assertTrue($userResponse);
        // Spie
        $repositoyMock->shouldHaveReceived('delete');
        $this->assertTrue(true);
        Mockery::close();
    }

    public function newsLetter()
    {
        return NewsLetter::create('newletter', 'description');
    }
}
