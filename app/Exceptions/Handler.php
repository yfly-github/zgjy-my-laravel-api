<?php

namespace App\Exceptions;

use Exception;
use App\Tools\ToolFunc;
use http\Exception\BadMethodCallException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class Handler extends ExceptionHandler
{
    use ToolFunc;
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        if (!self::handlerException($exception)) {
            parent::report($exception);
        }
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        if (self::handlerException($exception)) {
            # 获取常量类里面的异常常量
            $class =  '\\'.get_class($exception);
            $message = json_decode($exception->getMessage(), true);
            throw new $class(...$message);
        } elseif ($exception instanceof AuthenticationException) {
            return error($exception->getMessage(), Response::HTTP_UNAUTHORIZED);
        } elseif ($exception instanceof  NotFoundHttpException) {
            return error('resources not found', Response::HTTP_NOT_FOUND);
        }else if($exception instanceof UnauthorizedHttpException){
            return error($exception->getMessage(),Response::HTTP_UNAUTHORIZED);
        } else if($exception instanceof MethodNotAllowedHttpException){
            return error('该请求不被允许');
        }else if($exception instanceof HttpException){

            #使用throttle中间件，检测api访问频率
            if ($exception->getMessage()=='Too Many Attempts.'){
                return error('请求太频繁,请稍后再试！',429);
            }
        }
        return parent::render($request, $exception);
    }
}
