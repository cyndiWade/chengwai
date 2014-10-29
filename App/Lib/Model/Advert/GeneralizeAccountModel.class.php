<?php
	//订单号和账号关联表

	class GeneralizeAccountModel extends AdvertBaseModel
	{

		// public function insertAll($array,$id)
		// {
		// 	$new_array = array();
		// 	foreach($array as $key=>$value)
		// 	{
		// 		$new_array[$key] = addslashes($value);
		// 	}
		// 	//活动订单ID
		// 	$arr['generalize_id'] = $new_array['order_id'];
		// 	//获得该订单的发送类型和软硬广
		// 	$generalize_order = D('generalize_order');
		// 	$generalize = $generalize_order->where(array('id'=>$arr['generalize_id']))->field('fslx_type,ryg_type')->find();
		// 	//平台类型
		// 	$arr['account_type'] = $new_array['pt_type'];
		// 	//微博账号
		// 	$account_id = explode(',', $new_array['account_ids']);
		// 	foreach($account_id as $value)
		// 	{
		// 		$arr['account_id'] = $value;
		// 		if (parent::get_one_data($arr) == false) {
		// 			$AccountWeibo = D('AccountWeibo');
		// 			//用户ID
		// 			$arr['users_id'] = $AccountWeibo->getUserId($value);
		// 			$arr['price'] = $AccountWeibo->getRYMoney($generalize['fslx_type'],$generalize['ryg_type'],$arr['account_id']);
		// 			$status = $this->add($arr);
		// 		}
		// 	}
		// 	$update['all_price'] = $this->where(array('generalize_id'=>$new_array['order_id']))->sum('price');
		// 	$bool = $generalize_order->where(array('id'=>$arr['generalize_id']))->save($update);
		// 	if($bool)
		// 	{
		// 		return true;
		// 	}else{
		// 		return false;
		// 	}
		// }

		//微博新流程直接付钱
		public function insertAll($array,$id)
		{
			$new_array = array();
			foreach($array as $key=>$value)
			{
				$new_array[$key] = addslashes($value);
			}
			//活动订单ID
			$arr['generalize_id'] = $new_array['order_id'];
			//获得该订单的发送类型和软硬广
			$generalize_order = D('GeneralizeOrder');
			$generalize = $generalize_order->where(array('id'=>$arr['generalize_id']))->field('fslx_type,ryg_type')->find();
			//平台类型
			$arr['account_type'] = $new_array['pt_type'];
			//微博账号
			$account_id = explode(',', $new_array['account_ids']);
			foreach($account_id as $value)
			{
				$arr['account_id'] = $value;
				if (parent::get_one_data($arr) == false) {
					$AccountWeibo = D('AccountWeibo');
					//用户ID
					$arr['users_id'] = $AccountWeibo->getUserId($value);
					$arr['price'] = $AccountWeibo->getRYMoney($generalize['fslx_type'],$generalize['ryg_type'],$arr['account_id']);
					$status = $this->add($arr);
				}
			}
			$update['all_price'] = $this->where(array('generalize_id'=>$new_array['order_id']))->sum('price');
			$generalize_order->where(array('id'=>$arr['generalize_id']))->save($update);
			$UserAdvertisement = D('UserAdvertisement');
			$money = $UserAdvertisement->where(array('users_id'=>$id))->getField('money');
			$Fund = D('Fund');
			if($money > $update['all_price'])
			{
				$now_money['money'] = (int)$money - (int)$update['all_price'];
				$UserAdvertisement->where(array('users_id'=>$id))->save($now_money);
				$add_arr = array(
					'users_id' => $id,
					'shop_number' => 'XF'.time(),
					'money' => $update['all_price'],
					'type' => 3,
					'adormed' => 2,
					'member_info' => '用户消费',
					'admin_info' => '用户消费',
					'time' => time(),
					'status' => 1
				);
				$Fund->add($add_arr);
				$this->bigOrderChild($new_array['order_id']);
				return true;
			}else{
				$Order_Status = C('Order_Status');
				$Account_Order_Status = C('Account_Order_Status');
				$update_status = array('status'=>$Order_Status[2]['status']);
				$generalize_order->where(array('id'=>$arr['generalize_id']))->save($update_status);
				$audit_status = array('audit_status'=>$Account_Order_Status[1]['status']);
				$this->where(array('generalize_id'=>$arr['generalize_id']))->save($audit_status);
				return false;
			}
		}

		//根据总订单ID处理所有的子订单用户加钱
		private function bigOrderChild($order_id)
		{
			if($order_id!='')
			{
				$now_list = $this->where(array('generalize_id'=>$order_id))->field('users_id,price')->select();
				$Fund = D('Fund');
				$UserMedia = D('UserMedia');
				foreach($now_list as $value)
				{
					$money_media = $UserMedia->where(array('users_id'=>$value['users_id']))->getField('money');
					$now_val['money'] = (int)$value['price'] + (int)$money_media;
					$UserMedia->where(array('users_id'=>$value['users_id']))->save($now_val);
					$media_arr = array(
						'users_id' => $value['users_id'],
						'shop_number' => 'SL'.time(),
						'money' => $money_media,
						'type' => 5,
						'adormed' => 1,
						'member_info' => '收入',
						'admin_info' => '收入',
						'time' => time(),
						'status' => 1
					);
					$Fund->add($media_arr);
				}
				$Account_Order_Status = C('Account_Order_Status');
				$Order_Status = C('Order_Status');
				$all_status = array('audit_status'=>$Account_Order_Status[3]['status']);
				$this->where(array('generalize_id'=>$order_id))->save($all_status);
				$gen_arr = array('status'=>$Order_Status[4]['status']);
				D('GeneralizeOrder')->where(array('id'=>$order_id))->save($gen_arr);
			}
		}


		//获取账号下的订单列表
		public function get_account_order ($generalize_id) {
			$where['g.generalize_id'] = $generalize_id;
			$field = 'g.id AS g_id,g.price AS g_price,g.audit_status AS g_audit_status,g.account_id AS g_account_id,a.*';
				
			$data = $this->field($field)
			->table($this->prefix.'generalize_account AS g')
			->join($this->prefix.'account_weibo AS a ON g.account_id = a.id')
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
						$save = array('status'=>4,'all_price'=>$price);
						$OrderStatus = D('GeneralizeOrder')->where(array('id'=>$zhifu_id))->save($save);
						
						D('Fund')->djFund($account_id,$price);

						//修改关联表的状态为已支付状态
						$Account_Order_Status = C('Account_Order_Status');
						if ($OrderStatus == true) {
							return $this->where(array('generalize_id'=>$zhifu_id,'audit_status'=>1))->save(array('audit_status'=>$Account_Order_Status[3]['status']));
						}
						//return true;
					}else{
						return false;
					}
				}
				
			}
		}


				//新增数据
		public function insertNewAccount($ien_id,$explode_arr)
		{
			if($ien_id!='')
			{
				$exp_arr = explode(',', $explode_arr);
				if(is_array($exp_arr))
				{
					$IntentionWeiboAccount = D('IntentionWeiboAccount');
					$wxAcc = $IntentionWeiboAccount->where(array('id'=>array('in',$exp_arr)))->select();
					//订单下生成新账号
					foreach ($wxAcc as $value) {
						$add['users_id'] = $value['users_id'];
						$add['generalize_id'] = $ien_id;
						$add['account_id'] = $value['account_id'];
						$add['account_type'] = $value['account_type'];
						$add['price'] = $value['price'];
						$add['audit_status'] = 0;
						$this->add($add);
					}
					//修改
					$upadte['audit_status'] = 8;
					$IntentionWeiboAccount->where(array('id'=>array('in',$exp_arr)))->save($upadte);

					//计算总价
					$update_all['all_price'] = $this->where(array('generalize_id'=>$ien_id))->sum('price');
					$bool = D('GeneralizeOrder')->where(array('id'=>$ien_id))->save($update_all);
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
				$name = $this->where(array('a.id'=>$id))->table('app_generalize_account as a')
				->join('app_account_weibo as n on n.id = a.account_id')->field('n.account_name')->find();
				return $name['account_name'];
			}
		}
	}