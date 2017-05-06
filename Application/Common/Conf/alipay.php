<?php
return array (
    'app_id' => "2016080300156788",

    //商户私钥，您的原始格式RSA私钥
    'merchant_private_key' => "MIIEpAIBAAKCAQEAneEn5OP+OJbQswZP4xBAjnay7LXk5BY/Qm08P+mV3IF5dIKkWqTZtds2CebFtte971XqXDkT1XYmYh2mXybCbbY2ncO82yQ+hLYTUzAxthULwUqFugdVAPuCKBadicrQrquzlFbEjwahsTi5t4Y7gkYR0fH2CZxV8YjiEkw2060lm+tw5HjYqmTJOkxc21jRK8MZrbYam3U121VgXxeITyzrefs3Z/RgcLU1N46OdvHLQltaf6Sy7RV2x6ZrUk0lnssvi1gUGtTgUcgHmvPG7aEzsch+yfwJ6joQwyR3l8gqta4S9g9ivXQcqCee8O7Rw0yr8IRSDJNm21t8wGeogwIDAQABAoIBABfbQPr+VJjaeTjEGGg4OXkNiKXqKmco0XBJL1R9CG8khWTMpOcph9hKApVmcLPbT9ENi3daDJwx2UT0NAKmybRoV0JppGz8whGtrafhXXGlJnyTtTdSCk82sgk6uZ1rrzLlsSIWJmdyWhZ8etu3heB+lAzEYig7nmT8CSU4rQQ5JfH+68hbCwhVGRevKm33pHTjEO6HI9xZ0+jfL1PwGo1efNLZiHWM2UHkvHFfhkCkFKog850vxLkVCDSormitQqAn46XfwmedR/oWvb2K8C9rjQlOFtD+CLcMKmqg/ZE9/KSLSHXo1E9ceNiF4gZX4kaPZvVMACxHVSUSK4ZovckCgYEAzhhUyBFqZdfGGA+ZI0a3SFtZuJz1JxznjjmI99rveM3w4I1olNqa5QLShoXdp+TXfXUGJ4Ch/rMSu9NlpeqIwApca2EbSKQlcRI27piyKwcVn5P3wFAiW0CIi9zbL3LEiUMLgYS4EnY4p86X9SMvZP7LbJlv9mZZhB6AtDhc2HcCgYEAxBv7bnI/9/HERKAHrSfmn149v25iHtv1zP5/L9kkgZ+o68qEpEgw2YhbWFLsSh4grmAFGCjTwunnz+1T81vVKoakE1oaQD9mDgo31uaujRQtuoIwzWPHE8n1UExwGsU/qLlWuM73CiqEh/rCtFGxPh8Rgc9qdGg5EsipB/RFv1UCgYEAreZD64kecQ7PnM/UlLp/vrMuEqOGDFnMXOrZUuJOvG1xOdi4DRokJubpww7DiH6FAdwztDNK/YIWZZwrogMzHm/fqc+HWRUAbtdCuLLpa3sP2NXe8EvtoXfjf7h8zh2WDoge9kuJkjJk/dtJx1PIEv0XouwZWBzmnZ+rU+ZXGWMCgYEAnMlyX37gPa8BJ4x6FqoL6+ZAn8f0ko9xGQ9vSCXm1et79efX3DALPh+SSC8j0q37mu4RpJsfknnHZ6lqsOn+px02GHK1AiCtyxPISPvtGcXEOZTUx6C6DMwuYKB8ECsbFh33g5GUWIBdrQmCmP+nIO9d49acWazp1GaxHTbifHECgYAvJlIUWLq9F8NwF4pxG7HOvrbYM+cIycLZ+2TE3f2cZq/I/tDOlAqRdXaJVyUGjo2NtKfzIzyx0BntU4/wMLbc2WvxYYKAxNm9H61Yzl9E0kBXFB8ecXGYlWnDLjQjBKtknx/UmCWVQftuqf1fd+w2H4StdLTpLr482nXBvBJ3+w==",

    //异步通知地址
    'notify_url' => "http://abc.baoabowan.com/alipay/notify_url.php",

    //同步跳转
    'return_url' => "http://abc.baobaowan.com/alipay/return_url.php",

    //编码格式
    'charset' => "UTF-8",

    //签名方式
    'sign_type'=>"RSA2",

    //支付宝网关
    'gatewayUrl' => "https://openapi.alipaydev.com/gateway.do",

    //支付宝公钥,查看地址：https://openhome.alipay.com/platform/keyManage.htm 对应APPID下的支付宝公钥。
    'alipay_public_key' => "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAw9MjTLonLoqgfr+X8btoQ9yIzYxIk1xW8RvG7kjpookLBvLHN2ejzz6CeE1/pL3gJnJhuVDX8OuSKg1ZS5PymZxADjkhTN2grNAJsrwxj3AHN+mslrcVgjOv5iKiYIf7rwsNvXDX0oQKYynRLUm5kWKLtvR3Ggyo48HvJTWttGGnYB1XMQdARNKKb55JtS8LL8irODQdd3zTTUOX7KpCh2JFZ8wrjRe9NdlbSa2xafVCCSHLnmyGMaUZ2wh0jyun4c/EGlZCJxgesib1eFAsWen1rCckbx1Z8pty/dvkw3IcIGyGUw8/a6FzE9JWRsgHEQxT93xWbMps3X1Xj3X4MwIDAQAB",
);