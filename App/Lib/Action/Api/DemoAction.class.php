<?php

/**
 * 测试
 */
class DemoAction extends ApiBaseAction {
	
	protected  $is_check_rbac = false;		//当前控制是否需要验证RBAC
	
	protected  $not_check_fn = array();	//登陆后无需登录验证方法
	
	//和构造方法
	public function __construct() {
		parent::__construct();
	
	}
	
	
	
	//初始化数据库连接
	protected  $db = array(
		'Verify'=>'Verify',
	);
	
	public function index() {
		$this->request['verify'] = 112446;
		$Verify = $this->db['Verify'];
		$info = $Verify->seek_verify_data(13761951734,1);
		parent::check_verify(13761951734,1);
	}
	
	
	
}

?>