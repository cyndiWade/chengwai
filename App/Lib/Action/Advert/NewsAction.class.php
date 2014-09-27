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
			'FastindexWeibo' => 'FastindexWeibo',
			'IndexNews' => 'IndexNews'
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

		$this->show_news_category_tags();
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
	
	//推广活动订单
	public function generalize_activity() {
		parent::data_to_view(array(
				//二级导航属性
			'sidebar_two'=>array(2=>'select'),//第一个加依次类推
		));
		$this->display();
	}

	//提供新闻接口
	public function get_news_list()
	{
		//判断是名人还是草根
		$list_new = $this->db['IndexNews']->getPostArray($_POST,$this->oUser->id);
		parent::callback(1,'获取成功所有',array('list'=>$list_new['list'],'count'=>$list_new['count'],'p'=>$list_new['p']));
	}
	

	/**
	 * 新闻媒体导航分类
	 */
	private function show_news_category_tags () {
		$CategoryTags = $this->db['CategoryTags'];
	
		$tags_ids = C('Big_Nav_Class_Ids.xinwen_tags_ids');
	
		$this->now_classify_data = $CategoryTags->get_classify_data($tags_ids['top_parent_id']);

		$data['hyfl'] = $this->now_classify_data[$tags_ids['hyfl']];	
		$data['dqsx'] = $this->now_classify_data[$tags_ids['dqsx']];	
		$data['yhzq'] = $this->now_classify_data[$tags_ids['yhzq']];
		$data['jg'] = $this->now_classify_data[$tags_ids['jg']];	
		$data['mh_type'] = $this->now_classify_data[$tags_ids['mh_type']];		
		$data['sfxwy'] = $this->now_classify_data[$tags_ids['sfxwy']];	
		$data['dljzk'] = $this->now_classify_data[$tags_ids['dljzk']];		
	
		parent::data_to_view($data);
	}
	
}	

?>