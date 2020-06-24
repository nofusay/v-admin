<?php 

namespace app\admin\controller;
use app\admin\controller\Auth;
use think\facade\Session;
use think\facade\Request;
use think\Db;

# 商品信息
class Good extends Auth
{
	public function list(){

		$param = Request::param();
		
		$pagenum = ($param['pagenum']-1)*$param['pagesize'];
		$pagesize = $param['pagesize'];

		# 总记录数据
		$total = Db::name('b_basic')->count('id');

		$rows = Db::query("SELECT b_basic.`id`,b_basic.`stage_id`,b_basic.`artnum`,b_basic.`gcolor` color,b_basic.`imgurl`,b_basic.`addtime`,b_basic.`onlinetime`,b_basic.`lsaystatus`,b_basic.`state`,p_cate.`name` cate FROM b_basic LEFT JOIN p_cate ON b_basic.`cate_id` = p_cate.`id` ORDER BY b_basic.`id` DESC LIMIT {$pagenum},{$pagesize}");

		# 查询 SKU 中所有不重复的 basic_id
		$pidArr = Db::query("SELECT DISTINCT(basic_id) FROM c_ddp");
		$pids = array_column($pidArr, 'basic_id');

        foreach ($rows as $key => &$row) {
        	$row['addtime'] = date('Y-m-d',$row['addtime']);
        	$row['onlinetime'] = $row['onlinetime'] ? date('Y-m-d',$row['onlinetime']) : '';
        	if (in_array($row['id'], $pids)) {
        		$row['hasChildren'] = true;
        	}

        	# 图片
        	if ($row['imgurl']) {
        		$imgArr = explode('\\', $row['imgurl']);
	        	$baseUrl = substr($_SERVER['SCRIPT_NAME'],0,strrpos($_SERVER['SCRIPT_NAME'],'/'));
	        	$row['img'] = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'].$baseUrl.'\/uploads\/'.$imgArr[0].'/t_'.$imgArr[1];
        	} else {
        		$row['img'] = 'https://ss0.bdstatic.com/70cFuHSh_Q1YnxGkpoWK1HF6hhy/it/u=1843684934,646428704&fm=15&gp=0.jpg';
        	}
        }
        
        return ['code'=>0,'msg'=>'获取商品成功','data'=>$rows,'total'=>$total];
	}

	# 子级SKU列表
	public function skuList(){
		$param = Request::get();

		# 验证参数
		$v_res = $this->validate($param,'app\admin\validate\Good.skuList');
		if ($v_res !== true) {
			return ['code'=>1, 'msg'=>$v_res];
		}
		
		$rows = Db::query("SELECT c_ddp.`id`,c_incom.`code` incomcode,c_incode.`code` incode,c_ddp.`color`,p_size.`name` size,c_ddp.`addtime`,c_ddp.`state` FROM c_ddp LEFT JOIN c_incom ON c_ddp.`id` = c_incom.`ddp_id` LEFT JOIN c_incode ON c_ddp.`incode_id` = c_incode.`id` LEFT JOIN p_size ON c_ddp.`size_id` = p_size.`id` WHERE c_ddp.`basic_id`={$param['basicId']}");

		foreach ($rows as $key => &$row) {
			$row['addtime'] = date('Y-m-d',$row['addtime']);
			$row['img'] = 'https://erp.thantrue.com/tt/public/static/goods/img/uploads/20200430/t_21745a54f4ead78f865ee0ee64eb9ceb.jpg';
		}

		return ['code'=>0,'msg'=>'获取SKU成功','data'=>$rows];
	}

	# 品类列表
	public function cateList(){
		
		$rows = Db::query("SELECT id,mcate_id,name FROM p_cate");
		$arr = $this->gettreeitems($rows);

		# 获取父级品类数据
		$prows = Db::name('p_mcate')->column('id,name');

		# 填充父级品类名称
		$cateList = [];
		foreach ($arr as $key => $val) {
			$val['name'] = $prows[$val['id']] ? $prows[$val['id']] : '';
			$cateList[] = $val;
		}

		# 获取材质、年份、品牌列表数据 
		$mrows = Db::name('p_mater')->select();
		$yrows = Db::name('p_wave')->select();
		$brows = Db::name('p_brand')->select();

		return [
			'code'		=> 0,
			'msg'		=> '获取品类列表成功',
			'cateList'	=> $cateList,
			'materList'	=> $mrows,
			'waveList'	=> $yrows,
			'brandList'	=> $brows
		];
	}

