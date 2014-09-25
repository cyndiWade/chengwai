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
		'GeneralizeAccount' => 'GeneralizeAccount',
		'GeneralizeOrder' => 'GeneralizeOrder',
		'GeneralizeFiles' => 'GeneralizeFiles'
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
	
	//添加推广 选择账号
	public function add_users()
	{

		if($this->isPost())
		{
			$this->db['GeneralizeAccount']->insertAll($_POST,$this->oUser->id);

		}
	}



	//添加推广 填写意向单信息
	public function add_extension()
	{
		if($this->isPost())
		{
			//获得新增数据ID
			$id = $this->db['GeneralizeOrder']->insertPost($_POST,$this->oUser->id);
			if($id!='')
			{
				$img_array = array();
				$contentTypeRetweet = $_FILES['contentTypeRetweet'];
				$upload_dir = C('UPLOAD_DIR');
				$dir = $upload_dir['web_dir'].$upload_dir['image'];
				$status_content = parent::upload_file($contentTypeRetweet,$dir,5120000);
				if($status_content['status']==true)
				{
					$img_array['contentTypeRetweet']['users_id'] = $this->oUser->id;
					$img_array['contentTypeRetweet']['generalize_order_id'] = $id;
					$img_array['contentTypeRetweet']['type'] = 1;
					$img_array['contentTypeRetweet']['url'] = $status_content['info'][0]['savename'];
				}
				$genuineFile = $_FILES['genuineFile'];
				$status_genuineFile = parent::upload_file($genuineFile,$dir,5120000);
				if($status_genuineFile['status']==true)
				{
					$img_array['genuineFile']['users_id'] = $this->oUser->id;
					$img_array['genuineFile']['generalize_order_id'] = $id;
					$img_array['genuineFile']['type'] = 2;
					$img_array['genuineFile']['url'] = $status_genuineFile['info'][0]['savename'];
				}
				$this->db['GeneralizeFiles']->insertImg($img_array);
				//根据ID跳转
				if($_POST['tfpt_type']==1)
				{
					//新浪草根
					$this->redirect('Advert/Weibo/caogen_weibo',array('pt_type'=>1,'order_id'=>$id));
				}else{
					//腾讯草根
					$this->redirect('Advert/Weibo/caogen_weibo',array('pt_type'=>2,'order_id'=>$id));
				}
			}else{
				parent::callback(C('STATUS_DATA_LOST'),'参数错误!');
			}
		}
	}

}

?>