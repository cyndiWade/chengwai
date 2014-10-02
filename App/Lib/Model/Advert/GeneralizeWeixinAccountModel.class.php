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
			//用户ID
			$arr['users_id'] = $id;
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
					$arr['price'] = D('AccountWeixin')->getWeiType($ggw['ggw_type'],$value);
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
						$save = array('status'=>4);
						D('GeneralizeWeixinOrder')->where(array('id'=>$zhifu_id))->save($save);
						return true;
					}else{
						return false;
					}
				}
				
			}
		}
	}