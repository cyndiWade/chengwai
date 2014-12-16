<?php

/**
 * 预约订单控制器
 */
class PlaceAnOrderAction extends MediaBaseAction {
	
	//每个类都要重写此变量
	protected  $is_check_rbac = true;		//是否需要RBAC登录验证
	
	protected  $not_check_fn = array();	//无需登录和验证rbac的方法名
	
	//控制器说明
	private $module_explain = '意向单列表';


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
     * 意向单列表首页(待执行订单)
     * 
     * @author lurongchang
     * @date   2014-09-19
     * @return void
     */
	public function index()
    {
    	$this->redirect('/Media/PlaceAnOrder/allorder');
		/*parent::data_to_view(array(
			//二级导航
			'secondSiderbar' => array(
				//'待执行订单'		=> array('select' => true, 'url' => U('/Media/PlaceAnOrder/index')),
				'微博预约订单'	=> array('select' => false, 'url' => U('/Media/PlaceAnOrder/allorder')),
				'微信预约订单'	=> array('select' => false, 'url' => U('/Media/PlaceAnOrder/allorderWeixin')),
			),
            'secondPosition' => '待执行订单',
		));
		$this->display();*/
	}
    
    /**
     * 全部微博订单
     * 
     * @author lurongchang
     * @date   2014-09-19
     * @return void
     */
	public function allorder()
    {
		parent::data_to_view(array(
            //二级导航
			'secondSiderbar' => array(
				//'待执行订单'		=> array('select' => false, 'url' => U('/Media/PlaceAnOrder/index')),
				'微博意向单'	=> array('select' => true, 'url' => U('/Media/PlaceAnOrder/allorder')),
				'微信意向单'	=> array('select' => false, 'url' => U('/Media/PlaceAnOrder/allorderWeixin')),
			),
            'secondPosition' => '微博意向单',
		));
		

		$new_array = addsltrim($_REQUEST);
		
		$data = $this->searchWeiboList();
		
		parent::data_to_view(array(
				'page' => $data['page'] ,
				'list' => $data['list'],
				'search_name' => $new_array['search_name'],
				'start_time' => $new_array['start_time'],
				'end_time' => $new_array['end_time'],
				'status' => $new_array['status'],
				'search_order_id' => $new_array['order_id'],
				'search_account' => $new_array['search_account'], 
				'count' =>  $data['count'] ? $data['count'] : 0,
				'sum' => $data['sum'] ? $data['sum'] : 0
		));
		
		$this->display('allorder_weibo');
	}
	
	
	 /**
     * 微博意向单详情
     * 
     * @author bumtime
     * @date   2014-10-08
     * @return void
     */
	public function showWeibo()
    {
		parent::data_to_view(array(
            //二级导航
			'secondSiderbar' => array(
				//'待执行订单'		=> array('select' => false, 'url' => U('/Media/PlaceAnOrder/index')),
				'微博意向单'	=> array('select' => true, 'url' => U('/Media/PlaceAnOrder/allorder')),
				'微信意向单'	=> array('select' => false, 'url' => U('/Media/PlaceAnOrder/allorderWeixin')),
			),
            'secondPosition' => '微博意向单',
		));

		//过滤去空格 防SQL
		$new_array = addsltrim($_REQUEST);
		$order_id  = intval($new_array['id']);
		$GeneralizeOrder 	= D('IntentionWeiboOrder');
		$GeneralizeAccount	= D('IntentionWeiboAccount');
		
		//媒体帐户ID
		$media_list = D("AccountWeibo")->getListsByUserID($this->oUser->id);
		$where["account_id"]	= array("IN", array_keys($media_list));
		$where['intention_id'] = $order_id;
		
		//订单详情
		$order_info = $GeneralizeOrder->getOrderInfo($order_id);
		 
		$account_list = $GeneralizeAccount->alias('ga')->join(" ".C('db_prefix')."account_weibo wb on ga.account_id = wb.id")->where($where)
					->order('ga.id desc')->field('ga.id,  ga.`audit_status`,`account_name`, price')->select();
				
					 
		//统计
		$count      	= $GeneralizeAccount->where($where)->count();
		$sum			= $GeneralizeAccount->where($where)->sum('price');
		//配图
		$file_where = array("intention_order_id"=>$order_id, "type"=>2);
		$order_file = D('IntentionWeiboFiles')->field('url')->where($file_where)->find();
		$order_info["order_file"] = $order_file ? $order_file : "";
		
		parent::data_to_view(array(
				'order_info'		=> $order_info,
				'account_list'	=> $account_list,
				'count' =>  $count ? $count : 0,
				'sum' => $sum ? $sum : 0,
		));
		
		$this->display('show_weibo');
	}
	
	
    /**
     * 全部微信订单
     * 
     * @author lurongchang
     * @date   2014-09-19
     * @return void
     */
	public function allorderWeixin()
    {
		parent::data_to_view(array(
            //二级导航
			'secondSiderbar' => array(
				//'待执行订单'		=> array('select' => false, 'url' => U('/Media/PlaceAnOrder/index')),
				'微博意向单'	=> array('select' => false, 'url' => U('/Media/PlaceAnOrder/allorder')),
				'微信意向单'	=> array('select' => true, 'url' => U('/Media/PlaceAnOrder/allorderWeixin')),
			),
            'secondPosition' => '微信意向单',
		));
		
		$new_array = addsltrim($_REQUEST);
		
		$data = $this->searchWeixinList();
		
		parent::data_to_view(array(
				'page' => $data['page'] ,
				'list' => $data['list'],
				'search_name' => $new_array['search_name'],
				'start_time' => $new_array['start_time'],
				'end_time' => $new_array['end_time'],
				'status' => $new_array['status'],
				'search_order_id' => $new_array['order_id'],
				'search_account' => $new_array['search_account'], 
				'count' =>  $data['count'] ? $data['count'] : 0,
				'sum' => $data['sum'] ? $data['sum'] : 0
		));
		
		$this->display('allorder_weixin');
	}	  
	
