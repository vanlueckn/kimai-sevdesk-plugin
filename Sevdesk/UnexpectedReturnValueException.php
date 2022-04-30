<?php

namespace KimaiPlugin\KimaiSevdesk\Sevdesk;

use Exception;
use JetBrains\PhpStorm\Pure;
use Throwable;

class UnexpectedReturnValueException extends Exception
{
    #[Pure] public function __construct($message, $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function __toString()
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}