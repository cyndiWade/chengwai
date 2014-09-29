<?php
/**
 * 后台订单管理
 */
class OrderAction extends AdminBaseAction {
  	
	//控制器说明
	private $module_name = '订单管理';
	
	//初始化数据库连接
	protected  $db = array(
		//新闻媒体订单
		'GeneralizeNewsOrder'=>'GeneralizeNewsOrder',	
		'GeneralizeNewsAccount'=>'GeneralizeNewsAccount',

		//微博推广单	
		'GeneralizeOrder'=>'GeneralizeOrder',
		//微博意向单
		'IntentionWeiboOrder'=>'IntentionWeiboOrder',
			
		//微信推广单
		'GeneralizeWeixinOrder'=>'GeneralizeWeixinOrder',
		//微信意向单表
		'IntentionWeixinOrder'=>'IntentionWeixinOrder',
			
		//订单日志表	
		'OrderLog'=>'OrderLog',

	);

	private $OrderLog;	

	/**
	 * 构造方法
	 */
	public function __construct() {
	
		parent::__construct();
	
		parent::global_tpl_view(array('module_name'=>$this->module_name));
		
		$this->_init_data();
	}
	
	
	private function _init_data () {
		$this->OrderLog = $this->db['OrderLog'];
	}
	
	
	
	//新闻媒体数据列表
	public function news_generalize () {
	
		$GeneralizeNewsOrder = $this->db['GeneralizeNewsOrder'];
		$where['status'] = 1;
		$list = $GeneralizeNewsOrder->get_order_list($where);
	
		$data['list'] = $list;
		parent::global_tpl_view( array(
			'action_name'=>'新闻媒体推广单',
			'title_name'=>'新闻媒体推广单',
			'add_name'=>'添加类别'	
		));
	
		parent::data_to_view($data);
		$this->display();
	}
	
	public function news_generalize_edit() {
		
		$order_id = $this->_get('id');
		$type = 1;
		$act = $this->_get('act');						//操作类型
		
		
		if ($act == 'update') {
			if ($this->isPost()) {
					
				//修改订单状态
				$this->db['GeneralizeNewsOrder']->create();
				$this->db['GeneralizeNewsOrder']->where(array('id'=>$order_id))->save();
				
				//创建日志
				$this->OrderLog->create();
				$is_insert = $this->OrderLog->add_order_log($this->oUser->id,$order_id,$type);
			}
			$order_log_list = $this->OrderLog->get_order_list(array('order_id'=>$order_id,'type'=>$type));
			
		}
		
		$data['order_log_list'] = $order_log_list;
		parent::data_to_view($data);
		$this->display();
	}
	
	
	
	
	//微博推广单列表
	public function weibo_generalize () {
	
		$GeneralizeOrder = $this->db['GeneralizeOrder'];
		$where['status'] = 1;
		$list = $GeneralizeOrder->get_order_list($where);
	
		$data['list'] = $list;
		parent::global_tpl_view( array(
				'action_name'=>'微博推广单',
				'title_name'=>'微博推广单',
				'add_name'=>'添加类别'
		));
	
		parent::data_to_view($data);
		$this->display();
	}
	
	//微博推广单编辑
	public function weibo_generalize_edit() {
		$order_id = $this->_get('id');
		$type = 2;
		$act = $this->_get('act');						//操作类型
		
		if ($act == 'update') {
			if ($this->isPost()) {
					
				//修改订单状态
				$this->db['GeneralizeOrder']->create();
				$this->db['GeneralizeOrder']->where(array('id'=>$order_id))->save();
				
				//创建日志
				$this->OrderLog->create();
				$is_insert = $this->OrderLog->add_order_log($this->oUser->id,$order_id,$type);
			}
			$order_log_list = $this->OrderLog->get_order_list(array('order_id'=>$order_id,'type'=>$type));
			
		}
		
		$data['order_log_list'] = $order_log_list;
		parent::data_to_view($data);
		$this->display();
	}
	
	
	
	
	//微博意向单列表
	public function weibo_intention () {
	
		$IntentionWeiboOrder = $this->db['IntentionWeiboOrder'];
		$where['status'] = 1;
		$list = $IntentionWeiboOrder->get_order_list($where);
	
		$data['list'] = $list;
		parent::global_tpl_view( array(
				'action_name'=>'微博意向单',
				'title_name'=>'微博意向单',
				'add_name'=>'添加类别'
		));
	
		parent::data_to_view($data);
		$this->display();
	}
	
