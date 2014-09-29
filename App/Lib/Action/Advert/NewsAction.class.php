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
			'AccountNews' => 'AccountNews',
			'GeneralizeNewsOrder' => 'GeneralizeNewsOrder',
			'GeneralizeNewsAccount' => 'GeneralizeNewsAccount'
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
		//验证
		$order_id = $this->_get('order_id') ;
		if (!empty($order_id)) {
			$order_info = $this->db['GeneralizeNewsOrder']->get_OrderInfo_By_Id($order_id,$this->oUser->id);
			if ($order_info == false) {
				$this->redirect('Advert/News/add_generalize');
			}
		}

		
		$this->show_news_category_tags();
		
		parent::data_to_view(array(
			//二级导航属性
			'sidebar_two'=>array(0=>'select',),//第一个加依次类推
			'order_id' =>$order_id
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

		$number = $this->db['GeneralizeNewsOrder']->get_OrderInfo_num($this->oUser->id);
		$new_array = addsltrim($_REQUEST);
		$start_time = strtotime($new_array['start_time']);
		$end_time = strtotime($new_array['end_time']);
		//时间范围
		if($new_array['start_time']!='' && $new_array['end_time']=='')
		{
			$where['start_time'] = array('EGT',$start_time);
		}
		if($new_array['start_time']=='' && $new_array['end_time']!='')
		{
			$where['start_time'] = array('ELT',$end_time);
		}
		if($new_array['start_time']!='' && $new_array['end_time']!='')
		{
			$where['start_time'] = array('between',array($start_time,$end_time));
		}
		//活动名字
		if($new_array['search_name']!='')
		{
			$where['title'] = array('like','%'.$new_array['search_name'].'%');
		}
		import('ORG.Util.Page');
		$GeneralizeNewsOrder = D('GeneralizeNewsOrder');
		$where['users_id'] =  $this->oUser->id;
		$count      = $GeneralizeNewsOrder->where($where)->count();
		$Page       = new Page($count,10);
		$show       = $Page->show();
		$list = $GeneralizeNewsOrder->where($where)->limit($Page->firstRow.','.$Page->listRows)
		->order('id desc')->field('id,title,start_time,web_url,status')->select();
		parent::data_to_view(array(
				'page' => $show ,
				'list' => $list,
				'search_name' => $new_array['search_name'],
				'start_time' => $new_array['start_time'],
				'end_time' => $new_array['end_time'],
				'status_0' => $number[0],
				'status_1' => $number[1]
		));
		$this->display();
	}

	//提供新闻接口
	public function get_news_list()
	{
		//判断是名人还是草根
		$list_new = $this->db['AccountNews']->getPostArray($_POST,$this->oUser->id);
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

	//添加推广活动
	public function add_extension()
	{
		if($this->isPost())
		{
			$id = $this->db['GeneralizeNewsOrder']->insertPost($_POST,$this->oUser->id);
			if($id!='')
			{
				$this->redirect('Advert/News/news_list',array('order_id'=>$id));
			}
		}
	}

	//为推广单加上账号
	public function add_people()
	{

		if($this->isPost())
		{
			$status = $this->db['GeneralizeNewsAccount']->insertAll($_POST,$this->oUser->id);
			
			if ($status == true) {
				
				//修改订单状态为1，平台审核的类型
				$this->db['GeneralizeNewsOrder']->where(array('id'=>$_POST['order_id']))->save(array('status'=>1));
				
				parent::callback(C('STATUS_SUCCESS'),'添加成功',array('go_to_url'=>U('Advert/News/generalize_activity')));
			} else {
				parent::callback(C('STATUS_UPDATE_DATA'),'添加是失败');
			}
			
		}
	}


	//删除操作
	public function del_info()
	{
		$del_id = intval($_POST['id']);
		$GeneralizeNewsOrder = D('GeneralizeNewsOrder');
		$bool = $GeneralizeNewsOrder->del_info($del_id,$this->oUser->id);
		switch($bool)
		{
			case 1:
				parent::callback(C('STATUS_SUCCESS'),'删除成功');
			break;
			case 2:
				parent::callback(C('STATUS_SUCCESS'),'删除失败');
			break;
			case 3:
				parent::callback(C('STATUS_SUCCESS'),'已审核通过，禁止删除');
			break;
		}
	}
	
}	

?>