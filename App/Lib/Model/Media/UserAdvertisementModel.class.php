<?php

//用户数据模型
class UserAdvertisementModel extends AppBaseModel  
{
	/**	
	 *  给广告主解冻金额
	 *
	 * @param int   $money	订单中媒体号价格
	 * @param int   $userID	广告主用户ID
	 * @param int   $type	操作类型，1为拒单退款，2为过期退款
	 * @param int   $adverttype	1为新闻 2为微信 3为微博
	 * @param int   $order_id	订单ID
	 * 
	 * @author bumtime 2014-11-14
	 * @return array
	 */	
	public function setMoney($money,  $userID, $type =1, $adverttype = 1, $order_id =0)
	{
		//计算折扣*原价从广告主冻结资金里面扣除
		
		$userInfo = $this->field('money, freeze_funds')->where(array("users_id"=>$userID))->find();
	
	    $allMoney 		= $userInfo['money'] + $money;
	    $freeze_funds   = $userInfo['freeze_funds'] - $money;
		$arryMoney['money']			= $allMoney;
		$arryMoney['freeze_funds']	= $freeze_funds;
			
	    $this->where(array("users_id"=>$userID))->save($arryMoney);
	    
	    //保存资金记录
	    $add['users_id'] = $userID;
		$add['shop_number'] = 'JD'.time();
		$add['money'] = $money;
		$add['adormed'] = 2;
		$add['type'] = 6;
		$add['member_info'] = $type==1 ? '该订单无法执行，解冻金额返回' : "该订单已过期，解冻金额返回";
		$add['admin_info']  = '解冻资金';
		$add['time'] = time();
		$add['status'] = 1;
		$add['paytype']  = 2;
		$add['adverttype'] = $adverttype;
		$add['generalizeid'] = $order_id;		

		D('Fund')->add($add);
	    		
	}
	
	/**	
	 *  广告主冻结的资金直接扣款
	 *
	 * @param int   $money	订单中媒体号价格
	 * @param int   $userID	广告主用户ID
	 * @param int   $adverttype	1为新闻 2为微信 3为微博
	 * @param int   $order_id	订单ID
	 * 
	 * @author bumtime 2014-12-06
	 * @return array
	 */	
	public function setXFMoney($money,  $userID, $adverttype = 1, $order_id =0)
	{
		//计算折扣*原价从广告主冻结资金里面扣除
		
		$userInfo = $this->field('money, freeze_funds')->where(array("users_id"=>$userID))->find();
	
	    $freeze_funds   = $userInfo['freeze_funds'] - $money;
		$arryMoney['freeze_funds']	= $freeze_funds;
	    $this->where(array("users_id"=>$userID))->save($arryMoney);
	    
	    //保存资金记录
	    $add['users_id'] = $userID;
		$add['shop_number'] = 'XF'.time();
		$add['money'] = $money;
		$add['adormed'] = 2;
		$add['type'] = 3;
		$add['member_info'] = '该订单执行完成，按订单金额支出';
		$add['admin_info']  = '消费';
		$add['time'] = time();
		$add['status'] = 1;
		$add['paytype']  = 2;
		$add['adverttype'] = $adverttype;
		$add['generalizeid'] = $order_id;	
		D('Fund')->add($add);
	    		
	}
	
	
}