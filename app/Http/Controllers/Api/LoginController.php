<?php

namespace App\Http\Controllers\Api;

use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Validator;
use App\User;

/**
 * 用户登录注册控制器
 * Class LoginController
 * @package App\Http\Controllers\Api
 */
class LoginController extends Controller
{
    public $successStatus = 200;

    use ThrottlesLogins;

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 401);
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);

//        $success['token'] =  $user->createToken('MyApp')->accessToken;
//        $success['name'] =  $user->name;

        return success($user);
        //return response()->json(['success'=>$success], $this->successStatus);
    }

    //api登录
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            //'email' => 'required|email',
            'password' => 'required',
            'name'  =>  'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 401);
        }
        return $this->sendLoginResponse($request);
    }

    protected function sendLoginResponse(Request $request)
    {
        $this->clearLoginAttempts($request);

        return $this->authenticated($request);
    }

    protected function authenticated(Request $request)
    {
        return $this->authenticateClient($request);
    }

    protected function authenticateClient(Request $request)
    {
        try {
            $data = $request->all();

            if ($request->refresh_token) {
                $request->request->add([
                    'grant_type' => 'refresh_token',//获取新的access_token，grant_type使用refresh_token
                    'client_id' =>  '2',
                    'client_secret' => 'g0ba6h1InjI85P3kXbCdfLl2kAqJJu1V2YvruICM',
                    'refresh_token' => $data['refresh_token'],
                    'scope' => '*'
                ]);
            } else {
                //php artisan passport:client --password 使用该命令产生客户端
                $request->request->add([
                    'grant_type' =>'password',//获取access_token，grant_type使用password，再次提醒一下，要使用password产生客户端
                    'client_id' => '2',
                    'client_secret' => 'g0ba6h1InjI85P3kXbCdfLl2kAqJJu1V2YvruICM',
                    'username' => $data['name'],
                    'password' => '123456',
                    'scope' => '*',
                ]);
            }

            $proxy = Request::create(
                'oauth/token',
                'POST'
            );

            $response = \Route::dispatch($proxy);
        } catch (\Exception $e) {
            throw new \Exception('dsfs:'.$e->getMessage());
        }
        return $response;
    }

    /**
     * 根据refresh_token获取access_token
     * @param Request $req
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function refresh_token(Request $req){

        $validator = Validator::make($req->all(), [
            'name' => 'required',
            'password' => 'required',
            'refresh_token' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 401);
        }
        return $this->authenticateClient($req);
    }


    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username()
    {
        return 'staffid';
    }
}
