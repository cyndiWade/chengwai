<?php

/**
 * 媒体主表账号控制器
 */
class AccountAction extends MediaBaseAction {
	
	//每个类都要重写此变量
	protected  $is_check_rbac = false;		//是否需要RBAC登录验证
	
	protected  $not_check_fn = array('register','check_login','login','logout');	//无需登录和验证rbac的方法名
	
	//控制器说明
	private $module_name = '媒体主';
	
	//媒体主类型
	private $type  = 1;


	//和构造方法
	public function __construct() {
		parent::__construct();
		
		parent::global_tpl_view(array('module_name'=>$this->module_name));
	}

	
	//初始化数据库连接
	protected  $db = array(
		'Users' => 'Users',
		'User_media' => 'User_media'
	);
	
	
	public function login () {
		$this->display();
	}
	
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
			$media['iphone'] = $iphone = $this->_post('iphone');
			$phone_verify= $this->_post('phone_verify');
			$Users = $this->db['Users'];
			$User_media = $this->db['User_media'];
			if (Validate::checkNull($account)){echo '账号不得为空';exit;}
			if (Validate::checkNull($password)){echo '密码不得为空';exit;}
			if (Validate::checkNull($phone_verify)){echo '手机验证码不得为空';exit;}
			if (!Validate::check_string_num($account)){echo '账号密码只能输入英文或数字';exit;}
			if (!Validate::checkEquals($password,$password_check)){echo '2次密码输入不一致';exit;}
			if (!Validate::checkPhone($iphone)){echo '手机号码格式错误';exit;}
			if($Users->account_is_have($account)!=''){echo '会员名已存在!';exit;}
			if($User_media->iphone_is_have($iphone)!=''){echo '手机号已存在!';exit;}
			// if (!Validate::checkEquals(md5($validateImage),$_SESSION['verify'])) $this->error('验证码错误！');
			$media['users_id'] = $id = $Users->add_account($account,$password);
			//这里自定义设置session
			$db_data= array('user_id'=>$id,'user_name'=>$account);
			$User_media->add_account_list($media);
			parent::set_session($db_data);echo 'ok';exit;
		} else {
			$this->error('非法访问！');
		}
	}
	

	/**
	 * 登陆验证
	 */
	public function check_login() {
	
		if ($this->isPost()) {
			$Users = $this->db['Users'];									//系统用户表模型
	
			import("@.Tool.Validate");							//验证类
			 
			$account = $_POST['account'];					//用户账号
			$password = $_POST['password'];				//用户密码
			 
			//数据过滤
			if (Validate::checkNull($account)) $this->error('账号不得为空');
			if (Validate::checkNull($password)) $this->error('密码不得为空');
			if (!Validate::check_string_num($account)) $this->error('账号密码只能输入英文或数字');
			 
			$user_type = C('ACCOUNT_TYPE.Media');
			//读取用户数据
			$user_info = $Users->get_user_info(array('account'=>$account,'type'=>$user_type,'is_del'=>0));

			//验证用户数据
			if (empty($user_info)) {
				$this->error('此用户不存在或被删除！');
			} else {
 				
				$status_info = C('ACCOUNT_STATUS');
				//状态验证
				if ($user_info['status'] != $status_info[0]['status']) {
					$this->error($status_info[$user_info['status']]['explain']);
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
				$this->redirect('/Media/Account/user_system');
			}
		} else {
			$this->redirect('/Media/Account/login');
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