<?php

/**
 * 媒体主表账号控制器
 */
class AccountAction extends MediaBaseAction {
	
	//每个类都要重写此变量
	protected  $is_check_rbac = true;		//是否需要RBAC登录验证
	
	protected  $not_check_fn = array('register');	//无需登录和验证rbac的方法名
	
	//控制器说明
	private $module_name = '我是说明';
	
	//和构造方法
	public function __construct() {
		parent::__construct();
		
		parent::global_tpl_view(array('module_name'=>$this->module_name));
	}
	

	//初始化数据库连接
	protected  $db = array(
			'CategoryTags'=>'CategoryTags',
	);
	
	
	//账号注册	
	public function register () {
		$this->display();	
	}
	
	public function register_accout() {
		if ($this->isPost()) {
			import("@.Tool.Validate");
			
			$account = $this->_post('account');					//用户账号
			$password = $this->_post('password');				//用户密码
			$password_check = $this->_post('password_check');	//二次用户密码
			$qq = $this->_post('qq');
			$iphone = $this->_post('iphone');
			$validateImage= $this->_post('validateImage');

			//短信验证模块
			//parent::check_verify($account,1);			//验证类型1为注册验证
			
			//数据过滤
			if (Validate::checkNull($account)) $this->error('账号不得为空');
			if (Validate::checkNull($password)) $this->error('密码不得为空');
			if (Validate::checkNull($qq)) $this->error('qq不得为空');
			if (Validate::checkNull($iphone)) $this->error('电话号码不得为空');
			if (Validate::checkNull($validateImage)) $this->error('验证码不得为空');
			
			if (!Validate::check_string_num($account)) $this->error('账号密码只能输入英文或数字');
			if (!Validate::checkEquals($password,$password_check)) $this->error('2次密码输入不一致');
			if (!Validate::checkQQ($qq)) $this->error('QQ号码格式错误');
			if (!Validate::checkPhone($iphone)) $this->error('手机号码格式错误');

			if (!Validate::checkEquals(md5($validateImage),$_SESSION['verify'])) $this->error('验证码错误！');
			
			//这里自定义设置session
			$db_data= array('user_info'=>'xxxxxxx');
			parent::set_session(array(
				'MediaAaccount' =>$db_data
			));
			
		} else {
			$this->error('非法访问！');
		}
	}
	
	
	
	/**
	 * 登陆验证
	 */
	public function check_login() {
	
		if ($this->isPost()) {
			$Users = D('Users');									//系统用户表模型
	
			import("@.Tool.Validate");							//验证类
			 
			$account = $_POST['account'];					//用户账号
			$password = $_POST['password'];				//用户密码
			 
			//数据过滤
			if (Validate::checkNull($account)) $this->error('账号不得为空');
			if (Validate::checkNull($password)) $this->error('密码不得为空');
			if (!Validate::check_string_num($account)) $this->error('账号密码只能输入英文或数字');
			 
			//读取用户数据
			$user_info = $Users->get_user_info(array('account'=>$account,'is_del'=>0));
			 
			//验证用户数据
			if (empty($user_info)) {
				$this->error('此用户不存在或被删除！');
			} else {
				//状态验证
				if ($user_info['status'] != C('ACCOUNT_STATUS_NUM.normal')) {
					$status_info = C('ACCOUNT_STATUS');
					$this->error($status_info[$user_info['status']]);
				}
				 
				//验证密码
				if (md5($password) != $user_info['password']) {
					$this->error('密码错误！');
				} else {
	
					$tmp_arr = array(
							'id' =>$user_info['id'],
							'account' => $user_info['account'],
							'nickname' => $user_info['nickname'],
							'type'=>$user_info['type'],
					);
				}
	
				//写入SESSION
				parent::set_session(array('user_info'=>$tmp_arr));
				//更新用户信息
				$Users->up_login_info($user_info['id']);
				//$this->redirect('/Admin/User/personal');
			}
		} else {
			//$this->redirect('/Admin/Login/login');
		}
	}
	
	
 //退出登陆
    public function logout () {
    	if (session_start()) {
    		parent::del_session('user_info');
    		//$this->success('退出成功',U(GROUP_NAME.'/Login/login'));
    	} 
    }
    
	
	
}
	



	

?>