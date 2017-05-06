<?php
namespace Back\Controller;
use Think\Controller;
use Think\Page;

class GalleryController extends Controller{
    /**
     *列表展示
     */
    public function listAction(){
        $model = M('Gallery');
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
            $gallery_id = I('post.gallery_id','');
            $model = D('Gallery');
            if($model->create()){
                //判断gallery_id 是否等于空，如果为空表示是添加的动作，否则是修改
                if($gallery_id === ''){
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
                if($gallery_id !== ''){
                    $param['gallery_id'] = $gallery_id;
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
                $gallery_id = I('get.gallery_id');
                $data = M('Gallery')->find($gallery_id);
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
                M('Gallery')->where(['gallery_id'=>['in', $selected]])->delete();;
                break;
        }
        redirect('list');
    }
}

