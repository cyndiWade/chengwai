<?php

/**
 * 微信公众号
 */
class WeixinAction extends AdvertBaseAction {
	
	
	//每个类都要重写此变量
	protected  $is_check_rbac = false;		//是否需要RBAC登录验证
	
	protected  $not_check_fn = array();	//无需登录和验证rbac的方法名
	
	private $pt_type;	//平台类型
	
	private $big_type = 1;	//当前平台大分类
	
	private $caogen_Top_Tags_ParentId = 251;	//草根微信账号顶层ID
	
	private $celebrity_Top_Tags_ParentId = 445;	//名人分类顶层 
	
	private $now_classify_data;
	
	//控制器说明
	private $module_explain = '微信账号';
	
	//初始化数据库连接
	protected  $db = array(
			'CategoryTags'=>'CategoryTags',
			'Users' => 'Users',
			'AccountWeixin' => 'AccountWeixin',
			'BlackorcollectionWeixin' => 'BlackorcollectionWeixin',
			'GeneralizeWeixinOrder' => 'GeneralizeWeixinOrder'
	);
	
	//和构造方法
	public function __construct() {
		parent::__construct();
		
		$this->_init_data();
	}
	
	
	private function _init_data () {
		parent::global_tpl_view(array(
				'module_explain'=>$this->module_explain,
		));
		
		parent::big_type_urls($this->big_type);		//大分类URL
		
		//初始化URL
		parent::two_urls($this->big_type);			//微博二级分类URL
		
		//初始化URL
		//parent::weixin_urls();
	}
	
	
	//微信名人列表页
	public function celebrity_weixin () {
		$order_id = $this->_get('order_id') ;
		$account_ids = $this->_get('account_ids') ;
		$this->show_celebrity_category_tags();
		parent::data_to_view(array(
					
			//二级导航加样式
			'sidebar_two'=>array(
				0=>'select',//第一个加
			),
			'order_id'=>$order_id,
			'account_ids'=>$account_ids
				
		));
		$this->display();
	}
	
	
	//微信
	public function weixin() {

		//验证
		$order_id = $this->_get('order_id') ;
		$account_ids = $this->_get('account_ids') ;
		if (!empty($order_id)) {
			$order_info = $this->db['GeneralizeWeixinOrder']->get_OrderInfo_By_Id($order_id,$this->oUser->id);
			if ($order_info == false) {
				$this->redirect('Advert/WeixinOrder/add_generalize');
			}
		}

		$this->show_caogen_category_tags();
		parent::data_to_view(array(
			
			//二级导航加样式
			'sidebar_two'=>array(
				1=>'select',//第一个加
			),
				
			'order_id'=>$order_id,
			'account_ids'=>$account_ids
		));
		$this->display();
		
	}
	
	
	/**
	 * 草根导航分类数据
	 */
	private function show_caogen_category_tags () {
		$CategoryTags = $this->db['CategoryTags'];
		
		$tags_ids = C('Big_Nav_Class_Ids.weixin_caogen_tags_ids');

		$this->now_classify_data = $CategoryTags->get_classify_data($tags_ids['top_parent_id']);
	
		$data['cjfl'] = $this->now_classify_data[$tags_ids['cjfl']];	
		$data['zfjg_type'] = $this->now_classify_data[$tags_ids['zfjg_type']];
		$data['jg'] = $this->now_classify_data[$tags_ids['jg']];
		$data['dfmr_mt'] = $this->now_classify_data[$tags_ids['dfmr_mt']];
		
		$data['fans_num'] = $this->now_classify_data[$tags_ids['fans_num']];
		$data['sprz'] = $this->now_classify_data[$tags_ids['sprz']];	
		
		$data['zhsfrz'] = $this->now_classify_data[$tags_ids['zhsfrz']];
		$data['szxb'] = $this->now_classify_data[$tags_ids['szxb']];
		$data['fsrzsj'] = $this->now_classify_data[$tags_ids['fsrzsj']];
		$data['zpjyds'] = $this->now_classify_data[$tags_ids['zpjyds']];
		parent::data_to_view($data);
	}
	
	/**
	 * 名人导航分类数据
	 */
	private function show_celebrity_category_tags () {
		$CategoryTags = $this->db['CategoryTags'];
		
		$tags_ids = C('Big_Nav_Class_Ids.weixin_celebrity_tags_ids');
		
		$this->now_classify_data = $CategoryTags->get_classify_data($tags_ids['top_parent_id']);
	
		$data['mrzy'] = $this->now_classify_data[$tags_ids['mrzy']];	//名人职业
		$data['mtly'] = $this->now_classify_data[$tags_ids['mtly']];	//名人领域
		$data['ckbj_type'] = $this->now_classify_data[$tags_ids['ckbj_type']];	//参考报价类型
		$data['jg'] = $this->now_classify_data[$tags_ids['jg']];		//价格
		$data['dfmr_mt'] = $this->now_classify_data[$tags_ids['dfmr_mt']];		//地方名人媒体
		$data['xqbq'] = $this->now_classify_data[$tags_ids['xqbq']];	//兴趣标签
	
		$data['mr_mtlb'] = $this->now_classify_data[$tags_ids['mr_mtlb']];	//名人/媒体类别
		$data['phd'] = $this->now_classify_data[$tags_ids['phd']];	//配合度
		$data['mr_fans_num'] = $this->now_classify_data[$tags_ids['mr_fans_num']];	//名人粉丝数
		$data['zhyc'] = $this->now_classify_data[$tags_ids['zhyc']];	//是否支持原创
		$data['mrxb'] = $this->now_classify_data[$tags_ids['mrxb']];	//名人性别
	
		parent::data_to_view($data);
	}

    	
    //微信草根 或者 名人 接口
    public function get_weixin_list()
    {
    	//判断是名人还是草根AccountWeixin
		$is_celeprity = intval($_POST['is_celebrity']);
		if($is_celeprity==0)
		{
			$list_new = $this->db['AccountWeixin']->getPostcgArray($_POST,$this->oUser->id,$this->global_finance['weixin_proportion']);
			parent::callback(1,'获取成功所有',array('list'=>$list_new['list'],'count'=>$list_new['count'],'p'=>$list_new['p']));
		}else{
			$list_new = $this->db['AccountWeixin']->getPostmrArray($_POST,$this->oUser->id,$this->global_finance['weixin_proportion']);
			parent::callback(1,'获取成功所有',array('list'=>$list_new['list'],'count'=>$list_new['count'],'p'=>$list_new['p']));
		}
    }

