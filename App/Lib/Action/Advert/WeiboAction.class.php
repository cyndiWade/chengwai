<?php

/**
 * 微博账号
 */
class WeiboAction extends AdvertBaseAction {
	
	
	//每个类都要重写此变量
	protected  $is_check_rbac = false;		//是否需要RBAC登录验证
	
	protected  $not_check_fn = array();	//无需登录和验证rbac的方法名
	
	//控制器说明
	private $module_explain = '微博账号';
	

	
	//和构造方法
	public function __construct() {
		parent::__construct();
		
		parent::global_tpl_view(array(
			'module_explain'=>$this->module_explain,
				
			//给微博页面加样式
			'sidebar_one'=> array('wb'=>'select')	
		));
	
	}
	
	//初始化数据库连接
	protected  $db = array(
			'CategoryTags'=>'CategoryTags',
			'Users' => 'Users',
	);
	
	
	//新浪微博
	public function sina() {
		
		$this->get_category_tags(293);
		
		parent::data_to_view(array(
			//二级导航加样式
			'sidebar_two'=>array(
				0=>'select',//第一个加
			)
		));
		$this->display();
		
	}
	
	
	/**
	 * 获取标签类别表
	 */
	private function get_category_tags ($parent_id) {
		$CategoryTags = $this->db['CategoryTags'];
		//$CategoryTags->seek_parent_list($parent_id);
		($CategoryTags->get_classify_data($parent_id));
		//exit;
	}

    
}

?>