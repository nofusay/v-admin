<?php 

namespace app\admin\validate;
use think\Validate;

class Menu extends Validate
{
    protected $rule = [
      'title'  => ['require','chsAlphaNum'],
      'pid' => ['require','number'],
      'menuId' => ['require','number'],   
      'id' => ['require'],   
    ];

    protected $message = [
      'title.require'   => '名称不能为空',
      'title.chsAlphaNum'  => '名称只允许为汉字、字母或数字',
      'pid.require'  => '参数接收异常',
      'pid.number'   => '参数接收类型异常',
      'menuId.require'  => '参数接收异常',
      'menuId.number'   => '参数接收类型异常',
      'id.require'  => '参数接收异常',
    ];

    protected $scene = [
        'add'  =>  ['title','pid'],
        'del'  =>  ['menuId'], 
        'menuDetai' =>  ['menuId'],
        'edit' =>  ['id','title'],
    ];

}