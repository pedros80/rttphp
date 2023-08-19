<?php

namespace Pedros80\RTTphp\Exceptions;

use Exception;

final class InvalidDateFormat extends Exception
{
    private function __construct(string $message)
    {
        parent::__construct($message, 400);
    }

    public static function fromString(string $string): InvalidDateFormat
    {
        return new InvalidDateFormat("'{$string}' is not a valid date - yyyy/mm/dd");
    }
}
