<?php
	
	//微博推广信息表
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
			$now_val['sfyq'] = $new_post['sfyq']=='' ? 0 : 1;
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

		//获得推广订单数据
		public function get_OrderInfo_list($id)
		{
			$where['users_id'] = $id;
			return parent::get_spe_data($where);
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
				D('GeneralizeWeixinAccount')->where($where)->delete();
				$GeneralizeWeixinFiles = D('GeneralizeWeixinFiles');
				$url = $GeneralizeWeixinFiles->where($where)->field('url')->select();
				//删除文件
				foreach($url as $value)
				{
					@unlink($dir . $value['url']);
				}
				$GeneralizeWeixinFiles->where($where)->delete();
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