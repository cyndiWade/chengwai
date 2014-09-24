<?php

/**
 * 微博订单管理
 */
class WeiboOrderAction extends AdvertBaseAction {
	
	
	//每个类都要重写此变量
	protected  $is_check_rbac = true;		//是否需要RBAC登录验证
	
	protected  $not_check_fn = array();	//无需登录和验证rbac的方法名
	
	//控制器说明
	private $module_explain = '微博订单';
		
	private $pt_type;	//平台类型
	
	private $big_type = 2;	//当前平台大分类
	

	
	//初始化数据库连接
	protected  $db = array(
		'GeneralizeOrder' => 'GeneralizeOrder'
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
	
	
	//添加推广单
	public function add_generalize () {
		
		parent::data_to_view(array(
				//二级导航属性
				'sidebar_two'=>array(4=>'select'),//第一个加依次类推
		));
		$this->display();
	}
	
	
	//推广活动订单
	public function generalize_activity() {
		parent::data_to_view(array(
				//二级导航属性
				'sidebar_two'=>array(5=>'select'),//第一个加依次类推
		));
		$this->display();
	}
	
	
	//添加意向单
	public function add_intention () {
		parent::data_to_view(array(
				//二级导航属性
				'sidebar_two'=>array(6=>'select'),//第一个加依次类推
		));
		$this->display();
	}

    
	//意向单列表
	public function intention_list() {
		parent::data_to_view(array(
				//二级导航属性
				'sidebar_two'=>array(6=>'select'),//第一个加依次类推
		));
		$this->display();
	}
	

	//添加推广
	public function add_extension()
	{
		if($this->isPost())
		{
			//获得新增数据ID
			$id = $this->db['GeneralizeOrder']->insertPost($_POST,$this->oUser->id);
			if($id!='')
			{
				$img_array = array();
				$file_one = $_FILES[''];
				$upload_dir = C('UPLOAD_DIR');
				$dir = $upload_dir['web_dir'].$upload_dir['image'];
				$status_one = parent::upload_file($file_one,$dir,5120000);
				if($status_one['status']==true)
				{

				}
				$file_two = $_FILES[''];
				$status_two = parent::upload_file($file_one,$dir,5120000);
				if($status_two['status']==true)
				{

				}
				
			}else{
				parent::callback(C('STATUS_DATA_LOST'),'参数错误!');
			}
		}
	}

}

?>