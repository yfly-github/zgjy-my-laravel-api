<?php
/**
 * ModelFactory 工厂处理类
 */
namespace App\Tool;


use App\Tools\Constant;

class ModelFactory
{
    # 关联模型
    public $orm_field;
    /**
     * 构造查询模型类
     * @param Model $model 查询model对象
     * @param Array $requestParams 请求参数
     * @return mixd
     */
    public function __construct($model, $requestParams = [])
    {
        $this->model = $model;
        $this->fillable = $model->fillable;
        $this->requestParams = $requestParams;
    }
    /**
     *  获取用户请求的查询参数
     * @param Array $query_where where参数条件
     * @return Array $array 查询参数数组
     */
    private function hasWHereQUery($query_where) : array
    {
        return array_intersect_key($query_where, $this->requestParams);
    }
    /**
     * 构造查询条件
     * @param Array $query_where 查询参赛
     * @return Object  $this
     */
    public function constructWhereParam($query_where) : ModelFactory
    {
        $query_where = $this->hasWHereQUery($query_where, $this->requestParams);

        if ($query_where) {
            foreach ($query_where as $v) {
                if (in_array($v[0], $this->fillable)) {
                    $this->model = $this->model->where(...$v);
                }
            }
        }
        return $this;
    }
    /**
     * 构造排序条件
     * @param Array $order_by_params 查询参赛
     * @return Object  $this
     */
    public function hasOrderParams($order_by_params) : ModelFactory
    {
        $this->model = array_reduce($order_by_params, function ($carry, $item) {
            return $this->model->orderBy(...$item);
        });
        return $this;
    }

     /**
     * 分页列表
     * @param Array $pagesize 页数
     * @return Object  $this
     */
    public function modelFactoryPaginate($pagesize = null, $all = false)
    {
        return $all ? $this->model->get() : $this->model->paginate($pagesize ?? Constant::PAGE_NUMBER);
    }

    /**
     * 关联模型表查询(仅支持外健)
     * @param String $orm_field 关联模型
     * @param Array $orm_where 关联模型查询条件
     * @return Object $this
     */
    public function relationQueryWhere($orm_field, $orm_where = [])
    {
        $this->orm_field = $orm_field;
        $this->model = $this->model::whereHas($orm_field, function ($query) use ($orm_where) {
            $orm_where = $this->hasWHereQUery($orm_where, $this->requestParams);
            if (!empty($orm_where)) {
                foreach ($orm_where as $v) {
                    $query->where(...$v);
                }
            }
        });
        return $this;
    }
    /**
     * 定义关联表获取关联表数据
     * @param String $filed 定义关联的方法名
     * @param Integer $pagesize 自定义分页数
     * @return LengthAwarePaginator $object 分页对象
     */
    public function constructRelation($pagesize = null, $all = false)
    {
        if (!$all) {
            $object = $this->modelFactoryPaginate($pagesize);
        } else {
            $object = $this->model->get();
        }
        $filed = $this->orm_field;
        foreach ($object as $k => $v) {
            $object[$k]->$filed = $v->$filed;
        }
        return $object;
    }
}
