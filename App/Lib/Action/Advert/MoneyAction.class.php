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
	// 	充值订单 充值+UNIX前5位+用户ID+后五位
		$spnumber = 'CZ'.substr(time(),0,5).'U'.$this->oUser->id.'U'.substr(time(),5);
		parent::data_to_view(array(
				'spnumber'=>$spnumber,
				'spshow' => base64_encode(U('Advert/Money/index'))
		));
		$this->display();
	}
	
	
	//流水订单
	public function record () {
		$this->display();
	}

	public function okAlpay()
	{
		var_dump($_POST);
	}
    
}

?>