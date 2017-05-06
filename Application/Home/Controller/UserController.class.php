<?php
namespace Home\Controller;

use Home\DxLogin\SMS;
use Home\Wxlogin\Wxlogin;
use Think\Controller;
class UserController extends Controller {
    //用户登录
//  public function loginAction(){        //拉起授权
//      $wx = new  Wxlogin();
//      $wx-> Oauth();
//  }
//  public function loginCallBackAction(){
//      $code = $_GET['code'];
//      $wx = new WxLogin();
//      $res = $wx->getToken($code);
//      $access_token = $res['access_token'];
//      $openid = $res['openid'];
//      $user_info = $wx->getUserInfo($access_token,$openid);
//      //实例化模型并查询数据库中是否有数据
//      $user =M('User');
//      $data = ['open_id'=>$openid];
//      $re = $user->where( $data)->find();
//      if(!$re){
//          $data['create_at'] =time();
//          $data['update_at'] =time();
//          $userid  = $user->add($data);
//          $user_info['user_id'] =$userid;
//          session('userinfo',$user_info);
//      }else{
//          $user_info['user_id']= $re['user_id'] ;
//          session('userinfo' ,$user_info);
//      }
//      $this->redirect('Home/Shop/index');
//  }

//	用户登录模拟
//	public function loginAction(){
//      if(IS_POST){
//          $code = I('post.code');
//          if(isset($code) && $code){
//              $uid = '4514';
//              $userinfo = ['name'=>'张三'];
//              $res = M('User')->where(['open_id'=>'4514'])->find();
//              if(res){
//                  session('userinfo',array_merge($res,$userinfo));
//              }else{
//                  $user_id = M('User')->add(['open_id'=>$uid]);
//                  $userinfo['user_id'] = $user_id;
//                  session('userinfo',$userinfo);
//              }
//          }
//      }else{
//          $this->display();
//      }
//  }

//获取验证吗
    public function getCodeAction(){
        $tel = I('request.tel','');
        if($tel){
            $sms = new SMS();
           $res = $sms->GetCode($tel);
            if($res){
                $this->ajaxReturn(['error'=>0 ,'message'=>'获取成功']);
            }else{
                $this->ajaxReturn(['error'=>1 ,'message'=>'获取失败']);
            }
        }else{
            $this->ajaxReturn(['error'=>'3','message'=>'电话缺失']);
        }
    }
    //验证登陆
    public function checkCodeAction(){
        $tel =I('request.tel','');
        $code = I('request.code','');
        if($tel && $code){
            //实例化模型
            $sms = new SMS();
            $res = $sms->VerifyCode($tel,$code);
            if($res){
                $model = M('user');
                $re = $model ->where(['tel'=>$tel])->find();
                if($re) {
                    $data = [
                        'create_at' => time(),
                        'update_at' => time(),
                        'tel' => $tel
                    ];
                    $userid = $model->add($data);
                    $data['user_id'] = $userid;
                    session('userinfo', $data);
                }else{
                    session('userinfo',$re);
                }
                $this->ajaxReturn(['error'=>0 ,'message'=>'验证成功','data'=>session('userinfo')] );
            }else{
                $this->ajaxReturn(['error'=>1 ,'message'=>'验证失败','data'=>'验证码错误'] );
            }
        }else{
            $this->ajaxReturn(['error'=>2 ,'message'=>'参数缺失','data'=>'验证码或者电话缺失']);

        }
    }
    public function  loginAction(){
        $this->display();
    }

