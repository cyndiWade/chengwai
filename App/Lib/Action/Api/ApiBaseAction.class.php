<?php

/**
 * Api接口--基础类
 */
class ApiBaseAction extends AppBaseAction {

	protected  $is_check_rbac = true;		//当前控制是否需要验证RBAC
	
	protected  $not_check_fn = array();	//登陆后无需登录验证方法
	
	protected $request;					//获取请求的数据
	
	//构造方法
	public function __construct() {
		
		parent:: __construct();			//重写父类构造方法
		
		//全局系统变量
		$this->global_system();
		
		//初始化用户数据
		$this->check_system_info();

	}
	
	private function global_system () {
		$this->request['user_key'] = $this->_post('user_key');		//身份验证的user_key
		//$this->request['user_key'] = "UWRSbwgxBWsHNFVhAGUFYgUxA2gEaFUxAjxbO1E0CWRRbAY1BSBXMVdlUngNYVc0";
		$this->request['verify'] = $this->_post('verify');					//短信验证码
		
		
	}
	
	private function check_system_info() {
		$check_result = $this->init_check($this->oUser);
		if ($check_result['status'] == false) parent::callback(C('STATUS_NOT_DATA'),$check_result['message']);
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
					return array('status'=>false,'message'=>'身份信息为空');
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
			return array('status'=>true,'message'=>'放行，权限验证已关闭。');
		}
	
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
	
	/**
	 * 解密客户端秘钥，获取用户数据
	 */
	private function deciphering_user_info() {
		//获取加密身份标示
		$identity_encryption = $this->request['user_key'];	
		
		//解密获取用户数据
		$decrypt = passport_decrypt($identity_encryption,C('UNLOCAKING_KEY'));	
		$user_info = explode(':',$decrypt);		
		$uid = $user_info[0];				//用户id
		$account = $user_info[1];		//用户账号
		$date = $user_info[2];			//账号时间

		//安全过滤
		if (count($user_info) < 3) $this->callback(C('STATUS_OTHER'),'身份验证失败');			
		if (countDays($date,date('Y-m-d'),1) >= 30 ) $this->callback(C('STATUS_NOT_LOGIN'),'登录已过期，请重新登录');		//钥匙过期时间为30天

		//去数据库获取用户数据
		$user_data = $this->db['Member']->field('id,account,nickname')->where(array('id'=>$uid,'status'=>0))->find();

		if ($user_data ==  false || $account != $user_data['account']) {
			parent::callback(C('STATUS_NOT_DATA'),'此用户不存在，或被禁用');
		} else {
			$this->oUser = (object) $user_data;	
		}

	}
	
		
	/**
	 * 上传文件
	 * @param Array    $file  $_FILES['pic']	  上传的数组
	 * @param String   $type   上传文件类型    pic为图片 	
	 * @return Array	  上传成功返回文件保存信息，失败返回错误信息
	 */
	protected function upload_file($file,$type,$dir) {
		import('@.ORG.Util.UploadFile');				//引入上传类
		
		$upload = new UploadFile();
		$upload->maxSize  = 3145728 ;			// 设置附件上传大小
		$upload->allowExts  = array('jpg', 'gif', 'png', 'jpeg');		// 上传文件的(后缀)（留空为不限制），，
		//上传保存
		$upload->savePath =  $dir;					// 设置附件上传目录
		$upload->autoSub = true;					// 是否使用子目录保存上传文件
		$upload->subType = 'date';					// 子目录创建方式，默认为hash，可以设置为hash或者date日期格式的文件夹名
		$upload->saveRule =  'uniqid';				// 上传文件的保存规则，必须是一个无需任何参数的函数名
			
		//执行上传
		$execute = $upload->uploadOne($file);
		//执行上传操作
		if(!$execute) {						// 上传错误提示错误信息
			//$upload->getErrorMsg();
			return false;
		}else{	//上传成功 获取上传文件信息
			return $execute;
		}
	}

	
	/**
	 * 短信验证模块
	 * @param String $telephone		//验证的手机号码
	 * @param Number $type				//验证类型：1为注册验证
	 */
	protected function check_verify ($telephone,$type) {
	
		$Verify = $this->db['Verify'];
		$verify_code = $this->request['verify'];		//短信验证码
	
		$shp_info = $Verify->seek_verify_data($telephone,$type);
	
		//手机验证码验证
		if (empty($shp_info)) {
			self::callback(C('STATUS_NOT_DATA'),'验证码不存在');
		} elseif ($verify_code != $shp_info['verify']) {
			self::callback(C('STATUS_OTHER'),'验证码错误');
		} elseif ($shp_info['expired'] - time() < 0 ) {
			self::callback(C('STATUS_OTHER'),'验证码已过期');
		}
		//把验证码状态变成已使用
		$Verify->save_verify_status($shp_info['id']);
	}
	
}


?>