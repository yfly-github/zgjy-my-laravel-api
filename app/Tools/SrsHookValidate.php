<?php
/**
 * Created by PhpStorm.
 * User: li
 * Date: 18/5/16
 * Time: 12:14
 */

namespace App\Tools;
use App\Tools\ToolFunc;

class SrsHookValidate {
    use ToolFunc;
    /***
     * 返回验证message
     * @return array
     */
    public static function  getMessage(){
        return [
            'required' => '字段 :attribute 是必须的.',
            'unique'   => '该数据已经存在.',
            'between'  => '字段 :attribute 必须在 :min - :max.之间',
            'ip'       => '不是合法的IP',
            'integer'  => '字段必须是整形'
        ];
    }
    /**
     * 公有验证方法
     * @param $all request请求参数
     * @param $rules 验证规则
     * @param bool 验证规则回调hook
     * @param bool 验证错误提示
     * @return bool
     */
    public static function validate($request,$rules,$callback=false,$message=false) {
        if(!$message){
            $message=self::getMessage();
        }
        # 过滤多余参数
        $params = $request->all();
        $request->replace(self::filterField($params,array_keys($rules)));

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), $rules,$message);
        if ($callback) {
            $validator->after(function() use ($callback,$validator,$request){
                if (is_callable($callback)) {
                    return $callback($request,$validator);
                }
            });
        }
        if ($validator->fails()) {
            return $validator->errors()->toArray();
        }
        return false;
    }
    /**
     * 过滤多余请求请求
     * @param Array $requestParams 请求参数
     * @return Array $requestParams 保留自定义后的字段
     */
    public static function filterField($requestParams,$keys) {
        return array_filter($requestParams,function($k) use ($keys){
            return in_array($k,$keys);
        },ARRAY_FILTER_USE_KEY);
    }
    /**
     * srsHookCallback 验证不通过抛出异常
     */
    public static function srsHookCallback(...$params) {
        $result = self::validate(...$params);
        # 参数不合法
        if ($result) {
            throw new \App\Exceptions\FormException(array_shift($result)[0]);
        }
    }
}