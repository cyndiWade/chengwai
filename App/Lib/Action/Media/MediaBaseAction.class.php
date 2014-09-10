<?php

/**
 * 后台核心类--所有后台方法必须继承此类
 */
class MediaBaseAction extends AppBaseAction {

	
	protected  $is_check_rbac = true;		//当前控制是否需要验证RBAC
	
	protected  $log_not_check_fn = array();	//登陆后无需登录验证方法
	
	//构造方法
	public function __construct() {
		parent:: __construct();			//重写父类构造方法
		
		//全局系统变量
		$this->global_system();
		
		//初始化用户数据
		$this->check_system_info();

	}
	
	
	/**
	 * 全局系统用到的数据
	 */
	private function global_system () {
		$path = preg_replace("/[\\\+]/", "/",dirname($_SERVER['SCRIPT_NAME']));
		//全局模板变量
		parent::global_tpl_view(array(
				'button'=>array(
						'prve'=>C('PREV_URL')
				),
				'path'=>'http://'.$_SERVER['SERVER_NAME'].$path.''.'/Public/'.GROUP_NAME.'/',
				'group_name' =>GROUP_NAME
		));
	
	
		/* SESSION信息验证保存 */
		$session_userinfo = parent::get_session('user_info');
		if (!empty($session_userinfo)) {
			$this->oUser = (object) $session_userinfo;					//转换成对象
		}
	}
	
	
	//验证方法
	private function check_system_info () {
		$check_result = $this->init_check($this->oUser);
		if ($check_result['status'] == false) $this->error($check_result['message']);
	}
	
	/**
	 * 初始化RBAC方法
	 */
	private function init_rbac() {
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
	private function init_check($user_info) {
	
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
					$check_result = RBAC::check($user_info->account->id);
						
					return array('status'=>$check_result['status'],'message'=>$check_result['message']);
				}
	
			} else {
				return array('status'=>true,'message'=>'放行，本Action验证关闭');
			}
				
		} else {
			return array('status'=>true,'message'=>'放行，权限验证已关闭。');
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
	 * 上传文件
	 * @param Array    $file  $_FILES['pic']	  上传的数组
	 * @param String   $type   上传文件类型    pic为图片
	 * @return Array	  上传成功返回文件保存信息，失败返回错误信息
	 */
	protected function upload_file($file,$dir,$size= 3145728,$type=array('jpg', 'gif', 'png', 'jpeg')) {
		import('@.ORG.Util.UploadFile');				//引入上传类
	
		$upload = new UploadFile();
		$upload->maxSize  =  $size;					// 设置附件上传大小
		$upload->allowExts  = $type;				// 上传文件的(后缀)（留空为不限制），，
		//上传保存
		$upload->savePath =  $dir;					// 设置附件上传目录
		$upload->autoSub = true;					// 是否使用子目录保存上传文件
		$upload->subType = 'date';					// 子目录创建方式，默认为hash，可以设置为hash或者date日期格式的文件夹名
		$upload->saveRule =  'uniqid';				// 上传文件的保存规则，必须是一个无需任何参数的函数名

		//执行上传
		$execute = $upload->uploadOne($file);
		//执行上传操作
		if(!$execute) {						// 上传错误提示错误信息
			return array('status'=>false,'info'=>$upload->getErrorMsg());
		}else{	//上传成功 获取上传文件信息
			return array('status'=>true,'info'=>$execute);
		}
	}
    	
}


?>