<?php
namespace App\Repositories;
use Illuminate\Http\Request;
use App\User;

class UserRepository {

    /**
     * 根据用户属性获取用户信息
     * @param $req
     * @return mixed
     * @throws \App\Exceptions\DatabaseException
     */
    public function getUserList($req){
        $user = User::findUesrByAttributes($req);
        return $user;
    }

    /**
     * 获取用户详情
     * @param Request $req
     * @return mixed
     */
    public function getUserDetail(Request $req){
        $user = User::findUesrById($req);
        return $user;
    }


}
