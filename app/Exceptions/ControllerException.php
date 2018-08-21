<?php

namespace App\Exceptions;

use Exception;

class ControllerException extends Exception
{

    public function __construct() {
        $message = func_get_args();
        # 记录表单错误日志
        parent::__construct(json_encode($message));
    }
}
