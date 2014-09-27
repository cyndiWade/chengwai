<?php

	class IntentionWeixinAccountModel extends AdvertBaseModel
	{

		//新增数据
		public function insertAll($array,$id)
		{
			$new_array = array();
			foreach($array as $key=>$value)
			{
				$new_array[$key] = addslashes($value);
			}
			//用户ID
			$arr['users_id'] = $id;
			//活动订单ID
			$arr['generalize_id'] = $new_array['order_id'];
			//微博账号
			$account_id = explode(',', $new_array['account_ids']);
			foreach($account_id as $value)
			{
				$arr['account_id'] = $value;
				if (parent::get_one_data($arr) == false) {
					$status = $this->add($arr);
				}
				
			}

			return $status;
		}

		//获得账号数量
		public function getListNum($array,$users_id)
		{
			if($array!='')
			{
				$where['intention_id'] = array('IN',$array);
				$where['users_id'] = $users_id;
				$val_list = $this->where($where)->group('intention_id')->field('count(id) as number,intention_id')->select();
				$last_array = array();
				foreach($val_list as $value)
				{
					$last_array[$value['intention_id']] = $value['number'];
				}
				foreach($array as $val)
				{
					if(!in_array($val,array_keys($last_array)))
					{
						$last_array[$val] = '0';
					}
				}
				return $last_array;
			}
		}

	}