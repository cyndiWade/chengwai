<?php
	//订单号和账号关联表

	class IntentionWeiboAccountModel extends AdvertBaseModel
	{

		public function insertAll($array,$id,$finance)
		{
			$new_array = array();
			foreach($array as $key=>$value)
			{
				$new_array[$key] = addslashes($value);
			}
			//活动订单ID
			$arr['intention_id'] = $new_array['order_id'];
			//平台类型
			$arr['account_type'] = $new_array['pt_type'];
			//调用折扣比例
			$arr['rebate'] = $finance;
			//微博账号
			$account_id = explode(',', $new_array['account_ids']);
			foreach($account_id as $value)
			{
				$arr['account_id'] = $value;
				if (parent::get_one_data($arr) == false) {
					$AccountWeibo = D('AccountWeibo');
					//用户ID
					$arr['users_id'] = $AccountWeibo->getUserId($value);
					$arr['price'] = $AccountWeibo->getCkMoney($value);
					$status = $this->add($arr);
				}
				
			}
			$update['all_price'] = $this->where(array('intention_id'=>$new_array['order_id']))->sum('price');
			$update['smallnumber'] = $this->where(array('intention_id'=>$new_array['order_id']))->count();
			$bool = D('IntentionWeiboOrder')->where(array('id'=>$new_array['order_id']))->save($update);
			if($bool)
			{
				return true;
			}else{
				return false;
			}
		}

		public function getListNum($array,$users_id)
		{
			if($array!='')
			{
				$where['intention_id'] = array('IN',$array);
			//	$where['users_id'] = $users_id;
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
		
		//获取账号下的订单列表
		public function get_account_order ($intention_id) {
			$where['g.intention_id'] = $intention_id;
			//$field = 'g.id AS g_id,g.price AS g_price,g.audit_status AS g_audit_status,a.*';
			$field ='g.id AS g_id,g.audit_status AS g_audit_status,g.rebate AS g_rebate,g.price as g_price,a.*';
			$data = $this->field($field)
			->table($this->prefix.'intention_weibo_account AS g')
			->join($this->prefix.'account_weibo AS a ON g.account_id = a.id')
			->where($where)
			->select();
		
			return $data;
		}
		
	}