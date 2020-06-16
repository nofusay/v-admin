<?php
namespace app\common;
use \Firebase\JWT\JWT;

class Token
{
    static public function createToken($userid, $key='ts_aaaaabb')
	{
		$time = time(); //当前时间
   		$param = [
	    	'iss' => 'http://www.wbyyo.com', //签发者 可选
	       	'aud' => 'http://www.wbyyo.com', //接收该JWT的一方，可选
	       	'iat' => $time, //签发时间
	       	'nbf' => $time , //(Not Before)：某个时间点后才能访问，比如设置time+30，表示当前时间30秒后才能使用
	       	'exp' => $time+73600*8, //过期时间,这里设置2个小时
	    	'data' => [ //自定义信息，不要定义敏感信息
	       		'userid' => $userid
	       	]
	    ];
	    return JWT::encode($param, $key); //输出Token
	}

	static public function checkToken($token, $key='ts_aaaaabb')
	{
	    try {
       		$Result = JWT::decode($token, $key, ['HS256']);
	        return $Result;
    	} catch(\Firebase\JWT\SignatureInvalidException $e) {  //签名不正确
    		return $e->getMessage();
    	}catch(\Firebase\JWT\BeforeValidException $e) {  // 签名在某个时间点之后才能用
    		return $e->getMessage();
    	}catch(\Firebase\JWT\ExpiredException $e) {  // token过期
    		return $e->getMessage();
	   	}catch(Exception $e) {  //其他错误
    		return $e->getMessage();
    	}
	}
}