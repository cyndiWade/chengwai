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
			/*
			$add['users_id'] = $id;
			$add['tfpt_type'] = $new_array['tfpt_type'];
			$add['fslx_type'] = $new_array['fslx_type'];
			$add['ryg_type'] = $new_array['ryg_type'];
			$add['yxd_name'] = $new_array['yxd_name'];
			$add['zf_url'] = $new_array['zf_url'];
			$add['zfy_type'] = $new_array['zfy_type'];
			$add['zw_info'] = $new_array['zw_info'];
			$add['zfnr_type'] = $new_array['zfnr_type'];
			$add['wa_url_status'] = $new_array['wa_url_status'];
			$add['start_time'] = strtotime($new_array['start_time']);
			$add['over_time'] = strtotime($new_array['over_time']);
			$add['sfyq'] = $new_array['sfyq'] =='' ? 0 : 1;
			$add['dx_status'] = $new_array['dx_status'];
			$add['dx_phone'] = $new_array['dx_phone'];
			$add['bz_info'] = $new_array['bz_info'];
			$add['create_time'] = time();
			*/
			
			$this->create();
			$this->users_id = $id;
			$this->start_time = strtotime($new_array['start_time']);
			$this->over_time = strtotime($new_array['over_time']);
			$this->sfyq = $new_array['sfyq']=='' ? 0 : 1;
			$this->create_time = time();
			
			return $this->add();
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
			$qrz = $this->where(array('users_id'=>array('eq',$id),'status'=>array('IN',array(0,1))))->count();
			$yqr = $this->where(array('users_id'=>array('eq',$id),'status'=>array('eq',2)))->count();
			return array('qrz'=>$qrz,'yqr'=>$yqr);
		}

		//获取一条本人订单信息
		public function get_OrderInfo_By_Id ($order_Id,$users_id) {
			$where['id'] = $order_Id;
			$where['users_id'] = $users_id;
			return parent::get_one_data($where);
		}


		//删除书数据
		public function del_info($del_id,$users_id)
		{
			$upload_dir = C('UPLOAD_DIR');
			$dir = $upload_dir['web_dir'].$upload_dir['image'];
			$where = array('users_id'=>$users_id,'id'=>$del_id);
			$status = $this->where($where)->field('status')->find();
			if($status['status']==0)
			{
				D('IntentionWeiboAccount')->where($where)->delete();
				$IntentionWeiboFiles = D('IntentionWeiboFiles');
				$url = $IntentionWeiboFiles->where($where)->field('url')->select();
				//删除文件
				foreach($url as $value)
				{
					@unlink($dir . $value['url']);
				}
				$IntentionWeiboFiles->where($where)->delete();
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