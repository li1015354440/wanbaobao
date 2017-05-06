<?php
namespace Home\DxLogin;
class SMS {


	//发送验证码curl请求
	private function PostDataCurl($url,$data)
	{
		$appkey = "6c263277ba8772180f81dffc97448d86"; 	//网易分配的appkey
		$appsecret = "33c486b6128c";  					//网页分配的appsecret
		$curtime = (string)(time()); 					//当前时间戳
		$nonce = "123456789";  							//随机数
		$checksum = sha1($appsecret.$nonce.$curtime); //生成checksum
		
        $timeout = 5000; 
		//组建http请求头
        $http_header = array(
            'AppKey:'.$appkey,
            'Nonce:'.$nonce,
            'CurTime:'.$curtime,
            'CheckSum:'.$checksum,
            'Content-Type:application/x-www-form-urlencoded;charset=utf-8'
        );
        
        $postdataArray = array();
        foreach ($data as $key=>$value){
            array_push($postdataArray, $key.'='.urlencode($value));
        }
        $postdata = join('&', $postdataArray);

        $ch = curl_init(); 
        curl_setopt ($ch, CURLOPT_URL, $url);
        curl_setopt ($ch, CURLOPT_POST, 1);
        curl_setopt ($ch, CURLOPT_POSTFIELDS, $postdata);
        curl_setopt ($ch, CURLOPT_HEADER, false ); 
        curl_setopt ($ch, CURLOPT_HTTPHEADER,$http_header);
        curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER,false); //处理http证书问题  
        curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout); 
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
        
        $result = curl_exec($ch);  
        if (false === $result) {
            $result =  false;
        }
        curl_close($ch);    
        return $result ;
	}

	//获取验证码
	public function GetCode($tel)
	{
		$url = 'https://api.netease.im/sms/sendcode.action';
		$data= array(
            'mobile' => $tel,
			'templateid'=>"3064240"
        );
        $result = $this->PostDataCurl($url,$data);
        return $result;
	}
		//校验验证码
	public function VerifyCode($tel,$code)
	{
		$url = 'https://api.netease.im/sms/verifycode.action';
        $data= array(
            'mobile' => $tel,
            'code' => $code
        );
        $result = $this->PostDataCurl($url,$data);
        
        return $result;
	}
}