	 /**
     * 微信意向单详情
     * 
     * @author bumtime
     * @date   2014-10-08
     * @return void
     */
	public function showWeixin()
    {
		parent::data_to_view(array(
            //二级导航
			'secondSiderbar' => array(
				//'待执行订单'		=> array('select' => false, 'url' => U('/Media/PlaceAnOrder/index')),
				'微博意向单'	=> array('select' => false, 'url' => U('/Media/PlaceAnOrder/allorder')),
				'微信意向单'	=> array('select' => true, 'url' => U('/Media/PlaceAnOrder/allorderWeixin')),
			),
            'secondPosition' => '微信意向单',
		));

		//过滤去空格 防SQL
		$new_array = addsltrim($_REQUEST);
		$order_id  = intval($new_array['id']);
		$GeneralizeOrder 	= D('IntentionWeixinOrder');
		$GeneralizeAccount	= D('IntentionWeixinAccount');
		
		//媒体帐户ID
		$media_list = D("AccountWeixin")->getListsByUserID($this->oUser->id);
		$where["account_id"]	= array("IN", array_keys($media_list));
		$where['generalize_id'] = $order_id;
		
		//订单详情
		$order_info = $GeneralizeOrder->getOrderInfo($order_id);
		 
		$account_list = $GeneralizeAccount->alias('ga')->join(" ".C('db_prefix')."account_weixin wb on ga.account_id = wb.id")->where($where)
					->order('ga.id desc')->field('ga.id,  ga.`audit_status`,`account_name`, price')->select();
				
					 
		//统计
		$count      	= $GeneralizeAccount->where($where)->count();
		$sum			= $GeneralizeAccount->where($where)->sum('price');
		//配图
		$file_where = array("intention_order_id"=>$order_id, "type"=>2);
		$order_file = D('IntentionWeiwinFiles')->field('url')->where($file_where)->find();
		$order_info["order_file"] = $order_file ? $order_file : "";
		
		parent::data_to_view(array(
				'order_info'		=> $order_info,
				'account_list'	=> $account_list,
				'count' =>  $count ? $count : 0,
				'sum' => $sum ? $sum : 0,
		));
		
		$this->display('show_weixin');
	}
	
	 /**
     * 意向订单状态---拒绝订单（status==2） 接受订单（status==1）
     * 
     * @author bumtime
     * @date   2014-10-12
     * @return void
     */
	public function setAujectStatus()
    {
    	$id 		= I("a_id", 0,"intval");
    	$order_id 	= I("order_id", 0, "intval");
    	$status 	= I("status", 0, "intval");
    	$type		= I('type');
   		$reason 	= I("reason");
   		$typeTip	= "";
   		
   		switch ($type)
   		{
   			case 'weibo':
   				$GeneralizeAccount	= D('IntentionWeiboAccount');
    			$mediaObject		= D('AccountWeibo');
    			$typeTip			= 3;
    			break;
   			case 'weixin':
   				$GeneralizeAccount	= D('IntentionWeixinAccount');
    			$mediaObject		= D('AccountWeixin');
    			$typeTip			= 5;
    			break; 
   			    			   			
   		}

    	$media_Info = $GeneralizeAccount->getInfoById($id, "account_id");
    	//检查是否是本人
    	$test = $mediaObject->checkAccountByUserId($media_Info['account_id'], $this->oUser->id);
    	if(!$test)
    	{
    		$this->error('操作非法，该账号不属于您旗下！');
    	} 	
    	
		//改变状态	
    	if($id)
    	{
    		//拒绝订单
    		if(2 == $status || 3 == $status)
    		{
	    		$arryOrderLog = array();
	    		$arryOrderLog['user_id'] 		= $this->oUser->id;
	    		$arryOrderLog['order_id']		= $order_id;
	    		$arryOrderLog['account_id']		= $id;
	    		$arryOrderLog['type']			= $typeTip;
	    		$arryOrderLog['content']		= $reason;
	    		$arryOrderLog['create_time']	= time();
	    		D('OrderLog')->orderLogAdd($arryOrderLog);
    		}
    		    		
    		$GeneralizeAccount->setAccountStatus($id, $status);
			$this->success('处理成功');
    	}
    	else 
    	{
    		$this->error('处理失败');
    	} 
    }
    
    
	
