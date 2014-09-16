<?php

/**
 * 微博账号
 */
class WeiboAction extends AdvertBaseAction {
	
	
	//每个类都要重写此变量
	protected  $is_check_rbac = true;		//是否需要RBAC登录验证
	
	protected  $not_check_fn = array();	//无需登录和验证rbac的方法名
	
	//控制器说明
	private $module_explain = '微博账号';
	
	private $top_tags_parentId = 293;
	
	private $weibo_search_classify_data = array();

	private $pt_type;	//平台类型
	
	private $big_type = 2;	//当前平台大分类
	
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
	

	//名人微博
	public function celebrity_weibo () {
		
		if ($this->pt_type == 1) {
			$show_num = 0;
		} elseif ($this->pt_type == 2) {
			$show_num = 2;
		}
		parent::data_to_view(array(
			//二级导航属性
			'sidebar_two'=>array($show_num=>'select',),//第一个加依次类推
			
				
		));
		$this->display();
	}
	
	
	//草根微博
	public function caogen_weibo() {

		//显示常见分类
		$this->show_category_tags();
		
		if ($this->pt_type == 1) {
			$show_num = 1;
		} elseif ($this->pt_type == 2) {
			$show_num = 3;
		}
		parent::data_to_view(array(
			//二级导航属性
			'sidebar_two'=>array($show_num=>'select'),//第一个加
			
		));
		$list = $this->get_new_list();
		parent::data_to_view($list);
		$this->display();
	}
	
	//获得列表数据
	public function get_new_list()
	{
		if($this->isPost()){
			$this->db['FastindexWeibo']->getPostArray($_POST);
		}else{
			return $this->db['AccountWeibo']->getListTen();
		}
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