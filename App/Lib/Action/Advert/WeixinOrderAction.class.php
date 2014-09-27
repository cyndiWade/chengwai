<?php

/**
 * 微信订单管理
 */
class WeixinOrderAction extends AdvertBaseAction {
	
	
	//每个类都要重写此变量
	protected  $is_check_rbac = true;		//是否需要RBAC登录验证
	
	protected  $not_check_fn = array();	//无需登录和验证rbac的方法名
	
	//控制器说明
	private $module_explain = '微博订单';
		
	private $pt_type;	//平台类型
	
	private $big_type = 1;	//当前平台大分类
	

	
	//初始化数据库连接
	protected  $db = array(
			'GeneralizeWeixinOrder' => 'GeneralizeWeixinOrder',
			'GeneralizeWeixinFiles' => 'GeneralizeWeixinFiles'
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
				'sidebar_two'=>array(2=>'select'),//第一个加依次类推
		));
		$this->display();
	}
	
	
	//推广活动订单
	public function generalize_activity() {
		parent::data_to_view(array(
				//二级导航属性
				'sidebar_two'=>array(3=>'select'),//第一个加依次类推
		));
		$this->display();
	}
	
	
	//添加意向单
	public function add_intention () {
		parent::data_to_view(array(
				//二级导航属性
				'sidebar_two'=>array(4=>'select'),//第一个加依次类推
		));
		$this->display();
	}

    
    //添加推广
    public function add_extension()
    {
    	$id = $this->db['GeneralizeWeixinOrder']->insertPost($_POST,$this->oUser->id);
    	if($id!='')
    	{
    		$img_array = $this->upload_img($_FILES,$id);
			$this->db['GeneralizeWeixinFiles']->insertImg($img_array);
			$this->redirect('Advert/Weixin/weixin',array('order_id'=>$id));
    	}
    }



    	//上传图片 传入表单路径 和 订单ID 上传文件name contentTypeRetweet genuineFile
	private function upload_img($save_file,$order_id,$bool=true)
	{
		$img_where = array();
		$contentTypeRetweet = $save_file['contentTypeRetweet'];
		if($contentTypeRetweet!='')
		{
			$upload_dir = C('UPLOAD_DIR');
			$dir = $upload_dir['web_dir'].$upload_dir['image'];
			$status_content = parent::upload_file($contentTypeRetweet,$dir,5120000);
			if($status_content['status']==true)
			{
				$img_where['contentTypeRetweet']['users_id'] = $this->oUser->id;
				if($bool==true)
				{
					$img_where['contentTypeRetweet']['generalize_order_id'] = $order_id;
				}else{
					$img_where['contentTypeRetweet']['intention_order_id'] = $order_id;
				}
				$img_where['contentTypeRetweet']['type'] = 1;
				$img_where['contentTypeRetweet']['url'] = $status_content['info'][0]['savename'];
			}
		}
		$genuineFile = $save_file['genuineFile'];
		if($genuineFile!='')
		{
			$status_genuineFile = parent::upload_file($genuineFile,$dir,5120000);
			if($status_genuineFile['status']==true)
			{
				$img_where['genuineFile']['users_id'] = $this->oUser->id;
				if($bool==true)
				{
					$img_where['genuineFile']['generalize_order_id'] = $order_id;
				}else{
					$img_where['genuineFile']['intention_order_id'] = $order_id;
				}
				$img_where['genuineFile']['type'] = 2;
				$img_where['genuineFile']['url'] = $status_genuineFile['info'][0]['savename'];
			}
		}
		return $img_where;
	}
}

?>