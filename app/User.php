<?php

namespace App;

use App\Exceptions\DatabaseException;
use HuangYi\Rbac\RbacTrait;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens,Notifiable,RbacTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];


    public function roles()
    {
        return $this->belongsToMany('App\Models\RoleModel','user_role','user_id','role_id');
    }


    //\Laravel\Passport\Birdge\UserRepository的getUserEntityByUserCredentials方法
    //里面有一句  method_exists($model,'findForPassport')
    public function findForPassport($name)
    {
        return $this->where('name',$name)->first();
    }

    /**
     * 根据用户id查看用户信息
     * @param Request $req
     * @return mixed
     * @throws DatabaseException
     */
    public static function findUesrByAttributes(Request $req){


        $query_where = array(
            'id'    => ['id','=',$req->id],
            'name'  => ['name','like','%'.$req->name.'%'],
        );
        try{
            $user = self::whereQuery( new User,$query_where );
            if (empty($user)){
                throw new \Exception('用户不存在');
            }
        }catch (\Exception $exception){
            throw new DatabaseException('findUesrById：'.$exception->getMessage());
        }
        return $user->first();
    }

    /**
     * 构造查询条件
     * @param $model
     * @param mixed ...$where_arr
     * @return mixed
     */
    protected static function whereQuery($model,...$where_arr){

        foreach ($where_arr as $where) {
            foreach ($where as $key => $val){
                if (is_array($val) && isset($val[0]) ) {
                    $model = $model->where($val[0], $val[1], $val[2]);
                } else {
                    $model = $model->where($key, $val);
                }
            }
        }
        return $model;
    }


    public static function findUesrById(Request $request){

        $query_where = array(
            'name'  => ['name','like','%'.$request->name.'%'],
        );

        return self::whereQuery(new self(),$query_where)->paginate(15);

    }




}
