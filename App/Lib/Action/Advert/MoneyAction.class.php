<?php

/**
 * 充值页面
 */
class MoneyAction extends AdvertBaseAction {
	
	
	//每个类都要重写此变量
	protected  $is_check_rbac = false;		//是否需要RBAC登录验证
	
	protected  $not_check_fn = array();	//无需登录和验证rbac的方法名
	
	private $big_type = 4;
	
	
	private $module_explain = '充值页面页面';
	
	//初始化数据库连接
	protected  $db = array(
		
	);
	
	//和构造方法
	public function __construct() {
		parent::__construct();
		
		$this->_init_data();
	}
	
	
	private function _init_data () {
		parent::global_tpl_view(array(
				'module_explain'=>$this->module_explain,
		));
		
		parent::big_type_urls($this->big_type);		//大分类URL

	}
	
	//充值首页
	public function index () {
// 		
		$this->display();
	}
	
	
	//流水订单
	public function record () {
		$this->display();
	}

    
}

?>