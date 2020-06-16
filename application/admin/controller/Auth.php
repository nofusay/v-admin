<?php 

namespace app\admin\controller;
use app\admin\controller\Base;
use think\facade\Session;
use think\facade\Request;
use think\Db;

# 拦截器
class AUTH extends Base
{
	public function initialize(){

		parent::initialize();

		$userId = Session::get('userId');
		if (!$userId) {
			exit(json_encode(array('code'=>403,'msg'=>'非法登录状态，无法使用')));
		}
		
		$contro = Request::controller();
		$method = Request::action();

		# 查询菜单表
		$rows = Db::query("SELECT id FROM s_menu WHERE `contro`='{$contro}' AND `method`='{$method}' LIMIT 1");
		if (!$rows) {
			exit(json_encode(array('code'=>1,'msg'=>'权限不足')));
		}
		$menus_id = $rows[0]['id'];

		# 查询权限表
		$grows = Db::query("SELECT s_group.`rights` FROM s_group LEFT JOIN s_user ON s_user.`gid`=s_group.`id` WHERE s_user.`id`={$userId} LIMIT 1");
		if (!$grows) {
			exit(json_encode(array('code'=>403,'msg'=>'没有权限的账户禁止使用')));
		}
		$rights = $grows[0]['rights'];
		$rights = json_decode($rights,true);
		
		# 判断是否有权限
		$res = in_array($menus_id, $rights);
	
		if (!$res) {
			exit(json_encode(array('code'=>1,'msg'=>'权限不足')));
		}
	}
}