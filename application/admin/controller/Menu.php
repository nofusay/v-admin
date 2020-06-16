<?php 

namespace app\admin\controller;
use app\admin\controller\Auth;
use think\facade\Session;
use think\facade\Request;
use think\Db;

# 菜单管理
class Menu extends Auth
{
	public function list(){

		$rows = Db::query("SELECT * FROM s_menu ORDER BY crd ASC");

        $data = [];
	    foreach ($rows as $key => $row) {
	        $data[$row['id']] = $row;
	    }

        $menus = $this->gettreeitems($data);

        # 菜单层级
		$type = Request::param('type');
		if ($type == 2) {
			foreach ($menus as $key => &$menu) {
				if (isset($menu['children'])) {
					foreach ($menu['children'] as $k=> &$v) {
						if (isset($v['children'])) {
							unset($v['children']);
						}
					}
				}
			}
		}
        return ['code'=>0,'msg'=>'获取菜单成功','data'=>$menus];
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

	# 添加菜单
	public function add(){
		$param = Request::param('params');

		# 参数验证
		$v_res = $this->validate($param,'app\admin\validate\Menu.add');
		if ($v_res !== true) {
			return ['code'=>1, 'msg'=>$v_res];
		}

		# 查询是否已经存在同名控制器和方法
		if (isset($param['contro']) && isset($param['method']) && $param['contro'] != '') {
			$row = Db::query("SELECT id FROM s_menu WHERE contro='{$param['contro']}' AND `method`='{$param['method']}' LIMIT 1");
			if ($row) {
				return ['code'=>1,'msg'=>'已存在重复的控制器和方法'];
			}
		}

		$res = Db::name('s_menu')->insert($param);
		if (!$res) {
			return ['code'=>1,'msg'=>'添加菜单失败'];
		} 
		return ['code'=>0,'msg'=>'添加菜单成功'];
	}

	# 删除菜单
	public function del(){
		$param = Request::param();

		# 参数验证
		$v_res = $this->validate($param,'app\admin\validate\Menu.del');
		if ($v_res !== true) {
			return ['code'=>1, 'msg'=>$v_res];
		}

		# 获取子级PID
		$srow = Db::table('s_menu')->where('pid',$param['menuId'])->column('id');
		$srow[] = $param['menuId'];
		$pidStr = join(',',$srow);
		
		# 删除操作
		$res = Db::execute("DELETE FROM s_menu WHERE id={$param['menuId']} OR pid in ({$pidStr})");
		if (!$res) {
			return ['code'=>1,'msg'=>'删除菜单失败'];
		} 
		return ['code'=>0,'msg'=>'删除菜单成功'];
	}

	# 菜单明细数据
	public function menuDetai(){
		$param = Request::param();

		# 参数验证
		$v_res = $this->validate($param,'app\admin\validate\Menu.menuDetai');
		if ($v_res !== true) {
			return ['code'=>1, 'msg'=>$v_res];
		}

		$rows = Db::table('s_menu')->where('id',$param['menuId'])->find();
		return ['code'=>0,'msg'=>'删除菜单成功','data'=>$rows];
	}

	# 菜单修改
	public function edit(){
		$param = Request::param('params');

		# 参数验证
		$v_res = $this->validate($param,'app\admin\validate\Menu.edit');
		if ($v_res !== true) {
			return ['code'=>1, 'msg'=>$v_res];
		}

		$id = $param['id'];
		unset($param['id']);

		try {
			$res = Db::name('s_menu')
			    ->where('id', $id)
			    ->data($param)
			    ->update();
			if ($res) {
				return ['code'=>0,'msg'=>'菜单修改成功'];
			}
		} catch (Exception $e) {
			return ['code'=>1,'msg'=>'菜单修改失败'];
		}
	}
}