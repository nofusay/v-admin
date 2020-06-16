<?php 

namespace app\admin\validate;
use think\Validate;

class Role extends Validate
{
    protected $rule = [
      'title'  => ['require','chsAlphaNum'],
      'rights' => ['require'],
      'roleId' => ['require','number'],
      'id'     => ['require','number'],
    ];

    protected $message = [
      'title.require'     => '名称不能为空',
      'title.chsAlphaNum' => '名称只允许为汉字、字母或数字',
      'rights.require'  => '请选择权限',
      'roleId.require'  => '参数接收异常',
      'roleId.number'   => '参数接收类型异常',
      'id.require'      => '参数接收类型异常',
      'id.number'       => '参数接收类型异常',
    ];

    protected $scene = [
        'add'     =>  ['title','rights'],
        'detail'  =>  ['roleId'],
        'edit'    =>  ['id','title','rights'],
        'del'     =>  ['roleId'],
    ];
}