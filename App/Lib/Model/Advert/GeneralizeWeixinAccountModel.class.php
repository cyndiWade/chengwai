<?php

	class GeneralizeWeixinAccountModel extends AdvertBaseModel
	{

		//新增数据
		// public function insertAll($array,$id)
		// {
		// 	$new_array = array();
		// 	foreach($array as $key=>$value)
		// 	{
		// 		$new_array[$key] = addslashes($value);
		// 	}
			
		// 	//活动订单ID
		// 	$arr['generalize_id'] = $new_array['order_id'];
		// 	//获得图文类型
		// 	$GeneralizeWeixinOrder = D('GeneralizeWeixinOrder');
		// 	$ggw = $GeneralizeWeixinOrder->where(array('id'=>$new_array['order_id']))->field('ggw_type')->find();
		// 	//微博账号
		// 	$account_id = explode(',', $new_array['account_ids']);
		// 	foreach($account_id as $value)
		// 	{
		// 		$arr['account_id'] = $value;
		// 		if (parent::get_one_data($arr) == false) {
		// 			$AccountWeixin = D('AccountWeixin');
		// 			$arr['users_id'] = $AccountWeixin->getUserId($value);
		// 			$arr['price'] = $AccountWeixin->getWeiType($ggw['ggw_type'],$value);
		// 			$status = $this->add($arr);
		// 		}
		// 	}
		// 	$update['all_price'] = $this->where(array('generalize_id'=>$new_array['order_id']))->sum('price');
		// 	$bool = $GeneralizeWeixinOrder->where(array('id'=>$arr['generalize_id']))->save($update);
		// 	if($bool)
		// 	{
		// 		return true;
		// 	}else{
		// 		return false;
		// 	}
		// }


		//新增数据直接付款的流程
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

			//计算小订单价格价格
			$sum_price = $this->where(array('generalize_id'=>$new_array['order_id']))->field('price,rebate,audit_status')->select();
			//状态未支付订单价格
			$all_price = 0;
			//总订单价格
			$now_price = 0;
			//获得小订单的数量
			$number = 0;
			foreach($sum_price as $price)
			{
				//获得未计算的小订单价格
				if($price['audit_status']==0 || $price['audit_status']==1)
				{
					$all_price += $price['price'] + ($price['price'] * $price['rebate']);
				}
				$now_price += $price['price'] + ($price['price'] * $price['rebate']);
				$number++;
			}

			$update['all_price'] = $now_price;
			$update['smallnumber'] = $number;
			
			$GeneralizeWeixinOrder->where(array('id'=>$arr['generalize_id']))->save($update);


			$UserAdvertisement = D('UserAdvertisement');

			//获得用户的账户信息
			$money = $UserAdvertisement->where(array('users_id'=>$id))->field('money,freeze_funds')->find();


			$Fund = D('Fund');
			$Order_Status = C('Order_Status');
			$Account_Order_Status = C('Account_Order_Status');


			
			if($money['money'] > $all_price)
			{
				$now_money['money'] = $money['money'] - $all_price;

				$now_money['freeze_funds'] = $money['freeze_funds'] + $all_price;

				$UserAdvertisement->where(array('users_id'=>$id))->save($now_money);

				$add_arr = array(
					'users_id' => $id,
					'shop_number' => 'DJ'.time(),
					'money' => $all_price,
					'type' => 4,
					'adormed' => 2,
					'member_info' => '冻结资金',
					'admin_info' => '冻结资金',
					'time' => time(),
					'adverttype' => 2,
					'generalizeid' => $arr['generalize_id'],
					'status' => 1
				);
				$Fund->add($add_arr);

				$all_status = array('audit_status'=>$Account_Order_Status[3]['status']);
				$old_where['generalize_id'] = $new_array['order_id'];
				$old_where['audit_status'] = array(array('eq',0),array('eq',1),'or');
				$this->where($old_where)->save($all_status);
				$gen_arr = array('status'=>$Order_Status[4]['status']);
				$GeneralizeWeixinOrder->where(array('id'=>$new_array['order_id']))->save($gen_arr);

				$this->send_media_mess($arr['generalize_id']);
				//$this->bigOrderChild($new_array['order_id']);
				return true;
			}else{
				$update_status = array('status'=>$Order_Status[2]['status']);
				$GeneralizeWeixinOrder->where(array('id'=>$arr['generalize_id']))->save($update_status);
				$audit_status = array('audit_status'=>$Account_Order_Status[1]['status']);
				$old_where['generalize_id'] = $new_array['order_id'];
				$old_where['audit_status'] = array(array('eq',0),array('eq',1),'or');
				$this->where($old_where)->save($audit_status);
				return true;
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
				D('GeneralizeWeixinOrder')->where(array('id'=>$order_id))->save($gen_arr);
			}
		}



		//支付开始
		public function siteMoney($zhifu_id,$account_id)
		{
			if($zhifu_id!='')
			{
				$UserAdvertisement = D('UserAdvertisement');

				$Account_Order_Status = C('Account_Order_Status');
				//审核通过的价格
				$price_select = $this->where(array('generalize_id'=>$zhifu_id,'audit_status'=>1))->field('price,rebate')->select();
				$price = 0;
				foreach($price_select as $val)
				{
					$price += $val['price'] + ($val['price'] * $val['rebate']);
				}
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
						$save = array('status'=>$Account_Order_Status[4]['status'],'all_price'=>$price);
						$WeixinOrderStatus =  D('GeneralizeWeixinOrder')->where(array('id'=>$zhifu_id))->save($save);
						
						D('Fund')->djFund($account_id,$price,$zhifu_id,2);

						//修改关联表的状态为已支付状态
						$Account_Order_Status = C('Account_Order_Status');
						if ($WeixinOrderStatus == true) {

							$this->send_media_mess($zhifu_id);

							return $this->where(array('generalize_id'=>$zhifu_id,'audit_status'=>$Account_Order_Status[1]['status']))->save(array('audit_status'=>$Account_Order_Status[3]['status']));
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
			$field = 'g.id AS g_id,g.price AS g_price,g.rebate AS g_rebate,g.audit_status AS g_audit_status,g.account_id AS g_account_id,a.*';
				
			$data = $this->field($field)
			->table($this->prefix.'generalize_weixin_account AS g')
			->join($this->prefix.'account_weixin AS a ON g.account_id = a.id')
			->where($where)
			->select();
				
			return $data;
		}

		//新增数据
		public function insertNewAccount($ien_id,$explode_arr,$user_id)
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
						$add['rebate'] = $value['rebate'];
						$add['audit_status'] = 0;
						$this->add($add);
					}
					//修改
					$upadte['audit_status'] = 8;
					$IntentionWeixinAccount->where(array('id'=>array('in',$exp_arr)))->save($upadte);

					//计算价格
					$sum_price = $this->where(array('generalize_id'=>$ien_id))->field('price,rebate,audit_status')->select();
					//状态未支付订单价格
					$all_price = 0;
					//总订单价格
					$now_price = 0;
					//获得小订单的数量
					$number = 0;
					foreach($sum_price as $price)
					{
						//获得未计算的小订单价格
						if($price['audit_status']==0 || $price['audit_status']==1)
						{
							$all_price += $price['price'] + ($price['price'] * $price['rebate']);
						}
						$now_price += $price['price'] + ($price['price'] * $price['rebate']);
						$number++;
					}
					$update['all_price'] = $now_price;
					$update['smallnumber'] = $number;

					$GeneralizeWeixinOrder = D('GeneralizeWeixinOrder');
					$GeneralizeWeixinOrder->where(array('id'=>$ien_id))->save($update);


					$UserAdvertisement = D('UserAdvertisement');

					//获得用户的账户信息
					$money = $UserAdvertisement->where(array('users_id'=>$user_id))->field('money,freeze_funds')->find();


					$Fund = D('Fund');
					$Order_Status = C('Order_Status');
					$Account_Order_Status = C('Account_Order_Status');



					if($money['money'] > $all_price)
					{
						$now_money['money'] = $money['money'] - $all_price;

						$now_money['freeze_funds'] = $money['freeze_funds'] + $all_price;

						$UserAdvertisement->where(array('users_id'=>$user_id))->save($now_money);

						$add_arr = array(
							'users_id' => $user_id,
							'shop_number' => 'DJ'.time(),
							'money' => $all_price,
							'type' => 4,
							'adormed' => 2,
							'member_info' => '冻结资金',
							'admin_info' => '冻结资金',
							'time' => time(),
							'status' => 1
						);
						$Fund->add($add_arr);

						$all_status = array('audit_status'=>$Account_Order_Status[3]['status']);
						$old_where['generalize_id'] = $ien_id;
						$old_where['audit_status'] = array(array('eq',0),array('eq',1),'or');
						$this->where($old_where)->save($all_status);
						$gen_arr = array('status'=>$Order_Status[4]['status']);
						$GeneralizeWeixinOrder->where(array('id'=>$ien_id))->save($gen_arr);

						$this->send_media_mess($ien_id);
						//$this->bigOrderChild($new_array['order_id']);
						return true;
					}else{
						$update_status = array('status'=>$Order_Status[2]['status']);
						$GeneralizeWeixinOrder->where(array('id'=>$ien_id))->save($update_status);
						$audit_status = array('audit_status'=>$Account_Order_Status[1]['status']);
						$old_where['generalize_id'] = $ien_id;
						$old_where['audit_status'] = array(array('eq',0),array('eq',1),'or');
						$this->where($old_where)->save($audit_status);
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
				$all_array = $this->where(array('id'=>$small_order_id))->field('generalize_id,users_id,price,rebate')->find();
				$UserMedia = D('UserMedia');
				$UserMedia->insertPirce($all_array['users_id'],$all_array['price'],2,$all_array['generalize_id']);
				//计算折扣*原价从广告主冻结资金里面扣除
				$sum_price = $all_array['price'] + ($all_array['price'] * $all_array['rebate']);
				$bool = D('UserAdvertisement')->updateFreeze($users_id,$sum_price,2,$all_array['generalize_id']);
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


		//获得账号数量
		public function getListNum($array)
		{
			if($array!='')
			{
				$where['generalize_id'] = array('IN',$array);
				$val_list = $this->where($where)->group('generalize_id')
				->field('count(id) as number,generalize_id')->select();
			
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


		//给状态为3的每个媒体主账户发送短信
		private function send_media_mess($generalize_id)
		{
			$Account_Order_Status = C('Account_Order_Status');
			$users_array = $this->where(array('generalize_id'=>array('eq',$generalize_id),'audit_status'=>array('eq',$Account_Order_Status[3]['status'])))->field('users_id,account_id')->select();
			$account_weixin = D('account_weixin');
			$user_media = D('user_media');
			foreach($users_array as $value)
			{
				$account_name = $account_weixin->where(array('id'=>$value['account_id']))->getField('account_name');
				$phone = $user_media->where(array('users_id'=>array('eq',$value['users_id'])))->getField('iphone');
				if($phone!='')
				{
					$msg = '媒体主你好：您发布的账号名称：'. $account_name . ' 已被广告主确认下单，需要执行！';
					parent::send_mall($phone,$msg);
				}
			}
		}

	}