	/**
	 * [gettreeitems 加工品类数据二级]
	 * @param  [type] $rows [原品类数据]
	 * @return [type]       [array]
	 */
	private function gettreeitems($rows){

	    $tree = array();
	    foreach ($rows as $key => $row) {
    		$pid = $row['mcate_id'];
    		$tree[$pid]['id'] = $pid;
    		$tree[$pid]['children'][] = $row;
	    }
	    return $tree;
	}

	# 商品添加
	public function add(){
		$param = Request::post('params');

		# 验证参数
		$v_res = $this->validate($param,'app\admin\validate\Good.add');
		if ($v_res !== true) {
			return ['code'=>1, 'msg'=>$v_res];
		}

		# 获取品类对应编码
		$cateCode = Db::name('p_cate')->where('id',$param['cate_id'])->value('code');
		
		# 根据 cate_id 计算货号
		// $artnum = self::producNewArtnum($param);
		// if ($artnum['code']) {
		// 	return $artnum;
		// }
		
		// $param['artnum'] = $cateCode.$artnum;
		$param['artnum'] = rand(1000,9999);
		$param['addtime'] = time();

		$res = Db::name('b_basic')->insert($param);
		if ($res) {
			return ['code'=>0, 'msg'=>'添加商品成功'];
		}
		return ['code'=>1, 'msg'=>'添加商品失败'];
	}

	# 计算新的短编码
	public static function producNewArtnum( $param ){

		$cateCode = Db::name('p_cate')->where('id',$param['cate_id'])->value('code');
		$numRows = Db::query("SELECT artnum FROM b_basic WHERE artnum LIKE '%{$cateCode}%'");
		$numRow = array_column($numRows, 'artnum');
		foreach ($numRow as $key => &$val) {

			# 剪切货号 如 212121P1922 和 W311 两种类型判断
			if (strlen($val) === 11) {
				$val = intval(substr($val, 7, 3));
			}

			if (strlen($val) === 4) {
				$val = intval(substr($val, 1));
			}

			if (!$val) {
				unset($numRow[$key]);
			}
		}

		# 生成 1-999 的数组
		$numbers = range(1,999);

		$diffNum = array_diff($numbers, $numRow);
		if (empty($diffNum)) {
			return ['code'=>1, 'msg'=>'编码已经超支，无法生成新编码'];
		}
		$artnum = array_shift($diffNum);

		return $artnum;
	}

	# 图片上传
	public function upload(){

		$file = Request::file('file');
        $info = $file->validate(['size'=>10*1024*1024,'ext'=>'jpg,png,gif,jpeg'])->move('./uploads');
        if($info){
            // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
            $imgurl = $info->getSaveName();
            $imgarr = explode('\\', $imgurl);

            // 输出 42a79759f284b767dfcb2a0197904287.jpg
            $imgname = $info->getFilename(); 

            // 图片缩略
            $url = './uploads/'.$imgurl;
            $image = \think\Image::open($url);

            $simgurl = $imgarr['0'].'/t_'.$imgname;
            $image->thumb(300, 300)->save('./uploads/'.$simgurl);

            return ['code'=>0,'msg'=>'图片上传成功','imgurl'=>$imgurl];
        }else{
            // 上传失败获取错误信息
            return ['code'=>1,'data'=>$file->getError()];
        }
	}

