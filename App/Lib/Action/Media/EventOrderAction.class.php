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
			'AccountNews'   => 'AccountNews',
            'AccountWeibo'  => 'AccountWeibo',
            'AccountWeixin' => 'AccountWeixin',
			'UserMedia'     => 'UserMedia',
			'Users'         => 'Users',
			'GeneralizeOrder'       => 'GeneralizeOrder',
			'GeneralizeNewsOrder'   => 'GeneralizeNewsOrder',
			'GeneralizeWeixinOrder' => 'GeneralizeWeixinOrder',
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
		$userInfos = parent::get_session('user_info');
        $userMediaModel = $this->db['UserMedia'];
        $userMediaInfo = $userMediaModel->getInfoByIds($userInfos['id']);
        $userMediaInfo = $userMediaInfo ? reset($userMediaInfo) : array();
        
        // 收藏或者黑名单数量 
        $mediaWeiboList		= D("AccountWeibo")->getListsByUserID($this->oUser->id);
        $mediaWeixinList	= D("AccountWeixin")->getListsByUserID($this->oUser->id);
        $mediaNewsList		= D("AccountNews")->getListsByUserID($this->oUser->id);
      
        if(!empty($mediaWeiboList))
        {
        	$whereWeibo['weibo_id'] = array("in",  array_keys($mediaWeiboList)) ;
        	$weiboDatas = M('BlackorcollectionWeibo')->where($whereWeibo)->getField('or_type', true); 
        }
        if(!empty($mediaWeixinList))
        {
        	$whereWeibo['weixin_id'] = array("in",  array_keys($mediaWeixinList)) ;
        	$weixinDatas = M('BlackorcollectionWeixin')->where($whereWeibo)->getField('or_type', true);
        }       
        if(!empty($mediaNewsList))
        {
        	$whereWeibo['news_id'] = array("in",  array_keys($mediaNewsList)) ;
        	$newsDatas = M('BlackorcollectionNews')->where($whereWeibo)->getField('or_type', true);
        } 
            
        $newsDatas = $newsDatas ? array_count_values($newsDatas) : array(0, 0);
        $weiboDatas = $weiboDatas ? array_count_values($weiboDatas) : array(0, 0);
        $weixinDatas = $weixinDatas ? array_count_values($weixinDatas) : array(0, 0);
        $blackNums = $newsDatas[0] + $weiboDatas[0] + $weixinDatas[0];
        $markNums = $newsDatas[1] + $weiboDatas[1] + $weixinDatas[1];
        
        //好评数
        $arryAll = array();
        if(!is_array($mediaWeiboList))
        	$arryAll = array_keys($mediaWeiboList);
        if(!is_array($mediaWeixinList))
        	$arryAll = array_keys($mediaWeixinList );
        if(!is_array($mediaNewsList))
        	$arryAll = array_keys( $mediaNewsList);
         
        if(!empty($arryAll))
        {       
	        $whereWeibo['account_id'] = array("in", $arryAll) ;
	        $orderEvaluate = M('evaluate')->where($whereWeibo)->getField('type', true); 
        }
        $allDatas = $orderEvaluate ? array_count_values($orderEvaluate) : array(0, 0);
        $all = $allDatas[1] + $allDatas[2]  + $allDatas[3];
        $allDatas = $all >0 ?  round($allDatas[1] / $all, 2)  * 100 : 0; 

        // 帐号、粉丝数量
        $newsModel      = $this->db['AccountNews'];
        $weiboModel     = $this->db['AccountWeibo'];
        $weixinModel    = $this->db['AccountWeixin'];
        $newsInfo = $newsModel->getAccountList(array('users_id' => $userInfos['id']));
        $weiboInfo = $weiboModel->getAccountList(array('users_id' => $userInfos['id']));
        $weixinInfo = $weixinModel->getAccountList(array('users_id' => $userInfos['id']));
        // 帐号数量
        $accountNums = 0;
        // 粉丝数量
        $fansNums = 0;
        // 平台数量
        $typeNums = 0;
        // 不可接单
        $notallowNums = 0;
        // 审核失败
        $auditFailNums = 0;
        // 等待审核
        $auditWaitNums = 0;
        // 暂不接单
        $leaveNums = 0;
        // 下架账号
        $putawayNums = 0;
        // 不接硬广
        $disabledYGNums = 0;
        // 无硬广转发价
        $noRetweetNums = 0;
        // 无硬广直发价
        $noTweetNums = 0;
        // 无软广转发价
        $noSoftRetweetNums = 0;
        // 无软广直发价
        $noSoftTweetNums = 0;
        
        if ($newsInfo) {
            $typeNums += 1;
            $accountNums += count($newsInfo);
            foreach ($newsInfo AS $info) {
                $notallowNums += $info['receiving_status'] ? 0 : 1;
                if ($info['status'] == 2) {
                    $auditFailNums += 1;
                } elseif ($info['status'] == 0) {
                    $auditWaitNums += 1;
                }
                $leaveNums += $info['tmp_receiving_status'] ? 1 : 0;
                $putawayNums += $info['putaway_status'] ? 0 : 1;
                $disabledYGNums += $info['is_yg_status'] ? 0 : 1;
            }
        }
        if ($weiboInfo) {
            $typeNums += 1;
            $accountNums += count($weiboInfo);
            foreach ($weiboInfo AS $info) {
                $fansNums += $info['fans_num'];
                $notallowNums += $info['receiving_status'] ? 0 : 1;
                if ($info['status'] == 2) {
                    $auditFailNums += 1;
                } elseif ($info['status'] == 0) {
                    $auditWaitNums += 1;
                }
                $leaveNums += $info['tmp_receiving_status'] ? 1 : 0;
                $putawayNums += $info['putaway_status'] ? 0 : 1;
                $disabledYGNums += $info['is_yg_status'] ? 0 : 1;
                $noRetweetNums += $info['yg_zhuanfa'] ? 0 : 1;
                $noTweetNums += $info['yg_zhifa'] ? 0 : 1;
                $noSoftRetweetNums += $info['rg_zhuanfa'] ? 0 : 1;
                $noSoftTweetNums += $info['rg_zhifa'] ? 0 : 1;
            }
        }
        if ($weixinInfo) {
            $typeNums += 1;
            $accountNums += count($weixinInfo);
            foreach ($weixinInfo AS $info) {
                $fansNums += $info['fans_num'];
                $notallowNums += $info['receiving_status'] ? 0 : 1;
                if ($info['status'] == 2) {
                    $auditFailNums += 1;
                } elseif ($info['status'] == 0) {
                    $auditWaitNums += 1;
                }
                $leaveNums += $info['tmp_receiving_status'] ? 1 : 0;
                $putawayNums += $info['putaway_status'] ? 0 : 1;
                $disabledYGNums += $info['is_yg_status'] ? 0 : 1;
                $noRetweetNums += $info['yg_zhuanfa'] ? 0 : 1;
                $noTweetNums += $info['yg_zhifa'] ? 0 : 1;
                $noSoftRetweetNums += $info['rg_zhuanfa'] ? 0 : 1;
                $noSoftTweetNums += $info['rg_zhifa'] ? 0 : 1;
            }
        }  
        
		$arrayTotal = $this->getTotal($mediaWeiboList, $mediaWeixinList, $mediaNewsList);
		
		// 通告
		$noticeWhere = array(
			'parent_id' 	=> 37, 
			'show_status' 	=> 1,
			'is_del' 		=> 0
		);
		$notices = M('Help')->where($noticeWhere)->limit(3)->order('id DESC')->select();

        parent::data_to_view(array(
			//二级导航
			'secondSiderbar' 	=> array(
				'待执行订单'		=> array('select' => true, 'url' => U('/Media/EventOrder/index')),
				'新闻媒体订单'	=> array('select' => false, 'url' => U('/Media/EventOrder/allorderNews')),
				'微信订单'		=> array('select' => false, 'url' => U('/Media/EventOrder/allorderWeixin')),
				'微博订单'		=> array('select' => false, 'url' => U('/Media/EventOrder/allorder')),
			),
            'userMediaInfo' => $userMediaInfo,
            'markNums' => $markNums,
            'blackNums' => $blackNums,
            'accountNums' => $accountNums,
            'notallowNums' => $notallowNums,
            'typeNums' => $typeNums,
            'fansNums' => $fansNums,
            'leaveNums' => $leaveNums,
            'auditFailNums' => $auditFailNums,
            'auditWaitNums' => $auditWaitNums,
            'putawayNums' => $putawayNums,
            'disabledYGNums' => $disabledYGNums,
            'noRetweetNums' => $noRetweetNums,
            'noTweetNums' => $noTweetNums,
            'noSoftRetweetNums' => $noSoftRetweetNums,
            'noSoftTweetNums' => $noSoftTweetNums,
            'todayShotNums' => $todayShotNums,
            'noTodayShotNums' => $noTodayShotNums,
            'orderEvaluate'=>$allDatas,
            'mediaTotal' => $arrayTotal['total'],
            'mediaPrice' => $arrayTotal['price'],
            'notices' => $notices
		)); 
		$this->display('standbys');
	}
    
    /**
     * 可执行订单 待上传数据截图订单
     * 
     * @author lurongchang
     * @date   2014-10-11
     * @return void
     */
    public function todoOrderList()
    {
        if ($this->isPost()) {
            $type = I('type', 0, 'intval');
            // 今日可执行订单
            $newsOrderModel = $this->db['GeneralizeNewsOrder'];
            $weiboOrderModel = $this->db['GeneralizeOrder'];
            $weixinOrderModel = $this->db['GeneralizeWeixinOrder'];
            
            $userInfos = parent::get_session('user_info');
            $todayStartTime = strtotime(date('Y-m-d', $_SERVER['REQUEST_TIME']));
            $todayEndTime = $todayStartTime + 86399;
            $betweenOrNot = in_array($type, array(0, 1)) ? 'BETWEEN' : 'NOT BETWEEN';
            // 订单帐号状态
            $status = $type % 2  == 0 ? 3 : 5;
            $where = array(
                'status' => 4,
                'start_time' => array($betweenOrNot, array($todayStartTime, $todayEndTime))
            );
            
            // 获取帐号信息
            $datas = array();
            $newsOrder = $newsOrderModel->getOrderList($userInfos['id'], $status, $where);
            $weiboOrder = $weiboOrderModel->getOrderList($userInfos['id'], $status, $where);
            $weixinOrder = $weixinOrderModel->getOrderList($userInfos['id'], $status, $where);
            $datas = array_merge($datas, $newsOrder, $weiboOrder, $weixinOrder);
            
            parent::callback(1, '成功获取数据', $datas);
        } else {
            parent::callback(C('STATUS_ACCESS'), '访问方式错误');
        }
    }
    
    /**
     * 所有订单(微博)
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
				'待执行订单'		=> array('select' => false, 'url' => U('/Media/EventOrder/index')),
				'新闻媒体订单'	=> array('select' => false, 'url' => U('/Media/EventOrder/allorderNews')),
				'微信订单'		=> array('select' => false, 'url' => U('/Media/EventOrder/allorderWeixin')),
				'微博订单'		=> array('select' => true, 'url' => U('/Media/EventOrder/allorder')),
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
		if($new_array['status']!='')
		{
			$where['ga.audit_status'] = intval($new_array['status']);
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
			$count      = $GeneralizeAccount->alias('ga')->where($where)->count();
			
			$sum		= $GeneralizeAccount->alias('ga')->where($where)->sum('price');
			
			$Page       = new Page($count,10);
			$show       = $Page->show();
 
			$list = $GeneralizeAccount->alias('ga')->join(" ".C('db_prefix')."generalize_order go on ga.generalize_id = go.id")->where($where)->limit($Page->firstRow.','.$Page->listRows)
					->order('ga.id desc')->field('`generalize_id`, `account_type`, `account_id`, `price`,  go.`id` as order_id, ga.id,ga.audit_status, `tfpt_type`, `fslx_type`, `ryg_type`, `hd_name`, `start_time`, `all_price`, `create_time`, `status`')->select();
			
			 
			if($list)
			{
				$stutas_list = C('Account_Order_Status');
				$Order_Status_list = C('Order_Status');
				foreach ($list as $key=>$value)
				{
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
		
        $this->display('allorder_weibo');
	}
	
	 /**
     * 订单详情(微博)
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
				'待执行订单'		=> array('select' => false, 'url' => U('/Media/EventOrder/index')),
				'新闻媒体订单'	=> array('select' => false, 'url' => U('/Media/EventOrder/allorderNews')),
				'微信订单'		=> array('select' => false, 'url' => U('/Media/EventOrder/allorderWeixin')),
				'微博订单'		=> array('select' => true, 'url' => U('/Media/EventOrder/allorder')),
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
					->order('ga.id desc')->field('ga.id, `price`, wb.`status`,`account_name`, ga.audit_status, account_id')->select();
					
		//统计
		$count      	= $GeneralizeAccount->where($where)->count();
		$sum			= $GeneralizeAccount->where($where)->sum('price');

		//配图
		$file_where = array("generalize_order_id"=>$order_id);
		$order_file_new = array();
		$order_file = D('GeneralizeFiles')->where($file_where)->field('type,url')->select();
		foreach ($order_file as $value)
		{
			$order_file_new[$value['type']][] = $value['url'];
		}
		$order_info['file'] = $order_file_new;


		parent::data_to_view(array(
				'order_info'		=> $order_info,
				'account_list'	=> $account_list,
				'count' =>  $count ? $count : 0,
				'sum' => $sum ? $sum : 0,
		));
		
        $this->display('show');
    }
    
    
     /**
     * 所有订单(微信)
     * 
     * @author bumtime
     * @date   2014-10-07
     * @return void
     */
	public function allorderWeixin()
    {
    	 parent::data_to_view(array(
			//二级导航
			'secondSiderbar' => array(
				'待执行订单'		=> array('select' => false, 'url' => U('/Media/EventOrder/index')),
				'新闻媒体订单'	=> array('select' => false, 'url' => U('/Media/EventOrder/allorderNews')),
				'微信订单'		=> array('select' => true, 'url' => U('/Media/EventOrder/allorderWeixin')),
				'微博订单'		=> array('select' => false, 'url' => U('/Media/EventOrder/allorder')),
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
			$where['yxd_name'] = array('like','%'.$new_array['search_name'].'%');
		}
		//状态
		if($new_array['status']!='')
		{
			$where['ga.audit_status'] = intval($new_array['status']);
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
		$GeneralizeOrder 	= D('GeneralizeWeixinOrder');
		$GeneralizeAccount	= D('GeneralizeWeixinAccount');
		//媒体帐户ID
		$media_list = D("AccountWeixin")->getListsByUserID($this->oUser->id, $media_where );
		
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
 
			$list = $GeneralizeAccount->alias('ga')->join(" ".C('db_prefix')."generalize_weixin_order go on ga.generalize_id = go.id")->where($where)->limit($Page->firstRow.','.$Page->listRows)
					->order('ga.id desc')->field('`generalize_id`, `account_id`, `price`, `status`,  go.`id` as order_id, ga.id,ga.audit_status,
												 `ggw_type`, `yxd_name`, `title`, `start_time`, `create_time`, `status`')->select();
				 
					 
			if($list)
			{
				$Order_Status_list = C('Order_Status');
				foreach ($list as $key=>$value)
				{
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
				'sum' => $sum ? $sum : 0,
		));
		
        $this->display('allorder_weixin');
	}
	
	 /**
     * 订单详情(微信)
     * 
     * @author bumtime
     * @date   2014-10-03
     * @return void
     */
	public function showWeixin()
    {
    	 parent::data_to_view(array(
			//二级导航
			'secondSiderbar' => array(
				'待执行订单'		=> array('select' => false, 'url' => U('/Media/EventOrder/index')),
				'新闻媒体订单'	=> array('select' => false, 'url' => U('/Media/EventOrder/allorderNews')),
				'微信订单'		=> array('select' => true, 'url' => U('/Media/EventOrder/allorderWeixin')),
				'微博订单'		=> array('select' => false, 'url' => U('/Media/EventOrder/allorder')),
			)
		)); 
		
		//过滤去空格 防SQL
		$new_array = addsltrim($_REQUEST);
		$order_id  = intval($new_array['id']);
		$GeneralizeOrder 	= D('GeneralizeWeixinOrder');
		$GeneralizeAccount	= D('GeneralizeWeixinAccount');
		
		//媒体帐户ID
		$media_list = D("AccountWeixin")->getListsByUserID($this->oUser->id);
		$where["account_id"]	= array("IN", array_keys($media_list));
		$where['generalize_id'] = $order_id;
		
		//订单详情
		$order_info = $GeneralizeOrder->getOrderInfo($order_id);
		 
		$account_list = $GeneralizeAccount->alias('ga')->join(" ".C('db_prefix')."account_weixin wb on ga.account_id = wb.id")->where($where)
					->order('ga.id desc')->field('ga.id, `price`, wb.`status`,`account_name`, ga.audit_status,account_id')->select();
				 
		//统计
		$count      	= $GeneralizeAccount->where($where)->count();
		$sum			= $GeneralizeAccount->where($where)->sum('price');
		//配图
		$file_where = array("generalize_order_id"=>$order_id);
		$order_file = D('GeneralizeWeixinFiles')->where($file_where)->getField('type,url');
		$order_info['file'] = $order_file;
		parent::data_to_view(array(
				'order_info'		=> $order_info,
				'account_list'	=> $account_list,
				'count' =>  $count ? $count : 0,
				'sum' => $sum ? $sum : 0,
		));
		
        $this->display('show_weixin');
    }
    

      /**
     * 所有订单(新闻媒体)
     * 
     * @author bumtime
     * @date   2014-10-07
     * @return void
     */
	public function allorderNews()
    {
    	 parent::data_to_view(array(
			//二级导航
			'secondSiderbar' => array(
				'待执行订单'		=> array('select' => false, 'url' => U('/Media/EventOrder/index')),
				'新闻媒体订单'	=> array('select' => true, 'url' => U('/Media/EventOrder/allorderNews')),
				'微信订单'		=> array('select' => false, 'url' => U('/Media/EventOrder/allorderWeixin')),
				'微博订单'		=> array('select' => false, 'url' => U('/Media/EventOrder/allorder')),
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
			$where['title'] = array('like','%'.$new_array['search_name'].'%');
		}
		//状态
		if($new_array['status']!='')
		{
			$where['ga.audit_status'] = intval($new_array['status']);
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
		$GeneralizeOrder 	= D('GeneralizeNewsOrder');
		$GeneralizeAccount	= D('GeneralizeNewsAccount');
		//媒体帐户ID
		$media_list = D("AccountNews")->getListsByUserID($this->oUser->id, $media_where );
		
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
 
			$list = $GeneralizeAccount->alias('ga')->join(" ".C('db_prefix')."generalize_news_order go on ga.generalize_id = go.id")->where($where)->limit($Page->firstRow.','.$Page->listRows)
					->order('ga.id desc')->field('`generalize_id`, `account_id`, `price`, `status`, go.`id` as order_id, ga.id, ga.audit_status, 
												 `title`, `start_time`, `web_url`, `bz_info`, `zf_info`, `create_time`, `status`')->select();

			if($list)
			{
				$Order_Status_list = C('Order_Status');
				foreach ($list as $key=>$value)
				{
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
				'sum' => $sum ? $sum : 0,
		));
		
        $this->display('allorder_news');
	}
	
	 /**
     * 订单详情(新闻媒体)
     * 
     * @author bumtime
     * @date   2014-10-03
     * @return void
     */
	public function showNews()
    {
    	 parent::data_to_view(array(
			//二级导航
			'secondSiderbar' => array(
				'待执行订单'		=> array('select' => false, 'url' => U('/Media/EventOrder/index')),
				'新闻媒体订单'	=> array('select' => true, 'url' => U('/Media/EventOrder/allorderNews')),
				'微信订单'		=> array('select' => false, 'url' => U('/Media/EventOrder/allorderWeixin')),
				'微博订单'		=> array('select' => false, 'url' => U('/Media/EventOrder/allorder')),
			)
		)); 
		
		//过滤去空格 防SQL
		$new_array = addsltrim($_REQUEST);
		$order_id  = intval($new_array['id']);
		$GeneralizeOrder 	= D('GeneralizeNewsOrder');
		$GeneralizeAccount	= D('GeneralizeNewsAccount');
		
		//媒体帐户ID
		$media_list = D("AccountNews")->getListsByUserID($this->oUser->id);
		$where["account_id"]	= array("IN", array_keys($media_list));
		$where['generalize_id'] = $order_id;
		
		//订单详情
		$order_info = $GeneralizeOrder->getOrderInfo($order_id);
		
		$account_list = $GeneralizeAccount->alias('ga')->join(" ".C('db_prefix')."account_news wb on ga.account_id = wb.id")->where($where)
					->order('ga.id desc')->field('ga.id, `price`, wb.`status`,`account_name`, ga.audit_status')->select();
		
		//统计
		$count      	= $GeneralizeAccount->where($where)->count();
		$sum			= $GeneralizeAccount->where($where)->sum('price');
		
		//配图
		$file_where = array("generalize_order_id"=>$order_id);
		$order_file = D('GeneralizeNewsFiles')->where($file_where)->getField('type,url');
		$order_info['file'] = $order_file;
	
		
		parent::data_to_view(array(
				'order_info'		=> $order_info,
				'account_list'	=> $account_list,
				'count' =>  $count ? $count : 0,
				'sum' => $sum ? $sum : 0,
		));
		
        $this->display('show_news');
    }
    
    
    /**
     * 订单状态---拒绝订单（status==4） 执行订单（status==5）
     * 
     * @author bumtime
     * @date   2014-10-04
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
   		$type_info	= 1;
   		
   		switch ($type)
   		{
   			case 'weibo':
   				$GeneralizeAccount	= D('GeneralizeAccount');
    			$mediaObject		= D('AccountWeibo');
    			$orderModel			= M('generalize_order');
    			$typeTip			= 2;
    			$type_info			= 3;
    			break;
   			case 'weixin':
   				$GeneralizeAccount	= D('GeneralizeWeixinAccount');
    			$mediaObject		= D('AccountWeixin');
    			$orderModel			= M('generalize_weixin_order');
    			$typeTip			= 4;
    			$type_info			= 2;
    			break; 
   			case 'news':
   				$GeneralizeAccount	= D('GeneralizeNewsAccount');
    			$mediaObject		= D('AccountNews');
    			$orderModel			= M('generalize_news_order');
    			$typeTip			= 1;
    			$type_info			= 1;
    			break;    			   			
   		}

    	$media_Info = $GeneralizeAccount->getInfoById($id, "account_id, price, rebate, generalize_id");
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
    		if(4 == $status)
    		{
	    		$arryOrderLog = array();
	    		$arryOrderLog['user_id'] 		= $this->oUser->id;
	    		$arryOrderLog['order_id']		= $order_id;
	    		$arryOrderLog['account_id']		= $id;
	    		$arryOrderLog['type']			= $typeTip;
	    		$arryOrderLog['content']		= $reason;
	    		$arryOrderLog['create_time']	= time();
	    		D('OrderLog')->orderLogAdd($arryOrderLog);
	    		
	    		//处理返回冻结金额（给广告主解冻金额）
	    		$adUserID = $orderModel->where(array("id"=>$media_Info['generalize_id']))->getField('users_id');
	    		//总金额
	    		$allMoney = getAdMoney($media_Info['price'], $type, $media_Info['rebate']);
	    		D("UserAdvertisement")->setMoney($allMoney, $adUserID, 1, $type_info, $order_id);
    		    		
	    		
    		}
    		$GeneralizeAccount->setAccountStatus($id, $status);
			$this->success('处理成功');
	
    	}
    	else 
    	{
    		$this->error('订单信息不完');
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
    	$id 		= I("a_id", 0, "intval");
    	$order_id 	= I("order_id", 0, "intval");
    	$status 	= I("status", 0, "intval");
    	$type		= I('type');
    	$link_url	= I('link_url');
    	$flat		= "";

    	switch ($type)
   		{
   			case 'weibo':
   				$GeneralizeAccount	= D('GeneralizeAccount');
    			$mediaObject		= D('AccountWeibo');
    			$fileObject			= M('GeneralizeFiles');
    			$flat				= "show";
    			break;
   			case 'weixin':
   				$GeneralizeAccount	= D('GeneralizeWeixinAccount');
    			$mediaObject		= D('AccountWeixin');
    			$fileObject			= M('GeneralizeWeixinFiles');
    			$flat				= "showWeixin";
    			break; 
   			case 'news':
   				$GeneralizeAccount	= D('GeneralizeNewsAccount');
    			$mediaObject		= D('AccountNews');
    			$fileObject			= M('GeneralizeNewsFiles');
    			$flat				= "showNews";
    			break;    			   			
   		}
   		
   		import("@.Tool.Validate");
   		if( !Validate::checkUrl($link_url))
   		{
   			$this->error('链接格式不对！');
   		}
   		
    	$media_Info = $GeneralizeAccount->getInfoById($id, "account_id");
    	
    	//检查是否是本人
    	if(!$mediaObject ->checkAccountByUserId($media_Info['account_id'], $this->oUser->id))
    	{
    		$this->error('操作非法，该账号不属于您旗下！');
    	}
    	//微信需要上传截图
    	if('weixin' ==  $type )
    	{
	    	$upload_dir = C('UPLOAD_DIR');
			$dir = $upload_dir['web_dir'].$upload_dir['image']."screenshot/";
			$status_content = parent::upload_file($_FILES['upload_info'], $dir, 2048000);
			if($status_content['status'] == true)
			{
				$pic_url = $status_content['info'][0]['savename'];
				
				$data['users_id']				= $this->oUser->id;
				$data['generalize_order_id']	= $order_id;
				$data['account_id']				= $id;
				//$data['account_id']				= $media_Info['account_id'];  
				$data['type']					= 3;
				$data['url']					= $pic_url;
				$data['link_url']				= $link_url;
				$fileObject->add($data);
			}
		}
		//新闻、微博不需要截图
		else 
		{
				$data['users_id']				= $this->oUser->id;
				$data['generalize_order_id']	= $order_id;
				$data['account_id']				= $id;
				//$data['account_id'] = 			$media_Info['account_id']; 
				$data['type']					= 3;
				$data['link_url']				= $link_url;
				$fileObject->add($data);
		}
		
    	if($id)
    	{
    		$GeneralizeAccount->setAccountStatus($id, $status);
			$this->success('处理成功', U('Media/EventOrder/'.$flat.'?id='.$order_id));
    	}
    	else 
    	{
    		$this->error('处理失败');
    	} 
    }
        
     /**
     * 查看执行反馈数据
     * 
     * @author bumtime
     * @date   2014-12-14
     * @return void
     */
    public function showExcuteInfo()
    {
    	$id 		= I("aid", 0, "intval");
    	$order_id 	= I("order_id", 0, "intval");
    	$type		= I('type', 0, "intval");

    	switch ($type)
   		{
   			case 3:
    			$fileObject			= M('GeneralizeFiles');
    			break;
   			case 2:
    			$fileObject			= M('GeneralizeWeixinFiles');
    			break; 
   			case 1:
    			$fileObject			= M('GeneralizeNewsFiles');
    			break;    			   			
   		}
   		//配图 
   		if($order_id && $id)
   		{
			$file_where = array("users_id"=>$this->oUser->id, "generalize_order_id"=>$order_id, "account_id"=>$id, 'type'=>3);
			$order_file = $fileObject->where($file_where)->field('link_url,url')->find();
			
		    $imgDir =  C('UPLOAD_DIR');
			parent::callback(1, '成功获取数据', array("img"=> "/".$imgDir['image'].'screenshot/'.$order_file['url'], 'url'=>$order_file['link_url']));
        } 
        else {
            parent::callback(C('STATUS_ACCESS'), '数据出错');
        }
	
    }
    /**
     * 统计日 周 月订单数
     * 
     * @author bumtime
     * @date   2014-10-19
     * 
     * @return array
     */
    private  function getTotal($mediaWeiboList, $mediaWeixinList, $mediaNewsList)
    {
    	//日订单数
		$countDay = 0;
		//日总金额
		$countPriceDay = 0;		
		//日完成订单数
		$countFinshedDay = 0;
		//日完成订单总金额
		$countPriceFinishedDay = 0;		
		//日不合格订单数
		$countFailedDay = 0;
		//日不合格订单总金额
		$countPriceFailedDay = 0;			
		//日流单数
		$countOverDay = 0;
		//日流单订单总金额
		$countPriceOverDay = 0;				
		//日拒单数
		$countAjectDay = 0;
		//日拒单订单总金额
		$countPriceAjectDay = 0;
		//日取消数
		$countCancelDay = 0;
		//日取消订单总金额
		$countPriceCancelDay = 0;
		//日派单数
		$countDoingDay = 0;
		//日派单订单总金额
		$countPriceDoingDay = 0;

			

			
		//周订单数
		$countWeek = 0;
		//周总金额	
		$countPriceWeek = 0;	
		//周完成订单数
		$countFinshedWeek = 0;
		//周完成总金额	
		$countPriceFinishedWeek = 0;
		//周不合格订单数
		$countFailedWeek = 0;
		//周不合格总金额	
		$countPriceFailedWeek = 0;
		//周流单数
		$countOverWeek = 0;
		//周流单总金额	
		$countPriceOverWeek = 0;
		//周拒单数
		$countAjectWeek = 0;
		//周流单总金额	
		$countPriceAjectWeek = 0;
		//周取消数
		$countCancelWeek = 0;
		//周取消订单总金额
		$countPriceCancelWeek = 0;
		//周派单数
		$countDoingWeek = 0;
		//周派单订单总金额
		$countPriceDoingWeek = 0;
		
				
		//月订单数
		$countMonth = 0;
		//月总金额
		$countPriceMonth = 0;
		//月完成订单数
		$countFinshedMonth = 0;
		//月完成总金额
		$countPriceFinishedMonth = 0;		
		//月不合格订单数
		$countFailedMonth = 0;
		//月不合格总金额
		$countPriceFailedMonth = 0;		
		//月流单数
		$countOverMonth = 0;
		//月流单总金额
		$countPriceOverMonth = 0;		
		//月拒单数
		$countAjectMonth = 0;	
		//月拒单总金额
		$countPriceAjectMonth = 0;	
		//月取消数
		$countCancelMonth = 0;
		//月取消订单总金额
		$countPriceCancelMonth = 0;
		//月派单数
		$countDoingMonth = 0;
		//月派单订单总金额
		$countPriceDoingMonth = 0;
			
		
		$whereWeibo = $whereWeixin = $whereNew = array();
		
        $newsAccountModel	= M('GeneralizeNewsAccount');
        $weiboAccountModel	= M('GeneralizeAccount');
        $weixinAccountModel	= M('GeneralizeWeixinAccount');
        //月起止时间 
        $startMonth 	= mktime(0, 0 , 0,date("m"),1,date("Y"));
        $endMonth		= mktime(23,59,59,date("m"),date("t"),date("Y"));
        //日起止时间 
        $startYesterday	= mktime(0,0,0,date('m'),date('d')-1,date('Y'));
		$endYesterday	= mktime(0,0,0,date('m'),date('d'),date('Y'))-1;
		//周起止时间
		$startWeek		= mktime(0,0,0,date('m'),date('d')-date('w')+1-7,date('Y'));
		$endWeek		= mktime(23,59,59,date('m'),date('d')-date('w')+7-7,date('Y'));

        $whereWeibo['account_id'] = array("in",  array_keys($mediaWeiboList)) ;
		$whereWeibo['go.start_time'] = array("between", array($startMonth, $endMonth));
		
        $whereWeixin['account_id'] = array("in",  array_keys($mediaWeixinList)) ;
		$whereWeixin['go.start_time'] = array("between", array($startMonth, $endMonth));  
		      
		$whereNews['account_id'] = array("in",  array_keys($mediaNewsList)) ;
		$whereNews['go.start_time'] = array("between", array($startMonth, $endMonth));		
		
        //微博
		$listWeibo	= $weiboAccountModel->alias('ga')->join(" ".C('db_prefix')."generalize_order go on ga.generalize_id = go.id")->where($whereWeibo)
					->order('ga.id desc')->field('`generalize_id`,  `account_id`, `price`, `status`,  go.`id` as order_id, ga.id,
												 `start_time`, `status`')->select();
		//微信
		$listWeixin = $weixinAccountModel->alias('ga')->join(" ".C('db_prefix')."generalize_weixin_order go on ga.generalize_id = go.id")->where($whereWeixin)
					->order('ga.id desc')->field('`generalize_id`,  `price`, `status`,  go.`id` as order_id, ga.id,
												  `start_time`, `status`')->select();
		//新闻媒体
		$listNew 	= $newsAccountModel->alias('ga')->join(" ".C('db_prefix')."generalize_news_order go on ga.generalize_id = go.id")->where($whereNews)
					->order('ga.id desc')->field('`generalize_id`,  `price`, `status`, go.`id` as order_id, ga.id,
												 `start_time`, `status`')->select();
					 $beginToday=mktime(0,0,0,date('m'),date('d'),date('Y'));

	 
		if($listWeibo)
		{
			foreach ($listWeibo  as $value)
			{
				if($value['start_time'] >= $startYesterday && $value['start_time'] <= $endYesterday)
				{
					switch ($value['status'])
					{
						case 7:
							$countFinshedDay ++;
							$countPriceFinishedDay += $value['price'];
							break;
						case 8:
							$countOverDay ++;
							$countPriceOverDay += $value['price'];
							break;
						case 10:
							$countFailedDay ++;
							$countPriceFailedDay += $value['price'];
						case 4:
							$countAjectDay ++;
							$countPriceAjectDay += $value['price'];
							break;	
						case 5:
							$countDoingDay ++;
							$countPriceDoingDay += $value['price'];
							break;	
						case 9:
							$countCancelDay ++;
							$countPriceCancelDay += $value['price'];
							break;															
					}
					$countDay ++;		
					$countPriceDay += $value['price'];	
				}
				if($value['start_time'] >= $startWeek && $value['start_time'] <= $endWeek)
				{
					switch ($value['status'])
					{
						case 7:
							$countFinshedWeek ++;
							$countFinshedWeek += $value['price'];
							break;
						case 8:
							$countOverWeek ++;
							$countPriceOverWeek += $value['price'];
							break;
						case 10:
							$countFailedWeek ++;
							$countPriceFailedWeek += $value['price'];
						case 4:
							$countAjectWeek ++;
							$countPriceAjectWeek += $value['price'];
							break;	
						case 5:
							$countDoingWeek ++;
							$countPriceDoingWeek += $value['price'];
							break;	
						case 9:
							$countCancelWeek ++;
							$countPriceCancelWeek += $value['price'];
							break;	
					}
					$countWeek ++;	
					$countPriceWeek += $value['price'];		
				}
				if ($value['start_time'] >= $startMonth && $value['start_time'] <= $endMonth)
				{
					switch ($value['status'])
					{
						case 7:
							$countFinshedMonth ++;
							$countPriceFinishedMonth += $value['price'];
							break;
						case 8:
							$countOverMonth ++;
							$countPriceOverMonth += $value['price'];
							break;
						case 10:
							$countFailedMonth ++;
							$countPriceFailedWeek += $value['price'];
						case 4:
							$countAjectMonth ++;
							$countPriceAjectMonth += $value['price'];
							break;	
						case 5:
							$countDoingMonth ++;
							$countPriceDoingMonth += $value['price'];
							break;	
						case 9:
							$countCancelMonth ++;
							$countPriceCancelMonth += $value['price'];
							break;	
					}
					$countMonth ++;		
					$countPriceMonth += $value['price'];	
				}
			}
		}
		if($listWeixin)
		{
			foreach ($listWeixin  as $value)
			{
				if($value['start_time'] >= $startYesterday && $value['start_time'] <= $endYesterday)
				{
					switch ($value['status'])
					{
						case 7:
							$countFinshedDay ++;
							$countPriceFinishedDay += $value['price'];
							break;
						case 8:
							$countOverDay ++;
							$countPriceOverDay += $value['price'];
							break;
						case 10:
							$countFailedDay ++;
							$countPriceFailedDay += $value['price'];
						case 4:
							$countAjectDay ++;
							$countPriceAjectDay += $value['price'];
							break;	
						case 5:
							$countDoingDay ++;
							$countPriceDoingDay += $value['price'];
							break;	
						case 9:
							$countCancelDay ++;
							$countPriceCancelDay += $value['price'];
							break;	
					}
					$countDay ++;		
					$countPriceDay += $value['price'];	
				}
				if($value['start_time'] >= $startWeek && $value['start_time'] <= $endWeek)
				{
					switch ($value['status'])
					{
						case 7:
							$countFinshedWeek ++;
							$countFinshedWeek += $value['price'];
							break;
						case 8:
							$countOverWeek ++;
							$countPriceOverWeek += $value['price'];
							break;
						case 10:
							$countFailedWeek ++;
							$countPriceFailedWeek += $value['price'];
						case 4:
							$countAjectWeek ++;
							$countPriceAjectWeek += $value['price'];
							break;	
						case 5:
							$countDoingWeek ++;
							$countPriceDoingWeek += $value['price'];
							break;	
						case 9:
							$countCancelWeek ++;
							$countPriceCancelWeek += $value['price'];
							break;
					}
					$countWeek ++;	
					$countPriceWeek += $value['price'];		
				}
				if ($value['start_time'] >= $startMonth && $value['start_time'] <= $endMonth)
				{
					switch ($value['status'])
					{
						case 7:
							$countFinshedMonth ++;
							$countPriceFinishedMonth += $value['price'];
							break;
						case 8:
							$countOverMonth ++;
							$countPriceOverMonth += $value['price'];
							break;
						case 10:
							$countFailedMonth ++;
							$countPriceFailedWeek += $value['price'];
						case 4:
							$countAjectMonth ++;
							$countPriceAjectMonth += $value['price'];
							break;	
						case 5:
							$countDoingMonth ++;
							$countPriceDoingMonth += $value['price'];
							break;	
						case 9:
							$countCancelMonth ++;
							$countPriceCancelMonth += $value['price'];
							break;	
					}
					$countMonth ++;		
					$countPriceMonth += $value['price'];
				}
			}
		}
	 
		if($listNew)
		{
			foreach ($listNew  as $value)
			{
				if($value['start_time'] >= $startYesterday && $value['start_time'] <= $endYesterday)
				{
					switch ($value['status'])
					{
						case 7:
							$countFinshedDay ++;
							$countPriceFinishedDay += $value['price'];
							break;
						case 8:
							$countOverDay ++;
							$countPriceOverDay += $value['price'];
							break;
						case 10:
							$countFailedDay ++;
							$countPriceFailedDay += $value['price'];
						case 4:
							$countAjectDay ++;
							$countPriceAjectDay += $value['price'];
							break;	
						case 5:
							$countDoingDay ++;
							$countPriceDoingDay += $value['price'];
							break;	
						case 9:
							$countCancelDay ++;
							$countPriceCancelDay += $value['price'];
							break;	
					}
					$countDay ++;		
					$countPriceDay += $value['price'];	
				}
				if($value['start_time'] >= $startWeek && $value['start_time'] <= $endWeek)
				{
					switch ($value['status'])
					{
						case 7:
							$countFinshedWeek ++;
							$countFinshedWeek += $value['price'];
							break;
						case 8:
							$countOverWeek ++;
							$countPriceOverWeek += $value['price'];
							break;
						case 10:
							$countFailedWeek ++;
							$countPriceFailedWeek += $value['price'];
						case 4:
							$countAjectWeek ++;
							$countPriceAjectWeek += $value['price'];
							break;	
						case 5:
							$countDoingWeek ++;
							$countPriceDoingWeek += $value['price'];
							break;	
						case 9:
							$countCancelWeek ++;
							$countPriceCancelWeek += $value['price'];
							break;	
					}
					$countWeek ++;	
					$countPriceWeek += $value['price'];		
				}
				if ($value['start_time'] >= $startMonth && $value['start_time'] <= $endMonth)
				{
					switch ($value['status'])
					{
						case 7:
							$countFinshedMonth ++;
							$countPriceFinishedMonth += $value['price'];
							break;
						case 8:
							$countOverMonth ++;
							$countPriceOverMonth += $value['price'];
							break;
						case 10:
							$countFailedMonth ++;
							$countPriceFailedWeek += $value['price'];
						case 4:
							$countAjectMonth ++;
							$countPriceAjectMonth += $value['price'];
							break;
						case 5:
							$countDoingMonth ++;
							$countPriceDoingMonth += $value['price'];
							break;	
						case 9:
							$countCancelMonth ++;
							$countPriceCancelMonth += $value['price'];
							break;		
					}
					$countMonth ++;		
					$countPriceMonth += $value['price'];
				}
			}
		}
	 
		$arryTotal['day']   = array($countDay, $countFinshedDay, $countFailedDay, $countOverDay, $countAjectDay, $countCancelDay, $countDoingDay);
		$arryTotal['week']  = array($countWeek, $countFinshedWeek, $countFailedWeek, $countOverWeek, $countAjectWeek, $countCancelWeek, $countDoingWeek);
		$arryTotal['month'] = array($countMonth, $countFinshedMonth, $countFailedMonth, $countOverMonth, $countAjectMonth, $countCancelWeek, $countDoingWeek);
		
		$arryPrice['day']  = array($countPriceDay, $countPriceFinishedDay, $countPriceFailedDay, $countOverDay, $countPriceAjectDay, $countPriceCancelDay, $countPriceDoingDay);
		$arryPrice['week']  = array($countPriceWeek, $countPriceFinishedWeek, $countPriceFailedWeek, $countOverWeek, $countPriceAjectWeek, $countPriceCancelWeek, $countPriceDoingWeek);
		$arryPrice['month']  = array($countPriceMonth, $countPriceFinishedMonth, $countPriceFailedMonth, $countOverMonth, $countPriceAjectMonth, $countPriceCancelMonth, $countPriceDoingMonth);
		
		return array("total"=>$arryTotal, 'price'=>$arryPrice);
    }
    
   
}