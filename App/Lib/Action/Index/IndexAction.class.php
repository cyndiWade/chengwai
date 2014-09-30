<?php

/**
 * 首页
 */
class IndexAction extends IndexBaseAction {
	
	
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
	
	//初始化数据库连接
	protected  $db = array(
			
	);
	
	//首页
	public function index() {
		
		parent::data_to_view(array(
			//二级导航属性
			'sidebar_one'=>array(0=>'fir_cur',),//第一个加依次类推
		));
		$this->display();
	}
	
	
	//新闻
	public function news() {
		parent::data_to_view(array(
			//二级导航属性
			'sidebar_one'=>array(1=>'fir_cur',),//第一个加依次类推
		));
		$this->display();
	}
	
	
	//微信
	public function wechat() {
		parent::data_to_view(array(
				//二级导航属性
				'sidebar_one'=>array(2=>'fir_cur',),//第一个加依次类推
		));
		$this->display();
	}
	
	
	//微博
	public function weibo() {
		parent::data_to_view(array(
				//二级导航属性
				'sidebar_one'=>array(3=>'fir_cur',),//第一个加依次类推
		));
		$this->display();
	}
	
	
	//名人代言
	public function spokesperson() {
		parent::data_to_view(array(
				//二级导航属性
				'sidebar_one'=>array(4=>'fir_cur',),//第一个加依次类推
		));
		$this->display();
	}
	
	
	//客户案例
	public function kh_case() {
		parent::data_to_view(array(
				//二级导航属性
				'sidebar_one'=>array(5=>'fir_cur',),//第一个加依次类推
		));
		$this->display();
	}
	
	
	//大客户服务
	public function vip() {
		parent::data_to_view(array(
				//二级导航属性
				'sidebar_one'=>array(6=>'fir_cur',),//第一个加依次类推
		));
		$this->display();
	}
	
	
	//关于我们
	public function about_us() {
		parent::data_to_view(array(
				//二级导航属性
				'sidebar_one'=>array(7=>'fir_cur',),//第一个加依次类推
				'sidebar_three'=>array(0=>'select',),
		));
		$this->display();
	}
	
	
	public function contact (){
		parent::data_to_view(array(
			//二级导航属性
			'sidebar_one'=>array(7=>'fir_cur',),//第一个加依次类推
			'sidebar_three'=>array(3=>'select',),
		));
		$this->display();
	}
	
}

?>