	/**
	 * [moveImg 图片移除]
	 * @method post
	 * @param param[imgUrl]
	 * @return [type] [json]
	 */
	public function moveImg(){
		
		$param = Request::post('params');

		# 验证参数
		$v_res = $this->validate($param,'app\admin\validate\Good.moveImg');
		if ($v_res !== true) {
			return ['code'=>1, 'msg'=>$v_res];
		}

		$imgArr = explode('\\', $param['imgUrl']);
		$simgUrl = $imgArr[0].'/t_'.$imgArr[1];

		try {

			unlink('./uploads/'.$param['imgUrl']);
			unlink('./uploads/'.$simgUrl);
			return ['code'=>0, 'msg'=>'移除图片成功'];

		} catch (Exception $e) {
			
			return ['code'=>1, 'msg'=>'移除图片异常'];
		}
	}

	/**
	 * [goodDetail 商品明细]
	 * @method post
	 * @param param[type: 0(父级) 1(子级), id]
	 * @return [type] [json]
	 */
	public function goodDetail(){

		$param = Request::post('params');

		# 验证参数
		$v_res = $this->validate($param,'app\admin\validate\Good.goodDetail');
		if ($v_res !== true) {
			return ['code'=>1, 'msg'=>$v_res];
		}

		switch ($param['type']) {

			# 操作父级
			case 0:
				$res = self::getParentDetai($param['id']);
				return ['code'=>0, 'msg'=>'获取明细成功', 'data'=>$res];
				break;
			
			# 操作子级
			case 1:
				# code...
				break;

			default:
				return ['code'=>1, 'msg'=>'参数异常'];
				break;
		}
	}

	/**
	 * [getParentDetai 获取父级商品明细]
	 * @param  [type] $id [basic_id]
	 * @return [type]     [json]
	 */
	private static function getParentDetai($id){
		$row = Db::name('b_basic')
		->field('id,mater_id,cate_id,brand_id,wave_id,proname,gcolor,imgurl,state')
		->where('id',$id)->find();

		# 图片
    	if ($row['imgurl']) {
    		$imgArr = explode('\\', $row['imgurl']);
        	$baseUrl = substr($_SERVER['SCRIPT_NAME'],0,strrpos($_SERVER['SCRIPT_NAME'],'/'));
        	$row['img'] = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'].$baseUrl.'\/uploads\/'.$imgArr[0].'/t_'.$imgArr[1];
    	}

		return $row;
	}

	/**
	 * [edit 修改商品]
	 * @param  [type] $params [提交的表单] POST
	 * @return [type]         [json]
	 */
	public function edit(){

		$param = Request::post('params');

		# 验证参数
		$v_res = $this->validate($param,'app\admin\validate\Good.edit');
		if ($v_res !== true) {
			return ['code'=>1, 'msg'=>$v_res];
		}

		$id = $param['id'];

		# 判断字段是否正常，过滤非正常字段
		foreach ($param as $key => $val) {
			$fields = ['proname','mater_id','gcolor','wave_id','cate_id','brand_id','state','imgurl'];
			if (!in_array($key, $fields)) {
				unset($param[$key]);
			}
		}
		
		$res = Db::name('b_basic')->where('id', $id)->update($param);
		if (!$res) {
			return ['code'=>1, 'msg'=>'修改失败'];
		}
		return ['code'=>0, 'msg'=>'修改成功'];
	}

	/**
	 * [del 删除商品]
	 * @param  [type] $type [0: SPU  1: SKU]
	 * @param  [type] $id   [spuid || skuid]
	 * @return [type]       [json]
	 */
	public function del(){

		$param = Request::post('params');

		# 验证参数
		$v_res = $this->validate($param,'app\admin\validate\Good.del');
		if ($v_res !== true) {
			return ['code'=>1, 'msg'=>$v_res];
		}

		switch ($param['type']) {

			# 操作父级
			case 0:
				$res = self::delGoods($param['id']);
				return ['code'=>1, 'msg'=>'功能待续'];
				break;
			
			# 操作子级
			case 1:
				# code...
				break;

			default:
				return ['code'=>1, 'msg'=>'参数异常'];
				break;
		}
	}

	private static function delGoods($basic_id){

		# 查询图片地址
		

		# 启动事务
		Db::startTrans();
		try {

			# 删除SPU的商品数据
			


			# 删除SKU的商品数据



		    # 提交事务
		    Db::commit();

		} catch (\Exception $e) {

		    # 回滚事务
		    Db::rollback();
		}
	}
}