<?php

use Symfony\Component\HttpFoundation\Response;

/**
 * 获取Debug信息
 * @return string
 */
function getDebugTrace()
{
    $array = debug_backtrace();
    unset($array[0]);
    $html = '';

    foreach ($array as $row) {
        if (isset($row['file'])) {
            $param = json_encode($row['args'], JSON_UNESCAPED_UNICODE);
            $html .= $row['file'] . '(' . $row['line'] . ')->' . $row['function'] . "({$param})" . PHP_EOL;
        }
    }

    return $html;
}

function success($data = [], $code = Response::HTTP_OK, $msg = '操作成功')
{
    return response()->json(['code' => $code, 'result' => $data, 'msg' => $msg]);
}

function error(string $msg, $code = Response::HTTP_INTERNAL_SERVER_ERROR)
{
    return response()->json(['code' => $code, 'msg' => $msg]);
}

;

function handler_drive($callback)
{
    try {
        if (!is_callable($callback)) {
            throw new \Exception('callback is not callable');
        }
        $result = $callback();
    } catch (\Exception $e) {
        $error_message = $e->getMessage();
        $result = \App\Tools\ToolFunc::handlerException($e);
        if ($result) {
            $message = json_decode($error_message, true);
            $exception = '\\App\\Exceptions\\' . $result[0];
            throw new $exception(...$message);
        }

        throw new \App\Exceptions\ControllerException($error_message);
    }
    return $result;
}


/**
 * 返回数组中指定的key=>val
 * @param array $data
 * @param array $fields
 * @return array
 */
function getArrayField(array $data, array $fields)
{
    $result = [];
    foreach ($fields as $key) {
        if (isset($data[$key])) {
            $result[$key] = $data[$key];
        }
    }


    return $result;
}


/**
 * 记录行为日志，并执行该行为的规则
 * @param string $model 触发行为的模型名
 * @param int $record_id 触发行为的记录id
 * @param int $user_id 执行行为的用户id
 * @return boolean
 * @author huajie <banhuajie@163.com>
 */
function action_log(string $path, array $data)
{
    //参数检查
    if (empty($path) || empty($data)) {
        return '参数不能为空';
    }

    //插入行为日志
    $data['path'] = $path;
    $data['data'] = json_encode($data);

}

/**
 * 通过curl获取数据
 * @param $url
 * @param bool $isHearder
 * @param bool $post
 * @return mixed
 */
function http_request($url, $isHearder = null, $post = 'GET', $data = null, $timeout = 1)
{
    //初始化curl
    $ch = curl_init($url);

    //设置URL地址
    curl_setopt($ch, CURLOPT_URL, $url);

    //设置header信息
    if (!empty($isHearder)) {
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $isHearder);
    }
    //如果是post，则把data的数据传递过去
    if (($post == 'POST') && $data) {
        #假如data为数组将其转换为json格式
        if (is_array($data)) {
            $data = json_encode($data);
        }
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    }

    //如果是删除方法，则是以delete请求
    if ($post == 'DELETE') {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
    }

    //设置超时时间，毫秒
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, $timeout*1000);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    //执行CURL时间
    $result = curl_exec($ch);

    //如果有异常，记录到日志当中
    $curl_errno = curl_errno($ch);
    if ($curl_errno > 0) {
    }

    //关闭URL，返回数据
    curl_close($ch);
    return $result;
}

