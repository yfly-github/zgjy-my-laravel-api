<?php
namespace App\Repositories;
use Illuminate\Http\Request;

use App\User;
class UserRepository {

    public function getUserList(Request $req){
        $user = User::findUesrById($req);
        return $user;
    }

    /**
     * 根据用户属性获取用户信息
     * @param $req
     * @return mixed
     * @throws \App\Exceptions\DatabaseException
     */
    public function getUserDetail($req){
        $user = User::findUesrByAttributes($req);
        return $user;
    }





}
