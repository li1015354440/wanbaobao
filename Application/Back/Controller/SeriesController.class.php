<?php
namespace Back\Controller;
use Home\Cart\Cart;
use Think\Controller;
use Think\Page;
use Think\Upload;
use Think\Image;
class SeriesController extends Controller{

    /**
     *列表展示
     */
    public function listAction(){
        $this->checkLogin();
        $model = M('Series');
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
        $this->checkLogin();
        if(IS_POST){
            $series_id = I('post.series_id','');
            $model = D('Series');
            if($model->create()){
                //判断series_id 是否等于空，如果为空表示是添加的动作，否则是修改
                if($series_id === ''){
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
                if($series_id !== ''){
                    $param['series_id'] = $series_id;
                    redirect('set');
                }
                redirect('set');
            }
        }else{
            $message = session('message');
            session('message',null);
            $this->assign('message',$message);
            $data = session('data');
            session('data',null);
            if(!$data){
                $series_id = I('get.series_id');
                $data = M('Series')->find($series_id);
            }
            $this->assign('data',$data ? $data : []);
            $this->display();
        }
    }
    public function multiAction(){
        $this->checkLogin();
        $operate = 'delete';
        $selected = I('post.selected',[]);
        //dump($selected);die;
        if(empty($selected)){
            $operate = '';
        }
        switch ($operate){
            case 'delete':
                $res = M('Series')->field('series_image,series_thumb')->where(['series_id'=>['in', $selected]])->select();
                foreach ($res as $v){
                    unlink('./Upload/'.$v['series_image']);
                    unlink('./Public/Thumb/'.$v['series_thumb']);
                }
                M('Series')->where(['series_id'=>['in', $selected]])->delete();;
                break;
        }
        redirect('list');
    }
    //文件上传
    public function uploadAction(){
        //上传图像
        $upload = new Upload();
        $upload->savePath = 'Series/';
        $upload->rootPath = './Upload/';
//        var_dump(I('request.')) ;
        @unlink( $upload->rootPath.I('request.series_image'));
        $upload->exts = ['gif','jpg','jpeg','png'];
        $upload->maxSize = 5*1024*1024;
        $info = $upload->upload();

        //如果上传成功制作缩略图
        if($info) {
            $image = new Image();
            $path= $upload->rootPath.$info['series']['savepath'].$info['series']['savename'];
            $image->open($path);
            $image->thumb(702,190,Image::IMAGE_THUMB_FILLED);
            //保存到路径
            $thumb_root = './Public/Thumb/';
            @unlink($thumb_root.I('request.series_thumb'));
            $save_path = date('Y-m-d').'/';
            if(!is_dir($thumb_root.$save_path)){
                mkdir($thumb_root.$save_path);
            }
            $image->save($thumb_root.$save_path.'thumb_'.$info['series']['savename']);
            $data['error'] = 0;
            $data['series_image'] = $info['series']['savepath'].$info['series']['savename'];
            $data['series_thumb'] = $save_path.'thumb_'.$info['series']['savename'];
        }else{
            $data['error'] = 1;
            $data['errorInfo'] = $upload->getError();
        }
        $this->ajaxReturn($data);
    }
    //ajax 验证是否系列名重复
    public function ajaxAction()
    {
        // 验证title数据是否重复
        $title = I('request.title');
        // 标题作为条件检索
        $cond['title'] = $title; // title = '$title'
        // 判断是否存在series_id
        $series_id = I('request.series_id','');
        if ($series_id  !== '') { // !==
            $cond['series_id'] = ['neq', $series_id]; // brand_id != $brand_id
        }
        // title = '$title' && brand_id != $brand_id
        $row = M('Series')->where($cond)->find();
        // 如果检索到数据, 则响应false, 否则响应true字符串
        echo $row ? 'false' : 'true';
    }
}

