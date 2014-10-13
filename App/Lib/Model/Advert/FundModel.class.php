<?php
	//增加数据

	class FundModel extends AdvertBaseModel
	{
		//得到用户数据
		public function getUserId($array)
		{
			$where = array('f.shop_number'=>$array['out_trade_no']);
			$val = $this->where($where)->table('app_fund as f')
			->join('app_users as u on u.id = f.users_id')->Field('u.id,u.account,u.type,u.nickname')->find();
			return array('id'=>$val['id'],'account'=>$val['account'],'type'=>$val['type'],'nickname'=>$val['nickname']);
		}

		//流水表插数据
		public function instertFund($array,$type)
		{
			$where = array('shop_number'=>$array['out_trade_no']);
			$update['pay_number']  	= $array['trade_no'];
			$update['time'] 		= strtotime($array['notify_time']);
			$update['money']	= $array['total_fee'];
			$update['status'] 		= 1;
			$this->where($where)->save($update);
		}

		//未存入先插一笔数据
		public function insertNoArr($array,$users_id,$type)
		{
			$add['users_id'] = $users_id;
			$add['money']  = $array['spprice'];
			$add['shop_number']  = $array['spnumber'];
			$add['type'] = $type;
			$add['member_info'] = $array['spname'];
			$add['admin_info'] = $array['spinfo'];
			$add['time'] = time();
			$add['status'] = 0;
			return $this->add($add);
		}

		//冻结资金流水
		public function djFund($account_id,$price)
		{
			$insertArr = array(
				'users_id'	=>	$account_id,
				'shop_number'	=>	'DJ'.time(),
				'money'	=>	$price,
				'type'	=>	4,
				'member_info'	=>	'冻结资金',
				'admin_info'	=>	'冻结资金',
				'time'	=>	time()
			);
			$this->add($insertArr);
		}
	}