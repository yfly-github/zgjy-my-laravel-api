<?php

namespace App\Http\Middleware;

use App\Exceptions\ControllerException;
use App\Models\PermissionModel;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

/**
 * 验证请求权限
 * Class PermissionMiddleware
 * @package App\Http\Middleware
 */
class PermissionMiddleware extends Authenticate
{
    protected static $except_router = [
        'LoginController.login',
        'LoginController.register',
        'LoginController.refresh_token',
    ];


    protected function authenticate(array $guards)
    {
        try{

            #获取当前请求控制器及方法相关信息
            $action = \Request::route()->getAction();
            $action = substr($action['controller'],strripos($action['controller'],'\\')+1);

            $url = str_replace('@','.',$action);

            if (in_array($url,self::$except_router)){
                return;
            }

            #获取当前请求的路径
            if ($this->auth->guard('api')->check()) {

                #获取api登录用户相关信息
                $user = $this->auth->guard('api')->user();

                #获取权限信息
                $permissions = array();
                foreach ($user->roles as $role) {
                    $permission = PermissionModel::getPermissionByRoleLists($role->id);
                    $permissions = array_merge($permissions,$permission);
                }

                #获取权限列表详情
                $slug_arr = array_column($permissions,'slug');

                if (!in_array($url,$slug_arr)){
                    throw new \Exception('用户没有权限或者权限被限制');
                }

                #判断是否在黑名单
                if ($user->is_black){
                    throw new \Exception('用户权限被限制，请联系管理员');
                }
                return $this->auth->shouldUse('api');
            }
        }catch (\Exception $exception){
            throw new ControllerException(Response::HTTP_UNAUTHORIZED,'authenticate：'.$exception->getMessage());
        }
        throw new UnauthorizedHttpException('', 'Unauthenticated');
    }
}
