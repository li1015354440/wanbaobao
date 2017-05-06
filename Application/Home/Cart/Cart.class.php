<?php
namespace Home\Cart;
/*
 * 购物车类
 */
class Cart{
    //存储所购商品列表
    private $goods_list = [];
    //购物车ID
    private $cart_id = '';
    //用户id
    private $user_id = '';
    //静态对象实例
    private static $instance;
    //构造方法
    private function __construct(){
        $this->user_id = $this->user_id = session('userinfo.user_id');
        $cart = M('Cart')->where(['user_id'=>$this->user_id])->find();
        if($cart > 0){
            $this->cart_id = $cart['cart_id'];
            $this->goods_list = unserialize($cart['goods_info']);
        }
    }
    //防止克隆
    private function __clone(){
    }
    //实例化单例
    public static function instance(){
        if(!self::$instance instanceof self){
            self::$instance = new self();
        }
        return self::$instance;
    }

    //添加商品到购物车
    public function addGoods($goods_id,$quantity=1){
        $goods_key = $goods_id;
//        var_dump($this->goods_list);die;
        if(isset($this->goods_list[$goods_key])){
            $this->goods_list[$goods_key]['buy_quantity'] += $quantity;
            return true;
        }else{
            $this->goods_list[$goods_key]=[
                'goods_id'=>$goods_id,
                'buy_quantity'=>$quantity,
                'add_time'=>time(),
                'checked'=>1,
            ];
            return true;
        }
    }

    //获取购物车内信息
    public function getGoodsList(){
        return $this->goods_list;
    }

    //设置选中商品
    public function setChecked($goods_key_list=[]){
        foreach ($this->goods_list as $key => $goods){
            $this->goods_list[$key]['checked'] = in_array($key,$goods_key_list) ? 1 : 0;
        }
    }

    //获取商品详细信息
    public function getFullGoodsList($type=''){
        $goods_list = $this->getGoodsList();
        $goods_model = M('Goods');
        $res = [];
        foreach ($goods_list as $goods_key=>$goods){
            if($type=='checked' && $goods['checked']==0) continue;
            $goods_info = $goods_model
                ->field('goods_id,name,thumb,shop_price,s.title as series_title')
                ->join('left join __SERIES__ s using(series_id)')
                ->find($goods['goods_id']);
            $goods_info['buy_quantity'] = $goods['buy_quantity'];
            $goods_info['checked'] = $goods['checked'];
           $res[] = $goods_info;
        }
        return $res;
    }

    //修改购买数量
    public function updateQuantity($goods_key,$quantity){
        if(isset($this->goods_list[$goods_key])){
            $this->goods_list[$goods_key]['buy_quantity'] = $quantity;
            return true;
        }
            return false;
    }
    //删除购物车中的商品
    public function deletGoods($goods_key){
        if(isset($this->goods_list[$goods_key])){
            unset($this->goods_list[$goods_key]);
            return true;
        }
        return false;
    }
    //析构方法数据持久化存储
    public function __destruct(){
        $cart_model = M('Cart');
        $goods_list_serialize = serialize($this->goods_list);
        $data= [
            'goods_info' => $goods_list_serialize,
            'user_id' => $this->user_id,
        ];
        if($this->cart_id != ''){
            $data['cart_id'] = $this->cart_id;
        }
        $cart_model->add($data,[],true);
    }
}
