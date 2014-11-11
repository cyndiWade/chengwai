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
		
	private $now_classify_data = array();	//当前分类数据

	private $pt_type;	//平台类型
	
	private $big_type = 2;	//当前平台大分类
	
	private $pt_page_data;	//平台类型的数据
	
	//初始化数据库连接
	protected  $db = array(
			'CategoryTags'=>'CategoryTags',
			'Users' => 'Users',
			'AccountWeibo' => 'AccountWeibo',
			'BlackorcollectionWeibo' => 'BlackorcollectionWeibo',
			'GeneralizeOrder'=>'GeneralizeOrder',
			'IntentionWeiboOrder' => 'IntentionWeiboOrder'
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
		
		
		$this->pt_page_data = array(
			1 => array(
				'pt_type'=>1,
				'pt_name'=> '新浪'
			),
			2 => array(
				'pt_type'=>2,
				'pt_name'=> '腾讯'
			),
		);
		
		
		parent::data_to_view(array(
			'pt_info'=>$this->pt_page_data[$this->pt_type]
		));
		
		
	}
	
	private function check_url () {
		$check_type = false;
		foreach ($this->pt_page_data as $key=>$val) {
			if($val['pt_type'] == $this->pt_type) {
				$check_type = true;
				break;
			}
		}
		
		if ($check_type == false) {
			$this->success('非法操作！',U('/Advert/Weibo/celebrity_weibo',array('pt_type'=>1)));
			exit;
		}
	}
	

	//名人微博
	public function celebrity_weibo () {
		$this->check_url();
		
		//验证
		$order_id = $this->_get('order_id') ;
		if (!empty($order_id)) {
			$order_info = $this->db['IntentionWeiboOrder']->get_OrderInfo_By_Id($order_id,$this->oUser->id);
			if ($order_info == false) {
				$this->redirect('/Advert/WeiboOrder/add_intention');
			}
		}

		$this->show_celebrity_category_tags();
		if ($this->pt_type == 1) {
			$show_num = 0;
		} elseif ($this->pt_type == 2) {
			$show_num = 2;
		}
		parent::data_to_view(array(
			//二级导航属性
			'sidebar_two'=>array($show_num=>'select',),//第一个加依次类推
			'pt_type' => $this->pt_type,
			'order_id' => $order_id
				
		));
		$this->display();
	}
	
	
	//草根微博
	public function caogen_weibo() {
		$this->check_url();
		
		//验证
		$order_id = $this->_get('order_id') ;
		if (!empty($order_id)) {
			$order_info = $this->db['GeneralizeOrder']->get_OrderInfo_By_Id($order_id,$this->oUser->id);
			if ($order_info == false) {
				$this->redirect('/Advert/WeiboOrder/add_generalize');
			}
		}
 		
	
		//显示常见分类
		$this->show_caogen_category_tags();
		if ($this->pt_type == 1) {
			$show_num = 1;
		} elseif ($this->pt_type == 2) {
			$show_num = 3;
		}
		parent::data_to_view(array(
			//二级导航属性
			'sidebar_two'=>array($show_num=>'select'),//第一个加
			'pt_type' => $this->pt_type,
			'order_id' => $order_id
		));
		
		$this->display();
	}
	
	//草根控制器
	//获得草根 新浪,腾讯微博 列表数据 0
	public function get_grassroots_list()
	{
		//草根 新浪 1 或者 腾讯 2 AccountWeibo
		$pt_type = intval($_POST['pt_type']);
		if(!empty($pt_type))
		{
			$list_new = $this->db['AccountWeibo']->getPostcgArray($_POST,$pt_type,$this->oUser->id,$this->global_finance['weibo_proportion']);
			parent::callback(1,'获取成功所有',array('list'=>$list_new['list'],'count'=>$list_new['count'],'p'=>$list_new['p']));
		}
	}

	//名人控制器
	//获得名人 新浪 腾讯微博 数据列表 1
	public function get_celeprity_list()
	{
		//名人 新浪 1 或者 腾讯 2
		$pt_type = intval($_POST['pt_type']);
		if(!empty($pt_type))
		{
			$list_new = $this->db['AccountWeibo']->getPostmrArray($_POST,$pt_type,$this->oUser->id,$this->global_finance['weibo_proportion']);
			parent::callback(1,'获取成功所有',array('list'=>$list_new['list'],'count'=>$list_new['count'],'p'=>$list_new['p']));
		}
	}

	//拉黑
	public function insert_blackorcollection()
	{
		$act = $this->_post('action');
		
		if ($act == 'add') {
			$bool = $this->db['BlackorcollectionWeibo']->insertBlackorcollection($_POST,$this->oUser->id);
			if($bool)
			{
				if($_POST['or_type']==0)
				{
					parent::callback(1,'拉入黑名单成功!','ok');
				}else{
					parent::callback(1,'收藏成功!','ok');
				}
			}else{
				if($_POST['or_type']==0)
				{
					parent::callback(0,'拉入黑名单失败,数据已存在!','no');
				}else{
					parent::callback(0,'收藏失败,数据已存在!','no');
				}
			}
		} elseif ($act == 'del') {
			$bool = $this->db['BlackorcollectionWeibo']->deleteBlackorcollection($_POST,$this->oUser->id);
			if($bool)
			{
				parent::callback(1,'操作成功!','ok');
			}else{
				parent::callback(0,'操作成功','no');
			}
		}
		
		
	}


	/**
	 * 草根导航分类数据
	 */
	private function show_caogen_category_tags () {
		$CategoryTags = $this->db['CategoryTags'];
		
		$tags_ids = C('Big_Nav_Class_Ids.caogen_tags_ids');
	
		$this->now_classify_data = $CategoryTags->get_classify_data($tags_ids['top_parent_id']);
		
		$data['cjfl'] = $this->now_classify_data[$tags_ids['cjfl']];
		$data['jg'] = $this->now_classify_data[$tags_ids['jg']];
		$data['dfmr_mt'] = $this->now_classify_data[$tags_ids['dfmr_mt']];	
		$data['fans_num'] = $this->now_classify_data[$tags_ids['fans_num']];
		$data['fans_sex'] = $this->now_classify_data[$tags_ids['fans_sex']];
		$data['zfjg_type'] = $this->now_classify_data[$tags_ids['zfjg_type']];
		parent::data_to_view($data);
	}
	
	
	/**
	 * 名人导航分类数据
	 */
	private function show_celebrity_category_tags () {
		$CategoryTags = $this->db['CategoryTags'];
		
		$tags_ids = C('Big_Nav_Class_Ids.celebrity_tags_ids');

		$this->now_classify_data = $CategoryTags->get_classify_data($tags_ids['top_parent_id']);
		
		$data['mrzy'] = $this->now_classify_data[$tags_ids['mrzy']];	//名人职业
		$data['mtly'] = $this->now_classify_data[$tags_ids['mtly']];	//名人领域
		$data['ckbj_type'] = $this->now_classify_data[$tags_ids['ckbj_type']];	//参考报价类型
		$data['jg'] = $this->now_classify_data[$tags_ids['jg']];	//价格
		$data['dfmr_mt'] = $this->now_classify_data[$tags_ids['dfmr_mt']];		//地方名人媒体
		$data['xqbq'] = $this->now_classify_data[$tags_ids['xqbq']];	//兴趣标签
		
		$data['mr_mtlb'] = $this->now_classify_data[$tags_ids['mr_mtlb']];	//名人/媒体类别
		$data['phd'] = $this->now_classify_data[$tags_ids['phd']];	//配合度
		$data['mr_fans_num'] = $this->now_classify_data[$tags_ids['mr_fans_num']];	//名人粉丝数
		$data['zhyc'] = $this->now_classify_data[$tags_ids['zhyc']];	//是否支持原创
		
		parent::data_to_view($data);
	}
	
	

    //导出CSV
	public function export_csv()
	{
		$ids = $_REQUEST['ids'];
		$array = array('ids'=>$ids);
		$type = $_REQUEST['type'];
		$pt_type = $_REQUEST['pt_type'];
		if($ids!='')
		{
			switch($type)
			{
				case 0:
					$data = $this->db['AccountWeibo']->getPostcgArray($array,$pt_type,$this->oUser->id,$this->global_finance['weibo_proportion']);
				break;
				case 1:
					$data = $this->db['AccountWeibo']->getPostmrArray($array,$pt_type,$this->oUser->id,$this->global_finance['weibo_proportion']);
				break;
			}
		}else{
			switch($type)
			{
				case 0:
					$ides = $this->db['AccountWeibo']->where(array('is_celebrity'=>0,'pt_type'=>$pt_type))->getField('id',0);
					$array = array('ids'=>implode(',',$ides));
					$data = $this->db['AccountWeibo']->getPostcgArray($array,$pt_type,$this->oUser->id,$this->global_finance['weibo_proportion']);
				break;
				case 1:
					$ides = $this->db['AccountWeibo']->where(array('is_celebrity'=>1,'pt_type'=>$pt_type))->getField('id',0);
					$array = array('ids'=>implode(',',$ides));
					$data = $this->db['AccountWeibo']->getPostmrArray($array,$pt_type,$this->oUser->id,$this->global_finance['weibo_proportion']);
				break;
			}
		}
		$new_array = array();
		switch($type)
		{
			case 0:
				$new_array[] = array('账号名','粉丝量','硬广转发价','软广转发价','硬广直发价','软广直发价','周订单数','月订单数');
				foreach($data['list'] as $value)
				{
					$lin_arr = array();
					$lin_arr[] = $value['bs_account_name'];
					$lin_arr[] = $value['bs_fans_num'] / 10000 . '万';
					$lin_arr[] = $value['bs_yg_zhuanfa'] . '元';
					$lin_arr[] = $value['bs_rg_zhuanfa'] . '元';
					$lin_arr[] = $value['bs_yg_zhifa'] . '元';
					$lin_arr[] = $value['bs_rg_zhifa'] . '元';
					$lin_arr[] = $value['bs_week_order_num'];
					$lin_arr[] = $value['bs_month_order_nub'];
					$new_array[] = $lin_arr;
				}
			break;
			case 1;
				$new_array[] = array('账号名','职业','领域','粉丝量','参考价格','地区','配合度','简介','周订单数','月订单数');
				foreach($data['list'] as $value)
				{
					$lin_arr = array();
					$lin_arr[] = $value['bs_account_name'];
					$lin_arr[] = $value['pg_occupation_explain'];
					$lin_arr[] = $value['pg_field_explain'];
					$lin_arr[] = $value['bs_fans_num'] / 10000 . '万';
					$lin_arr[] = $value['bs_ck_money'] /10000 . '万';
					$lin_arr[] = $value['pg_cirymedia_explain'];
					$lin_arr[] = $value['pg_phd_explain'];
					$lin_arr[] = $value['bs_introduction'];
					$lin_arr[] = $value['bs_week_order_num'];
					$lin_arr[] = $value['bs_month_order_nub'];
					$new_array[] = $lin_arr;
				}
			break;
		}
		//var_dump($new_array);
		create_excel('news',$new_array);
	}

}

?>