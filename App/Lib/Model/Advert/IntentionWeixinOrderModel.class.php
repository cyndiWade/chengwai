<?php
	//微信意向
	
	class IntentionWeixinOrderModel extends AdvertBaseModel
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
			$add['ggw_type'] = $new_array['ggw_type'];
			$add['yxd_name'] = $new_array['yxd_name'];
			$add['title'] = $new_array['title'];
			$add['fmzw_status'] = $new_array['fmzw_status'];
			$add['zw_info'] = $new_array['zw_info'];
			$add['ly_url'] = $new_array['ly_url'];
			$add['start_time'] = strtotime($new_array['start_time']);
			$add['over_time'] = strtotime($new_array['over_time']);
			$add['sfyq'] = $new_array['sfyq'] =='' ? 0 : 1;
			$add['dx_status'] = $new_array['dx_status'];
			$add['dx_phone'] = $new_array['dx_phone'];
			$add['bz_info'] = $new_array['bz_info'];
			$add['create_time'] = time();
			return $this->add($add);
		}



	}