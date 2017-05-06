<?php
namespace Home\Controller;
use Think\Controller;
class ShopController extends Controller{
    //首页展示
    public function indexAction(){
        $banner_list = M('Banner')
            ->field('banner_thumb')
            ->order('sort_number')
            ->select();
        $type_list = M('Type')
            ->field('type_id,title,type_thumb')
            ->order('sort_number')
            ->where(['is_display'=>1])
            ->select();
        $hot_goods_list = M('Goods')
            ->alias('g')
            ->field('goods_id,name,thumb,shop_price,t.title as type_title,s.title as series_title')
            ->join('left join __TYPE__ t  using(type_id)')
            ->join('left join __SERIES__ s using(series_id)')
            ->order('g.sort_number')
            ->where(['is_hot'=>1,'is_online'=>1])
            ->select();
//        var_dump($hot_goods_list);die;
        $this->assign('banner_list',$banner_list);
        $this->assign('type_list',$type_list);
        $this->assign('hot_goods_list',$hot_goods_list);
        //var_dump($type_list);die;
        $this->display();

    }
    //商品列表页面
    public function listAction(){
        //按照类型或者系列展示商品列表
        $type_id = I('get.type_id','');
        $series_id = I('get.series_id','');
        if($type_id != '') {
            if($type_id == 0){
                $condition = ['t.is_display' => 1, 'g.is_online' => 1];
            }else{
                $condition = ['t.type_id' => $type_id, 't.is_display' => 1, 'g.is_online' => 1];
            }
        }
        if($series_id != '') {
            $condition = ['s.series_id' => $series_id, 's.is_display' => 1, 'g.is_online' => 1];
        }
            $goods_list = M('Goods')
                ->alias('g')
                ->field('goods_id,name,thumb,shop_price,t.title as type_title,s.title as series_title')
                ->join('left join __TYPE__ t using(type_id)')
                ->join('left join __SERIES__ s using(series_id)')
                ->where($condition)
                ->order('g.sort_number')
                ->select();
        $this->assign('goods_list',$goods_list);
        $this->display();
    }
    //系列数据的展示
    public function SeriesAction(){
        //获取系列数据显示到页面
        $series_list = M('Series')
            ->field('series_id,title,series_thumb')
            ->where(['is_display'=>1])
            ->order('sort_number')
            ->select();
        $this->assign('series_list',$series_list);
        $this->display();
    }
    //商品详情页的展示
    public function detailAction(){
        $goods_id = I('get.goods_id');
        $goods_info = M('Goods')
            ->field('goods_id,name,shop_price,thumb,stock,description as description_img,s.title as series_title')
            ->join('left join __SERIES__ s using(series_id)')
            ->where(['goods_id'=>$goods_id])
            ->find();
        $goods_galleries = M('Gallery')
            ->field('thumb_path as gallery_img')
            ->where(['goods_id'=>$goods_id])
            ->order('sort_number')
            ->select();
        $this->assign('goods_galleries',$goods_galleries);
        $this->assign('goods_info',$goods_info);
        $this->display();
    }
}