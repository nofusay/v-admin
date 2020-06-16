<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
use Firebase\JWT\JWT;

function signToken($user_id,$series_id,$user_power,$ip){
    $key="dt".$ip;      //这里是自定义的一个随机字串，应该写在config文件中的，解密时也会用，相当    于加密中常用的 盐  salt
    $token=array(
        "iss"=>$key,    //签发者 可以为空
        "aud"=>$ip,     //面象的用户，可以为空
        "iat"=>time(),  //签发时间
        "nbf"=>time()+1,    //在什么时候jwt开始生效  （这里表示生成1秒后才生效）
        "exp"=> time()+604800,  //token 过期时间,这里表示一个星期（60*60*24*7）=604800
        "data"=>[           //记录的data的信息，这里是自已添加上去的，如果有其它信息，可以再添加数组的键值对
            "series_id"=>$series_id,
            "user_id"=>$user_id,
            "power"=>$user_power
        ]
    );
        $jwt = JWT::encode($token, $key);  
    //  print_r($token);
   

    return $jwt;
}
