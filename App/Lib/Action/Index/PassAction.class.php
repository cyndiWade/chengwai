<?php

/**
 * 找回密码
 */
class PassAction extends IndexBaseAction {
	
	
	//每个类都要重写此变量
	protected  $is_check_rbac = false;		//是否需要RBAC登录验证
	
	protected  $not_check_fn = array();	//无需登录和验证rbac的方法名
	
	//控制器说明
	private $module_explain = '';
	
	//和构造方法
	public function __construct() {
		parent::__construct();
		parent::global_tpl_view(array('module_explain'=>$this->module_explain));
		$this->_init_data_();
	}
	
	
	private function _init_data_ () {
		$this->two_urls();
	}
	
	
	public function index() {
		
		$this->display();
		
	}
}

?>