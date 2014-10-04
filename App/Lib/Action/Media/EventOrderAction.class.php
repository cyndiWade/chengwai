<?php

/**
 * 活动订单控制器
 */
class EventOrderAction extends MediaBaseAction {
	
	//每个类都要重写此变量
	protected  $is_check_rbac = true;		//是否需要RBAC登录验证
	
	protected  $not_check_fn = array();	//无需登录和验证rbac的方法名
	
	//控制器说明
	private $module_explain = '活动订单';


	//初始化数据库连接
	protected  $db = array(
			// 'CategoryTags'=>'CategoryTags',
			// 'Users' => 'Users',
			// 'Verify'=>'Verify',
			// 'User_media' => 'User_media'
	);
	
	//和构造方法
	public function __construct() {
		parent::__construct();
		parent::global_tpl_view(array('module_explain'=>$this->module_explain));
	}
    
    /**
     * 活动订单首页(待执行订单)
     * 
     * @author lurongchang
     * @date   2014-09-19
     * @return void
     */
	public function index()
    {
		 parent::data_to_view(array(
			//二级导航
			'secondSiderbar' => array(
				'待执行订单' => array('select' => true, 'url' => U('/Media/EventOrder/index')),
				'全部订单'   => array('select' => false, 'url' => U('/Media/EventOrder/allorder')),
			)
		)); 
		$this->display('standbys');
	}
    
