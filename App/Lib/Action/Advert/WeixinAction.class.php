<?php

/**
 * 微信公众号
 */
class WeixinAction extends AdvertBaseAction {
	
	
	//每个类都要重写此变量
	protected  $is_check_rbac = false;		//是否需要RBAC登录验证
	
	protected  $not_check_fn = array();	//无需登录和验证rbac的方法名
	
	private $pt_type;	//平台类型
	
	private $big_type = 1;	//当前平台大分类
	
	//控制器说明
	private $module_explain = '微信账号';
	
	//初始化数据库连接
	protected  $db = array(
			'CategoryTags'=>'CategoryTags',
			'Users' => 'Users',
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
		
		//初始化URL
		parent::two_urls($this->big_type);			//微博二级分类URL
		
		//初始化URL
		//parent::weixin_urls();
	}
	
	
	//微信名人列表页
	public function celebrity_weixin () {
		
		parent::data_to_view(array(
					
			//二级导航加样式
			'sidebar_two'=>array(
				0=>'select',//第一个加
			)
		));
		$this->display();
	}
	
	
	//微信
	public function weixin() {
		
		parent::data_to_view(array(
			
			//二级导航加样式
			'sidebar_two'=>array(
				1=>'select',//第一个加
			)
		));
		$this->display();
		
	}

    
}

?>