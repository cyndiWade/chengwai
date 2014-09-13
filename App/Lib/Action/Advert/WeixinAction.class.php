<?php

/**
 * 微信公众号
 */
class WeixinAction extends AdvertBaseAction {
	
	
	//每个类都要重写此变量
	protected  $is_check_rbac = false;		//是否需要RBAC登录验证
	
	protected  $not_check_fn = array();	//无需登录和验证rbac的方法名
	
	//控制器说明
	private $module_explain = '微信账号';
	
	//和构造方法
	public function __construct() {
		parent::__construct();
		parent::global_tpl_view(array(
			'module_explain'=>$this->module_explain,
				
			//给导航加样式，wx表示给微信页面加样式
			'sidebar_one'=> array('wx'=>'select'),
		));
	
	}
	
	//初始化数据库连接
	protected  $db = array(
			'CategoryTags'=>'CategoryTags',
			'Users' => 'Users',
	);
	
	
	//微信
	public function weixin() {
		
		parent::data_to_view(array(
			
			//二级导航加样式
			'sidebar_two'=>array(
				0=>'select',//第一个加
			)
		));
		$this->display();
		
	}

    
}

?>