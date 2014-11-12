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
		'AccountNews'=>'AccountNews',
		'AccountWeixin'=>'AccountWeixin',
		'AccountWeibo'=>'AccountWeibo',
		'Help'=>'Help'
	);
	
	private $links_explain = array(
		0=>'无',
		1=>'可带文本网址',
		2=>'不能带网址'	
	);
	private $type_of_portal_explain = array(
		0 => '所有',
		1 => '地方门户',
		2 => '行业门户',
		3 => '重点门户',
		4 => '其他'
	);

	
	//首页
	public function index() {

		//新闻推荐数据
		$news_recommended_list = $this->db['AccountNews']->get_news_account_list(array('a.recommended_status'=>1,'a.is_del'=>0));
		if ($news_recommended_list == true) {
			foreach ($news_recommended_list as $key=>$val) {
				$news_recommended_list[$key]['links_explain'] = $this->links_explain[$val['links']];
				$news_recommended_list[$key]['type_of_portal_explain'] = $this->type_of_portal_explain[$val['type_of_portal']];
			}
		}
		$data['news_recommended_list'] = $news_recommended_list;
	
		
		//微博推荐数据
		$weibo_recommended_list = $this->db['AccountWeibo']->get_weibo_account_list(array('a.recommended_status'=>1,'a.is_del'=>0));
		if ($weibo_recommended_list == true) {
			foreach ($weibo_recommended_list as $key=>$val) {
				$weibo_recommended_list[$key]['fans_num_format'] = ($val['fans_num'] / 10000).'万';
			}
		}
		$data['weibo_recommended_list'] = $weibo_recommended_list;
		
		
		//微信推荐资源
		$weixin_recommended_list = $this->db['AccountWeixin']->get_weixin_account_list(array('a.recommended_status'=>1,'a.is_del'=>0));
		if ($weixin_recommended_list == true) {
			foreach ($weixin_recommended_list as $key=>$val) {
				$weixin_recommended_list[$key]['fans_num_format'] = ($val['fans_num'] / 10000).'万';
			}
		}
		
		$data['weixin_recommended_list'] = $weixin_recommended_list;
		
		
	
		//获取网站公告、帮助信息
		$data['announcement'] =  $this->db['Help']->where(array('type'=>2,'parent_id'=>37,'is_del'=>0))->select();
		$data['help'] = $this->db['Help']->where(array('type'=>2,'parent_id'=>38,'is_del'=>0))->select();
		
		parent::data_to_view($data);
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