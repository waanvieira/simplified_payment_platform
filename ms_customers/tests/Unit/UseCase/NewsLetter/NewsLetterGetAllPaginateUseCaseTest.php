<?php

namespace Tests\Unit\UseCase;

use App\Domain\Repositories\NewsletterEntityRepositoryInterface;
use App\Models\NewsLetter as ModelsNewsLetter;
use App\UseCases\NewsLetter\NewsLetterGetAllPaginateUseCase;
use Mockery;
use Tests\TestCase;
use stdClass;

class NewsLetterGetAllPaginateUseCaseTest extends TestCase
{
    public function testGetAllUseCase()
    {
        $modelNewsLetter = ModelsNewsLetter::factory()->count(10)->create();
        $repositoyMock = Mockery::mock(stdClass::class, NewsletterEntityRepositoryInterface::class);
        $repositoyMock->shouldReceive('getAllPaginate')->andReturn($modelNewsLetter);
        $useCase = new NewsLetterGetAllPaginateUseCase($repositoyMock);
        $newsLetterResponse = $useCase->execute();
        $this->assertEquals(10, count($newsLetterResponse));
        // Spie
        $repositoyMock->shouldHaveReceived('getAllPaginate');
        $this->assertTrue(true);
        Mockery::close();
    }
}
