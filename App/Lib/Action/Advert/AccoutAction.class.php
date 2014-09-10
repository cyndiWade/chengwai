<?php

/**
 * 广告主账号控制器
 */
class AccoutAction extends AdvertBaseAction {
	
	
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
	
}

?>