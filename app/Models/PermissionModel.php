<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class PermissionModel extends Model
{

    protected $table = 'permissions';


    /**
     * 建立角色与权限之间的关系
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany('Models\RoleModel', 'role_permission', 'permission_id', 'role_id');
    }

    /**
     * 根据用户角色获取权限不分页
     * @param Request $request
     * @return PermissionModel[]|\Illuminate\Database\Eloquent\Collection
     * @throws \Exception
     */
    public static function getPermissionByRoleLists($id){

        try{
            $role = RoleModel::find($id);

            if (!$role){
                throw new \Exception('角色不存在');
            }
            $permissions = $role->permissions;

        }catch (\Exception $exception){
            throw new \Exception('permissionAllLists：'.$exception->getMessage());
        }
        return $permissions->toArray();
    }














}
