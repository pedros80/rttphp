<?php

namespace Pedros80\RTTphp\Exceptions;

use Exception;

final class InvalidServiceIdFormat extends Exception
{
    private function __construct(string $message)
    {
        parent::__construct($message, 400);
    }

    public static function fromString(string $string): InvalidServiceIdFormat
    {
        return new InvalidServiceIdFormat("'{$string}' is not a valid service id - [A-Z][0-9]{5}");
    }
}
