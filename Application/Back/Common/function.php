<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/2/28
 * Time: 0:04
 */
function sortU($route,$param=[],$sort=[],$filed=''){
    $param['filed'] = $filed;
    #当前排序字段等于用户提交的排序字段且当前的排序为升序
    #则参数类型为降序，否则为升序
    $param['type'] = $sort['filed'] == $filed && $sort['type'] == 'asc' ? 'desc' : 'asc';
    return U($route,$param);
}
function sortClass($sort,$filed){
    #如果没有排序字段class为空否则class等于当前排序类型
    $class = $sort['filed'] != $filed ? '' : $sort['type'];
    return $class;
}

/*
 * 获取项目配置项
 */
function GC($key,$default=''){
    $model = M('setting');
    $value = $model->getFieldBykey($key,'value');
    return $value ? $value : $default;
}