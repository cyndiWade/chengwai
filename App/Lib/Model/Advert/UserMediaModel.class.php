<?php

	class UserMediaModel extends AdvertBaseModel 
	{

		//塞入价格
		public function insertPirce($user_id,$price)
		{
			$money = $this->where(array('users_id'=>$user_id))->field('money')->find();
			$update['money'] = $money['money'] + $price;
			$add['users_id'] = $user_id;
			$add['shop_number'] = 'SL'.time();
			$add['money'] = $price;
			$add['type'] = 5;
			$add['member_info'] = '收入';
			$add['admin_info'] = '收入';
			$add['time'] = time();
			D('Fund')->add($add);
			$this->where(array('users_id'=>$user_id))->save($update);
		}
	}