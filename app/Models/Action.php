<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Action extends Model
{
    public static $actions = [
        'teacher_list',
        'teacher_create',
        'teacher_update',
        'teacher_del'
    ];

    /**
     * 获取行为列表
     * @param Request $req
     * @return array
     */
    public static function actionList(Request $req){
        return self::$actions;
    }




}
