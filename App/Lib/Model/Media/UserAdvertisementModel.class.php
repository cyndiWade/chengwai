<?php

//用户数据模型
class UserAdvertisementModel extends MediaBaseModel  
{
	/**	
	 *  给广告主解冻金额
	 *
	 * @param int   $money	订单中媒体号价格
	 * @param int   $userID	广告主用户ID
	 * 
	 * @author bumtime 2014-11-14
	 * @return array
	 */	
	public function setMoney($money,  $userID)
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
		$add['shop_number'] = 'XF'.time();
		$add['money'] = $money;
		$add['adormed'] = 2;
		$add['type'] = 6;
		$add['member_info'] = '该订单无法执行，解冻订单金额并返回';
		$add['admin_info']  = '解冻资金';
		$add['time'] = time();
		$add['status'] = 1;
		D('Fund')->add($add);
	    		
	}
	
	
	
	
}