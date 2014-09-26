<?php
	
	//推广信息表
	class GeneralizeOrderModel extends AdvertBaseModel
	{

		//新增数据
		public function insertPost($array,$id)
		{
			$new_post = array();
			foreach($array as $key=>$value)
			{
				$new_post[$key] = addslashes($value);
			}
			$now_val['users_id'] = $id;
			$now_val['tfpt_type'] = $new_post['tfpt_type'];
			$now_val['fslx_type'] = $new_post['fslx_type'];
			$now_val['ryg_type'] = $new_post['ryg_type'];
			$now_val['hd_name'] = $new_post['hd_name'];
			$now_val['zf_url'] = $new_post['zf_url'];
			$now_val['zfy_type'] = $new_post['zfy_type'];
			$now_val['zw_info'] = $new_post['zw_info'];
			$now_val['zfnr_type'] = $new_post['zfnr_type'];
			$now_val['wasfbalj'] = $new_post['wasfbalj'];
			$now_val['start_time'] = strtotime($new_post['start_time']);
			$now_val['sfyq'] = $new_post['sfyq'];
			$now_val['bz_info'] = $new_post['bz_info'];
			$now_val['create_time'] = time();
			return $this->add($now_val);
		}
		
		
		//获取一条本人订单信息
		public function get_OrderInfo_By_Id ($order_Id,$users_id) {
			$where['id'] = $order_Id;
			$where['users_id'] = $users_id;
			return parent::get_one_data($where);
		}

	}