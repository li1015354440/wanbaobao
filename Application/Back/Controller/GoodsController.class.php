<?php
namespace Back\Controller;
use Think\Controller;
use Think\Image;
use Think\Page;
use Think\Upload;

class GoodsController extends Controller{
    /*
     *列表展示
     */
    public function listAction(){
        $model = M('Goods');
        $condition = [];
        $sort['filed'] = I('get.filed','sort_number');
        $sort['type'] = I('get.type','asc');
        $order = "`{$sort['filed']}` {$sort['type']}";
        $this->assign('sort',$sort);
        //筛选条件
        $title = $filter['title'] = I('get.title','');
        $site = $filter['site'] = I('get.site','');
        if($title != ''){
            $condition['title'] = ['like','%'.$title.'%'];
        }
        if($site != ''){
            $condition['site'] = ['like','%'.$site.'%'];
        }
        $this->assign('filter',$filter);
        //分页条件
        $limit = 10;
        $total = $model->where($condition)->count();
        $page = new Page($total,$limit);
        $rows = $model->where($condition)->order($order)->limit($page->firstRow.','.$limit)->select();
        $this->assign('rows',$rows);
        //获取分页导航,可以通过page类的setConfig来配置相关信息
        $page->setConfig('prev','<');//上一页
        $page->setConfig('next','>');//下一页
        $page->setConfig('first','|<');//首页
        $page->setConfig('last','>|');//尾页
        $page->setConfig('header', '显示开始 %PAGE_START% 到 %PAGE_END% 条记录(总 %TOTAL_PAGE% 页)');
        $page->setConfig('theme', '<div class="col-sm-6 text-left"><ul class="pagination">%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%</ul></div> <div class="col-sm-6 text-right">%HEADER%</div>');
        $page_bar = $page->show();
        $this->assign('page_bar',$page_bar);
        $this->display();
    }

    /*
     * 商品添加修改
     */
    public function setAction(){
        if(IS_POST){
            $goods_id = I('post.goods_id','');
            $model = D('Goods');
            //模型自动验证及填充
            if($model->create()){
                //判断goods_id 是否等于空，如果为空表示是添加的动作，否则是修改
                if($goods_id === ''){
                    $goods_id = $model->add();
                }else{
                    $model->save();
                }
                //商品相册的维护
                $gallery_model = D('Gallery');
                foreach(I('post.galleries', []) as $row) {
                    $row['goods_id'] = $goods_id;
                    // create时, 传递处理好的数据,否者会认为是post数据
                    if ($gallery_model->create($row)) {
                        // 存在主键, 则更新
                        if (isset($row['gallery_id'])) {
                            $gallery_model->save();
                        } else {
                            // 否则则添加
                            $gallery_model->add();
                        }
                    }
                }
                redirect('list');
            }else{
                session('message',$model->getError());
                session('data',I('post.'));
                //失败两种情况添加失败的跳转还是编辑失败的跳转
                $param = [];
                if($goods_id !== ''){
                    $param['goods_id'] = $goods_id;
                    redirect('set');
                }
                var_dump($model->getError());
                echo 1;die;
                redirect('set');
            }
        }else{
            $message = session('message');
            session('message',null);
            $this->assign('message',$message);
            $data = session('data');
            session('data',null);
            if(!$data){
                $goods_id = I('get.goods_id','');
                $data = M('Goods')->join()->find($goods_id);
                $gallery_list = M('Gallery')->where(['goods_id'=>$goods_id])->select();
            }
            //分配商品基本数据
            $this->assign('data',$data ? $data : []);
            //分配相册信息
            $this->assign('gallery_list',$gallery_list);
            //分配商品类型
            $this->assign('goods_type',M('Type')->order('sort_number')->select());
            //分配商品系列
            $this->assign('goods_series',M('Series')->order('sort_number')->select());
            $this->display();
        }
    }
    //批量操作
    public function multiAction(){
        $operate = 'delete';
        $selected = I('post.selected',[]);
        //dump($selected);die;
        if(empty($selected)){
            $operate = '';
        }
        switch ($operate){
            case 'delete':
                $thumb = M('Goods')->field('thumb,des_thumb')->where(['goods_id'=>['in', $selected]])->select();
                foreach ($thumb as $value){
                    $image = './Upload/Goods/'.str_replace('thumb_','',$value['thumb']);
                    $description = './Upload/Description/'.str_replace('thumb_','',$value['des_thumb']);
                    $thumb_path = './Public/Thumb/'.$value['thumb'];
                    $des_thumb = './Public/Thumb/'.$value['des_thumb'];
                   // var_dump($image,$description,$thumb_path,$des_thumb);die;
                    @unlink($image);
                    @unlink($description);
                    @unlink($thumb_path);
                    @unlink($des_thumb);
                }
                M('Goods')->where(['goods_id'=>['in', $selected]])->delete();
                $thumb = M('Gallery')->field('thumb_path')->where(['goods_id'=>['in', $selected]])->select();
                //var_dump($thumb);die;
                foreach ($thumb as $value){
                    $gallery_thumb = './Public/Thumb/'.$value['thumb_path'];
                    $gallery_image = './Upload/Gallery/'.str_replace('thumb_','',$value['thumb_path']);
                    @unlink($gallery_image);
                    @unlink($gallery_thumb);
                }
                M('Gallery')->where(['goods_id'=>['in',$selected]])->delete();
                break;
        }
        redirect('list');
    }

