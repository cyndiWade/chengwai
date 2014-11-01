<?php
/**
 * 报表管理
 */
class StatementAction extends AdminBaseAction {
  	
	//控制器说明
	private $module_name = '报表管理';
	
	//初始化数据库连接
	protected  $db = array(
		//新闻媒体订单
		'GeneralizeNewsOrder'=>'GeneralizeNewsOrder',	

		//新闻推广单关联表
		'GeneralizeNewsAccount'=>'GeneralizeNewsAccount',

		//微博推广单	
		'GeneralizeOrder'=>'GeneralizeOrder',
		//微博推广单，订单关联表
		'GeneralizeAccount'=>'GeneralizeAccount',
			
		//微博意向单
		'IntentionWeiboOrder'=>'IntentionWeiboOrder',
		//微博意向单、订单关联边	
		'IntentionWeiboAccount'=>'IntentionWeiboAccount',
			
		//微信推广单
		'GeneralizeWeixinOrder'=>'GeneralizeWeixinOrder',
		//微信推广单，订单关联表
		'GeneralizeWeixinAccount'=>'GeneralizeWeixinAccount',	
			
		//微信意向单表
		'IntentionWeixinOrder'=>'IntentionWeixinOrder',
	);
	

	/**
	 * 构造方法
	 */
	public function __construct() {
	
		parent::__construct();
	
		parent::global_tpl_view(array('module_name'=>$this->module_name));

	}
	
	
	
	//数据列表
	public function index () {

		if ($this->isPost()) {
			$type = $this->_post('type');
			$this->create_table($type);
		}
		
		parent::data_to_view();
		parent::global_tpl_view( array(
				'action_name'=>'基本设置',
				'title_name' =>'基本设置'
		));
		$this->display();
	}
	
	
	private function create_table($type) {
		
		$where = array();
		
		if ($type == 1) {
		
			$list = $this->db['GeneralizeNewsOrder']->get_order_list($where);
			
 			$Arr_result = format_array($list,array(
 				'id','title','start_time','create_time','order_user_account','all_price','account_num','status_explain'
 			));
			//$Arr_title = array('订单ID','标题','预计执行时间','创建日期','客户账号','订单总额','媒体数量','状态');
			
			//array_unshift($Arr_result,$Arr_title);
			
			create_excel('baobiao',$Arr_result);
			
		} elseif ($type == 2) {
		
			$list = $this->db['GeneralizeOrder']->get_order_list($where);
			
 			$Arr_result = format_array($list,array(
 				'id','hd_name','start_time','create_time','order_user_account','all_price','account_num','status_explain'
 			));
			//$Arr_title = array('订单ID','标题','预计执行时间','创建日期','客户账号','订单总额','媒体数量','状态');
			
			//array_unshift($Arr_result,$Arr_title);
			
			create_excel('baobiao',$Arr_result);
			
		} elseif ($type == 3) {
			
			$list = $this->db['IntentionWeiboOrder']->get_order_list($where);
				
			$Arr_result = format_array($list,array(
					'id','yxd_name','start_time','create_time','order_user_account','all_price','account_num','status_explain'
			));
			//$Arr_title = array('订单ID','标题','预计执行时间','创建日期','客户账号','订单总额','媒体数量','状态');
				
			//array_unshift($Arr_result,$Arr_title);
				
			create_excel('baobiao',$Arr_result);
		
		} elseif ($type == 4) {
			
			$list = $this->db['GeneralizeWeixinOrder']->get_order_list($where);
				
			$Arr_result = format_array($list,array(
					'id','title','start_time','create_time','order_user_account','all_price','account_num','status_explain'
			));
			//$Arr_title = array('订单ID','标题','预计执行时间','创建日期','客户账号','订单总额','媒体数量','状态');
				
			//array_unshift($Arr_result,$Arr_title);
				
			create_excel('baobiao',$Arr_result);
		
		} elseif ($type == 5) {
			
			$list = $this->db['IntentionWeixinOrder']->get_order_list($where);
				
			$Arr_result = format_array($list,array(
					'id','title','start_time','create_time','order_user_account','all_price','account_num','status_explain'
			));
			//$Arr_title = array('订单ID','标题','预计执行时间','创建日期','客户账号','订单总额','媒体数量','状态');
				
			//array_unshift($Arr_result,$Arr_title);
				
			create_excel('baobiao',$Arr_result);
		
		}
		

		exit;
	}
	


}