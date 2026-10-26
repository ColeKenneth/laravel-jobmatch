<?php

namespace App\Exceptions;

use RuntimeException;
use Throwable;

final class BusinessRuleException extends RuntimeException
{
    public function __construct(string $message, int $code = 422, Throwable|null $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