    //微信拉黑或者收藏
    public function insert_weixin_borc()
    {
    	$act = $this->_post('action');
    	 
    	if ($act == 'add') {
	    	$bool = $this->db['BlackorcollectionWeixin']->insertBlackorcollection($_POST,$this->oUser->id);
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
    		$bool = $this->db['BlackorcollectionWeixin']->deleteBlackorcollection($_POST,$this->oUser->id);
    		if($bool)
    		{
    			parent::callback(1,'操作成功!','ok');
    		}else{
    			parent::callback(0,'操作成功','no');		
    		}
    	}
    }

    //确认支付
	public function insertPrice($order_id)
	{
		if($order_id!='')
		{
			$bool = $this->db['GeneralizeNewsAccount']->getAllUserPr($order_id,$this->oUser->id);
			if($bool)
			{
				parent::callback(C('STATUS_SUCCESS'),'支付成功!');
			}else{
				parent::callback(C('STATUS_UPDATE_DATA'),'支付失败,请稍后尝试!');
			}
		}
	}
    
	//导出CSV
	public function export_csv ()
	{
		$ids = $_REQUEST['ids'];
		$array = array('ids'=>$ids);
		$type = $_REQUEST['type'];	//0为草根 1是名人
		if($ids!='')
		{
			switch($type)
			{
				case 0;
					$data = $this->db['AccountWeixin']->getPostcgArray($array,$this->oUser->id,$this->global_finance['weixin_proportion']);
				break;
				case 1:
					$data = $this->db['AccountWeixin']->getPostmrArray($array,$this->oUser->id,$this->global_finance['weixin_proportion']);
				break;
			}
		}else{
			switch($type)
			{
				case 0;
					$ides = $this->db['AccountWeixin']->where(array('is_celeprity'=>0))->getField('id',0);
					$array = array('ids'=>implode(',',$ides));
					$data = $this->db['AccountWeixin']->getPostcgArray($array,$this->oUser->id,$this->global_finance['weixin_proportion']);
				break;
				case 1:
					$ides = $this->db['AccountWeixin']->where(array('is_celeprity'=>1))->getField('id',0);
					$array = array('ids'=>implode(',',$ides));
					$data = $this->db['AccountWeixin']->getPostmrArray($array,$this->oUser->id,$this->global_finance['weixin_proportion']);
				break;
			}
		}
		$new_array = array();
		switch($type)
		{

			case 0:
				$new_array[] = array('微信号','账号名','行业分类','粉丝量','单图文价格','多图文第一条','多图文第二条','多图文第3-N条','性别分布:男','性别分布:女','周订单数','月订单数');
				foreach($data['list'] as $value)
				{
					$lin_arr = array();
					$lin_arr[] = $value['bs_weixinhao'];
					$lin_arr[] = $value['bs_account_name'];
					$lin_arr[] = $value['pg_cjfl_explain'];
					$lin_arr[] = $value['bs_fans_num'] / 10000 . '万';
					$lin_arr[] = $value['bs_dtb_money'] . '元';
					$lin_arr[] = $value['bs_dtwdyt_money'] . '元';
					$lin_arr[] = $value['bs_dtwdet_money'] . '元';
					$lin_arr[] = $value['bs_dtwqtwz_money'] . '元';
					$lin_arr[] = $value['bs_male_precent'] .'%';
					$lin_arr[] = $value['bs_female_precent'] .'%';
					$lin_arr[] = $value['bs_week_order_num'];
					$lin_arr[] = $value['bs_month_order_nub'];
					$new_array[] = $lin_arr;
				}
			break;
			case 1;
				$new_array[] = array('微信号','账号名','职业','领域','粉丝量','参考价格','配合度','简介','周订单数','月订单数');
				foreach($data['list'] as $value)
				{
					$lin_arr = array();
					$lin_arr[] = $value['bs_weixinhao'];
					$lin_arr[] = $value['bs_account_name'];
					$lin_arr[] = $value['pg_occupation_explain'];
					$lin_arr[] = $value['pg_field_explain'];
					$lin_arr[] = $value['bs_fans_num'] / 10000 . '万';
					$lin_arr[] = $value['bs_ck_money']/ 10000 . '万';
					$lin_arr[] = $value['pg_phd_explain'];
					$lin_arr[] = $value['bs_introduction'];
					$lin_arr[] = $value['bs_week_order_num'];
					$lin_arr[] = $value['bs_month_order_nub'];
					$new_array[] = $lin_arr;
				}
			break;
		}
		//var_dump($new_array);
		create_excel('weixin',$new_array);
	}

}

?>