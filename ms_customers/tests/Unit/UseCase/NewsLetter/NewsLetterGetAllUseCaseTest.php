<?php

namespace Tests\Unit\UseCase;

use App\Domain\Repositories\NewsletterEntityRepositoryInterface;
use App\Models\NewsLetter as ModelsNewsLetter;
use App\UseCases\NewsLetter\NewsLetterGetAllUseCase;
use Mockery;
use Tests\TestCase;
use stdClass;

class NewsLetterGetAllUseCaseTest extends TestCase
{
    public function testGetAllUseCase()
    {
        $modelNewsLetter = ModelsNewsLetter::factory()->count(10)->create();
        $repositoyMock = Mockery::mock(stdClass::class, NewsletterEntityRepositoryInterface::class);
        $repositoyMock->shouldReceive('getAll')->andReturn($modelNewsLetter->toArray());
        $useCase = new NewsLetterGetAllUseCase($repositoyMock);
        $newsLetterResponse = $useCase->execute();
        $this->assertIsArray($newsLetterResponse);
        $this->assertEquals(10, count($newsLetterResponse));
        // Spie
        $repositoyMock->shouldHaveReceived('getAll');
        $this->assertTrue(true);
        Mockery::close();
    }
}