    /**
     * 搜索微博意向订单列表
     * 
     * @author lurongchang
     * @date   2014-09-22
     * @return void
     */
	private  function searchWeiboList()
    {
        $expiredStartTime   = I('expiredStartTime', 0, 'strtotime');
        $expiredEndTime     = I('expiredEndTime', 0, 'strtotime');
        $executionStartTime = I('executionStartTime', 0, 'strtotime');
        $executionEndTime   = I('executionEndTime', 0, 'strtotime');
        $accountName        = I('account_name', '', 'setString');
        $requirementName    = I('requirement_name', '', 'setString');
        $requirementStatus  = I('requirement_status', 0, 'intval');
        
        $where = array();
        
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
		$GeneralizeOrder 	= D('IntentionWeiboOrder');
		$GeneralizeAccount	= D('IntentionWeiboAccount');
		//媒体帐户ID
		$media_list = D("AccountWeibo")->getListsByUserID($this->oUser->id, "all", $media_where);
		
		$show = "";
		$list = array();
		$count = $sum = 0;
		if($media_list)
		{
			$where["account_id"] = array("IN", array_keys($media_list));
			$count      = $GeneralizeAccount->alias('ga')->where($where)->count();
			
			$sum		= $GeneralizeAccount->alias('ga')->where($where)->sum('price');
			
			$Page       = new Page($count,10);
			$show       = $Page->show();
 
			$list = $GeneralizeAccount->alias('ga')->join(" ".C('db_prefix')."intention_weibo_order go on ga.intention_id = go.id")->where($where)->limit($Page->firstRow.','.$Page->listRows)
					->order('ga.id desc')->field('`intention_id`, `account_type`, `account_id`, `audit_status`,  go.`id` as order_id, 
						ga.`id`,`tfpt_type`, `fslx_type`, `ryg_type`, `yxd_name`, `start_time`, `over_time`, `create_time`, `status`, price')->select();
			
			if($list)
			{
				$stutas_list = C('Account_Order_Status');
				$Order_Status_list = C('Order_Status');
				foreach ($list as $key=>$value)
				{
					$list[$key]['status_name']			= $Order_Status_list[$value['status']]['explain_yxd'];
					$list[$key]['account_name']			= $media_list[$value['account_id']];
				}
			}
		}
		return array("sum"=>$sum, "count"=>$count, "page"=>$show, "list"=>$list);
	            
	}
	
	 /**
     * 搜索微讯意向订单列表
     * 
     * @author bumtime
     * @date   2014-10-08
     * @return void
     */
	private function searchWeixinList()
    {
        $expiredStartTime   = I('expiredStartTime', 0, 'strtotime');
        $expiredEndTime     = I('expiredEndTime', 0, 'strtotime');
        $executionStartTime = I('executionStartTime', 0, 'strtotime');
        $executionEndTime   = I('executionEndTime', 0, 'strtotime');
        $accountName        = I('account_name', '', 'setString');
        $requirementName    = I('requirement_name', '', 'setString');
        $requirementStatus  = I('requirement_status', 0, 'intval');
        
        $where = array();
        
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
		$GeneralizeOrder 	= D('IntentionWeixinOrder');
		$GeneralizeAccount	= D('IntentionWeixinAccount');
		//媒体帐户ID
		$media_list = D("AccountWeixin")->getListsByUserID($this->oUser->id, $media_where);

		$show = "";
		$list = array();
		$count = $sum = 0;
		if($media_list)
		{
			$where["account_id"] = array("IN", array_keys($media_list));
			$count      = $GeneralizeAccount->alias('ga')->where($where)->count();
			
			$sum		= $GeneralizeAccount->alias('ga')->where($where)->sum('price');
			
			$Page       = new Page($count,10);
			$show       = $Page->show();
 
			$list = $GeneralizeAccount->alias('ga')->join(" ".C('db_prefix')."intention_weixin_order go on ga.generalize_id = go.id")->where($where)->limit($Page->firstRow.','.$Page->listRows)
					->order('ga.id desc')->field('`generalize_id`, `account_id`, `audit_status`,  go.`id` as order_id, 
						ga.`id`,`ggw_type`, `yxd_name`, `title`, `start_time`, `over_time`, `create_time`, `status`, price')->select();
			
			if($list)
			{
				$stutas_list = C('Account_Order_Status');
				$Order_Status_list = C('Order_Status');
				foreach ($list as $key=>$value)
				{
					$list[$key]['status_name']			= $Order_Status_list[$value['status']]['explain_yxd'];
					$list[$key]['account_name']			= $media_list[$value['account_id']];
				}
			}
		}
		return array("sum"=>$sum, "count"=>$count, "page"=>$show, "list"=>$list);
	            
	}
	
}