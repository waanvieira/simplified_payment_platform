<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Response;

class InvalidArgumentException extends Exception
{
    public $message;

    public function __construct($message = '')
    {
        $this->message = $message;
    }
    /**
     * Report the exception.
     *
     * @return void
     */
    public function report()
    {
        //
    }

    /**
     * Render the exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function render($request)
    {
        return response()->json(['message' => $this->message ?? 'Bad Request'], Response::HTTP_BAD_REQUEST);
    }
}