	//微博推广单编辑
	public function weibo_intention_edit() {
		$order_id = $this->_get('id');
		$type = 3;
		$act = $this->_get('act');						//操作类型
		
		if ($act == 'update') {
			if ($this->isPost()) {
					
				//修改订单状态
				$this->db['IntentionWeiboOrder']->create();
				$this->db['IntentionWeiboOrder']->where(array('id'=>$order_id))->save();
				
				//创建日志
				$this->OrderLog->create();
				$is_insert = $this->OrderLog->add_order_log($this->oUser->id,$order_id,$type);
			}
			$order_log_list = $this->OrderLog->get_order_list(array('order_id'=>$order_id,'type'=>$type));
			
		}
		
		$data['order_log_list'] = $order_log_list;
		parent::data_to_view($data);
		$this->display();
	}
	
	
	

	//微信推广单列表
	public function weixin_generalize () {
	
		$GeneralizeWeixinOrder = $this->db['GeneralizeWeixinOrder'];
		$where['status'] = 1;
		$list = $GeneralizeWeixinOrder->get_order_list($where);
	
		$data['list'] = $list;
		parent::global_tpl_view( array(
				'action_name'=>'微信推广单',
				'title_name'=>'微信推广单',
				'add_name'=>'添加类别'
		));
	
		parent::data_to_view($data);
		$this->display();
	}
	//微信推广单编辑
	public function weixin_generalize_edit() {
		$order_id = $this->_get('id');
		$type = 4;
		$act = $this->_get('act');						//操作类型
		
		if ($act == 'update') {
			if ($this->isPost()) {
					
				//修改订单状态
				$this->db['GeneralizeWeixinOrder']->create();
				$this->db['GeneralizeWeixinOrder']->where(array('id'=>$order_id))->save();
				
				//创建日志
				$this->OrderLog->create();
				$is_insert = $this->OrderLog->add_order_log($this->oUser->id,$order_id,$type);
			}
			$order_log_list = $this->OrderLog->get_order_list(array('order_id'=>$order_id,'type'=>$type));
			
		}
		
		$data['order_log_list'] = $order_log_list;
		parent::data_to_view($data);
		$this->display();
	}
	
	
	
	//微信意向单
	public function weixin_intention () {
		$IntentionWeixinOrder = $this->db['IntentionWeixinOrder'];
		$where['status'] = 1;
		$list = $IntentionWeixinOrder->get_order_list($where);
		
		$data['list'] = $list;
		parent::global_tpl_view( array(
				'action_name'=>'微信意向单',
				'title_name'=>'微信意向单',
				'add_name'=>'添加类别'
		));
		
		parent::data_to_view($data);
		$this->display();
	}
	//微信推广单编辑
	public function weixin_intention_edit() {
		$order_id = $this->_get('id');
		$type = 5;
		$act = $this->_get('act');						//操作类型
		
		if ($act == 'update') {
			if ($this->isPost()) {
					
				//修改订单状态
				$this->db['IntentionWeixinOrder']->create();
				$this->db['IntentionWeixinOrder']->where(array('id'=>$order_id))->save();
				
				//创建日志
				$this->OrderLog->create();
				$is_insert = $this->OrderLog->add_order_log($this->oUser->id,$order_id,$type);
			}
			$order_log_list = $this->OrderLog->get_order_list(array('order_id'=>$order_id,'type'=>$type));
			
		}
		
		$data['order_log_list'] = $order_log_list;
		parent::data_to_view($data);
		$this->display();
	}
	
	
	
	
	
	

	
    
}