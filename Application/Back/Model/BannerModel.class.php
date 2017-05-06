<?php
namespace Back\Model;
use Think\Model;
/*
 *品牌模型
 */
class BannerModel extends Model{
	//自动验证
	//[验证字段,验证规则,错误提示,[验证条件,附加规则,验证时间]),
	protected $patchValidate = true;
    protected $_validate = [
        ['sort_number','require','排序字段不能为空'],
        ['sort_number','number','排序字段必须填写数字']
    ];

    //自动完成
    //[完成字段,完成规则,[完成条件,附加规则]]
    protected $_auto = [
        ['create_at','time',self::MODEL_INSERT,'function'],
        ['update_at','time',self::MODEL_BOTH,'function'],
    ];
}