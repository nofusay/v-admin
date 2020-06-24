<?php 

namespace app\admin\validate;
use think\Validate;

class Good extends Validate
{
    protected $rule = [
      'basicId'  => ['require','number'],
      'cate_id'  => ['require','number'],
      'imgUrl'   => ['require'],
      'type'     => ['require','number'],
      'id'       => ['require','number'],
    ];

    protected $message = [
      'basicId.require'  => '参数接收异常',
      'basicId.number'   => '参数接收类型异常',
      'cate_id.require'  => '参数接收异常',
      'cate_id.number'   => '参数接收类型异常',
      'imgUrl.require'   => '参数接收异常',
      'type.require'     => '参数接收异常',
      'type.number'      => '参数接收类型异常',
      'id.require'       => '参数接收异常',
      'id.number'        => '参数接收类型异常',
    ];

    protected $scene = [
        'skuList'   =>  ['basicId'],
        'add'       =>  ['cate_id'],
        'moveImg'   =>  ['imgUrl'],
        'goodDetail'  =>  ['type','id'],
        'edit'      =>  ['id','cate_id'],
        'del'       =>  ['id'],
    ];
}