    //显示用户信息
    public function myAction(){
//      if($_SESSION['userinfo'])
//      {
//          $userInfo = session('userinfo');
            $this->assign('userInfo',$userInfo);
            $this->display();
//      }else{
//          $this->loginAction();
//      }
    }
    //添加地址
    public function addAddressAction(){
        if(IS_POST){
            if(!isset($_SESSION['userinfo'])){
                $data = ['error'=>1,'message'=>'用户未登录','url'=>U('User/login')];
                $this->ajaxReturn($data);
            }
            $recept_id = I('post.recept_id','');
            //判断参数是否完整
            if(isset($_POST['province_id']) && isset($_POST['city_id'])&& isset($_POST['country_id']) && isset($_POST['tel']) && isset($_POST['detail']) && isset($_POST['recept_name']) && isset($_POST['from'])){
                $_POST['user_id'] = session('userinfo.user_id');
                $_POST['is_default'] = 1;
                //保存数据
                $recept_model = D('Recept');
                if($recept_model->create()){
                    if($recept_id == ''){
                        $recept_id = $recept_model->add();
                    }else{
                        $recept_model->save();
                    }

                    //将最新加入的地址设为默认
                    $cond = [
                        'user_id' => session('userinfo.user_id'),
                        'is_default'    => '1',
                        'recept_id'    => ['neq', $recept_id],
                    ];
                    $recept_model->where($cond)->save(['is_default'=>0]);
                    switch ($_POST['from']){
                        case 'cart':
                            $this->ajaxReturn(['error'=>0,'message'=>'请求成功','url'=>U('Buy/pay',['from'=>'cart'])]);
                        break;
                        case 'my':
                            $this->ajaxReturn(['error'=>0,'message'=>'请求成功','url'=>U('User/addressList')]);
                        break;
                        case 'buy_now':
                            $goods_id = $_POST['goods_id'];
                            $quantity = $_POST['quantity'];
                            $this->ajaxReturn(['error'=>0,'message'=>'请求成功','url'=>U('Buy/pay',['goods_id'=>$goods_id,'quantity'=>$quantity,'from'=>'buy_now'])]);
                    }

                }else{
                    $err = $recept_model->getError();
                    $this->ajaxReturn(['error'=>'3','message'=>'参数错误','data'=>$err]);
                }
            }else{
                $this->ajaxReturn(['error'=>'2','message'=>'参数不完整']);
            }
        }else{
            $province = M('Region')
                ->field('region_id,title')
                ->where(['level'=>1])
                ->select();
            $this->assign('province_list',$province);
            $this->assign('from',I('get.from'));
            $this->assign('goods_id',I('get.goods_id',''));
            $this->assign('quantity',I('get.quantity',''));
            $this->display();
        }
    }
    //修改地址
    public function updateAddressAction(){

    }
    //获取地区的接口
    public function getRegionAction(){
        $region_id = I('request.region_id','');
        if($region_id == ''){
            $this->ajaxReturn(['error'=>1,'message'=>'请求参数不完整']);
        }
        $city = M('Region')
            ->field('region_id,title')
            ->where(['parent_id'=>$region_id])
            ->select();
        if(!$city){
            $this->ajaxReturn(['error'=>2,'message'=>'获取数据失败']);
        }
        $this->ajaxReturn(['error'=>0,'message'=>'请求成功','data'=>$city]);
    }
    //显示全部地址
    public function addressListAction(){

//        $this->display();
//        die;
        $userid = I('request.userid','5');

        if($userid !='')
        {
            $where =[
                'wb_recept.user_id' =>$userid
            ];
            //实例模型
            $recept = M('Recept');
            $adds = $recept
                ->field('wb_recept.recept_id ,wb_recept.tel,wb_recept.detail, wb_recept.is_default,wb_recept.recept_name ,province.title province,city.title city')
                ->join('wb_region as province on wb_recept.province_id=province.region_id ')
                ->join('wb_region as city on wb_recept.city_id=city.region_id ')
                ->where($where)->select();
//            var_dump($re);
//            die;
            $this->assign('adds',$adds);
            $this->display();
        }else{
            redirect('Home/User/login');
        }

    }
    //显示全部订单
    public function ordersAction(){
        $orders =[];
        $_SESSION['userinfo']['user_id'] = 5;
       if(isset($_SESSION['userinfo'])){
           //实例化order模型
           $order = M('Order');
           $userid = $_SESSION['userinfo']['user_id'];
           $order_list = $order
               ->field('wb_order.order_id,wb_order_statu.order_statu_id,wb_order.user_id,wb_order.order_sn,wb_order.recept_id,wb_order.goods_info,wb_order.create_at,wb_order_statu.title')
               ->join('wb_order_statu on wb_order_statu.order_statu_id = wb_order.order_statu_id')
               -> where(['user_id'=>$userid])->select();

           foreach ($order_list as$key=> $value){
               $totalNum =0;
               $totalPrice =0;
               //获取商品信息并反序列化
               $goods_info = unserialize($value['goods_info']);
                //拼凑出总的数量和总价
                foreach ( $goods_info  as  $goods){
                    $totalNum +=$goods['buy_quantity'];
                    $totalPrice += $goods['buy_quantity']*$goods['shop_price'];
                }
               $value['goods_info'] = $goods_info;
               $value['totalPrice'] = $totalPrice;
               $value['totalNum'] = $totalNum;
               $orders[$key] =$value;
           }
//           echo '<pre>';
//           var_dump($orders);
//           die;
           $this->assign('orders',$orders);

           $this->display();

       }else{
            redirect('Home/User/login');
        }

    }
    
