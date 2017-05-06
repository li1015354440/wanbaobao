<?php
namespace Back\Model;
use Think\Model;
/*
 *品牌模型
 */
class GoodsModel extends Model{
    //自动验证
    //[验证字段,验证规则,错误提示,[验证条件,附加规则,验证时间]),
    protected $patchValidate = true;
    protected $_validate = [
        ['name','require','商品名称不能为空'],
        ['type_id','require','商品类型不能为空'],
        ['series_id','require','系列必须选择'],
        ['is_online','require','是否上架为必选信息'],
        ['is_hot','require','是否热销为必选项'],
        ['stock','require','库存必须填写'],
        ['stock','number','库存必须位数字'],
        ['shop_price','require','售价不能为空'],
        ['shop_price','number','必须位数字'],
        ['image','require','上传主图失败'],
        ['thumb','require','主图压缩失败'],
        ['description','require','请选择商品描述图片'],
        ['des_thumb','require','商品描述图压缩失败'],
    ];

    //自动完成
    //[完成字段,完成规则,[完成条件,附加规则]]
    protected $_auto = [
        ['create_at','time',SELF::MODEL_INSERT,'function'],
        ['update_at','time',SELF::MODEL_BOTH,'function'],
    ];
}