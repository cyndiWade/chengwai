<?php

/**
 * 媒体主表账号控制器
 */
class MediaAaccountAction extends HomeBaseAction {
	

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
	
	
	
 //退出登陆
    public function logout () {
    	if (session_start()) {
    		parent::del_session('MediaAaccount');
    		$this->success('退出成功',U(GROUP_NAME.'/Login/login'));
    	} 
    }
    
	
	
}
	



	

?>