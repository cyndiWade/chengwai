<?php

class PublicAction extends MediaBaseAction {
	
	//每个类都要重写此变量
	protected  $is_check_rbac = false;		//控制是否需要RBAC登录验证
	protected  $not_check_fn = array();		//无需登录和验证rbac的方法名
	
	//控制器说明
	private $module_name = '我是说明';
	
	//和构造方法
	public function __construct() {
		parent::__construct();
		
		parent::global_tpl_view(array('module_name'=>$this->module_name));
	}
	
	//输出验证码
	public function verify(){
		import('@.ORG.Util.Image');
		Image::buildImageVerify();
	}
}

?>