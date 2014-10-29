<?php

/**
 * 	项目---核心类
 *	 所有此项目分组的基础类，都必须继承此类
 */
class AppBaseAction extends GlobalParameterAction {
	
	/**
	 * 构造方法
	 */
	public function __construct() {
		//G('begin'); 							// 记录开始标记位（运行开始）
		
		parent::__construct();
		
		//初始化数据库连接
		$this->db_init();

		$this->global_system();		

	}
	
	private function global_system() {

		//系统基本属性
		$system_base_data = D('SystemBase')->where(array('id'=> C('WEB_SYSTEM.base_id')))->select();
		if ($system_base_data[0]['web_logo'] == true) {
			self::public_file_dir($system_base_data, 'web_logo', 'images/');
		} else {
			$system_base_data[0]['web_logo'] = '/App/Public/Index/images/logo.png';
		}
		$this->global_system = $system_base_data[0];
		
		$this->global_tpl_view(array(
			//网站公共的资源路径
			'Global_Resource_Path'=>C('LocalHost').'/'.APP_PATH.'Public/Global/'	
		));
	}
	
	
	//初始化DB连接
	private function db_init() {
		foreach ($this->db as $key=>$val) {
			if (empty($val)) continue;
			$this->db[$key] = D($val);
		}
		
	}
	

	/**
	 * 短信发送类
	 * @param String $telephone  电话号码
	 * @param String $msg			短信内容
	 * @return Array  						$result[status]：Boole发送状态    $result[info]：ARRAY短信发送后的详细信息 	$result[msg]：String提示内容
	 */
// 	protected function send_shp ($telephone,$msg) {
// 		//执行发送短信
// 		import("@.Tool.SHP");	//SHP短信发送类
// 		$SHP = new SHP(C('SHP.NAME'),C('SHP.PWD'));			//账号信息
// 		$send = $SHP->send($telephone,$msg);		//执行发送
// 		return $send;
// 	}
	protected function send_shp ($telephone,$msg) {
		$shp_type = C('SHP.TYPE');
		$shp_name = C('SHP.NAME');
		$shp_password = C('SHP.PWD');
		switch ($shp_type) {
			case 'SHP' :
				import("@.Tool.SHP");				//SHP短信发送类
				$SHP = new SHP($shp_name,$shp_password);			//账号信息
				$send = $SHP->send($telephone,$msg);		//执行发送
				break;
			case 'RD_SHP'	 :
				import("@.Tool.RD_SHP");		//RD_SHP短信发送类
				$SHP = new RD_SHP($shp_name,$shp_password);			//账号信息
				$send = $SHP->send($telephone,$msg);		//执行发送
				break;
			default:
				exit('illegal operation！');	
		}
		return $send;
	}
	
	
	/**
	 * 统一数据返回
	 * @param unknown_type $status
	 * @param unknown_type $msg
	 * @param unknown_type $data
	 */
	protected function callback($status, $msg = 'Yes!',$data = array(),$extend=array()) {
		$return = array(
				'status' => $status,
				'msg' => $msg,
				'data' => $data,
				'num' => count($data),
		);
		if (!empty($extend) && is_array($extend)) {
			foreach ($extend as $key=>$val) {
				$return[$key]  = $val;
			}
		}
		
		header('Content-Type:application/json;charset=utf-8');
	//	header("Content-type: text/xml;charset=utf-8");
		//header('charset=utf-8');	
		//die(json_encode($return));
		exit(JSON($return));
	}
	

	
	/**
	 * 组合图片外部访问地址
	 * @param Array $arr								//要组合地址的数组
	 * @param String Or Array	 $field			//组合的字段key  如：pic 或  array('pic','head')
	 * @param String $dir_type						//目录类型  如：images/
	 */
	protected function public_file_dir (Array &$arr,$field,$dir_type) {
		$public_file_dir =  C('PUBLIC_VISIT.domain').C('PUBLIC_VISIT.dir').$dir_type;			//域名、文件目录
		//递归
		if (is_array($field)) {
			for ($i=0;$i<count($field);$i++) {
				self::public_file_dir($arr,$field[$i],$dir_type);
			}
		} else {
			foreach ($arr AS $key=>$val) {
				if (empty($arr[$key][$field])) continue;
				$arr[$key][$field] = $public_file_dir.$val[$field];
			}
		}
	}
	
	
	/**
	 * 全局模板变量
	 */
	protected function global_tpl_view (Array $extend = array()) {
	
		if (is_array($extend)) {
			foreach ($extend as $key=>$val) {
				$this->global_tpl_view[$key] = $val;
			}
		}
			
		//写入模板
		$this->assign('global_tpl_view',$this->global_tpl_view);
	}
	
	
	/**
	 * 传出数据到view层
	 * @param Array $view_data
	 */
	protected function data_to_view(Array $view_data = array())
	{
		//添加数据
		if (is_array($view_data) && !empty($view_data)) {
	
			foreach ($view_data as $key => $val) {
				$this->view_data[$key] = $val;
			}
	
		} 
		//注入变量到视图层
		$this->assign('view_data',$this->view_data);
	}
	

