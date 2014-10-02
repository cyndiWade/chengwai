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
		'GeneralizeFiles' => 'GeneralizeFiles',
		'IntentionWeiboOrder' => 'IntentionWeiboOrder',
		'IntentionWeiboFiles' => 'IntentionWeiboFiles',
		'IntentionWeiboAccount' => 'IntentionWeiboAccount'
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
		$number = $this->db['GeneralizeOrder']->get_OrderInfo_num($this->oUser->id);
		//过滤去空格 防SQL
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
			$where['hd_name'] = array('like','%'.$new_array['search_name'].'%');
		}
		import('ORG.Util.Page');
		$GeneralizeOrder = D('GeneralizeOrder');
		$where['users_id'] =  $this->oUser->id;
		$count      = $GeneralizeOrder->where($where)->count();
		$Page       = new Page($count,10);
		$show       = $Page->show();
		$list = $GeneralizeOrder->where($where)->limit($Page->firstRow.','.$Page->listRows)
		->order('id desc')->field('id,hd_name,tfpt_type,fslx_type,ryg_type,start_time,all_price,status')->select();
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

	//删除草根微博
	public function del_caogen()
	{
		$del_id = intval($_POST['id']);
		$GeneralizeOrder = D('GeneralizeOrder');
		$bool = $GeneralizeOrder->del_info($del_id,$this->oUser->id);
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

	//删除名人微博
	public function del_mingren()
	{
		$del_id = intval($_POST['id']);
		$IntentionWeiboOrder = D('IntentionWeiboOrder');
		$bool = $IntentionWeiboOrder->del_info($del_id,$this->oUser->id);
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
		$number = $this->db['IntentionWeiboOrder']->get_OrderInfo_num($this->oUser->id);
		//过滤去空格 防SQL
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
			$where['yxd_name'] = array('like','%'.$new_array['search_name'].'%');
		}
		import('ORG.Util.Page');
		$IntentionWeiboOrder = D('IntentionWeiboOrder');
		$where['users_id'] =  $this->oUser->id;
		$count      = $IntentionWeiboOrder->where($where)->count();
		$Page       = new Page($count,5);
		$show       = $Page->show();
		$list = $IntentionWeiboOrder->where($where)->limit($Page->firstRow.','.$Page->listRows)
		->order('id desc')->field('id,yxd_name,tfpt_type,fslx_type,ryg_type,start_time,over_time,status')->select();
		$new_list_id = array();
		foreach($list as $value)
		{
			$new_list_id[] =$value['id'];
		}
		$intention_id_num = $this->db['IntentionWeiboAccount']->getListNum($new_list_id,$this->oUser->id);
		parent::data_to_view(array(
				'page' => $show ,
				'list' => $list,
				'search_name' => $new_array['search_name'],
				'start_time' => $new_array['start_time'],
				'end_time' => $new_array['end_time'],
				'status_0' => $number[0],
				'status_1' => $number[1],
				'intention_id_num' => $intention_id_num
		));
		$this->display();
	}
	
	//添加推广 选择账号
	public function add_users()
	{

		if($this->isPost())
		{
			$status = $this->db['GeneralizeAccount']->insertAll($_POST,$this->oUser->id);
			if ($status == true) {
				
				//修改订单状态为1，平台审核的类型
				$this->db['GeneralizeOrder']->where(array('id'=>$_POST['order_id']))->save(array('status'=>1));
				
				parent::callback(C('STATUS_SUCCESS'),'添加成功',array('go_to_url'=>U('/Advert/WeiboOrder/generalize_activity')));
			} else {
				parent::callback(C('STATUS_UPDATE_DATA'),'添加是失败');
			}
			
		}
	}

	//添加意向单 选择账号
	public function add_intens()
	{

		if($this->isPost())
		{
			$status = $this->db['IntentionWeiboAccount']->insertAll($_POST,$this->oUser->id);
			if ($status == true) {
				
				//修改订单状态为1，平台审核的类型
				$this->db['IntentionWeiboOrder']->where(array('id'=>$_POST['order_id']))->save(array('status'=>1));
				
				parent::callback(C('STATUS_SUCCESS'),'添加成功',array('go_to_url'=>U('/Advert/WeiboOrder/intention_list')));
			} else {
				parent::callback(C('STATUS_UPDATE_DATA'),'添加失败');
			}
			
		}
	}

	//填写意向单
	public function add_intenYx()
	{
		//新增数据
		if($this->isPost())
		{
			$id = $this->db['IntentionWeiboOrder']->insertPost($_POST,$this->oUser->id);
			if($id!='')
			{
				$img_array = $this->upload_img($_FILES,$id,false);
				$this->db['IntentionWeiboFiles']->insertImg($img_array);
				if($_POST['tfpt_type']==1)
				{
					$this->redirect('Advert/Weibo/celebrity_weibo',array('pt_type'=>1,'order_id'=>$id));
				}else{
					$this->redirect('Advert/Weibo/celebrity_weibo',array('pt_type'=>2,'order_id'=>$id));
				}
			}else{
				parent::callback(C('STATUS_DATA_LOST'),'参数错误!');
			}
		}
	}

	//添加推广 填写信息
	public function add_extension()
	{
		if($this->isPost())
		{
			//获得新增数据ID
			$id = $this->db['GeneralizeOrder']->insertPost($_POST,$this->oUser->id);
			if($id!='')
			{
				$img_array = $this->upload_img($_FILES,$id);
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



	//上传图片 传入表单路径 和 订单ID 上传文件name contentTypeRetweet genuineFile
	private function upload_img($save_file,$order_id,$bool=true)
	{
		$img_where = array();
		$contentTypeRetweet = $save_file['contentTypeRetweet'];
		$upload_dir = C('UPLOAD_DIR');
		$dir = $upload_dir['web_dir'].$upload_dir['image'];
		if($contentTypeRetweet!='')
		{
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
	
	
	//订单详情
	public function generalize_detail () {
		parent::data_to_view(array(
			//二级导航属性
			'sidebar_two'=>array(5=>'select',),//第一个加依次类推
		));
		$this->display();
	}
	
	
	//意向单订单详情
	public function intention_detail () {
		parent::data_to_view(array(
				//二级导航属性
				'sidebar_two'=>array(6=>'select',),//第一个加依次类推
		));
		$this->display();
	}

	//支付
	public function zhifu()
	{
		$zhifu_id = intval($_POST['id']);
		$GeneralizeNewsOrder = D('GeneralizeAccount')->siteMoney($zhifu_id,$this->oUser->id);
		if($GeneralizeNewsOrder==true)
		{
			parent::callback(C('STATUS_SUCCESS'),'支付成功!');
		}else{
			parent::callback(C('STATUS_UPDATE_DATA'),'支付失败,请检查余额!');
		}
	}
}

?>