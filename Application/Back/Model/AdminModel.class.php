<?php
namespace Back\Model;
use Think\Model;
/*
 *品牌模型
 */
class AdminModel extends Model{
    //自动验证
    //[验证字段,验证规则,错误提示,[验证条件,附加规则,验证时间]),
    protected $patchValidate = true;
    protected $_validate = [
        ['name','require','请输入用户名'],
        ['password', 'require', '请输入密码'],
        ['level','require','请输入权限等级'],
        ['sort_number', 'require', '请填写排序'],
        ['sort_number', 'number', '使用数字进行排序'],
    ];

    //自动完成
    //[完成字段,完成规则,[完成条件,附加规则]]
    protected $_auto = [
        ['password','makePassword',self::MODEL_BOTH,'callback'],
        ['create_at','time',self::MODEL_INSERT,'function'],
        ['update_at','time',self::MODEL_BOTH,'function'],
    ];
    protected function makePassword($value){
        return sha1($value.'admincode');
    }
}