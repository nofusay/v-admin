<?php 

namespace app\admin\validate;
use think\Validate;

class User extends Validate
{
  protected $rule = [
    'username'  => ['require','chsAlphaNum'],
    'phone'     => ['require','mobile'],
    'gid'       => ['require','number'],
    'userId'    => ['require','number'],
    'id'        => ['require','number'],
  ];

  protected $message = [
    'username.require'     => '名称不能为空',
    'username.chsAlphaNum' => '名称只允许为汉字、字母或数字',
    'phone.require'     => '手机号不能为空',
    'phone.mobile'      => '非法的手机号',
    'gid.require'       => '参数接收类型异常',
    'gid.number'        => '参数接收类型异常',
    'userId.require'    => '参数接收类型异常',
    'userId.number'     => '参数接收类型异常',
    'id.require'        => '参数接收类型异常',
    'id.number'         => '参数接收类型异常',
  ];

  protected $scene = [
    'add'     =>  ['username','phone','gid'],
    'del'     =>  ['userId'],
    'detail'  =>  ['userId'],
    'edit'    =>  ['username','phone','id','gid'],
    'resetPass' =>  ['userId'],
  ];
}