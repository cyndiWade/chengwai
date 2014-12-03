<?php

/**
 * 其他订单管理
 */
class OtherOrderAction extends AdvertBaseAction {
	
	
	//每个类都要重写此变量
	protected  $is_check_rbac = true;		//是否需要RBAC登录验证
	
	protected  $not_check_fn = array();		//无需登录和验证rbac的方法名
	
	//控制器说明
	private $module_explain = '其他订单';
		
	private $pt_type;	//平台类型
	
	private $big_type = 3;	//当前平台大分类
	

	
	//初始化数据库连接
	protected  $db = array(
		'ExportOrder'=>'ExportOrder'
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

	}
	
	
	public function export_order () {
		import('ORG.Util.Page');
		
		$where['users_id'] = $this->oUser->id;
		$count = $this->db['ExportOrder']->get_count($where);
		 
		$Page    = new Page($count,10);
		$show     = $Page->show();
		 
		$list = $this->db['ExportOrder']->get_list($where,$Page->firstRow.','.$Page->listRows);
		//$list = $Discss->where($map)->limit($Page->firstRow.','.$Page->listRows)->order('id desc')->select();
		//dump($list);
		$this->data_to_view(array(
				'member_sidebar_export_order_class'=>'class="on"',
				'page' => $show ,
				'list' => $list,
				'sidebar_order'=>array(5=>'select'),//第一个加依次类推
		));
		$this->display();
	}


	
}

?>