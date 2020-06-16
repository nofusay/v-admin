<?php 

namespace app\admin\validate;
use think\Validate;

class Login extends Validate
{
    protected $rule = [
        'username'  =>  'require',
        'password'  =>  ['require', 'min' => 6, 'max' => 20],
    ];

    protected $message = [
      'username.require' => '用户名不能为空',
      'password.require' => '密码不能为空',
      'password.min'     => '密码不能少于6位',
      'password.max'     => '密码不能大于20位',
    ];

    protected $scene = [
        'index'  =>  ['username','password'],
    ];

}