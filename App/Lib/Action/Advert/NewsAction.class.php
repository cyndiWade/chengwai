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
			'GeneralizeNewsAccount' => 'GeneralizeNewsAccount',
			'GeneralizeNewsFiles'=>'GeneralizeNewsFiles',
			'Discss'	=>	'Discss'
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
		->order('id desc')->field('id,title,start_time,web_url,all_price,status')->select();
		
		$Order_Status = C('Order_Status');
		if ($list == true) {
			foreach ($list as $key=>$val) {
				$list[$key]['status_explain'] = $Order_Status[$val['status']]['explain'];
			}
		}
		
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
			//走先选择账号流程
			//$account_id = passport_decrypt(trim($_GET['account_ids']),'account_ids');
			$account_id = urldecode(trim($_GET['account_ids']));
			
			$id = $this->db['GeneralizeNewsOrder']->insertPost($_POST,$this->oUser->id);
			if($id!='')
			{
				if($account_id!='')
				{
					//组合数据
					$arr = array('order_id'=>$id,'account_ids'=>$account_id);
					$this->db['GeneralizeNewsAccount']->insertAll($arr,$this->oUser->id);
					parent::updateMoney($this->oUser->id);
					//$this->db['GeneralizeNewsOrder']->where(array('id'=>$id))->save(array('status'=>1));
					$this->redirect('Advert/News/generalize_activity');
				}else{
					$this->redirect('Advert/News/news_list',array('order_id'=>$id));
				}
			}
		}
	}

	//为推广单加上账号	原来的
	// public function add_people()
	// {

	// 	if($this->isPost())
	// 	{
	// 		if(intval($_POST['order_id']!=''))
	// 		{
	// 			$status = $this->db['GeneralizeNewsAccount']->insertAll($_POST,$this->oUser->id);
				
	// 			if ($status == true) {
					
	// 				//修改订单状态为1，平台审核的类型
	// 				$this->db['GeneralizeNewsOrder']->where(array('id'=>$_POST['order_id']))->save(array('status'=>1));
					
	// 				parent::callback(C('STATUS_SUCCESS'),'添加成功',array('go_to_url'=>U('Advert/News/generalize_activity')));
	// 			} else {
	// 				parent::callback(C('STATUS_UPDATE_DATA'),'改订单已有重复账号');
	// 			}
	// 		}else{
	// 			parent::callback(C('STATUS_SUCCESS'),'正在跳转...',array('go_to_url'=>U('Advert/News/add_generalize',array('account_ids'=>passport_encrypt($_POST['account_ids'],'account_ids')))));
	// 		}
	// 	}
	// }

	//新执行流程
	public function add_people()
	{

		if($this->isPost())
		{
			if(intval($_POST['order_id']!=''))
			{
				$status = $this->db['GeneralizeNewsAccount']->insertAll($_POST,$this->oUser->id);
				
				if ($status == true) {
					parent::updateMoney($this->oUser->id);
					parent::callback(C('STATUS_SUCCESS'),'下单成功!',array('go_to_url'=>U('Advert/News/generalize_activity')));
				} else {
					parent::callback(C('STATUS_UPDATE_DATA'),'下单失败,请检查余额！');
				}
			}else{
				//$account_ids = $passport_encrypt($_POST['account_ids'],'account_ids');
				$account_ids = urlencode($_POST['account_ids']);
				parent::callback(C('STATUS_SUCCESS'),'正在跳转...',array('go_to_url'=>U('Advert/News/add_generalize',array('account_ids'=>$account_ids))));
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
				parent::callback(C('STATUS_UPDATE_DATA'),'删除失败');
			break;
			case 3:
				parent::callback(C('STATUS_UPDATE_DATA'),'已审核通过，禁止删除');
			break;
		}
	}
	
	
	//订单详情
	public function generalize_detail () {
		$order_id = $this->_get('order_id');
		if (empty($order_id)) alertBack('非法操作！');
		
		//获取订单数据
		$GeneralizeNewsOrder = $this->db['GeneralizeNewsOrder'];
		$order_info = $GeneralizeNewsOrder->get_OrderInfo_By_Id($order_id,$this->oUser->id);
		
		if (empty($order_info)) alertBack('订单不存在！');
		
		//获取订单下的账号列表
		$GeneralizeNewsAccount = $this->db['GeneralizeNewsAccount'];
		$account_order_list = $GeneralizeNewsAccount->get_account_order($order_id);	
				
		//订单状态
		$ORDER_STATUS = C('Order_Status');
		$order_info['status_explain'] = $ORDER_STATUS[$order_info['status']]['explain'];

		//关联边订单状态
		$Account_Order_Status = C('Account_Order_Status');
		if ($account_order_list == true) {
			
			$extend_order_info['sum_money'] = 0;	//订单总价格
			$extend_order_info['jy_order_sum'] = 0;	//据单数
			
			foreach ($account_order_list as $key=>$val) {
				//关联表订单状态
				$account_order_list[$key]['g_status_explain'] = $Account_Order_Status[$val['g_audit_status']]['explain'];
				
				//是否显示确认按钮
				// if ($val['g_audit_status'] == $Account_Order_Status[6]['status']) {
				// 	$account_order_list[$key]['is_show_affirm_btn'] = true;
				// }
				$account_order_list[$key]['is_show_affirm_btn'] = $val['g_audit_status'];
				//统计订单总金额
				$extend_order_info['sum_money'] += $val['g_price'];
				
				$account_order_list[$key]['other'] = $Account_Order_Status[$val['g_audit_status']]['other'];

				//统计据单数
				if ($val['g_audit_status'] == $Account_Order_Status[4]['status']) {
					$extend_order_info['jy_order_sum'] += 1;
				}
			}
			
			//关联订单账号总数
			$extend_order_info['order_num'] = count($account_order_list);
			
			
			//根据订单状态决定是否显示订单支付按钮
			$is_show_order_btn = false;	
			if ($order_info['status'] == $ORDER_STATUS[2]['status']) {
				$is_show_order_btn = true;
			}
		}
		

		//获取订单下的关联账号列表
		parent::data_to_view(array(
			'sidebar_two'=>array(2=>'select',),//第一个加依次类推，//二级导航属性
			'order_info'=>$order_info,
			'account_order_list'=>$account_order_list,
			'extend_order_info'=>$extend_order_info,
			'is_show_order_btn' => $is_show_order_btn,	
			'order_id'=>$order_id
		));
		$this->display();
	}


	//支付
	public function zhifu()
	{
		$zhifu_id = intval($_POST['id']);
		$GeneralizeNewsAccount = $this->db['GeneralizeNewsAccount']->siteMoney($zhifu_id,$this->oUser->id);
		if($GeneralizeNewsAccount==true)
		{
			parent::updateMoney($this->oUser->id);
			parent::callback(C('STATUS_SUCCESS'),'支付成功!');
		}else{
			parent::callback(C('STATUS_UPDATE_DATA'),'支付失败,请检查余额!');
		}
	}
	
	
	//查看订单执行图
	public function look_perform_pic () {
		$order_id = $this->_get('order_id');
		$account_id = $this->_get('account_id');
	
		$type = 3;
		$where['generalize_order_id'] = $order_id;
		$where['account_id'] = $account_id;
		$where['type'] = $type;
		$result = $this->db['GeneralizeNewsFiles']->get_fiels_list($where);
		parent::public_file_dir($result,array('url'),'images/');
	
	
		parent::data_to_view(array(
				'sidebar_two'=>array(2=>'select',),//第一个加依次类推，//二级导航属性
				'list'=>$result
		));
	
		$this->display();
	}
	

	//确认支付
	public function insertPrice()
	{
		$small_order_id = $this->_post('id');
		if($small_order_id!='')
		{
			$bool = $this->db['GeneralizeNewsAccount']->getUserPr($small_order_id,$this->oUser->id);
			if($bool)
			{
				parent::callback(C('STATUS_SUCCESS'),'支付成功!');
			}else{
				parent::callback(C('STATUS_UPDATE_DATA'),'支付失败,请稍后尝试!');
			}
		}
	}


	//评论数据
	public function addPl()
	{
		$pinfen = $this->_post('pinfen');
		$pinlun = $this->_post('pinlun');
		$ddid = $this->_post('ddid');
		$name = $this->db['GeneralizeNewsAccount']->getNickname($ddid);
		$discss = $this->db['Discss'];
		$array = array('pinfen'=>$pinfen,'pinlun'=>$pinlun,'ddid'=>$ddid,'name'=>$name,'users_id'=>$this->oUser->id,'type'=>1,'times'=>time());
		$select = array('ddid'=>$ddid,'users_id'=>$this->oUser->id,'type'=>1);
		$count = $discss->where($select)->count();
		if($count==0)
		{
			$bool = $discss->add($array);
			if($bool)
			{
				parent::callback(C('STATUS_SUCCESS'),'评论成功!');
			}else{
				parent::callback(C('STATUS_UPDATE_DATA'),'评论失败!');
			}
		}else{
			parent::callback(C('STATUS_UPDATE_DATA'),'请勿重复评论!');
		}
	}
	
	//导出CSV
	public function export_csv()
	{
		$ids = $_REQUEST['ids'];
		$array = array('ids'=>$ids);
		if($ids!='')
		{
			$data = $this->db['AccountNews']->getPostArray($array,$this->oUser->id);
		}else{
			$ides = $this->db['AccountNews']->getField('id',0);
			$array = array('ids'=>implode(',',$ides));
			$data = $this->db['AccountNews']->getPostArray($array,$this->oUser->id);
		}
		$new_array = array();
		$new_array[] = array('媒体名称','价格','新闻源','地区','能否带文本链接','门户','案例地址','周订单数','月订单数');
		foreach($data['list'] as $value)
		{
			$lin_arr = array();
			$lin_arr[] = $value['bs_account_name'];
			$lin_arr[] = $value['bs_money'].'元';
			$lin_arr[] = $value['pg_news_explain'];
			$lin_arr[] = $value['pg_area_name'];
			$lin_arr[] = $value['pg_links_explain'];
			$lin_arr[] = $value['pg_type_of_portal_explain'];
			$lin_arr[] = $value['bs_url'];
			$lin_arr[] = $value['bs_week_order_num'];
			$lin_arr[] = $value['bs_month_order_nub'];
			$new_array[] = $lin_arr;
		}
		//var_dump($new_array);
		create_excel('news',$new_array);
	}
	
}	

?>