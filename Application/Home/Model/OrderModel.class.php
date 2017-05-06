<?php
namespace Home\Model;
use Think\Model;

class OrderModel extends Model {
    protected $patchValidate = true;
    protected $_validate =[
        ['user_id','require','用户未登录'],
        ['order_sn','','订单号重复',0,'unique',1],
        ['recept_id','require','地址信息不正确'],
        ['order_statu_id','require','订单状态不正确'],

    ];
    protected $_auto = [
        ['create_at','time',self::MODEL_INSERT,'function'],
        ['update_at','time',self::MODEL_BOTH,'function'],
    ];
}