<?php

namespace App\Http\Middleware;
use App\Exceptions\ControllerException;
use Closure;
use Illuminate\Contracts\Encryption\DecryptException;
use Symfony\Component\HttpFoundation\Response;

class ApiMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            $response = $next($request);
        } catch (\App\Exceptions\FormException $e) {
            list($code, $message) = [Response::HTTP_BAD_REQUEST, json_decode($e->getMessage(), true)];
        } catch (\App\Exceptions\DatabaseException $e) {
            list($code, $message) = [Response::HTTP_INTERNAL_SERVER_ERROR, json_decode($e->getMessage(), true)];
        } catch (\App\Exceptions\ControllerException $e) {
            list($code, $message) = [Response::HTTP_INTERNAL_SERVER_ERROR, json_decode($e->getMessage(), true)];
        }

        if (isset($code)) {
            $client_info = $_SERVER['REMOTE_ADDR'];
            if (count($message) == 1) {
                $message = $message[0];
            } else if (count($message) == 2) {
                list($code, $message) = $message;
            } else if (count($message) == 3) {
                list($client_info, $code, $message) = $message;
            }
            return error($message, $code);
        }

        return $response;
    }
}
