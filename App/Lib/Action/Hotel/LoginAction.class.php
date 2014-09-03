<?php
class LoginAction extends HotelBaseAction{

         
	 public function login(){
	 	
	 	$this->display();
	 }
	 
	 
	 public function index(){
	 
	       if ($this->isPost()) {
    		$Users = D('Users');							//系统用户表模型
    		$StaffBase = D('StaffBase');					//员工基本信息模型表
    		
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
    			if ($user_info['status'] != 0) {
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
    						'type'=>$user_info['type'],
    				);
    			}
    				
    			//$_SESSION['hotle']['user_info'] = $tmp_arr;		//写入session
    			$_SESSION[C('SESSION_DOMAIN')][GROUP_NAME]['user_info'] = $tmp_arr;		//写入session
    			//更新用户信息
    			$Users->up_login_info($user_info['id']);
    			$this->redirect('/Hotel/Business/index');
    			
    		}
    	} else {
    	
    		$this->redirect('Login/login');
    	}
	 }

	 
	 
	 //退出登陆
	 public function logout () {
	 	if (session_start()) {
	 		unset($_SESSION[C('SESSION_DOMAIN')][GROUP_NAME]);
	 		//dump($_SESSION);
	 		$this->success('退出成功',U(GROUP_NAME.'/Login/login'));
	 	}
	 }

	 public function setPassWord() {
		if (session_start()) {
			if ($this->isPost()) {
				$Users = D('Users');
				$password = $_POST['password'];
				$newPassWord = $_POST['new_password'];
				$password_affirm = $_POST['password_affirm'];

				import("@.Tool.Validate");
				if (Validate::checkNull($password)) $this->error('密码不得为空！');
				if (Validate::checkNull($newPassWord)) $this->error('新密码不得为空！');
				if (!Validate::checkEquals($newPassWord,$password_affirm)) $this->error('重复密码不一致！');

				$id = $_SESSION[C('SESSION_DOMAIN')][GROUP_NAME]['user_info']['id'];  //获得session中用户id
				$user_info = $Users->get_user_info(array('id'=>$id,'is_del'=>0));	  //获得用户信息
				
				if(strcmp(md5($password),$user_info['password'])==0){
					if( $Users->modifi_user_password ($id,md5($newPassWord))){
						$this->success('修改成功',U(GROUP_NAME.'/Login/login'));
					}else{
						$this->error('修改失败');
					}
				}else{
					$this->error('密码错误');
				}
			}else{

			    $this->display();
			}
	 	} else {
    	
    		$this->redirect('Login/login');
    	}
	 }

}