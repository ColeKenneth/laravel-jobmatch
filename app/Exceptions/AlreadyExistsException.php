<?php

namespace App\Exceptions;

use RuntimeException;
use Throwable;

final class AlreadyExistsException extends RuntimeException
{
    public function __construct(string $message, int $code = 409, Throwable|null $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
