<?php
	//增加数据

	class FundModel extends AdvertBaseModel
	{

		//流水表插数据
		public function instertFund($array,$users_id,$type)
		{
			$add['users_id'] = $id;
			$add['money']  = $array['total_fee'];
			$add['pay_number']  = $array['trade_no'];
			$add['shop_number']  = $array['out_trade_no'];
			$add['type'] = $type;
			$add['member_info'] = $array['subject'];
			$add['admin_info'] = $array['subject'];
			$add['time'] = strtotime($array['notify_time']);
			$add['status'] = 1;
			$this->add($add);
		}
	}