<?php
	//订单号和账号关联表

	class GeneralizeAccountModel extends AdvertBaseModel
	{

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
			//平台类型
			$arr['account_type'] = $new_array['pt_type'];
			//微博账号
			$account_id = explode(',', $new_array['account_ids']);
			foreach($account_id as $value)
			{
				$arr['account_id'] = $value;
				$this->add($arr);
			}

		}

		
	}