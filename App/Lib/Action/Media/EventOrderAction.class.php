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
        
        // 收藏或者黑名单数量
        $newsDatas = M('BlackorcollectionNews')->where(array('user_id' => $userInfos['id']))->getField('or_type', true);
        $weiboDatas = M('BlackorcollectionWeibo')->where(array('user_id' => $userInfos['id']))->getField('or_type', true);
        $weixinDatas = M('BlackorcollectionWeixin')->where(array('user_id' => $userInfos['id']))->getField('or_type', true);
        $newsDatas = $newsDatas ? array_count_values($newsDatas) : array(0, 0);
        $weiboDatas = $weiboDatas ? array_count_values($weiboDatas) : array(0, 0);
        $weixinDatas = $weixinDatas ? array_count_values($weixinDatas) : array(0, 0);
        $blackNums = $newsDatas[0] + $weiboDatas[0] + $weixinDatas[0];
        $markNums = $newsDatas[1] + $weiboDatas[1] + $weixinDatas[1];
        
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
                if ($info['audit_status'] == 2) {
                    $auditFailNums += 1;
                } elseif ($info['audit_status'] == 0) {
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
                if ($info['audit_status'] == 2) {
                    $auditFailNums += 1;
                } elseif ($info['audit_status'] == 0) {
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
                if ($info['audit_status'] == 2) {
                    $auditFailNums += 1;
                } elseif ($info['audit_status'] == 0) {
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
        
        $newsOrderModel = $this->db['GeneralizeNewsOrder'];
        $weiboOrderModel = $this->db['GeneralizeOrder'];
        $weixinOrderModel = $this->db['GeneralizeWeixinOrder'];
        // 今日待上传数据截图订单 数量
        $todayShotNums = 0;
        // 非今日待上传数据截图订单 数量
        $noTodayShotNums = 0;
        $where = array(
            'users_id'  => $userInfos['id'],
            'status'    => 5
        );
        $todayStartTime = strtotime(date('Y-m-d', $_SERVER['REQUEST_TIME']));
        $todayEndTime = $todayStartTime + 86399;
        // 新闻媒体
        $newsShotOrder = $newsOrderModel->where($where)->select();
        if ($newsShotOrder) {
            foreach ($newsShotOrder AS $info) {
                if ($info['start_time'] >= $todayEndTime && $info['start_time'] <= $todayEndTime) {
                    $todayShotNums += 1;
                } else {
                    $noTodayShotNums += 1;
                }
            }
        }
        // 微博
        $weiboShotOrder = $weiboOrderModel->where($where)->select();
        if ($weiboShotOrder) {
            foreach ($weiboShotOrder AS $info) {
                if ($info['start_time'] >= $todayEndTime && $info['start_time'] <= $todayEndTime) {
                    $todayShotNums += 1;
                } else {
                    $noTodayShotNums += 1;
                }
            }
        }
        // 微信
        $weixinShotOrder = $weiboOrderModel->where($where)->select();
        if ($weixinShotOrder) {
            foreach ($weixinShotOrder AS $info) {
                if ($info['start_time'] >= $todayEndTime && $info['start_time'] <= $todayEndTime) {
                    $todayShotNums += 1;
                } else {
                    $noTodayShotNums += 1;
                }
            }
        }
        
        parent::data_to_view(array(
			//二级导航
			'secondSiderbar' => array(
				'待执行订单' => array('select' => true, 'url' => U('/Media/EventOrder/index')),
				'全部订单'   => array('select' => false, 'url' => U('/Media/EventOrder/allorder')),
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
            if ($type == 3) {
                $where = array(
                    'users_id' => $userInfos['id'],
                    'status' => 5,
                    'start_time' => array('NOT BETWEEN', array($todayStartTime, $todayEndTime))
                );
            } elseif ($type == 2) {
                $where = array(
                    'users_id' => $userInfos['id'],
                    'status' => 3,
                    'start_time' => array('NOT BETWEEN', array($todayStartTime, $todayEndTime))
                );
            } elseif ($type == 1) {
                $where = array(
                    'users_id' => $userInfos['id'],
                    'status' => 5,
                    'start_time' => array('BETWEEN', array($todayStartTime, $todayEndTime))
                );
            } else {
                $where = array(
                    'users_id' => $userInfos['id'],
                    'status' => 3,
                    'start_time' => array('BETWEEN', array($todayStartTime, $todayEndTime))
                );
            }
            
            // 获取帐号信息
            $datas = array();
            $newsOrder = $newsOrderModel->getOrderList($userInfos['id'], $where);
            $weiboOrder = $weiboOrderModel->getOrderList($userInfos['id'], $where);
            $weixinOrder = $weixinOrderModel->getOrderList($userInfos['id'], $where);
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
				'微博订单'		=> array('select' => true, 'url' => U('/Media/EventOrder/allorder')),
				'微信订单'		=> array('select' => false, 'url' => U('/Media/EventOrder/allorderWeixin')),
				'新闻媒体订单'	=> array('select' => false, 'url' => U('/Media/EventOrder/allorderNews')),
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
			$count      = $GeneralizeAccount->alias('ga')->where($where)->count();
			
			$sum		= $GeneralizeAccount->alias('ga')->where($where)->sum('price');
			
			$Page       = new Page($count,10);
			$show       = $Page->show();
 
			$list = $GeneralizeAccount->alias('ga')->join(" ".C('db_prefix')."generalize_order go on ga.generalize_id = go.id")->where($where)->limit($Page->firstRow.','.$Page->listRows)
					->order('ga.id desc')->field('`generalize_id`, `account_type`, `account_id`, `price`, `audit_status`,  go.`id` as order_id, ga.id,
												 `tfpt_type`, `fslx_type`, `ryg_type`, `hd_name`, `start_time`, `all_price`, `status`')->select();
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
				'微博订单'		=> array('select' => true, 'url' => U('/Media/EventOrder/allorder')),
				'微信订单'		=> array('select' => false, 'url' => U('/Media/EventOrder/allorderWeixin')),
				'新闻媒体订单'	=> array('select' => false, 'url' => U('/Media/EventOrder/allorderNews'))
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
				'微博订单'		=> array('select' => false, 'url' => U('/Media/EventOrder/allorder')),
				'微信订单'		=> array('select' => true, 'url' => U('/Media/EventOrder/allorderWeixin')),
				'新闻媒体订单'	=> array('select' => false, 'url' => U('/Media/EventOrder/allorderNews')),
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
					->order('ga.id desc')->field('`generalize_id`, `account_id`, `price`, `audit_status`,  go.`id` as order_id, ga.id,
												 `ggw_type`, `yxd_name`, `title`, `start_time`, `status`')->select();
					 
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
				'微博订单'		=> array('select' => false, 'url' => U('/Media/EventOrder/allorder')),
				'微信订单'		=> array('select' => true, 'url' => U('/Media/EventOrder/allorderWeixin')),
				'新闻媒体订单'	=> array('select' => false, 'url' => U('/Media/EventOrder/allorderNews'))
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
					->order('ga.id desc')->field('ga.id, `price`, ga.`audit_status`,`account_name`')->select();
				 
		//统计
		$count      	= $GeneralizeAccount->where($where)->count();
		$sum			= $GeneralizeAccount->where($where)->sum('price');
		//配图
		$file_where = array("generalize_order_id"=>$order_id, "type"=>2);
		$order_file = D('GeneralizeWeixinFiles')->field('url')->where($file_where)->find();
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
				'微博订单'		=> array('select' => false, 'url' => U('/Media/EventOrder/allorder')),
				'微信订单'		=> array('select' => false, 'url' => U('/Media/EventOrder/allorderWeixin')),
				'新闻媒体订单'	=> array('select' => true, 'url' => U('/Media/EventOrder/allorderNews')),
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
					->order('ga.id desc')->field('`generalize_id`, `account_id`, `price`, `audit_status`, go.`id` as order_id, ga.id,
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
				'微博订单'		=> array('select' => false, 'url' => U('/Media/EventOrder/allorder')),
				'微信订单'		=> array('select' => false, 'url' => U('/Media/EventOrder/allorderWeixin')),
				'新闻媒体订单'	=> array('select' => true, 'url' => U('/Media/EventOrder/allorderNews'))
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
					->order('ga.id desc')->field('ga.id, `price`, ga.`audit_status`,`account_name`')->select();
					 
		//统计
		$count      	= $GeneralizeAccount->where($where)->count();
		$sum			= $GeneralizeAccount->where($where)->sum('price');
		//配图
		$file_where = array("generalize_order_id"=>$order_id, "type"=>2);
		$order_file = D('GeneralizeNewsFiles')->field('url')->where($file_where)->find();
		$order_info["order_file"] = $order_file ? $order_file : "";
		
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
   		
   		switch ($type)
   		{
   			case 'weibo':
   				$GeneralizeAccount	= D('GeneralizeAccount');
    			$mediaObject		= D('AccountWeibo');
    			$typeTip			= 2;
    			break;
   			case 'weixin':
   				$GeneralizeAccount	= D('GeneralizeWeixinAccount');
    			$mediaObject		= D('AccountWeixin');
    			$typeTip			= 4;
    			break; 
   			case 'news':
   				$GeneralizeAccount	= D('GeneralizeNewsAccount');
    			$mediaObject		= D('AccountNews');
    			$typeTip			= 1;
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
}