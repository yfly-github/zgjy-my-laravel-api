<?php

namespace App\Models;

use App\Exceptions\DatabaseException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class RoleModel extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id','name','description','slug'];

    protected $table = 'roles';

    public $timestamps = false;
    /**
     * The users that belong to the role.
     */
    public function users()
    {
        return $this->belongsToMany('App\User');
    }

    /**
     * 建立角色与权限之间的关系
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissions()
    {
        return $this->belongsToMany(PermissionModel::class, 'role_permission', 'role_id', 'permission_id');
    }

    /**
     * 添加角色权限
     * @param Request $request
     * @return mixed
     * @throws DatabaseException
     */
    public static function addRole($data_role){
        try{
            $role = RoleModel::updateOrCreate($data_role);
        }catch (\Exception $exception){
            throw new DatabaseException('addRole角色创建失败：'.$exception->getMessage());
        }
        return $role;
    }





}
