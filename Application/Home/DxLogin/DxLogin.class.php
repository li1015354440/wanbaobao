<?php
namespace Home\DxLogin;
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/3
 * Time: 15:55
 */
class DxLogin
{


//将你注册的 key和 secret 定义好。
//这是你注册网易云信获得的xxxxxxxxx为你自己需要填写的地方

    const APP_KEY = '6c263277ba8772180f81dffc97448d86';
    const APP_SECRET = '33c486b6128c';
//发送验证码函数，传入手机号即可
    public function SendSmsCode($mobile = ""){
        $appKey = self::APP_KEY;

        $appSecret = self::APP_SECRET;

//填写短信

//下方填写的是模板id
        $nonce = '123456789';
        $curTime = time();
        $checkSum = sha1($appSecret . $nonce . $curTime);
        $data  = array(
            'mobile'=> $mobile,
//下方填写的是模板id
            'templateid'=>3064240,

        );
        $data = http_build_query($data);
        $opts = array (
            'http' => array(
                'method' => 'POST',
                'header' => array(
                    'Content-Type:application/x-www-form-urlencoded;charset=utf-8',
                    "AppKey:$appKey",
                    "Nonce:$nonce",
                    "CurTime:$curTime",
                    "CheckSum:$checkSum"
                ),
                'content' =>  $data
            ),
        );
        $context = stream_context_create($opts);
        $html = file_get_contents("https://api.netease.im/sms/sendcode.action", false, $context);
        echo $html;
    }
//验证码校验函数，传入手机号，以及该手机号反馈给你的验证码，
    public function CheckSmsYzm($mobile = "",$Code=""){
        $appKey = self::APP_KEY;
        $appSecret = self::APP_SECRET;
        $nonce = '100';
        $curTime = time();
        $checkSum = sha1($appSecret . $nonce . $curTime);
        $data  = array(
            'mobile'=> $mobile,
            'code' => $Code,
        );
        $data = http_build_query($data);
        $opts = array (
            'http' => array(
                'method' => 'POST',
                'header' => array(
                    'Content-Type:application/x-www-form-urlencoded;charset=utf-8',
                    "AppKey:$appKey",
                    "Nonce:$nonce",
                    "CurTime:$curTime",
                    "CheckSum:$checkSum"
                ),
                'content' =>  $data
            ),
        );
        $context = stream_context_create($opts);
        $html = file_get_contents("https://api.netease.im/sms/verifycode.action", false, $context);
        return $html;
    }
}