	//设置域->分组下的session
	protected function set_session ($data) {
		$_SESSION[C('SESSION_DOMAIN')][GROUP_NAME] = $data;
	}
	
	//获取域->分组下的session
	protected function get_session ($key) {
		return $_SESSION[C('SESSION_DOMAIN')][GROUP_NAME][$key];
	}
	
	//删除域->分组下的session
	protected function del_session ($key) {
		if ($key == GROUP_NAME) {	//如果key和分组名相同，则删除此分组下所有数据
			unset($_SESSION[C('SESSION_DOMAIN')][GROUP_NAME]);
		} else {
			unset($_SESSION[C('SESSION_DOMAIN')][GROUP_NAME][$key]);
		}	
	}
	
	
	
	/**
	 * 初始化RBAC方法
	 */
	protected function init_rbac() {
		import("@.Tool.RBAC"); 	//权限控制类库
		/* 初始化数据 */
		$Combination = new stdClass();
	
		/* 数据表配置 */
		$Combination->table_prefix =  C('DB_PREFIX');
		$Combination->node_table = C('RBAC_NODE_TABLE');
		$Combination->group_table = C('RBAC_GROUP_TABLE');
		$Combination->group_node_table = C('RBAC_GROUP_NODE_TABLE');
		$Combination->group_user_table = C('RBAC_GROUP_USER_TABLE');
	
		/* 方法配置 */
		$Combination->group = GROUP_NAME;					//当前分组
		$Combination->module = MODULE_NAME;				//当前模块
		$Combination->action = ACTION_NAME;					//当前方法
		$Combination->not_auth_group = C('NOT_AUTH_GROUP');			//无需认证分组
		$Combination->not_auth_module = C('NOT_AUTH_MODULE');		//无需认证模块
		$Combination->not_auth_action = C('NOT_AUTH_ACTION');			//无需认证操作
	
		RBAC::init($Combination);		//初始化数据
	}
	
	
	//初始化用户数据
	protected function init_check($user_info) {
		//$this->init_rbac();
		//$this->is_check_rbac();
		if (C('USER_AUTH_ON') == true) {	//权限验证开启
				
			//当前的Action开启RBAC权限
			if ($this->is_check_rbac == true) {
	
				//当前Action里放行无需验证的方法
				if (in_array(ACTION_NAME,$this->not_check_fn) == true) {
					return array('status'=>true,'message'=>'放行，本方法无需验证');
				}
	
				if (empty($user_info)) {
					return array('status'=>false,'message'=>'未登陆，请先登陆');
				}
	
				/* 对于不是管理员的用户进行权限验证 */
				if (in_array($user_info->account,explode(',',C('ADMIN_AUTH_KEY')))) {
					return array('status'=>true,'message'=>'本账号无需验证');
				} else {
					//初始化rbac
					$this->init_rbac();
					/* RBAC权限验证 */
					$check_result = RBAC::check($user_info->id);
						
					return array('status'=>$check_result['status'],'message'=>$check_result['message']);
				}
	
			} else {
				return array('status'=>true,'message'=>'放行，本Action验证关闭');
			}
				
		} else {
			if ($this->is_check_rbac == true) {
				//当前Action里放行无需验证的方法
				if (in_array(ACTION_NAME,$this->not_check_fn) == true) {
					return array('status'=>true,'message'=>'放行，本方法无需验证');
				}
				
				if (empty($user_info)) {
					return array('status'=>false,'message'=>'未登陆，请先登陆');
				} 
				
				return array('status'=>true,'message'=>'放行,验证通过');
				
			} else {
				return array('status'=>true,'message'=>'放行，权限验证已关闭。');
			}
		}
	
	}
	
	
	/**
	 * 手动验证当前用户权限
	 * @param String $module		//验证模块名
	 * @param String $action			//验证分组名
	 */
	protected function chenk_user_rbac ($module,$action,$group = GROUP_NAME) {
		/* RBAC权限系统开启 */
		if (C('USER_AUTH_ON') == true) {
			$this->init_rbac();		//RBAC权限控制类库
				
			$assign = new stdClass();
			$assign->group = $group;									//当前分组
			$assign->module = $module;							//当前模块
			$assign->action = $action;								//当前方法
			$assign->table_prefix =  C('DB_PREFIX');			//表前缀
	
			$assign->not_auth_group = C('NOT_AUTH_GROUP');			//无需认证分组
			$assign->not_auth_module = C('NOT_AUTH_MODULE');		//无需认证模块
			$assign->not_auth_action = C('NOT_AUTH_ACTION');			//无需认证操作
			RBAC::init($assign);		//初始化数据
	
			/* 对于不是管理员的用户进行权限验证 */
			if (!in_array($this->oUser->account,explode(',',C('ADMIN_AUTH_KEY')))) {
				/* RBAC权限验证 */
				$check_result = RBAC::check($this->oUser->id);
				return array('status'=>$check_result['status'],'message'=>$check_result['message']);
			} else {
				return array('status'=>true,'message'=>'放行，管理员账号无需验证。');
			}
		} else {
			return array('status'=>true,'message'=>'放行，权限验证已关闭。');
		}
	}
	
