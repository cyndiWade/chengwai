<?php

	class GeneralizeWeixinAccountModel extends AdvertBaseModel
	{

		//新增数据
		public function insertAll($array,$id)
		{
			$new_array = array();
			foreach($array as $key=>$value)
			{
				$new_array[$key] = addslashes($value);
			}
			
			//活动订单ID
			$arr['generalize_id'] = $new_array['order_id'];
			//获得图文类型
			$GeneralizeWeixinOrder = D('GeneralizeWeixinOrder');
			$ggw = $GeneralizeWeixinOrder->where(array('id'=>$new_array['order_id']))->field('ggw_type')->find();
			//微博账号
			$account_id = explode(',', $new_array['account_ids']);
			foreach($account_id as $value)
			{
				$arr['account_id'] = $value;
				if (parent::get_one_data($arr) == false) {
					$AccountWeixin = D('AccountWeixin');
					$arr['users_id'] = $AccountWeixin->getUserId($value);
					$arr['price'] = $AccountWeixin->getWeiType($ggw['ggw_type'],$value);
					$status = $this->add($arr);
				}
			}
			$update['all_price'] = $this->where(array('generalize_id'=>$new_array['order_id']))->sum('price');
			$bool = $GeneralizeWeixinOrder->where(array('id'=>$arr['generalize_id']))->save($update);
			if($bool)
			{
				return true;
			}else{
				return false;
			}
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
						$save = array('status'=>4,'all_price'=>$price);
						$WeixinOrderStatus =  D('GeneralizeWeixinOrder')->where(array('id'=>$zhifu_id))->save($save);
						
						D('Fund')->djFund($account_id,$price);

						//修改关联表的状态为已支付状态
						$Account_Order_Status = C('Account_Order_Status');
						if ($WeixinOrderStatus == true) {
							return $this->where(array('generalize_id'=>$zhifu_id,'audit_status'=>1))->save(array('audit_status'=>$Account_Order_Status[3]['status']));
						}
						
						//return true;
					}else{
						return false;
					}
				}
				
			}
		}
		
		
		public function get_account_order ($generalize_id) {
			$where['g.generalize_id'] = $generalize_id;
			$field = 'g.id AS g_id,g.price AS g_price,g.audit_status AS g_audit_status,g.account_id AS g_account_id,a.*';
				
			$data = $this->field($field)
			->table($this->prefix.'generalize_weixin_account AS g')
			->join($this->prefix.'account_weixin AS a ON g.account_id = a.id')
			->where($where)
			->select();
				
			return $data;
		}

		//新增数据
		public function insertNewAccount($ien_id,$explode_arr)
		{
			if($ien_id!='')
			{
				$exp_arr = explode(',', $explode_arr);
				if(is_array($exp_arr))
				{
					$IntentionWeixinAccount = D('IntentionWeixinAccount');
					$wxAcc = $IntentionWeixinAccount->where(array('id'=>array('in',$exp_arr)))->select();
					//订单下生成新账号
					foreach ($wxAcc as $value) {
						$add['users_id'] = $value['users_id'];
						$add['generalize_id'] = $ien_id;
						$add['account_id'] = $value['account_id'];
						$add['price'] = $value['price'];
						$add['audit_status'] = 0;
						$this->add($add);
					}
					//修改
					$upadte['audit_status'] = 8;
					$IntentionWeixinAccount->where(array('id'=>array('in',$exp_arr)))->save($upadte);

					//计算总价
					$update_all['all_price'] = $this->where(array('generalize_id'=>$ien_id))->sum('price');
					$bool = D('GeneralizeWeixinOrder')->where(array('id'=>$ien_id))->save($update_all);
					if($bool)
					{
						return true;
					}else{
						return false;
					}
				}

			}

		}

		//获取所有确认过的账户价格
		public function getUserPr($small_order_id,$users_id)
		{
			if($small_order_id!='' && $users_id!='')
			{
				$Account_Order_Status = C('Account_Order_Status');
				$all_array = $this->where(array('id'=>$small_order_id))->field('users_id,price')->find();
				$UserMedia = D('UserMedia');
				$UserMedia->insertPirce($all_array['users_id'],$all_array['price']);
				$bool = D('UserAdvertisement')->updateFreeze($users_id,$all_array['price']);
				if($bool)
				{
					$update['audit_status'] = $Account_Order_Status[7]['status'];
					$this->where(array('id'=>$small_order_id))->save($update);
					return true;
				}else{
					return false;
				}
			}else{
				return false;
			}
		}
		
		//获得账户别名
		public function getNickname($id)
		{
			if($id!='')
			{
				$name = $this->where(array('a.id'=>$id))->table('app_generalize_weixin_account as a')
				->join('app_account_weixin as n on n.id = a.account_id')->field('n.account_name')->find();
				return $name['account_name'];
			}
		}
	}