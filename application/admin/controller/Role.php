<?php 

namespace app\admin\controller;
use app\admin\controller\Auth;
use think\facade\Session;
use think\facade\Request;
use think\Db;

# 角色管理
class Role extends Auth
{
	public function list(){
		$param = Request::param();
		
		$pagenum = ($param['pagenum']-1)*$param['pagesize'];
		$pagesize = $param['pagesize'];

		# 总记录数据
		$total = Db::name('s_group')->count('id');

		$rows = Db::name('s_group')
		->field('id,title')
		->limit($pagenum,$pagesize)
		->select();
		return ['code'=>0,'msg'=>'获取角色列表成功','data'=>$rows, 'total'=>$total];
	}

	public function add(){
		$param = Request::param('params');

		# 验证参数
		$v_res = $this->validate($param,'app\admin\validate\Role.add');
		if ($v_res !== true) {
			return ['code'=>1, 'msg'=>$v_res];
		}

		# 验证名称是否已存在
		$row = Db::name('s_group')->where('title',$param['title'])->find();
		if ($row) {
			return ['code'=>1,'msg'=>'该角色名称已被占用'];
		}

		# 添加角色
		$param['rights'] = '[' . $param['rights'] . ']';
		$res = Db::name('s_group')->insert($param);
		if (!$res) {
			return ['code'=>1,'msg'=>'添加角色失败'];
		} 
		return ['code'=>0,'msg'=>'添加角色成功'];
	}

	# 角色详情
	public function detail(){

		$param = Request::param();

		# 验证参数
		$v_res = $this->validate($param,'app\admin\validate\Role.detail');
		if ($v_res !== true) {
			return ['code'=>1, 'msg'=>$v_res];
		}

		# 查询数据
		$row = Db::name('s_group')
		->where('id',$param['roleId'])
		->find();
		
		if (!$row) {
			return ['code'=>1,'msg'=>'查询数据失败'];
		}

		# 查询三级的菜单ID
		$rightStr = join(',', json_decode($row['rights']));
		$srows = Db::query("SELECT id FROM s_menu WHERE pid>0 AND LENGTH(path) IS NOT TRUE AND id IN({$rightStr})");

		$keys = array_column($srows, 'id');
		return ['code'=>0,'msg'=>'查询数据成功','data'=>$row, 'keys'=>$keys];
	}

	# 角色修改
	public function edit(){
		$param = Request::param('params');

		# 验证参数
		$v_res = $this->validate($param,'app\admin\validate\Role.edit');
		if ($v_res !== true) {
			return ['code'=>1, 'msg'=>$v_res];
		}

		$id = $param['id'];
		unset($param['id']);

		$param['rights'] = '[' . $param['rights'] . ']';

		$res = Db::name('s_group')
	    ->where('id', $id)
	    ->data($param)
	    ->update();

	    if (!$res) {
			return ['code'=>1,'msg'=>'角色修改失败'];
		} 
		return ['code'=>0,'msg'=>'角色修改成功'];
	}

	# 角色删除
	public function del(){
		$param = Request::post('params');

		# 验证参数
		$v_res = $this->validate($param,'app\admin\validate\Role.del');
		if ($v_res !== true) {
			return ['code'=>1, 'msg'=>$v_res];
		}

		$res = Db::name('s_group')->delete($param['roleId']);
		if (!$res) {
			return ['code'=>1,'msg'=>'角色删除失败'];
		} 
		return ['code'=>0,'msg'=>'角色删除成功'];
	}
}