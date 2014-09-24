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
			$date['users_id'] = $id;
			$date['tfpt_type'] = $new_post[''];
			$date['fslx_type'] = $new_post[''];
			$date['ryg_type'] = $new_post[''];
			$date['hd_name'] = $new_post[''];
			$date['zf_url'] = $new_post[''];
			$date['zfy_type'] = $new_post[''];
			$date['zw_info'] = $new_post[''];
			$date['zfnr_type'] = $new_post[''];
			$date['wasfbalj'] = $new_post[''];
			$date['start_time'] = $new_post[''];
			$date['over_time'] = $new_post[''];
			$date['bz_info'] = $new_post[''];
			return $this->add($date);
		}

	}