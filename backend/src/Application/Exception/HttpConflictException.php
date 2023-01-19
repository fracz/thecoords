<?php

namespace App\Application\Exception;

use Slim\Exception\HttpSpecializedException;

class HttpConflictException extends HttpSpecializedException
{
    /** @var int */
    protected $code = 409;
    /** @var string */
    protected $message = 'Conflict.';
    protected $title = '409 Conflict';
    protected $description = 'The HTTP 409 Conflict response status code indicates a request conflict with the current state of the target resource.';
}
