<?php
namespace Home\Wxlogin;
/**
 *
 */
class Wxlogin
{
    protected $appid = 'wxd381206eb47a300e';
    protected $secret = '8d18100ab6b7ab6816635bbabcc4e9d0';
    protected $redirect = 'http://abc.baobaowan.com/index.php/Home/User/LoginCallBack';
    public function Oauth(){
        $oauth_url  = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$this->appid.'&redirect_uri='.$this->redirect.'&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect';
        header("Location:".$oauth_url);
    }
    public function getToken($code){
        $get_token_url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$this->appid.'&secret='.$this->secret.'&code='.$code.'&grant_type=authorization_code';
        return $this->request($get_token_url);
    }
    public function getUserInfo($access_token,$openid){
        $get_user_info_url = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN';
        return $this->request($get_user_info_url);
    }
    public function request($url){
        $fp = curl_init();
        curl_setopt($fp,CURLOPT_URL,$url);
        curl_setopt($fp,CURLOPT_HEADER,0);
        curl_setopt($fp, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt($fp, CURLOPT_CONNECTTIMEOUT, 10);
        $res = curl_exec($fp);
        curl_close($fp);
        return json_decode($res,true);
    }
}