    //ajax操作
    public function checkAction(){
        $field = I('request.field');
        switch ($field){
            case 'sku_id':
                echo D('goods')->checkSkuId(I('request.sku_id')) ? 'true' : 'false';
                break;
        }
    }

    //上传图像
    public function uploadAction(){

        $type = I('request.type','goods');
        $upload = new Upload();
        switch ($type){
            case 'goods':
                $upload->savePath = 'Goods/';
                $name = 'image';
                break;
            case 'gallery':
                $upload->savePath = 'Gallery/';
                $name = 'galleries';
                break;
            case 'description':
                $upload->savePath = 'Description/';
                $name = 'description';
                break;
        }
        $upload->rootPath = './Upload/';

        $upload->exts = ['gif','jpg','jpeg','png'];
        $upload->maxSize = 5*1024*1024;
        $info = $upload->uploadOne($_FILES[$name]);

        //如果上传成功制作缩略图
        if($info){
            $image = new Image();
            $image->open($upload->rootPath.$info['savepath'].$info['savename']);
            //缩略图补白处理
            switch ($type){
                case 'goods':
                    $image->thumb(146,146,Image::IMAGE_THUMB_FILLED);
                    @unlink($upload->rootPath.I('request.image'));
                    break;
                case 'gallery':
                    $image->thumb(702,702,Image::IMAGE_THUMB_FILLED);
                    break;
                case 'description':
                    $image->thumb(100,100,Image::IMAGE_THUMB_FILLED);
                    @unlink($upload->rootPath.I('request.description'));
            }
            //保存到路径
            $thumb_root = './Public/Thumb/';
            $save_path = date('Y-m-d').'/';
            if(!is_dir($thumb_root.$save_path)){
                mkdir($thumb_root.$save_path);
            }
            $image->save($thumb_root.$save_path.'thumb_'.$info['savename']);
            switch ($type){
                case 'goods':
                    @unlink($thumb_root.I('request.thumb'));
                    break;
                case 'description':
                    @unlink($thumb_root.I('request.des_thumb'));
            }
            $data['error'] = 0;
            $data['image'] = $info['savepath'].$info['savename'];
            $data['image_thumb'] = $save_path.'thumb_'.$info['savename'];
        }else{
            $data['error'] = 1;
            $data['errorInfo'] = $upload->getError();
        }

        $this->ajaxReturn($data);
    }


    //删除相册图像

    //删除图像

    public function removeAction(){
            // 接收请求数据
            $gallery_id = I('request.gallery_id', null);
            $image_thumb = I('request.image_thumb', null);
            // 删除已经存在的相册相关资源
            if (! is_null($gallery_id)) {
                // 删除记录和对应的文件
                $model = M('Gallery');
                $model->find($gallery_id);// 对应记录 $row = $model->find();
                // 删除文件
                @unlink('./Upload/' . $model->image);// 原图  $row['image']
                @unlink('./Public/Thumb/' . $model->thumb_path);
                // 删除记录
                $model->delete(); // 此时model与记录 已经绑定了
            }
            // 直接删除图像, 此时没有gallery_id
            if (! is_null($image_thumb)) {
                // 三种缩略图删除
                @unlink('./Public/Thumb/' . $image_thumb);
                @unlink('./Upload/Gallery/' . str_replace('thumb-', '', $image_thumb));
            }
            // 做响应
            $this->ajaxReturn(['error'=>0]);
    }

    public function ajaxAction()
    {
        // 验证title数据是否重复
        $title = I('request.name');
        // 标题作为条件检索
        $cond['name'] = $title; // title = '$title'
        // 判断是否存在brand_id
        $goods_id = I('request.goods_id', '');
        if ($goods_id  !== '') { // !==
            $cond['goods_id'] = ['neq', $goods_id]; // brand_id != $brand_id
        }
        // title = '$title' && brand_id != $brand_id
        $row = M('Goods')->where($cond)->find();
        // 如果检索到数据, 则响应false, 否则响应true字符串
        echo $row ? 'false' : 'true';
    }
}

