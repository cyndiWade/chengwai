<?php
	
	//塞入信息
	class IntentionWeiboOrderModel extends AdvertBaseModel
	{

		public function insertPost($array,$id)
		{
			$new_array = array();
			foreach($array as $key=>$value)
			{
				$new_array[$key] = addslashes($value);
			}
			$add['users_id'] = $id;
			$add['tfpt_type'] = $new_array['tfpt_type'];
			$add['fslx_type'] = $new_array['fslx_type'];
			$add['ryg_type'] = $new_array['ryg_type'];
			$add['yxd_name'] = $new_array['yxd_name'];
			$add['zf_url'] = $new_array['zf_url'];
			$add['zfy_type'] = $new_array['zfy_type'];
			$add['zw_info'] = $new_array['zw_info'];
			$add['wa_url_status'] = $new_array['wa_url_status'];
			$add['start_time'] = strtotime($new_array['start_time']);
			$add['over_time'] = strtotime($new_array['over_time']);
			$add['sfyq'] = $new_array['sfyq'] =='' ? 0 : 1;
			$add['dx_status'] = $new_array['dx_status'];
			$add['dx_phone'] = $new_array['dx_phone'];
			$add['bz_info'] = $new_array['bz_info'];
			$add['create_time'] = time();
			return $this->add($add);
		}

		//获得推广订单数据
		public function get_OrderInfo_list($id)
		{
			$where['users_id'] = $id;
			return parent::get_spe_data($where);
		}


		//获取一条本人订单信息
		public function get_OrderInfo_By_Id ($order_Id,$users_id) {
			$where['id'] = $order_Id;
			$where['users_id'] = $users_id;
			return parent::get_one_data($where);
		}
	}