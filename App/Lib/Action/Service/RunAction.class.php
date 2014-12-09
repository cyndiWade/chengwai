<?php
/**
 * 定时执行服务
 */
class RunAction extends AppBaseAction 
{
	//每个类都要重写此变量
	protected  $is_check_rbac = true;	
		//无需登录和验证rbac的方法名
	protected  $not_check_fn = array('register', 'check_login', 'login', 'logout', 'register_accout', 'checkPhone', 'checkUserName','verify');	
	
	//控制器说明
	private $module_explain = '定时执行任务';
		
	//初始化数据库连接
	protected  $db = array(
		//新闻媒体订单
		'GeneralizeNewsOrder'=>'GeneralizeNewsOrder',	

		//新闻推广单关联表
		'GeneralizeNewsAccount'=>'GeneralizeNewsAccount',

		//微博推广单	
		'GeneralizeOrder'=>'GeneralizeOrder',
		//微博推广单，订单关联表
		'GeneralizeAccount'=>'GeneralizeAccount',
			
		//微博意向单
		'IntentionWeiboOrder'=>'IntentionWeiboOrder',
		//微博意向单、订单关联边	
		'IntentionWeiboAccount'=>'IntentionWeiboAccount',
			
		//微信推广单
		'GeneralizeWeixinOrder'=>'GeneralizeWeixinOrder',
		//微信推广单，订单关联表
		'GeneralizeWeixinAccount'=>'GeneralizeWeixinAccount',	
			
		//微信意向单表
		'IntentionWeixinOrder'=>'IntentionWeixinOrder',
		//微信意向单，订单关联边
		'IntentionWeixinAccount'=>'IntentionWeixinAccount',
	);
	private $serverIP = '127.0.0.1';
	private $orderConfirm = 5;
	private $orderExcute  = 5; 
	
	//和构造方法
	public function __construct() {
		parent::__construct();
	}
	
	public function index()
	{
		$where  = array();
		$arryID	=	array();
		$where['start_time'] = array("lt", time());
		//订单过期
		$weiboOrderList		=	$this->db['GeneralizeOrder']->where($where)->getField('id, status');
		$weixinOrderList	=	$this->db['GeneralizeWeixinOrder']->where($where)->getField('id, status');
		$newsOrderList		=	$this->db['GeneralizeNewsOrder']->where($where)->getField('id, status');	

		/*if($weiboOrderList)
		{ 
			 $this->setStatus($weiboOrderList, 'weibo');	 
		}*/

		if($weixinOrderList)
		{
			 $this->setStatus($weixinOrderList, 'weixin');	 
		}	
		/*	
		if($newsOrderList)
		{
			 $this->setStatus($newsOrderList, 'news');	 
		}	*/		
		
		//意向单过期
		$weiboIntenOrderList	=	$this->db['IntentionWeiboOrder']->where($where)->getField('id, status');
		$weixinIntenOrderList	=	$this->db['IntentionWeixinOrder']->where($where)->getField('id, status');	
		
		if($weiboIntenOrderList)
		{
			$this->setIntStatus($weiboIntenOrderList, 'weibo');
		}
		if($weixinIntenOrderList)
		{
			$this->setIntStatus($weixinIntenOrderList, "weixin");
		}
		
	}
	
