<?php 

namespace app\admin\controller;
use think\Controller;
use think\facade\Request;
use app\common\Token;
use think\Db;
use think\facade\Session;

# 拦截器
class Base extends Controller
{
	public function initialize(){

		# 获取发送的 token
		$token = Request::header('authorization');
		if (!$token) {
			exit(json_encode(array('code'=>403,'msg'=>'非法登录状态，无法使用')));
		}

		# 验证key是否正常
		$res = Token::checkToken($token);

		if ($res === false) {
			exit(json_encode(array('code'=>403,'msg'=>'非法登录状态，无法使用')));
		}

		$uid = $res->data->userid;

		$ures = Db::table('s_user')->where('id',$uid)->value('id');
		if (!$ures) {
			exit(json_encode(array('code'=>403,'msg'=>'非法登录状态，无法使用')));
		}
		Session::set('userId',$uid);
	}
}