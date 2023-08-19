<?php

namespace Pedros80\RTTphp\Exceptions;

use Exception;

final class InvalidTimeFormat extends Exception
{
    private function __construct(string $message)
    {
        parent::__construct($message, 400);
    }

    public static function fromString(string $string): InvalidTimeFormat
    {
        return new InvalidTimeFormat("'{$string}' is not a valid time - hhmm");
    }
}
