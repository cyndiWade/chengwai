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

		//统计确认和执行的数量
		public function get_OrderInfo_num($id)
		{
			$val = $this->where(array('users_id'=>$id))->field('status')->select();
			$i = 0;
			$j = 0;
			foreach($val as $v)
			{
				if($v['status']==0)
				{
					$i++;
				}else{
					$j++;
				}
			}
			return array('0'=>$i,'1'=>$j);
		}

		//删除数据
		//删除书数据
		public function del_info($del_id,$users_id)
		{
			$upload_dir = C('UPLOAD_DIR');
			$dir = $upload_dir['web_dir'].$upload_dir['image'];
			$where = array('users_id'=>$users_id,'id'=>$del_id);
			$status = $this->where($where)->field('status')->find();
			if($status['status']==0)
			{
				D('IntentionWeixinAccount')->where($where)->delete();
				$IntentionWeixinFiles = D('IntentionWeixinFiles');
				$url = $IntentionWeixinFiles->where($where)->field('url')->select();
				//删除文件
				foreach($url as $value)
				{
					@unlink($dir . $value['url']);
				}
				$IntentionWeixinFiles->where($where)->delete();
				$bool = $this->where($where)->delete();
				if($bool)
				{
					return '1';
				}else{
					return '2';
				}
			}else{
				return '3';
			}
		}
		
		//获取一条本人订单信息
		public function get_OrderInfo_By_Id ($order_Id,$users_id) {
			$where['id'] = $order_Id;
			$where['users_id'] = $users_id;
			return parent::get_one_data($where);
		}

	}