<?php

/**
 * 新闻媒体
 */
class NewsAction extends AdvertBaseAction {
	
	
	//每个类都要重写此变量
	protected  $is_check_rbac = true;		//是否需要RBAC登录验证
	
	protected  $not_check_fn = array();	//无需登录和验证rbac的方法名
	
	//控制器说明
	private $module_explain = '新闻媒体账号';
	
	private $top_tags_parentId = 293;
	
	private $weibo_search_classify_data = array();

	private $pt_type;	//平台类型
	
	private $big_type = 0;	//当前平台大分类
	
	
	//初始化数据库连接
	protected  $db = array(
			'CategoryTags'=>'CategoryTags',
			'Users' => 'Users',
			'AccountWeibo' => 'AccountWeibo',
			'FastindexWeibo' => 'FastindexWeibo'
	);
	
	//和构造方法
	public function __construct() {
		parent::__construct();
		
		$this->_init_data();
	}
	
	//初始化需要的数据
	private function _init_data () {
		parent::global_tpl_view(array(
			'module_explain'=>$this->module_explain,
		));
		
		parent::big_type_urls($this->big_type);		//大分类URL
		
		//初始化URL
		parent::two_urls($this->big_type);			//微博二级分类URL
		
		$this->pt_type = $this->_get('pt_type');	//平台类型
		

	}
	

	//新闻媒体列表
	public function news_list () {

		parent::data_to_view(array(
			//二级导航属性
			'sidebar_two'=>array(0=>'select',),//第一个加依次类推
		));
		$this->display();
	}
	
	
	//添加推广单
	public function add_generalize() {
		
		parent::data_to_view(array(
				//二级导航属性
				'sidebar_two'=>array(1=>'select',),//第一个加依次类推
		));
		$this->display();	
	}
	
}	

?>