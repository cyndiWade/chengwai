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
	
	private $top_tags_parentId = 293;
	
	private $weibo_search_classify_data = array();

	
	
	//和构造方法
	public function __construct() {
		parent::__construct();
		
		parent::global_tpl_view(array(
			'module_explain'=>$this->module_explain,
				
			//给微博页面加样式
			'sidebar_one'=> array('wb'=>'select')	
		));
		
		//URL
		parent::data_to_view(array(
			'sidebar_two_url'=> array(
				0 => U('/Advert/Weibo/sina_celebrity'),
				1 => U('/Advert/Weibo/sina')
			),//URL
		));
		
		
	}
	
	//初始化数据库连接
	protected  $db = array(
			'CategoryTags'=>'CategoryTags',
			'Users' => 'Users',
	);
	
	
	//新浪名人微博
	public function sina_celebrity () {
		
		parent::data_to_view(array(
			//二级导航属性
			'sidebar_two'=>array(0=>'select',),//第一个加依次类推
			
		));
		$this->display();
	}
	
	
	//新浪微博
	public function sina() {

		//显示常见分类
		$this->show_category_tags();
		
		parent::data_to_view(array(
			//二级导航属性
			'sidebar_two'=>array(1=>'select'),//第一个加
			
		));
		$this->display();
		
	}
	
	
	
	
	
	
	//显示常见分类
	private function show_category_tags () {
		$this->get_category_tags($this->top_tags_parentId);
		
		
		$data['cjfl'] = $this->cjfl;
		$data['jg'] = $this->jg;
		$data['fans_num'] = $this->fans_num;
		$data['fans_sex'] = $this->fans_sex;
		parent::data_to_view($data);
	}
	
	/**
	 * 获取标签类别表
	 */
	private function get_category_tags ($parent_id) {
		$CategoryTags = $this->db['CategoryTags'];
		$this->weibo_search_classify_data = $CategoryTags->get_classify_data($parent_id);
		$this->cjfl = $this->weibo_search_classify_data[295];
		$this->jg = $this->weibo_search_classify_data[296];
		$this->fans_num = $this->weibo_search_classify_data[297];
		$this->fans_sex = $this->weibo_search_classify_data[298];
	}

    
}

?>