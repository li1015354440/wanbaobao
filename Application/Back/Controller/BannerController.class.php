<?php
namespace Back\Controller;
use Think\Controller;
use Think\Page;
use Think\Upload;
use Think\Image;

class BannerController extends Controller{

    /**
     *列表展示
     */
    public function listAction(){
        $model = M('Banner');
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
        $limit = 5;
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


    public function setAction(){
        if(IS_POST){
            $banner_id = I('post.banner_id','');
            $model = D('Banner');
            if($model->create()){
                //判断banner_id 是否等于空，如果为空表示是添加的动作，否则是修改
                if($banner_id === ''){
                    $model->add();
                }else{
                    $model->save();
                }
                redirect('list');
            }else{
                session('message',$model->getError());
                session('data',I('post.'));
                //失败两种情况添加失败的跳转还是编辑失败的跳转
                $param = [];
                if($banner_id !== ''){
                    $param['banner_id'] = $banner_id;
                    redirect('set',$param);
                }
            }
        }else{
            $message = session('message');
            session('message',null);
            $this->assign('message',$message);
            $data = session('data');
            session('data',null);
            if(!$data){
                $banner_id = I('get.banner_id');
                $data = M('Banner')->find($banner_id);
            }

            $this->assign('data',$data ? $data : []);
            $this->display();
        }
    }
    public function multiAction(){
        $operate = 'delete';
        $selected = I('post.selected',[]);
        //dump($selected);die;
        if(empty($selected)){
            $operate = '';
        }
        switch ($operate){
            case 'delete':
                $res = M('Type')->field('image,banner_thumb')->where(['type_id'=>['in', $selected]])->select();
                foreach ($res as $v){
                    unlink('./Upload/'.$v['image']);
                    unlink('./Public/Thumb/'.$v['banner_thumb']);
                }
                M('Banner')->where(['banner_id'=>['in', $selected]])->delete();;
                break;
        }
        redirect('list');
    }
    public function uploadAction(){
        //上传图像
        $upload = new Upload();
        $upload->savePath = 'Banner/';
        $upload->rootPath = './Upload/';
//        var_dump(I('request.')) ;
        @unlink( $upload->rootPath.I('request.image'));
        $upload->exts = ['gif','jpg','jpeg','png'];
        $upload->maxSize = 5*1024*1024;
        $info = $upload->upload();

        //如果上传成功制作缩略图
        if($info) {
            $image = new Image();
            $path= $upload->rootPath.$info['banner']['savepath'].$info['banner']['savename'];
            $image->open($path);
            $image->thumb(375,210,Image::IMAGE_THUMB_FILLED);
            //保存到路径
            $thumb_root = './Public/Thumb/';
            @unlink($thumb_root.I('request.type_thumb'));
            $save_path = date('Y-m-d').'/';
            if(!is_dir($thumb_root.$save_path)){
                mkdir($thumb_root.$save_path);
            }
            $image->save($thumb_root.$save_path.'thumb_'.$info['banner']['savename']);
            $data['error'] = 0;
            $data['image'] = $info['banner']['savepath'].$info['banner']['savename'];
            $data['banner_thumb'] = $save_path.'thumb_'.$info['banner']['savename'];
        }else{
            $data['error'] = 1;
            $data['errorInfo'] = $upload->getError();
        }
        $this->ajaxReturn($data);
    }
}

