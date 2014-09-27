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

		//获取一条本人订单信息
		public function get_OrderInfo_By_Id ($order_Id,$users_id) {
			$where['id'] = $order_Id;
			$where['users_id'] = $users_id;
			return parent::get_one_data($where);
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

		//删除书数据
		public function del_info($del_id,$users_id)
		{
			$upload_dir = C('UPLOAD_DIR');
			$dir = $upload_dir['web_dir'].$upload_dir['image'];
			$where = array('users_id'=>$users_id,'id'=>$del_id);
			$status = $this->where($where)->filed('status')->find();
			if($status['status']==0)
			{
				D('GeneralizeNewsAccount')->where($where)->delete();
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
	}