	/**
	 * 微博同步这块数据处理 请在表数据增加好之后调用此方法 
	 * 传入该微博自增ID
	 */

	protected function weiboDataprocess($id)
	{
		//取得数据
		$AccountWeiboInfo = D('AccountWeibo')->where(array('id'=>$id))->find();
		if($AccountWeiboInfo!='')
		{
			//需要同步的数据
			$int_info['fansnumber'] = $grass_info['fans_num'] = $AccountWeiboInfo['fans_num'];
			$int_info['ck_price'] = $AccountWeiboInfo['ck_money'];
			$grass_info['yg_zhuanfa'] = $AccountWeiboInfo['yg_zhuanfa'];
			$grass_info['yg_zhifa'] = $AccountWeiboInfo['yg_zhifa'];
			$grass_info['rg_zhuanfa'] = $AccountWeiboInfo['rg_zhuanfa'];
			$grass_info['rg_zhifa'] = $AccountWeiboInfo['rg_zhifa'];
			$grass_info['recommend'] = $AccountWeiboInfo['recommended_status'];
			switch($AccountWeiboInfo['is_celebrity'])
			{
				//草根
				case 0:
					//判断索引表数据是否已经存在
					$GrassrootsWeibo = D('GrassrootsWeibo');
					$where = array('weibo_id'=>$id);
					$GrassrootsWeiboBool = $GrassrootsWeibo->where($where)->field('id')->find();
					if($GrassrootsWeiboBool!='')
					{
						//不为空就update
						$GrassrootsWeibo->where($where)->save($grass_info);
					}else{
						//否则就是新增
						$grass_info['weibo_id'] = $id;
						$GrassrootsWeibo->add($grass_info);
					}
				break;
				//名人
				case 1:
					$CeleprityindexWeibo = D('CeleprityindexWeibo');
					$where = array('weibo_id'=>$id);
					$CeleprityindexWeiboBool = $CeleprityindexWeibo->where($where)->field('id')->find();
					if($CeleprityindexWeiboBool!='')
					{
						//不为空就update
						$CeleprityindexWeibo->where($where)->save($int_info);
					}else{
						//否则就是新增
						$int_info['weibo_id'] = $id;
						$CeleprityindexWeibo->add($int_info);
					}
				break;
			}
		}
	}



	/**
	 * 微信同步这块数据处理	请在表数据增加好之后调用此方法
	 * 传入该微信自增ID
	 */

