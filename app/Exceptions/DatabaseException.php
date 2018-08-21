<?php

namespace App\Exceptions;

use Exception;

class DatabaseException extends Exception
{
    public function __construct() {
        $message = func_get_args();
        parent::__construct(json_encode($message));
    }
}