    //显示未付款订单
    public function unpayAction(){

        $orders =[];
        $_SESSION['userinfo']['user_id'] = 5;
        $_REQUSET['order_statu_id'] = 1;
        if(isset($_SESSION['userinfo'])) {

            if(isset($_REQUSET['order_statu_id'])){
                //实例化order模型
                $order = M('Order');
                $userid = $_SESSION['userinfo']['user_id'];
                $where = [
                    'user_id' => $userid,
                    'wb_order.order_statu_id'=> $_REQUSET['order_statu_id']
                ];
                $order_list = $order
                    ->field('wb_order.order_id,wb_order_statu.order_statu_id,wb_order.user_id,wb_order.order_sn,wb_order.recept_id,wb_order.goods_info,wb_order.create_at,wb_order_statu.title')
                    ->join('wb_order_statu on wb_order_statu.order_statu_id = wb_order.order_statu_id')
                    ->where($where)->select();
                foreach ($order_list as $key => $value) {
                    $totalNum =0;
                    $totalPrice =0;
                    //获取商品信息并反序列化
                    $goods_info = unserialize($value['goods_info']);
                    //拼凑出总的数量和总价
                    foreach ($goods_info as $goods) {
                        $totalNum += $goods['buy_quantity'];
                        $totalPrice += $goods['buy_quantity'] * $goods['shop_price'];
                    }
                    $value['goods_info'] = $goods_info;
                    $value['totalPrice'] = $totalPrice;
                    $value['totalNum'] = $totalNum;
                    $orders[$key] = $value;
                }

                $this->assign('orders',$orders);
                $this->display();
            }else{
                redirect('Home/User/orders');
            }

        }else{
            redirect('Home/User/login');
        }
    }
    //显示未发货订单
    public function unsendAction(){
        $orders =[];
        $_SESSION['userinfo']['user_id'] = 5;
        $_REQUSET['order_statu_id'] = 2;
        if(isset($_SESSION['userinfo'])) {

            if(isset($_REQUSET['order_statu_id'])){
                //实例化order模型
                $order = M('Order');
                $userid = $_SESSION['userinfo']['user_id'];
                $where = [
                    'user_id' => $userid,
                    'wb_order.order_statu_id'=> $_REQUSET['order_statu_id']
                ];
                $order_list = $order
                    ->field('wb_order.order_id,wb_order_statu.order_statu_id,wb_order.user_id,wb_order.order_sn,wb_order.recept_id,wb_order.goods_info,wb_order.create_at,wb_order_statu.title')
                    ->join('wb_order_statu on wb_order_statu.order_statu_id = wb_order.order_statu_id')
                    ->where($where)->select();
                foreach ($order_list as $key => $value) {
                    $totalNum =0;
                    $totalPrice =0;
                    //获取商品信息并反序列化
                    $goods_info = unserialize($value['goods_info']);
                    //拼凑出总的数量和总价
                    foreach ($goods_info as $goods) {
                        $totalNum += $goods['buy_quantity'];
                        $totalPrice += $goods['buy_quantity'] * $goods['shop_price'];
                    }
                    $value['goods_info'] = $goods_info;
                    $value['totalPrice'] = $totalPrice;
                    $value['totalNum'] = $totalNum;
                    $orders[$key] = $value;
                }
                $this->assign('orders',$orders);
                $this->display();
            }else{
                redirect('Home/User/orders');
            }

        }else{
            redirect('Home/User/login');
        }
    }
    //显示待收货订单
    public function undeliveryAction(){
        $orders =[];
        $_SESSION['userinfo']['user_id'] = 5;
        $_REQUSET['order_statu_id'] = 3;
        if(isset($_SESSION['userinfo'])) {

            if(isset($_REQUSET['order_statu_id'])){
                //实例化order模型
                $order = M('Order');
                $userid = $_SESSION['userinfo']['user_id'];
                $where = [
                    'user_id' => $userid,
                    'wb_order.order_statu_id'=> $_REQUSET['order_statu_id']
                ];
                $order_list = $order
                    ->field('wb_order.order_id,wb_order_statu.order_statu_id,wb_order.user_id,wb_order.order_sn,wb_order.recept_id,wb_order.goods_info,wb_order.create_at,wb_order_statu.title')
                    ->join('wb_order_statu on wb_order_statu.order_statu_id = wb_order.order_statu_id')
                    ->where($where)->select();
                foreach ($order_list as $key => $value) {
                    $totalNum =0;
                    $totalPrice =0;
                    //获取商品信息并反序列化
                    $goods_info = unserialize($value['goods_info']);
                    //拼凑出总的数量和总价
                    foreach ($goods_info as $goods) {
                        $totalNum += $goods['buy_quantity'];
                        $totalPrice += $goods['buy_quantity'] * $goods['shop_price'];
                    }
                    $value['goods_info'] = $goods_info;
                    $value['totalPrice'] = $totalPrice;
                    $value['totalNum'] = $totalNum;
                    $orders[$key] = $value;
                }

                $this->assign('orders',$orders);
                $this->display();
            }else{
               $this-> redirect('Home/User/orders');
            }

        }else{
           $this-> redirect('Home/User/login');
        }
    }
    //显示已完成订单
    public function finishorderAction(){
        $orders =[];
        $_SESSION['userinfo']['user_id'] = 5;
        $_REQUSET['order_statu_id'] = 4;
        if(isset($_SESSION['userinfo'])) {

            if(isset($_REQUSET['order_statu_id'])){
                //实例化order模型
                $order = M('Order');
                $userid = $_SESSION['userinfo']['user_id'];
                $where = [
                    'user_id' => $userid,
                    'wb_order.order_statu_id'=> $_REQUSET['order_statu_id']
                ];
                $order_list = $order
                    ->field('wb_order.order_id,wb_order_statu.order_statu_id,wb_order.user_id,wb_order.order_sn,wb_order.recept_id,wb_order.goods_info,wb_order.create_at,wb_order_statu.title')
                    ->join('wb_order_statu on wb_order_statu.order_statu_id = wb_order.order_statu_id')
                    ->where($where)->select();
                foreach ($order_list as $key => $value) {
                    $totalNum =0;
                    $totalPrice =0;
                    //获取商品信息并反序列化
                    $goods_info = unserialize($value['goods_info']);
                    //拼凑出总的数量和总价
                    foreach ($goods_info as $goods) {
                        $totalNum += $goods['buy_quantity'];
                        $totalPrice += $goods['buy_quantity'] * $goods['shop_price'];
                    }
                    $value['goods_info'] = $goods_info;
                    $value['totalPrice'] = $totalPrice;
                    $value['totalNum'] = $totalNum;
                    $orders[$key] = $value;
                }

                $this->assign('orders',$orders);
                $this->display();
            }else{
                redirect('Home/User/orders');
            }

        }else{
            redirect('Home/User/login');
        }
    }
//  显示订单详情2
//   public function orderDetailAction(){
//      //接收订单的id
//      $id = I('request.orderId',3);
//      if($id !=''){
//          //实例化模型
//          $order = M('Order');
//          $where = ['order_id'=>$id];
//          $res =  $order
//              ->field('wb_order.order_sn,wb_order.goods_info,wb_order.create_at ,wb_order.comment,recept.detail ,recept.tel,recept.recept_name,city.title city,province.title province')
//              ->join('wb_recept as recept on recept.recept_id = wb_order.recept_id')
//              ->join('wb_region as province on recept.province_id = province.region_id')
//              ->join('wb_region as city on recept.city_id = city.region_id')
//              ->where($where)
//              ->find();
//          $goods_info = unserialize($res['goods_info']);
//          $res['goods_info'] = $goods_info;
//          $totalNum =0;
//          $totalPrice = 0;
//          foreach ($res['goods_info'] as $v) {
//              $totalNum += $v['buy_quantity'];
//              $totalPrice +=  $v['buy_quantity']* $v['shop_price'];
//          }
//          $res[totalNum]=$totalNum;
//          $res[totalPrice] = $totalPrice;
//
//          $this->assign('detail',$res);
//          $this->display();
//      }else{
//
//      }
//  }
//  //获取验证吗
//  public function getCodeAction(){
//      $tel = I('request.tel','');
//      if($tel){
//          $sms = new SMS();
//         $res = $sms->GetCode($tel);
//          if($res){
//              echo '发送成功';
//          }else{
//              echo '发送失败';
//          }
//      }else{
//          $this->ajaxReturn(['error'=>'3','message'=>'电话缺失']);
//      }
//  }
//  //验证登陆
//  public function checkCodeAction(){
//      $tel =I('request.tel','');
//      $code = I('request.code','');
//      if($tel && $code){
//          //实例化模型
//          $sms = new SMS();
//          $res = $sms->VerifyCode($tel,$code);
//          if($res){
//              $model = M('user');
//              $re = $model ->where(['tel'=>$tel])->find();
//              if($re) {
//                  $data = [
//                      'create_at' => time(),
//                      'update_at' => time(),
//                      'tel' => $tel
//                  ];
//                  $userid = $model->add($data);
//                  $data['user_id'] = $userid;
//                  session('userinfo', $data);
//              }else{
//                  session('userinfo',$re);
//              }
//          }
//      }
//  }
    //整合订单的代码
    public function waitAction(){
        $userId =$_SESSION['userinfo']['user_id'] =5;
        if($userId !=''){
            $orders =[];
                //实例化order模型
                $order = M('Order');
                $userid = $_SESSION['userinfo']['user_id'];
                $order_list = $order
                    ->field('wb_order.order_id,wb_order_statu.order_statu_id,wb_order.user_id,wb_order.order_sn,wb_order.recept_id,wb_order.goods_info,wb_order.create_at,wb_order_statu.title')
                    ->join('wb_order_statu on wb_order_statu.order_statu_id = wb_order.order_statu_id')
                    -> where(['user_id'=>$userid])->select();

                foreach ($order_list as$key=> $value){
                    $totalNum =0;
                    $totalPrice =0;
                    //获取商品信息并反序列化
                    $goods_info = unserialize($value['goods_info']);
                    //拼凑出总的数量和总价
                    foreach ( $goods_info  as  $goods){
                        $totalNum +=$goods['buy_quantity'];
                        $totalPrice += $goods['buy_quantity']*$goods['shop_price'];
                    }
                    $value['goods_info'] = $goods_info;
                    $value['totalPrice'] = $totalPrice;
                    $value['totalNum'] = $totalNum;
                    $orders[$key] =$value;
                }
                $unpays = getorders($orders,1);
                $this->assign('unpays',$unpays);
                $unsends = getorders($orders,2);
                $this->assign('unsends',$unsends);
                $undeliverys= getorders($orders,3);
                $this->assign('undeliverys',$undeliverys);
                $finnshorders = getorders($orders,4);
                $this->assign('finshorders',$finnshorders);
                $this->display();
        }else{
        $this->redirect('login') ;
        }
    }
     //取消订单
    public function deleteOrderAction(){
        //接收订单
        $orderId = I('request.ordersn','');
        if($_SESSION['userinfo']){
            if($orderId){
                //实例化订单模型
                $order = M('Order');
                $res = $order->where(['order_sn'=>$orderId])->delete();
                if($res){
                    $this->ajaxReturn(['error'=>0,'message'=>'取消订单成功']);
                }else{
                    $this->ajaxReturn(['error'=>4,'message'=>'取消订单失败']);
                }
            }else{
                $this->ajaxReturn(['error'=>2,'message'=>'缺少订单id']);
            }
        }else{
            $data=[
                'url'=>'User/login'
            ];
            $this->ajaxReturn(['error'=>1,'message'=>'未登录']);
        }


    }
    //确认收货
    public function confirmOrderAction(){
        //获取订单id
        //接收订单id
        $orderId = I('request.ordersn','');
        if($_SESSION['userinfo']){
            if($orderId){
                //实例化订单模型
                $order = M('Order');
                $res = $order->where(['order_sn'=>$orderId])->save(['order_statu_id'=>4]);
                if($res){
                    $this->ajaxReturn(['error'=>0,'message'=>'确认收货成功']);
                }else{
                    $this->ajaxReturn(['error'=>4,'message'=>'确认收货失败']);
                }
            }else{
                $this->ajaxReturn(['error'=>2,'message'=>'缺少订单id']);
            }
        }else{
            $this->ajaxReturn(['error'=>1,'message'=>'未登陆','url'=>'User/login']);
        }

    }
    //退出登陆
    public function outUserAction(){
        $_SESSION['userinfo']='';
        $res = $_SESSION['userinfo'];
        if($res !=''){
            $this->ajaxReturn(['error'=>0,'message'=>'退出登陆成功']);
        }else{
            $this->ajaxReturn(['error'=>200,'message'=>'退出登陆失败']);
        }
    }
    //订单详情业
    public function orderDetailAction(){

        //接收订单的id
        $id = I('request.orderId','');
        if($id !=''){
            //实例化模型
            $order = M('Order');
            $where = ['order_sn'=>$id];
            $res =  $order
                ->field('wb_order.order_sn,wb_order.goods_info,wb_order.create_at ,wb_order.comment,recept.detail ,recept.tel,recept.recept_name,city.title city,province.title province')
                ->join('wb_recept as recept on recept.recept_id = wb_order.recept_id')
                ->join('wb_region as province on recept.province_id = province.region_id')
                ->join('wb_region as city on recept.city_id = city.region_id')
                ->where($where)
                ->find();
            $goods_info = unserialize($res['goods_info']);
            $res['goods_info'] = $goods_info;
            $totalNum =0;
            $totalPrice = 0;
            foreach ($res['goods_info'] as $v) {
                $totalNum += $v['buy_quantity'];
                $totalPrice +=  $v['buy_quantity']* $v['shop_price'];
            }
            $res[totalNum]=$totalNum;
            $res[totalPrice] = $totalPrice;

            $this->assign('detail',$res);
            $this->display();
        }else{

        }
    }
}