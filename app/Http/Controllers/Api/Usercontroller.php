<?php

namespace App\Http\Controllers\Api;

use App\Tools\SrsHookValidate;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\UserRepository as MyUserRepository;
use App\Repositories\ActionRepository as ActionRep;


class Usercontroller extends Controller
{
    protected $users;
    protected $actionRep;

    public function __construct(MyUserRepository $users, ActionRep $actionReps)
    {
        $this->users = $users;
        $this->actionRep = $actionReps;
    }

    /**
     * @param Request $req
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\FormException
     */
    public function index(Request $req)
    {
        SrsHookValidate::srsHookCallback($req, [
            'page' => '',
        ]);

        $userlist = $this->users->getUserList($req);

        return success($userlist);
    }

    /**
     * 获取用户详情
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $id)
    {
        $request->replace(array_merge($request->all(), array('id' => $id)));
        $user = $this->users->getUserDetail($request);
        $data['user'] = $user;

        $actions = $this->actionRep->actionList($request);
        $data['actions'] = $actions;


        return success($data);
    }


}
