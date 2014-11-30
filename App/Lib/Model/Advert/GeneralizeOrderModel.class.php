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
 			$now_val['wa_url_status'] = $new_post['wa_url_status'];
 			$now_val['start_time'] = strtotime($new_post['start_time']);
 			$now_val['over_time'] = strtotime($new_post['over_time']);
 			$now_val['sfyq'] = $new_post['sfyq']=='' ? 0 : 1;
 			$now_val['bz_info'] = $new_post['bz_info'];
 			$now_val['dx_status'] = $new_post['dx_status'];
 			$now_val['dx_phone'] = $new_post['dx_phone'];
 			$now_val['create_time'] = time();
			// $this->create();
			// $this->users_id = $id;
			// $this->start_time = strtotime($new_post['start_time']);
			// $this->over_time = strtotime($new_post['over_time']);
			// $this->sfyq = $new_post['sfyq']=='' ? 0 : 1;
			// $this->create_time = time();
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
			$new_array = array();
			//执行中
			$new_array['zxz'] = $this->where(array('users_id'=>array('eq',$id),'status'=>array('eq',4),'smallnumber'=>array('neq',0)))->count();
			//已取消
			$new_array['yqx'] = $this->where(array('users_id'=>array('eq',$id),'status'=>array('eq',6),'smallnumber'=>array('neq',0)))->count();
			//草稿
			$new_array['caogao'] = $this->where(array('users_id'=>array('eq',$id),'smallnumber'=>array('eq',0),'status'=>array('IN',array('0','1','2'))))->count();
			//已完成
			$new_array['ywc'] = $this->where(array('users_id'=>array('eq',$id),'status'=>array('eq',5),'smallnumber'=>array('neq',0)))->count();
			return $new_array;
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

		// 塞入数据
		public function insertGeneralize($arr)
		{
			if($arr!='')
			{
				$add['users_id'] = $arr['users_id'];
				$add['tfpt_type'] = $arr['tfpt_type'];
				$add['fslx_type'] = $arr['fslx_type'];
				$add['ryg_type'] = $arr['ryg_type'];
				$add['hd_name'] = $arr['yxd_name'];
				$add['zf_url'] = $arr['zf_url'];
				$add['zfy_type'] = $arr['zfy_type'];
				$add['zw_info'] = $arr['zw_info'];
				$add['wa_url_status'] = $arr['wa_url_status'];
				$add['zfnr_type'] = $arr['zfnr_type'];
				$add['start_time'] = $arr['start_time'];
				$add['over_time'] = $arr['over_time'];
				$add['sfyq'] = $arr['sfyq'];
				$add['dx_status'] = $arr['dx_status'];
				$add['dx_phone'] = $arr['dx_phone'];
				$add['bz_info'] = $arr['bz_info'];
				$add['all_price'] = $arr['all_price'];
				// $this->create();
				// $this->hd_name = $arr['yxd_name'];
				// $this->source_type = 1;
				// $this->create_time = time();
				$add['source_type'] = 1;
				$add['create_time'] = time();
				return $this->add($add);
			}
		}	
	}