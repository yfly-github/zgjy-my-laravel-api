<?php

namespace App\Http\Controllers;

use App\Models\RoleModel;
use App\Tools\SrsHookValidate;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * 添加角色
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\ControllerException
     */
    public function store(Request $request){

        $role = handler_drive(function() use( $request){

            SrsHookValidate::srsHookCallback($request,[
                'name'          => 'required',
                'slug'          => 'required',
                'description'   => ''
            ]);

            $role = RoleModel::addRole($request->all());

            return $role;
        });

        return success($role);
    }

}
