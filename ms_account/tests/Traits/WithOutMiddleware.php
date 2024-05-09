<?php

namespace Tests\Traits;

use App\Http\Middleware\JWTAuthenticateAccess;

trait WithOutMiddleware
{
    protected abstract function model();
    protected abstract function controller();

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware([
            JWTAuthenticateAccess::class
        ]);
        $this->model = $this->model();
        $this->controller = $this->controller();
    }
}
