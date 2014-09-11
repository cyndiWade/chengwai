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


	//初始化数据库连接
	protected  $db = array(
			'CategoryTags'=>'CategoryTags',
			'Users' => 'Users',
			'Verify'=>'Verify',
			'User_media' => 'User_media'
	);
	
	//和构造方法
	public function __construct() {
		parent::__construct();
		parent::global_tpl_view(array('module_name'=>$this->module_name));
	}


	public function user_system () {
		echo '1';
	}
	
	
	//验证注册的短信的模拟方法
	public function check_phone ($phone) {
		$phone_nub = $phone['phone'];
		$type = $phone['type'];	//1表示查询媒体主的注册，2表示查询广告主的注册，
		$phone_vr = $phone['phone_vr'];	//手机上的验证码
		//执行验证
		$phone_check_info = parent::check_verify($phone_nub,$type,$phone_vr);
		//验证结果
		//if ($phone_check_info['status'] == false) echo $phone_check_info['msg'];
		if ($phone_check_info['status'] == false) return false;
	}
	
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
			$iphone = $this->_post('iphone');
			$phone_verify= $this->_post('phone_verify');
			$Users = $this->db['Users'];
			if (Validate::checkNull($account)) parent::callback(C('STATUS_OTHER'),'账号不得为空');
			if ($Users->account_is_have($account)!='') parent::callback(C('STATUS_OTHER'),'会员名已存在');
			if (Validate::checkNull($password)) parent::callback(C('STATUS_OTHER'),'密码不得为空');
			if (Validate::checkNull($phone_verify)) parent::callback(C('STATUS_OTHER'),'手机验证码不得为空');
			if (!Validate::check_string_num($account)) parent::callback(C('STATUS_OTHER'),'账号密码只能输入英文或数字');
			if (!Validate::checkEquals($password,$password_check)) parent::callback(C('STATUS_OTHER'),'2次密码输入不一致');
			if (!Validate::checkPhone($iphone)) parent::callback(C('STATUS_OTHER'),'手机号码格式错误');
			$User_media = $this->db['User_media'];
			if ($User_media->iphone_is_have($iphone)!='') parent::callback(C('STATUS_OTHER'),'手机号已存在');
			$phone = array('phone'=>$iphone,'type'=>C('ACCOUNT_TYPE.Media'),'phone_vr'=>$phone_verify);
			if($this->check_phone($phone)===false) parent::callback(C('STATUS_OTHER'),'手机验证码错误');
			$id = $Users->add_account($account,$password);
			if($id!='')
			{
				//这里自定义设置session
				$db_data= array('user_id'=>$id,'user_name'=>$account);
				$media = array('user_id'=>$id,'iphone'=>$iphone);
				$User_media->add_account_list($media);
				parent::set_session(array('user_info'=>$db_data));
				echo 'ok';exit;
			}else{
				echo 'no';exit;
			}
		} else {
			$this->error('非法访问！');
		}
	}
	

	/**
	 * 登陆验证
	 */
	public function check_login() {
	
		if ($this->isPost()) {
			$Users = $this->db['Users'];						//系统用户表模型
	
			import("@.Tool.Validate");							//验证类
			 
			$account = $_POST['account'];					//用户账号
			$password = $_POST['password'];					//用户密码
			 
			//数据过滤
			if (Validate::checkNull($account)){echo '账号不能为空!';exit;};
			if (Validate::checkNull($password)){echo '密码不能为空!';exit;};
			if (!Validate::check_string_num($account)){echo '账号密码只能输入英文或数字';exit;};
			 
			$user_type = C('ACCOUNT_TYPE.Media');
			//读取用户数据
			$user_info = $Users->get_user_info(array('account'=>$account,'type'=>$user_type,'is_del'=>0));

			//验证用户数据
			if (empty($user_info)) {
				echo '此用户不存在或被删除！';exit;
			} else {
				$status_info = C('ACCOUNT_STATUS');
				//状态验证
				if ($user_info['status'] != $status_info[0]['status']) {
					echo $status_info[$user_info['status']]['explain'];exit;
				}
				
				//验证密码
				if (md5($password) != $user_info['password']) {
					echo '密码错误！';exit;
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