<?php

/**
 * 微博订单管理
 */
class WeiboOrderAction extends AdvertBaseAction {
	
	
	//每个类都要重写此变量
	protected  $is_check_rbac = true;		//是否需要RBAC登录验证
	
	protected  $not_check_fn = array();		//无需登录和验证rbac的方法名
	
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
		'IntentionWeiboAccount' => 'IntentionWeiboAccount',
		'Discss'	=>	'Discss',
		'AccountWeibo'=>'AccountWeibo'
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
		//add by bumtime 20141201
		$this->big_type = 3;
		parent::big_type_urls($this->big_type);		//大分类URL
		
		parent::data_to_view(array(
				//二级导航属性
				'sidebar_two'=>array(5=>'select'),//第一个加依次类推
				'sidebar_order'=>array(3=>'select'),//第一个加依次类推
		));
		$number = $this->db['GeneralizeOrder']->get_OrderInfo_num($this->oUser->id);
		//过滤去空格 防SQL
		$new_array = addsltrim($_REQUEST);
		$start_time = strtotime($new_array['start_time']);
		$end_time = strtotime($new_array['end_time']);
		//执行订单
		if($new_array['zxz']!='')
		{
			$where['status'] = 4;
		}
		//已取消
		if($new_array['yqx']!='')
		{
			$where['status'] = 6;
		}
		//草稿
		if($new_array['caogao']!='')
		{
			$where['status'] = array('IN',array('0','1','2'));
			$where['smallnumber'] = 0;
		}
		//已完成
		if($new_array['ywc']!='')
		{
			$where['status'] = 5;
		}
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
		$GeneralizeOrder = $this->db['GeneralizeOrder'];
		$where['users_id'] =  $this->oUser->id;
		$count      = $GeneralizeOrder->where($where)->count();
		$Page       = new Page($count,10);
		$show       = $Page->show();
		$list = $GeneralizeOrder->where($where)->limit($Page->firstRow.','.$Page->listRows)
		->order('id desc')->field('id,hd_name,tfpt_type,fslx_type,ryg_type,start_time,all_price,status,smallnumber,create_time')->select();
		$new_list_id = array();
		foreach($list as $value)
		{
			$new_list_id[] =$value['id'];
		}

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
				'zxz'=>$number['zxz'],
				'yqx'=>$number['yqx'],
				'caogao'=>$number['caogao'],
				'ywc'=>$number['ywc'],
		));
		$this->display();
	}

	//删除草根微博
	public function del_caogen()
	{
		$del_id = intval($_POST['id']);
		$GeneralizeOrder = $this->db['GeneralizeOrder'];
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
		$IntentionWeiboOrder = $this->db['IntentionWeiboOrder'];
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
		$type = I('type', 0, 'intval');
		parent::data_to_view(array(
				//二级导航属性
				'sidebar_two'=>array(6=>'select'),//第一个加依次类推
				'type' =>$type
		));
		$this->display();
	}

    
	//意向单列表
	public function intention_list() {
		//add by bumtime 20141201
		$this->big_type = 3;
		parent::big_type_urls($this->big_type);		//大分类URL
		
		parent::data_to_view(array(
				//二级导航属性
				'sidebar_two'=>array(6=>'select'),//第一个加依次类推
				'sidebar_order'=>array(4=>'select'),//第一个加依次类推
		));
		$number = $this->db['IntentionWeiboOrder']->get_OrderInfo_num($this->oUser->id);
		//过滤去空格 防SQL
		$new_array = addsltrim($_REQUEST);
		$start_time = strtotime($new_array['start_time']);
		$end_time = strtotime($new_array['end_time']);
		if($new_array['qrz']=='on')
		{
			$where['status'] = array('IN',array(0,1));
		}
		if($new_array['yqr']=='on')
		{
			$where['status'] = array('eq',2);
		}
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
		$IntentionWeiboOrder = $this->db['IntentionWeiboOrder'];
		$where['users_id'] =  $this->oUser->id;
		$count      = $IntentionWeiboOrder->where($where)->count();
		$Page       = new Page($count,5);
		$show       = $Page->show();
		$list = $IntentionWeiboOrder->where($where)->limit($Page->firstRow.','.$Page->listRows)
		->order('id desc')->field('id,yxd_name,tfpt_type,fslx_type,ryg_type,start_time,over_time,status,create_time,is_celebrity')->select();
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
				'qrz' => $number['qrz'],
				'yqr' => $number['yqr'],
				'intention_id_num' => $intention_id_num
		));
		$this->display();
	}
	
	//添加推广 选择账号
	public function add_users()
	{

		if($this->isPost())
		{
			$order_id = I('order_id', 0 ,'intval');
			$tfpt_type = I('tfpt_type', 1, 'intval');
			$account_id = urldecode(trim(I('account_ids')));
			if($order_id >0)
    		{
				$status = $this->db['GeneralizeAccount']->insertAll($_POST,$this->oUser->id,$this->global_finance['weibo_proportion']);
				if ($status == true) {
					
					//修改订单状态为1，平台审核的类型
					//$this->db['GeneralizeOrder']->where(array('id'=>$_POST['order_id']))->save(array('status'=>1));
					parent::updateMoney($this->oUser->id);
					
					//下单发站内短信 add by bumtime 20141223
					$this->messageOrderAdd($order_id, $account_id, 1, $tfpt_type);
					
					parent::callback(C('STATUS_SUCCESS'),'下单成功!',array('go_to_url'=>U('/Advert/WeiboOrder/generalize_activity')));
				} else {
					parent::callback(C('STATUS_UPDATE_DATA'),'下单失败,请检查余额！');
				}
			}else{
				//$account_ids = passport_encrypt($_POST['account_ids'],'account_ids');
				
				parent::callback(C('STATUS_SUCCESS'),'正在跳转...',array('go_to_url'=>U('Advert/WeiboOrder/add_generalize',array('account_ids'=>$account_ids,'pttype'=>$_POST['pt_type']))));
			}
		}
	}

	//添加意向单 选择账号
	public function add_intens()
	{

		if($this->isPost())
		{
			$order_id = I('order_id', 0 ,'intval');
			$tfpt_type  = I('tfpt_type', 1, 'intval');
			$account_id = urlencode(I('account_ids'));
			if($order_id >0)
    		{
				$status = $this->db['IntentionWeiboAccount']->insertAll($_POST,$this->oUser->id,$this->global_finance['weibo_proportion']);
				if ($status == true) {
					
					//修改订单状态为1，平台审核的类型
					$this->db['IntentionWeiboOrder']->where(array('id'=>$order_id))->save(array('status'=>1));
					
					//下单发站内短信 add by bumtime 20141223
					$this->messageOrderAdd($order_id, $account_id, 2, $tfpt_type);
					
					parent::callback(C('STATUS_SUCCESS'),'添加成功',array('go_to_url'=>U('/Advert/WeiboOrder/intention_list')));
				} else {
					parent::callback(C('STATUS_UPDATE_DATA'),'该订单账号已存在');
				}
			}else{
				
				//$account_ids = passport_encrypt($_POST['account_ids'],'account_ids');
				$account_ids = urlencode($_POST['account_ids']);
				parent::callback(C('STATUS_SUCCESS'),'正在跳转...',array('go_to_url'=>U('Advert/WeiboOrder/add_intention',array('account_ids'=>$account_ids,'pttype'=>$_POST['pt_type'],'type'=>1))));
			}
		}
	}

	//填写意向单
	public function add_intenYx()
	{
		//新增数据
		if($this->isPost())
		{
			$bool = parent::banwordCheck($_POST);
			if($bool!='')
			{
				$info = '您提交的内容中含有敏感词 "' . $bool['keyword'] . '",请修改!';
				alertBack($info);
			}
			
			$id = $this->db['IntentionWeiboOrder']->insertPost($_POST,$this->oUser->id);
			
			//$account_id = passport_decrypt(trim($_GET['account_ids']),'account_ids');
			$account_id = urldecode(trim(I('account_ids')));
			
			$pt_type = intval($_GET['pttype']);
			$tfpt_type = I('tfpt_type', 1, 'intval');
			
			if($id!='')
			{
				$img_array = $this->upload_img($_FILES,$id,false,2);
				//$this->db['IntentionWeiboFiles']->insertImg($img_array);
				if($account_id!='')
				{
					$arr = array('order_id'=>$id,'pt_type'=>$pt_type,'account_ids'=>$account_id, 'audit_status'=>1);
					$this->db['IntentionWeiboAccount']->insertAll($arr,$this->oUser->id,$this->global_finance['weibo_proportion']);
					//修改订单状态为1，平台审核的类型
					$this->db['IntentionWeiboOrder']->where(array('id'=>$id))->save(array('status'=>1));
					
					//下单发站内短信 add by bumtime 20141223
					$this->messageOrderAdd($id, $account_id, 2, $tfpt_type);
					
					$this->redirect('Advert/WeiboOrder/intention_list');
				}else{			 
						if($_POST['is_celebrity'] ==1)
							$this->redirect('Advert/Weibo/celebrity_weibo',array('pt_type'=>$_POST['tfpt_type'],'order_id'=>$id));
						else 
							$this->redirect('Advert/Weibo/caogen_weibo',array('pt_type'=>$_POST['tfpt_type'],'order_id'=>$id, 'inten_type'=>1));					
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
			$bool = parent::banwordCheck($_POST);
			if($bool!='')
			{
				$info = '您提交的内容中含有敏感词 "' . $bool['keyword'] . '",请修改!';
				alertBack($info);
			}
			//获得新增数据ID
			$id = $this->db['GeneralizeOrder']->insertPost($_POST,$this->oUser->id);
			
			//$account_id = passport_decrypt(trim($_GET['account_ids']),'account_ids');
			$account_id = urldecode(trim($_GET['account_ids']));
			
			$pt_type = intval($_GET['pttype']);
			$tfpt_type = I('tfpt_type', 1, 'intval');
			if($id!='')
			{
				$img_array = $this->upload_img($_FILES,$id,true,1);
				//$this->db['GeneralizeFiles']->insertImg($img_array);
				//根据ID跳转
				if($account_id!='')
				{
					$arr = array('order_id'=>$id,'pt_type'=>$pt_type,'account_ids'=>$account_id);
					$this->db['GeneralizeAccount']->insertAll($arr,$this->oUser->id,$this->global_finance['weibo_proportion']);
					//修改订单状态为1，平台审核的类型
					//$this->db['GeneralizeOrder']->where(array('id'=>$id))->save(array('status'=>1));
					parent::updateMoney($this->oUser->id);
					
					//下单发站内短信 add by bumtime 20141223
					$this->messageOrderAdd($id, $account_id, 1, $tfpt_type);
					
					$this->redirect('Advert/WeiboOrder/generalize_activity');
				}else{
					if($_POST['tfpt_type']==1)
					{
						//新浪草根
						$this->redirect('Advert/Weibo/caogen_weibo',array('pt_type'=>1,'order_id'=>$id));
					}else{
						//腾讯草根
						$this->redirect('Advert/Weibo/caogen_weibo',array('pt_type'=>2,'order_id'=>$id));
					}
				}
			}else{
				parent::callback(C('STATUS_DATA_LOST'),'参数错误!');
			}
		}
	}



	//上传图片 传入表单路径 和 订单ID 上传文件name contentTypeRetweet genuineFile
	private function upload_img($save_file,$order_id,$bool=true,$type)
	{
		$upload_dir = C('UPLOAD_DIR');
		$dir = $upload_dir['web_dir'].$upload_dir['image'];
		for($i=0;$i<=9;$i++)
		{
			$contentTypeRetweet = $save_file['contentTypeRetweet'.$i];
			if($contentTypeRetweet!='')
			{
				$status_content = parent::upload_file($contentTypeRetweet,$dir,5120000);
				if($status_content['status']==true)
				{
					$content['users_id'] = $this->oUser->id;
					if($bool==true)
					{
						$content['generalize_order_id'] = $order_id;
					}else{
						$content['intention_order_id'] = $order_id;
					}
					$content['type'] = 1;
					$content['url'] = $status_content['info'][0]['savename'];
				}
				if($type==2)
				{
					$this->db['IntentionWeiboFiles']->add($content);
				}else{
					$this->db['GeneralizeFiles']->add($content);
				}
			}
		}
		$genuineFile = $save_file['genuineFile'];
		if($genuineFile!='')
		{
			$status_genuineFile = parent::upload_file($genuineFile,$dir,5120000);
			if($status_genuineFile['status']==true)
			{
				$img_where['users_id'] = $this->oUser->id;
				if($bool==true)
				{
					$img_where['generalize_order_id'] = $order_id;
				}else{
					$img_where['intention_order_id'] = $order_id;
				}
				$img_where['type'] = 2;
				$img_where['url'] = $status_genuineFile['info'][0]['savename'];
			}
			if($type==2)
			{
				$this->db['IntentionWeiboFiles']->add($img_where);
			}else{
				$this->db['GeneralizeFiles']->add($img_where);
			}
		}
	}
	
	
	//订单详情
	public function generalize_detail () 
	{
		$order_id = I('order_id', 0, 'intval');
		if (empty($order_id)) alertClose('非法操作！');
		
		//add by bumtime 20141201
		$this->big_type = 3;
		parent::big_type_urls($this->big_type);		//大分类URL
		//获取订单数据
		$GeneralizeOrder = $this->db['GeneralizeOrder'];
		$order_info = $GeneralizeOrder->get_OrderInfo_By_Id($order_id,$this->oUser->id);
		
		if (get_magic_quotes_gpc()) {
	    	$order_info['zw_info'] =  stripslashes(stripslashes($order_info['zw_info']));
	    	$order_info['zfnr_info'] =  stripslashes(stripslashes($order_info['zfnr_info']));
		}
		
		if (empty($order_info)) alertBack('订单不存在！');
		
		//获取订单下的账号列表
		$GeneralizeAccount = $this->db['GeneralizeAccount'];
		$account_order_list = $GeneralizeAccount->get_account_order($order_id);	
				
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
				$extend_order_info['sum_money'] += $account_order_list[$key]['g_price'] = $val['g_price'] + ($val['g_price'] * $val['g_rebate']);
				
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
		
		//配图
		$file_where = array("generalize_order_id"=>$order_id);
		$order_file_new = array();
		$order_file = D('GeneralizeFiles')->where($file_where)->field('type,url')->select();
	
		foreach ($order_file as $value)
		{
			$order_file_new[$value['type']][] = $value['url'];
		}
		$order_info['file'] = $order_file_new;
		 

		//获取订单下的关联账号列表
		parent::data_to_view(array(
			'sidebar_two'=>array(5=>'select',),//第一个加依次类推，//二级导航属性
			'order_info'=>$order_info,
			'account_order_list'=>$account_order_list,
			'extend_order_info'=>$extend_order_info,
			'is_show_order_btn' => $is_show_order_btn,	
			'order_id'=>$order_id
		));
		$this->display();
	}
	
	
	//确认订单状态
	// public function set_account_status () {
	// 	$id = $this->_post('id');
	// 	//关联边订单状态
	// 	$Account_Order_Status = C('Account_Order_Status');
	// 	$data['audit_status'] = $Account_Order_Status[7]['status'];
	// 	$is_ok = $this->db['GeneralizeAccount']->where(array('id'=>$id))->save($data);
	
	// 	if ($is_ok == true) {
	// 		parent::callback(C('STATUS_SUCCESS'),'操作成功');
	// 	} else {
	// 		parent::callback(C('STATUS_UPDATE_DATA'),'操作失败');
	// 	}
	// }
	
	
	//意向单订单详情
	public function intention_detail () {
		$order_id = I('order_id', 0, 'intval');
		if (empty($order_id)) alertClose('非法操作！');
		
		//add by bumtime 20141201
		$this->big_type = 3;
		parent::big_type_urls($this->big_type);		//大分类URL
		//获取订单数据
		$IntentionWeiboOrder = $this->db['IntentionWeiboOrder'];
		$order_info = $IntentionWeiboOrder->get_OrderInfo_By_Id($order_id,$this->oUser->id);
	
		
		if (empty($order_info)) alertBack('订单不存在');
		
		//获取订单下的账号列表
		$IntentionWeiboAccount = $this->db['IntentionWeiboAccount'];
		$account_order_list = $IntentionWeiboAccount->get_account_order($order_id);	
				
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
				$account_order_list[$key]['g_status_explain'] = $Account_Order_Status[$val['g_audit_status']]['explain_yxd'];
				
				//是否显示确认按钮
				if ($val['g_audit_status'] == $Account_Order_Status[6]['status']) {
					$account_order_list[$key]['is_show_affirm_btn'] = true;
				}
				
				//统计订单总金额
				$extend_order_info['sum_money'] += $account_order_list[$key]['g_price'] = $val['g_price'] + ($val['g_price'] * $val['g_rebate']);
				
				$account_order_list[$key]['other'] = $Account_Order_Status[$val['g_audit_status']]['other'];
				
				//统计拒绝单数
				if ($val['g_audit_status'] == $Account_Order_Status[4]['status']) {
					$extend_order_info['jy_order_sum'] += 1;
				}
				
				//显示创建活动按钮
				if ($val['g_audit_status'] == $Account_Order_Status[5]['status']) {
					$account_order_list[$key]['create_order_status'] = true;
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
		
		//配图
		$file_where = array("intention_order_id"=>$order_id);
		$order_file_new = array();
		$order_file = D('IntentionWeiboFiles')->where($file_where)->field('type,url')->select();
	
		foreach ($order_file as $value)
		{
			$order_file_new[$value['type']][] = $value['url'];
		}
		$order_info['file'] = $order_file_new;
		
 
		//获取订单下的关联账号列表
		parent::data_to_view(array(
			'sidebar_two'=>array(6=>'select',),//第一个加依次类推，//二级导航属性
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
		$GeneralizeAccount = $this->db['GeneralizeAccount']->siteMoney($zhifu_id,$this->oUser->id);
		if($GeneralizeAccount==true)
		{
			parent::updateMoney($this->oUser->id);
			parent::callback(C('STATUS_SUCCESS'),'支付成功!');
		}else{
			parent::callback(C('STATUS_UPDATE_DATA'),'支付失败,请检查余额!');
		}
	}


	//微博推广
	public function YxZhuanTg()
	{
		if($this->isPost())
		{
			$Account_Order_Status = C('Account_Order_Status');
			//获得微信的订单数据
			$intentval = $this->db['IntentionWeiboOrder']->get_OrderInfo_By_Id(intval($_POST['intention_order_id']),$this->oUser->id);
			$img = $this->db['IntentionWeiboFiles']->getImg(intval($_POST['intention_order_id']));
			//把微信的订单输入塞入推广表
			$ien_id = $this->db['GeneralizeOrder']->insertGeneralize($intentval);
			//获得ID 存入数据
			$this->db['GeneralizeFiles']->insertImgs($ien_id,$img);	
			$bool = $this->db['GeneralizeAccount']->insertNewAccount($ien_id,$_POST['account_ids'],$this->oUser->id);
			if(bool==true)
			{
				parent::updateMoney($this->oUser->id);
				parent::callback(C('STATUS_SUCCESS'),'成功!');
			}else{
				parent::callback(C('STATUS_UPDATE_DATA'),'错误请稍后再试!');
			}
		}
	}
	
	
	//查看订单执行图
	public function look_perform_pic () {
		$order_id = I('get.order_id', 0, 'intval');
		$account_id = I('get.account_id', 0, 'intval');
	
		$type = 3;
		$where['generalize_order_id'] = $order_id;
		$where['account_id'] = $account_id;
		$where['type'] = $type;
		$result = $this->db['GeneralizeFiles']->get_fiels_list($where);
		parent::public_file_dir($result,array('url'),'images/screenshot/');
	
	
		parent::data_to_view(array(
				'sidebar_two'=>array(5=>'select',),//第一个加依次类推，//二级导航属性
				'list'=>$result
		));
	
		$this->display();
	}

	//确认支付
	public function insertPrice()
	{
		$small_order_id =  I('post.id', 0, 'intval');
		if($small_order_id!='')
		{
			$bool = $this->db['GeneralizeAccount']->getUserPr($small_order_id,$this->oUser->id);
			if($bool)
			{
				parent::updateMoney($this->oUser->id);
				
				//确认收货发站内短信 add by bumtime 20141223
				$tipsInfo = C('MESSAGE_TYPE_MEDIA');
    		    $sendWhere['id'] = $small_order_id;
    		    $info = $this->db['GeneralizeWeiboAccount']->where($sendWhere)->field('`users_id`,`generalize_id`')->find();
    		    $tipsInfo = C('MESSAGE_TYPE_MEDIA');
    		    $messageData['send_from_id']	=	C('MESSAGE_ADMIN_ID');
				$messageData['send_to_id']		=	$info['users_id'];
				$messageData['subject']			=	$tipsInfo[8]['subject'];
				$messageData['content']			=	sprintf($tipsInfo[8]['content'], U('/Media/EventOrder/show', array('id'=>$info['generalize_id'])));
				$messageData['message_time']	=	time();

				parent::sendMessageInfo($messageData);
				
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
		$name = $this->db['GeneralizeAccount']->getNickname($ddid);
		$discss = $this->db['Discss'];
		$array = array('pinfen'=>$pinfen,'pinlun'=>$pinlun,'ddid'=>$ddid,'name'=>$name,'users_id'=>$this->oUser->id,'type'=>3,'times'=>time());
		$select = array('ddid'=>$ddid,'users_id'=>$this->oUser->id,'type'=>3);
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
	
	
	//获取评论
	public function get_now_pl_info () {
		if ($this->isPost()) {
			$ddid = $this->_post('ddid');	//小订单ID
			$type = $this->_post('type');	//类型
			$users_id = $this->oUser->id;	//用户ID
				
			$select = array('ddid'=>$ddid,'users_id'=>$this->oUser->id,'type'=>$type);
			$result = D('Discss')->where($select)->find();
			if (!empty($result)) {
				parent::callback(C('STATUS_SUCCESS'),'获取成功!',$result);
			} else {
				parent::callback(C('STATUS_NOT_DATA'),'暂无数据!');
	
			}
		}
	}

	//下单发站内短信 add by bumtime 20141223
	private function messageOrderAdd($order_id, $account_id, $type=1, $mediaType =1 )
	{
		$tipsInfo = C('MESSAGE_TYPE_MEDIA');
		$data =  array();
		//账号ID
		$accoutWhere['id'] = array("in", $account_id);
		
		$accountList = $this->db['AccountWeibo']->where($accoutWhere)->getField("`id`, `users_id`, `account_name`");
		
		if($type == 1)
		{
			$tipsSubject = $tipsInfo[4]['subject'];
			$tipsContent = $tipsInfo[4]['content'];
			$tipsUrl	 = U('/Media/EventOrder/show',array('id'=>$order_id));
		}
		else {
			$tipsSubject = $tipsInfo[3]['subject'];
			$tipsContent = $tipsInfo[3]['content'];
			$tipsUrl	 = U('/Media/PlaceAnOrder/showWeibo',array('id'=>$order_id));
		}
		$i =0 ;
		$tipType = ($mediaType ==1) ? "新浪微博" : "腾讯微博";
		
		foreach ($accountList as $value) 
		{
			$data[$i]['send_from_id']	=	C('MESSAGE_ADMIN_ID');
			$data[$i]['send_to_id']		=	$value['users_id'];
			$data[$i]['subject']		=	$tipsSubject;
			$data[$i]['content']		=	sprintf($tipsContent, $tipType, $tipsUrl, $value['account_name']);
			$data[$i]['message_time']	=	time();
			$i++;
		}

		if($data)
			parent::sendMessageInfo($data, 2);
	}
	
}

?>