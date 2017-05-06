<?php
namespace Back\Model;
use Think\Model;
/*
 *品牌模型
 */
class GalleryModel extends Model{
	//自动验证
	//[验证字段,验证规则,错误提示,[验证条件,附加规则,验证时间]),
	protected $patchValidate = true;
	protected $_validate = [
        ['goods_id','require','商品ID缺失'],
        ['thumb_path','require','缩略图缺失'],
        ['image','require','相册图片添加失败'],
	];

	//自动完成
	//[完成字段,完成规则,[完成条件,附加规则]]
	protected $_auto = [
        ['create_at','time',SELF::MODEL_INSERT,'function'],
        ['update_at','time',SELF::MODEL_BOTH,'function'],
	];
}