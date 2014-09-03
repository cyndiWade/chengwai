<?php
/**
 * 后台登陆控制器
 */
class LoginAction extends AdminBaseAction {
    
	//获取首页信息
	public function login(){
	
		if (!empty($this->oUser)) $this->redirect('/Admin/User/personal');
	
		$this->display();
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
    			if ($user_info['status'] != C('ACCOUNT_TYPE.ADMIN')) {
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
    				
    			//$_SESSION['zun']['user_info'] = $tmp_arr;		//写入session
    			$_SESSION[C('SESSION_DOMAIN')][GROUP_NAME]['user_info'] = $tmp_arr;
    			//更新用户信息
    			$Users->up_login_info($user_info['id']);
    			$this->redirect('/Admin/User/personal');
    		}
    	} else {
    		$this->redirect('/Admin/Login/login');
    	}
    }
    
    
    //退出登陆
    public function logout () {
    	if (session_start()) {
    		//session_destroy();
    		unset($_SESSION[C('SESSION_DOMAIN')][GROUP_NAME]);
    		$this->success('退出成功',U(GROUP_NAME.'/Login/login'));
    	} 
    }
    
    
    public function get_ip () {
		echo get_client_ip();
	}
	
	
    
}