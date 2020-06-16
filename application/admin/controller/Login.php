<?php 

namespace app\admin\controller;
use think\Controller;
use think\facade\Request;
use app\common\Token;
use think\Db;

# 登录
class Login extends Controller
{
	public function index()
	{
		# 验证参数
		$data = Request::param();
		$v_res = $this->validate($data,'app\admin\validate\Login.index');
		if ($v_res !== true) {
			return ['code'=>1, 'msg'=>$v_res];
		}

		$username = $data['username'];
		$password = $data['password'];

		# 判断用户是否禁用
		$user = Db::table('s_user')->where('username',$username)->find();
		if (!$user) {
			return ['code'=>1, 'msg'=>'用户不存在，请重新输入'];
		}
		
		if ($user['status'] === 1) {
			return ['code'=>1, 'msg'=>'当前用户已被禁用'];
		}
		
		# 判断用户名密码是否正确
		$mdpassword = sha1(md5($password . 'wby'));
		if ($user['password'] !== $mdpassword) {
			return ['code'=>1, 'msg'=>'密码不正确，请重新输入'];
		}

		# 生成 token 值，并返回
		$token = Token::createToken($user['id']);

		return ['code'=>0, 'msg'=>'登录成功', 'token'=>$token];
	}
}