	protected function weixinDataprocess($id)
	{
		//取得数据
		$AccountWeixinInfo = D('AccountWeixin')->where(array('id'=>$id))->find();
		if($AccountWeixinInfo!='')
		{
			//需要同步的数据
			$cele_info['fansnumber'] = $grass_info['fans_number'] = $AccountWeixinInfo['fans_num'];
			$cele_info['ck_price'] = $AccountWeixinInfo['ck_money'];
			$grass_info['dtb_money'] = $AccountWeixinInfo['dtb_money'];
			$grass_info['dtwdyt_money'] = $AccountWeixinInfo['dtwdyt_money'];
			$grass_info['dtwdet_money'] = $AccountWeixinInfo['dtwdet_money'];
			$grass_info['dtwqtwz_money'] = $AccountWeixinInfo['dtwqtwz_money'];
			$grass_info['read_number'] = $AccountWeixinInfo['weekly_read_avg'];
			$grass_info['audience_man'] = $AccountWeixinInfo['male_precent'];
			$grass_info['audience_women'] = $AccountWeixinInfo['female_precent'];
			$grass_info['recommend'] = $AccountWeixinInfo['recommended_status'];
			switch($AccountWeixinInfo['is_celebrity'])
			{
				//草根
				case 0:
					//判断索引表数据是否已经存在
					$GrassrootsWeixin = D('GrassrootsWeixin');
					$where = array('weixin_id'=>$id);
					$GrassrootsWeixinBool = $GrassrootsWeixin->where($where)->field('id')->find();
					if($GrassrootsWeixinBool!='')
					{
						//不为空就update
						$GrassrootsWeixin->where($where)->save($grass_info);
					}else{
						//否则就是新增
						$grass_info['weixin_id'] = $id;
						$GrassrootsWeixin->add($grass_info);
					}
				break;
				//名人
				case 1:
					$CeleprityindexWeixin = D('CeleprityindexWeixin');
					$where = array('weixin_id'=>$id);
					$CeleprityindexWeixinBool = $CeleprityindexWeixin->where($where)->field('id')->find();
					if($CeleprityindexWeixinBool!='')
					{
						//为空就update
						$CeleprityindexWeixin->where($where)->save($cele_info);
					}else{
						//否则就是新增
						$cele_info['weixin_id'] = $id;
						$CeleprityindexWeixin->add($cele_info);
					}
				break;
			}
		}
	}



	/**
	 * 新闻同步这块数据处理	请在表数据增加好之后调用此方法
	 * 传入该新闻自增ID
	 */

	protected function newsDataprocess($id)
	{
		//取得数据
		$AccountNewsInfo = D('AccountNews')->where(array('id'=>$id))->find();
		if($AccountNewsInfo!='')
		{
			$new_info['area'] = $AccountNewsInfo['area_id'];
			$new_info['price'] = $AccountNewsInfo['money'];
			$new_info['type_of_portal'] = $AccountNewsInfo['pt_type'];
			$new_info['is_news'] = $AccountNewsInfo['url_type'];
			$new_info['links'] = $AccountNewsInfo['url_status'];
			//判断索引表数据是否已经存在
			$IndexNews = D('IndexNews');
			$where = array('news_id'=>$id);
			$IndexNewsBool = $IndexNews->where($where)->field('id')->find();
			if($IndexNewsBool!='')
			{
				//为空就update
				$IndexNews->where($where)->save($new_info);
			}else{
				//否则就是新增
				$new_info['news_id'] = $id;
				$IndexNews->add($new_info);
			}
		}
	}
	

	//更新SESSION的MONEY
	protected function updateMoney($id)
	{
		if($id)
		{
			$money = D('UserAdvertisement')->getMoney($id);
			$Users = D('Users')->where(array('id'=>array('eq',$id)))->field('account,nickname,type')->find();
			$db_data= array(
				'user_info'=>array(
					'id'=>$id,
					'account'=>$Users['account'],
					'nickname' => $Users['nickname'],
					'type' => $Users['type'],
					'money' => $money['money']
				)
			);
			$_SESSION[C('SESSION_DOMAIN')][GROUP_NAME] = $db_data;
		}
	}

	
	//敏感词过滤
	protected function banwordCheck($array)
	{
		$keywords = D('back_keywords')->getField('back_keywords',0);
		if($keywords!='')
		{
			foreach($array as $key=>$value)
			{
				foreach($keywords as $v)
				{
					if(is_numeric(stripos($value,$v)))
					{
						return array('keyword'=>$v,'name'=>$key);
					}
				}
			}
		}
	}
	
	
	
}


?>