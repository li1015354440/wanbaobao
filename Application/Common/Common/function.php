<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/4
 * Time: 10:23
 */

function  getorders($array ,$condi){
    $arr =[];
    foreach ($array as $value) {
        if($value['order_statu_id'] ==$condi){
            $arr[] = $value;
        }


    }
    return $arr;
}