	 /**
     * 处理推广单的状态
     * 
     * @param  array 	$orderList	订单数组
     * @param  string   $type	    类型：weibo weixin news
     * @author bumtime
     * @date   2014-12-06
     * 
     * @return array   
     **/
	private function setStatus($orderList, $type)
	{
		$AccoutList = array();
		$arryID = array_keys($orderList);
		$whereAccout['generalize_id'] = array('in', implode(",", $arryID));

		
		switch($type)
		{
			case 'weibo':
				$mediaObject = M('GeneralizeAccount');
				$orderObject = M('GeneralizeOrder');
				$mediaType	 =	3;
				break;
				
			case 'weixin':
				$mediaObject = M('GeneralizeWeixinAccount');
				$orderObject = M('GeneralizeWeixinOrder');
				$mediaType	 =	2;
				break;			
				
			case 'news':
				$mediaObject = M('GeneralizeNewsAccount');
				$orderObject = M('GeneralizeNewsOrder');
				$mediaType	 =	1;
				break;				
				
		}
		
		 $AccoutList = $mediaObject->where($whereAccout)->field('id,audit_status,generalize_id')->select();

		 
		if($AccoutList)
		{
			foreach ($AccoutList as $value) 
			{
					    		
				switch ($value['audit_status'])	
				{
					//未付款执行时间过期转成已过期
					case 1:
					case 2:
					case 0:
						$mediaObject->where(array('id'=>$value['id']))->save(array('audit_status'=>9)); 
						break;
						
					//支付后过期自动退款
					case 3:
						

						$totalPrice = 0 ;
						$order_info = $orderObject->field('users_id, id, start_time')->where(array('id'=>$value['generalize_id']))->find();
						$adUserID 	= $order_info['users_id'];
		
			    		$accoutList = $mediaObject->where(array('generalize_id'=>$order_info['id']))->field('`account_id`, `price`, `rebate`, `audit_status`')->select();
					
			    		//总金额
			    		foreach ($accoutList  as $value2)
			    		{
			    			$totalPrice += getAdMoney($value2['price'], $type, $value2['rebate']);
			    		}
						//给广告主解冻
			    		D("Media/UserAdvertisement")->setMoney($totalPrice, $adUserID, 2, $mediaType, $order_info['id']);
			    		
			    		$mediaObject->where(array('id'=>$value['id']))->save(array('audit_status'=>9)); 
			    		
			    		$orderObject->where(array("id"=>$order_info['id']))->save(array('status'=>5));	 	
						break;
						
					//媒体主执行中，5天后没上传资料转为订单完成，并退款
					case 5:
						//如果已支付，拒绝就需要退款 20141204 bumtime
						$totalPrice = 0 ;
						$order_info = $orderObject->field('users_id, id, start_time')->where(array('id'=>$value['generalize_id']))->find();
						$adUserID 	= $order_info['users_id'];
						
						//执行时间，5天后自动完成
						if(($order_info['start_time'] + 3600*24*$this->orderExcute) >= time() )
						{
							
				    		$accoutList = $mediaObject->where(array('generalize_id'=>$order_info['id']))->field('`account_id`, `price`, `rebate`, `audit_status`')->select();
				    		//总金额
				    		foreach ($accoutList  as $value2)
				    		{
				    			$totalPrice += getAdMoney($value2['price'], $type, $value2['rebate']);
				    		}
							//给广告主解冻
				    		D("Media/UserAdvertisement")->setMoney($totalPrice, $adUserID, 2, $mediaType, $order_info['id']);
				    		
				    		$mediaObject->where(array('id'=>$value['id']))->save(array('audit_status'=>7)); 
						}
						
				    	$orderObject->where(array("id"=>$order_info['id']))->save(array('status'=>5));
						break;
						
					//媒体主执行完成，5天后转为订单完成
					case 6:	 
						$totalPrice = 0 ;
						$order_info = $orderObject->field('users_id, id, start_time')->where(array('id'=>$value['generalize_id']))->find();
						$adUserID 	= $order_info['users_id'];
						
						//执行时间，5天后自动完成
						if(($order_info['start_time'] + 3600 * 24 * $this->orderConfirm) >= time() )
						{
							
				    		$accoutList = $mediaObject->where(array('generalize_id'=>$order_info['id']))->field('`account_id`, `price`, `rebate`, `audit_status`,	users_id')->select();
				    		
				    		//总金额
				    		foreach ($accoutList  as $value2)
				    		{
								//统计订单总金额
				    			$totalPrice += getAdMoney($value2['price'], $type, $value2['rebate']);
				    			
				    			//给媒体主加款
				    			$UserMedia = D('Media/UserMedia');
								$UserMedia->insertPirce($value['users_id'], $value['price'], 3, $order_info['id']);	
				    		}
				    		//给广告主扣款
				    		D("Media/UserAdvertisement")->setXFMoney($totalPrice, $adUserID);

				    		$mediaObject->where(array('id'=>$value['id']))->save(array('audit_status'=>7)); 
						}
						$orderObject->where(array("id"=>$order_info['id']))->save(array('status'=>5));
						break;					
				}
				
			}
		}
	}
	
	
	 /**
     * 处理意向单状态
     * 
     * @param  array 	$orderList	订单数组
     * @param  string   $type	    类型：weibo weixin
     * @author bumtime
     * @date   2014-12-06
     * 
     * @return array   
     **/
	private function setIntStatus($orderList, $type)
	{
		switch($type)
		{
			case 'weibo':
				$mediaObject = $this->db['IntentionWeiboAccount'];
				$orderObject = $this->db['IntentionWeiboOrder'];
				break;
				
			case 'weixin':
				$mediaObject = $this->db['IntentionWeixinAccount'];
				$orderObject = $this->db['IntentionWeixinOrder'];
				break;				
		}
		
		$arryID = array_keys($orderList);
		$whereAccout['intention_id'] = array('in', $arryID);
		$AccoutList = $mediaObject->where($whereAccout)->field('id,audit_status')->select();
			 
		if($AccoutList)
		{
			foreach ($AccoutList as $value) 
			{
				switch ($value['audit_status'])	
				{
					//未付款执行时间过期转成已过期
					case 0:
					case 3:
						$mediaObject->where(array('id'=>$value['id']))->save(array('audit_status'=>9)); 
						break;	
				}	
			}
		}
	}
}