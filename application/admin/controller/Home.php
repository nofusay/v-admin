<?php 

namespace app\admin\controller;
use app\admin\controller\Base;
use think\facade\Session;
use think\facade\Request;
use think\Db;

# 主页
class Home extends Base
{
	public function menus(){
		$userId = Session::get('userId');

        # 获取用户权限组
        $rightsRow = Db::query("SELECT s_group.`rights` FROM s_user LEFT JOIN s_group ON s_user.`gid` = s_group.`id` WHERE s_user.`id`={$userId} LIMIT 1");
        $rights = $rightsRow[0]['rights'];

        if (!$rights) {
        	return ['code'=>1,'msg'=>'请先联系管理员添加权限','data'=>[]];
        }

        $rightstr = join(',', json_decode($rights,true));
        $menuRow = Db::query("SELECT * FROM s_menu WHERE id IN({$rightstr}) AND `ishidden`=0 AND `status`=0 ORDER BY crd ASC");

        $data = $this->clist($menuRow);
        $menus = $this->gettreeitems($data);

        Session::set('userId',$userId);
        return ['code'=>0,'msg'=>'获取菜单成功','data'=>$menus];
	}

	private function clist($rows){
	    $data = array();
	    foreach ($rows as $key => $row) {
	        $data[$row['id']] = $row;
	    }
	    return $data;
	}

	private function gettreeitems($s_menu){
	    $tree = array();
	    foreach ($s_menu as $menu) {
	        
	        if ($menu['pid']) {
	            $pid1 = $menu['pid'];
	            $s_menu[$pid1]['children'][] = &$s_menu[$menu['id']];
	        }else{
	            $tree[] = &$s_menu[$menu['id']];
	        }
	    }
	    return $tree;
	}

	public function editPass(){
		$param = Request::post('params');
		
		# 验证参数
		$userId = $param['userId'];
		$oldpass = sha1(md5($param['oldpass'] . 'wby'));
		$newpass = $param['newpass'];
		$renewpass = $param['renewpass'];

		if (!$userId) {
			return ['code'=>1,'msg'=>'参数接收异常'];
		}

		if ($newpass !== $renewpass) {
			return ['code'=>1,'msg'=>'两次密码不一致'];
		}

		# 查询原密码是否正确
		$row = Db::query("SELECT * FROM s_user WHERE id={$userId} AND PASSWORD='{$oldpass}' LIMIT 1");
		if (!$row) {
			return ['code'=>1,'msg'=>'原密码不正确，请重新输入'];
		}

		# 更改密码
		$password = sha1(md5($newpass . 'wby'));
		$res = Db::name('s_user')
		->where('id',$userId)
		->update(['password' => $password]);

		if (!$res) {
			return ['code'=>1,'msg'=>'修改密码失败'];
		}
		return ['code'=>0,'msg'=>'修改密码成功'];
	}
}