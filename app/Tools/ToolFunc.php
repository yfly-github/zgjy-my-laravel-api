<?php

namespace App\Tools;
use \Symfony\Component\HttpFoundation\Response;
/**
 * 常用函数
 */
trait ToolFunc
{
    /**
     * 检测当前异常是否属于自定义异常
     * @param Exception $exception 抛出的异常
     * @return Array $arr 当前异常的类名和自定义异常的数组
     */
    public static function handlerException($exception) {
        $class_name = self::getClassName($exception);
        $files = self::getExceptionFileName();

        $bool = in_array($class_name,$files);
        if ($bool) {
            return [$class_name,$files];
        } else {
            return false;
        }
    }
    /**
     * getClassName 获取当前触发异常的异常类名
     * @param Exception $exception 异常对象
     * @return String $str 异常类名字
     */
    public static function getClassName($exception) {
        return substr(strrchr( get_class($exception), '\\'), 1);
    }
    /**
     * getExceptionFileName获取app/Exceptions下面所有的异常类文件并按照文件名排序
     * @return Array $files 文件名数组
     */
    public static function getExceptionFileName() {
        $files = array_map(function($v){ return basename($v,'.php');},glob(app_path('Exceptions').'/*Exception.php'));
        return $files;
    }

    /**
     * 处理put传过来的值
     * @param str
     * @return array data
     */
    public static function getPutValueHandle($request) {
        $str = file_get_contents('php://input');
        $r = explode(PHP_EOL,$str);
        $request_temp = [];

        foreach ($r as $k => $v) {
            $v = trim($v);
            if ( $v == '' || ( strpos( $v, '---') )  === 0 ) {
                unset($r[$k]);
            }
            if (preg_match('/"(.*)"/', $v,$name)) {
                $request_temp[$name[1]] = str_replace(["\r\n","\r","\n"],'',$r[$k+2]);
            }
        }
        $request->replace($request_temp);
    }


    /**
     * 节点排序
     */
    public static function sortPermission($temp,$temp1) {
        foreach ($temp as $key => $value) {
            foreach ($temp1 as $k => $v) {
                if ($value['id'] == $k) {
                    array_unshift($v, $value);
                    unset($temp[$key]);
                    array_splice($temp, $key,0,$v);
                    unset($temp1[$k]);
                    return [$temp,$temp1];
                }
            }
        }
    }


    /**
     * 添加分页浏览ID
     * @param obj $obj  数据集
     * @param int $page 当前页码
     * @return $obj
     */
    public static function addPageNum($obj,$page=1){
        foreach($obj as $k => $v){
            if($page && $page > 1){
                $v->num = 10 * ($page - 1) + $k + 1;
            }else{
                $v->num = $k+1;
            }

        }
        return $obj;
    }


    /**
     * 转换一个int为byte大小
     * @param $val 需要转换的字符串
     * @return **MB
     */
    public static function calc($size,$digits=2){
        $unit= array('','K','M','G','T','P');
        $base= 1024;
        $i = floor(log($size,$base));
        $n = count($unit);
        if($i >= $n){
            $i=$n-1;
        }
        return round($size/pow($base,$i),$digits).' '.$unit[$i] . 'B';
    }
}
