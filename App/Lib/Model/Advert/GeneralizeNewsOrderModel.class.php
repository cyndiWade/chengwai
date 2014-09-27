<?php
	//订单号和账号关联表

	class GeneralizeNewsOrderModel extends AdvertBaseModel
	{

		public function insertPost($array,$id)
		{
			$new_array = array();
			foreach($array as $key=>$value)
			{
				$new_array[$key] = addslashes($value);
			}
			$add['users_id'] = $id;
			$add['title'] = $new_array['title'];
			$add['start_time'] = strtotime($new_array['start_time']);
			$add['web_url'] = $new_array['web_url'];
			$add['bz_info'] = $new_array['bz_info'];
			$add['zf_info'] = $new_array['zf_info'];
			$add['create_time'] = time();
			return $this->add($add);
		}

		
	}