    /**
     * 所有订单
     * 
     * @author bumtime
     * @date   2014-10-02
     * @return void
     */
	public function allorder()
    {
    	 parent::data_to_view(array(
			//二级导航
			'secondSiderbar' => array(
				'待执行订单' => array('select' => false, 'url' => U('/Media/EventOrder/index')),
				'全部订单'   => array('select' => true, 'url' => U('/Media/EventOrder/allorder')),
			)
		)); 
		
		$where = $media_where = array();
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
		//状态
		if($new_array['order_status']!='')
		{
			$where['audit_status'] = intval($new_array['order_status']);
		}
		//账号
		if($new_array['search_account']!='')
		{
			$media_where['account_name'] = array('like', '%'.$new_array['search_account'].'%');
		}
		//订单号
		if($new_array['order_id']!='')
		{
			$where['ga.id'] = intval($new_array['order_id']);
		}		
				
		import('ORG.Util.Page');
		$GeneralizeOrder 	= D('GeneralizeOrder');
		$GeneralizeAccount	= D('GeneralizeAccount');
		//媒体帐户ID
		$media_list = D("AccountWeibo")->getListsByUserID($this->oUser->id, "all", $media_where );
		
		$show = "";
		$list = array();
		$count = $sum = 0;
		if($media_list)
		{
			$where["account_id"] = array("IN", array_keys($media_list));
			$count      = $GeneralizeAccount->where($where)->count();
			
			$sum		= $GeneralizeAccount->where($where)->sum('price');
			
			$Page       = new Page($count,10);
			$show       = $Page->show();
 
			$list = $GeneralizeAccount->alias('ga')->join(" ".C('db_prefix')."generalize_order go on ga.generalize_id = go.id")->where($where)->limit($Page->firstRow.','.$Page->listRows)
					->order('ga.id desc')->field('`generalize_id`, `account_type`, `account_id`, `price`, `audit_status`, ga.`id` as order_id,
												 `tfpt_type`, `fslx_type`, `ryg_type`, `hd_name`, `start_time`, `all_price`, `status`')->select();
			if($list)
			{
				/*$arry_order_id = array_keys($list);
				$order_where = array("in", array_unique($arry_order_id));
				$order_list =  $GeneralizeOrder->where($order_where)
								->getField('`id` as order_id, `tfpt_type`, `fslx_type`, `ryg_type`, `hd_name`, `start_time`, `all_price`, `status`');*/
				$stutas_list = C('Account_Order_Status');
				$Order_Status_list = C('Order_Status');
				foreach ($list as $key=>$value)
				{
					$list[$key]['audit_status_name']	= $stutas_list[$value['audit_status']]['explain'];
					$list[$key]['status_name']			= $Order_Status_list[$value['status']]['explain'];
					$list[$key]['account_name']			= $media_list[$value['account_id']];
				}
			}
		}
		
		parent::data_to_view(array(
				'page' => $show ,
				'list' => $list,
				'search_name' => $new_array['search_name'],
				'start_time' => $new_array['start_time'],
				'end_time' => $new_array['end_time'],
				'status' => $new_array['status'],
				'search_order_id' => $new_array['order_id'],
				'search_account' => $new_array['search_account'], 
				'count' =>  $count ? $count : 0,
				'sum' => $sum ? $sum : 0
		));
		
        $this->display('allorder');
	}
	
	 /**
     * 订单详情
     * 
     * @author bumtime
     * @date   2014-10-03
     * @return void
     */
	public function show()
    {
    	 parent::data_to_view(array(
			//二级导航
			'secondSiderbar' => array(
				'待执行订单' => array('select' => false, 'url' => U('/Media/EventOrder/index')),
				'全部订单'   => array('select' => true, 'url' => U('/Media/EventOrder/allorder')),
			)
		)); 
		
		//过滤去空格 防SQL
		$new_array = addsltrim($_REQUEST);
		$order_id  = intval($new_array['id']);
		$GeneralizeOrder 	= D('GeneralizeOrder');
		$GeneralizeAccount	= D('GeneralizeAccount');
		
		//媒体帐户ID
		$media_list = D("AccountWeibo")->getListsByUserID($this->oUser->id);
		$where["account_id"]	= array("IN", array_keys($media_list));
		$where['generalize_id'] = $order_id;
		
		//订单详情
		$order_info = $GeneralizeOrder->getOrderInfo($order_id);
		 
		$account_list = $GeneralizeAccount->alias('ga')->join(" ".C('db_prefix')."account_weibo wb on ga.account_id = wb.id")->where($where)
					->order('ga.id desc')->field('ga.id, `price`, ga.`audit_status`,`account_name`')->select();
					 
		//统计
		$count      	= $GeneralizeAccount->where($where)->count();
		$sum			= $GeneralizeAccount->where($where)->sum('price');
		//配图
		$file_where = array("generalize_order_id"=>$order_id, "type"=>2);
		$order_file = D('GeneralizeFiles')->field('url')->where($file_where)->find();
		$order_info["order_file"] = $order_file ? $order_file : "";
		
		parent::data_to_view(array(
				'order_info'		=> $order_info,
				'account_list'	=> $account_list,
				'count' =>  $count ? $count : 0,
				'sum' => $sum ? $sum : 0,
		));
		
        $this->display('show');
    }
    
    /**
     * 订单状态---拒绝订单 执行订单
     * 
     * @author bumtime
     * @date   2014-10-04
     * @return void
     */
	public function setAujectStatus()
    {
    	$id 		= $this->_post("a_id", "intval");
    	$order_id 	= $this->_post("order_id", "intval");
    	$status 	= $this->_post("status", "intval");
   		$reason 	= $this->_post("reason");
   		
    	$GeneralizeAccount	= D('GeneralizeAccount');
    	$media_Info = $GeneralizeAccount->getInfoById($id, "account_id");
    	//检查是否是本人
    	if(!D('AccountWeibo')->checkAccountByUserId($media_Info['account_id'], $this->oUser->id))
    	{
    		$this->error('操作非法，该账号不属于您旗下！', U('Media/EventOrder/show?id='.$order_id));
    	}
    	
    	
		//改变状态	
    	if($id)
    	{
    		$GeneralizeAccount->setAccountStatus($id, $status);
			$this->success('处理成功', U('Media/EventOrder/show?id='.$order_id));
    	}
    	else 
    	{
    		$this->error('处理失败', U('Media/EventOrder/show?id='.$order_id));
    	} 
    }
    
     /**
     * 订单状态---执行订单
     * 
     * @author bumtime
     * @date   2014-10-04
     * @return void
     */
	public function setFinishiedStatus()
    {
    	$id 		= $this->_post("a_id", "intval");
    	$order_id 	= $this->_post("order_id", "intval");
    	$status 	= $this->_post("status", "intval");
   		
   		
    	$GeneralizeAccount	= D('GeneralizeAccount');
    	$media_Info = $GeneralizeAccount->getInfoById($id, "account_id");
    	//检查是否是本人
    	if(!D('AccountWeibo')->checkAccountByUserId($media_Info['account_id'], $this->oUser->id))
    	{
    		$this->error('操作非法，该账号不属于您旗下！', U('Media/EventOrder/show?id='.$order_id));
    	}
    	
    	$upload_dir = C('UPLOAD_DIR');
		$dir = $upload_dir['web_dir'].$upload_dir['image']."screenshot/";
		$status_content = parent::upload_file($_FILES['upload_info'], $dir, 2048000);
		if($status_content['status']==true)
		{
			$pic_url = $status_content['info'][0]['savename'];
			
			$data['users_id']				= $this->oUser->id;
			$data['generalize_order_id']	= $order_id;
			$data['type']					= 3;
			$data['url']					= $pic_url;
			D('GeneralizeFiles')->addFinishImg($data);
		}
		
    	if($id)
    	{
    		$GeneralizeAccount->setAccountStatus($id, $status);
			$this->success('处理成功', U('Media/EventOrder/show?id='.$order_id));
    	}
    	else 
    	{
    		$this->error('处理失败', U('Media/EventOrder/show?id='.$order_id));
    	} 
    }
}