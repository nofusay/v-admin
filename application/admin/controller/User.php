<?php 

namespace app\admin\controller;
use app\admin\controller\Auth;
use think\facade\Session;
use think\facade\Request;
use app\common\Token;
use think\Db;

# 用户管理
class User extends Auth
{
	public function list(){

		$rows = Db::query("SELECT s_user.`id`,s_user.`username`,s_user.`key`,s_user.`phone`,s_user.`status`,s_group.`title` rolegroup FROM s_user LEFT JOIN s_group ON s_user.`gid` = s_group.`id` ORDER BY s_user.`id` ASC");
		return ['code'=>0,'msg'=>'获取用户列表成功','data'=>$rows];
	}

	public function roleList(){
		$rows = Db::name('s_group')->select();
		return ['code'=>0,'msg'=>'获取角色列表成功','data'=>$rows];
	}

	public function add(){
		$param = Request::post('params');

		# 验证参数
		$v_res = $this->validate($param,'app\admin\validate\User.add');
		if ($v_res !== true) {
			return ['code'=>1, 'msg'=>$v_res];
		}

		# 查询手机号是否重复
		$check = Db::name('s_user')->where('phone',$param['phone'])->find();
		if ($check) {
			return ['code'=>1,'msg'=>'已经存在的手机用户'];
		}

		# 定义初始密码为 111111
		$param['password'] = sha1(md5('111111' . 'wby'));
		$param['key'] = 'ts_' . self::rand(6);
		$param['add_time'] = time();

		# 添加数据库
		$res = Db::name('s_user')->insert($param);
		if (!$res) {
			return ['code'=>1,'msg'=>'用户添加失败'];
		}
		return ['code'=>0,'msg'=>'用户添加成功'];
	}

	public static function rand($len){
        $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz';
        $string=time();
        for(;$len>=1;$len--)
        {
            $position=rand()%strlen($chars);
            $position2=rand()%strlen($string);
            $string=substr_replace($string,substr($chars,$position,1),$position2,0);
        }
        return $string;
    }

    public function del(){
    	$param = Request::post('params');

    	# 验证参数
		$v_res = $this->validate($param,'app\admin\validate\User.del');
		if ($v_res !== true) {
			return ['code'=>1, 'msg'=>$v_res];
		}

		# 删除用户
		$res = Db::name('s_user')->delete($param['userId']);
		if (!$res) {
			return ['code'=>1,'msg'=>'用户删除失败'];
		}
		return ['code'=>0,'msg'=>'用户删除成功'];
    }

    # 用户详情
    public function detail(){
    	$param = Request::post('params');

    	# 验证参数
		$v_res = $this->validate($param,'app\admin\validate\User.detail');
		if ($v_res !== true) {
			return ['code'=>1, 'msg'=>$v_res];
		}

		# 查询用户
		$row = Db::name('s_user')->field('id,username,phone,gid,status')->find($param['userId']);
		if (!$row) {
			return ['code'=>1,'msg'=>'用户查询失败'];
		}
		return ['code'=>0,'msg'=>'用户查询成功','data'=>$row];
    }

    # 根据 token 获取用户信息
    public function userInfo(){

    	# 获取发送的 token
		$token = Request::header('authorization');
		$info = Token::checkToken($token);
		if ($info === false) {
			exit(json_encode(array('code'=>403,'msg'=>'非法登录状态，无法使用')));
		}

		$userId = $info->data->userid;

		# 查询用户
		$rows = Db::query("SELECT s_user.`id`,s_user.`username`,s_group.`title` rolegroup FROM s_user LEFT JOIN s_group ON s_user.`gid` = s_group.`id` WHERE s_user.`id`={$userId} LIMIT 1");
		if (!$rows) {
			return ['code'=>1,'msg'=>'用户查询失败'];
		}
		return ['code'=>0,'msg'=>'用户查询成功','data'=>$rows[0]];
    }

    # 用户修改
    public function edit(){
    	$param = Request::post('params');

    	# 验证参数
		$v_res = $this->validate($param,'app\admin\validate\User.edit');
		if ($v_res !== true) {
			return ['code'=>1, 'msg'=>$v_res];
		}

		$id = $param['id'];
		unset($param['id']);

		# 修改
		$res = Db::name('s_user')->where('id', $id)->update($param);
		if (!$res) {
			return ['code'=>1,'msg'=>'用户修改失败'];
		}
		return ['code'=>0,'msg'=>'用户修改成功'];
    }

    # 重置密码
   	public function resetPass(){
   		$param = Request::post('params');

    	# 验证参数
		$v_res = $this->validate($param,'app\admin\validate\User.resetPass');
		if ($v_res !== true) {
			return ['code'=>1, 'msg'=>$v_res];
		}

		$password = sha1(md5('111111' . 'wby'));
		$res = Db::name('s_user')
		->where('id',$param['userId'])
		->update(['password' => $password]);

		if (!$res) {
			return ['code'=>1,'msg'=>'重置密码失败，原密码为 111111'];
		}
		return ['code'=>0,'msg'=>'重置密码成功'];
   	}
}