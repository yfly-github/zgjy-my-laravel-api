<?php

namespace App\Exceptions;

use Exception;


class FormException extends Exception
{

    public function __construct()
    {
        $message = func_get_args();
        parent::__construct(json_encode($message));
    }


}
