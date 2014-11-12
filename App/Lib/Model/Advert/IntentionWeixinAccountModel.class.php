<?php

	class IntentionWeixinAccountModel extends AdvertBaseModel
	{

		//新增数据
		public function insertAll($array,$id,$finance)
		{
			$new_array = array();
			foreach($array as $key=>$value)
			{
				$new_array[$key] = addslashes($value);
			}
			//活动订单ID
			$arr['generalize_id'] = $new_array['order_id'];
			//调用折扣比例
			$arr['rebate'] = $finance;
			//微博账号
			$account_id = explode(',', $new_array['account_ids']);
			foreach($account_id as $value)
			{
				$arr['account_id'] = $value;
				if (parent::get_one_data($arr) == false) {
					$AccountWeixin = D('AccountWeixin');
					//用户ID
					$arr['users_id'] = $AccountWeixin->getUserId($value);
					//取得参考价格
					$arr['price'] = $AccountWeixin->getWXMoney($value);
					$status = $this->add($arr);
				}
			}
			$update['all_price'] = $this->where(array('generalize_id'=>$new_array['order_id']))->sum('price');
			$bool = D('IntentionWeixinOrder')->where(array('id'=>$new_array['order_id']))->save($update);
			if($bool)
			{
				return true;
			}else{
				return false;
			}
		}

		//获得账号数量
		public function getListNum($array,$users_id)
		{
			if($array!='')
			{
				$where['generalize_id'] = array('IN',$array);
				$where['users_id'] = $users_id;
				$val_list = $this->where($where)->group('generalize_id')->field('count(id) as number,generalize_id')->select();
				$last_array = array();
				foreach($val_list as $value)
				{
					$last_array[$value['generalize_id']] = $value['number'];
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
		public function get_account_order ($generalize_id) {
			$where['g.generalize_id'] = $generalize_id;
			//$field = 'g.id AS g_id,g.price AS g_price,g.audit_status AS g_audit_status,a.*';
			$field ='g.id AS g_id,g.rebate AS g_rebate,g.audit_status AS g_audit_status,g.price as g_price,a.*';
			$data = $this->field($field)
			->table($this->prefix.'intention_weixin_account AS g')
			->join($this->prefix.'account_weixin AS a ON g.account_id = a.id')
			->where($where)
			->select();

			return $data;
		}
		
		
		//批量修改订单账号状态
		public function update_account_status ($ids,$order_id,$status,$user_id) {
			return $this->where(array('id'=>$ids,'generalize_id'=>$order_id,'user_id'=>$user_id))->save(array('audit_status'=>$status));
		}

	}