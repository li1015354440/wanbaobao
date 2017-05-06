<?php
namespace Home\Model;
use Think\Model;

class ReceptModel extends Model {
    protected $patchValidate = true;
    protected $_validate =[
        ['province_id','require','省份为必选项'],
        ['city_id','require','城市为必选项'],
        ['country_id','require','县区为必选项'],
        ['tel','require','电话号码为必填选项'],
        ['detail','require','详细地址不能为空'],
        ['recept_name','require','收货人地址必须填写'],
    ];
    protected $_auto = [
        ['create_at','time',self::MODEL_INSERT,'function'],
        ['update_at','time',self::MODEL_BOTH,'function'],
    ];
}