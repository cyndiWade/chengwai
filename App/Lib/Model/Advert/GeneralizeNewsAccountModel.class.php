<?php
	
	class GeneralizeNewsAccountModel extends AdvertBaseModel
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
					$arr['price'] = D('AccountNews')->getNewsmoney($value);
					$status = $this->add($arr);
				}
				
			}
			$update['all_price'] = $this->where(array('generalize_id'=>$new_array['order_id']))->sum('price');
			$bool = D('GeneralizeNewsOrder')->where(array('id'=>$arr['generalize_id']))->save($update);
			if($bool)
			{
				return true;
			}else{
				return false;
			}
			return $status;
		}
		
		
		//获取账号下的订单列表
		public function get_account_order ($generalize_id) {
			$where['g.generalize_id'] = $generalize_id;
			$field = 'g.id AS g_id,g.price AS g_price,g.audit_status AS g_audit_status,g.account_id AS g_account_id,a.*';
			
			$data = $this->field($field)
			->table($this->prefix.'generalize_news_account AS g')
			->join($this->prefix.'account_news AS a ON g.account_id = a.id')
			->where($where)
			->select();
			
			return $data;
		}

		//支付开始
		public function siteMoney($zhifu_id,$account_id)
		{
		
			if($zhifu_id!='')
			{
				$UserAdvertisement = D('UserAdvertisement');
				//审核通过的价格
				$price = $this->where(array('generalize_id'=>$zhifu_id,'audit_status'=>1))->sum('price');
				$account_money = $UserAdvertisement->where(array('users_id'=>$account_id))->field('money,freeze_funds')->find();
				if($account_money['money'] < $price)
				{
					return false;
				}else{
					$money = $account_money['money'] - $price;
					$zprice = $price + $account_money['freeze_funds'];
					$update = array('money'=>$money,'freeze_funds'=>$zprice);
					$bool = $UserAdvertisement->where(array('users_id'=>$account_id))->save($update);
					if($bool)
					{
						$save = array('status'=>4);	//已支付待执行状态
						$NewsOrderStatus = D('GeneralizeNewsOrder')->where(array('id'=>$zhifu_id))->save($save);
						
						D('Fund')->djFund($account_id,$price);

						$Account_Order_Status = C('Account_Order_Status');
						
						if ($NewsOrderStatus == true) {
							return $this->where(array('generalize_id'=>$zhifu_id))->save(array('audit_status'=>$Account_Order_Status[3]['status']));
						}
						
						//return true;
					}else{
						return false;
					}
				}
				
			}
		}
		
	}