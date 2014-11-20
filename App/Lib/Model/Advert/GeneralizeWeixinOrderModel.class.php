<?php
	//微信推广单表
	
	class GeneralizeWeixinOrderModel extends AdvertBaseModel
	{
		public function insertPost($array,$id)
		{
			$new_array = array();
			foreach($array as $key=>$value)
			{
				$new_array[$key] = addslashes($value);
			}
			// $add['users_id'] = $id;
			// $add['tfpt_type'] = $new_array['tfpt_type'];
			// $add['fslx_type'] = $new_array['fslx_type'];
			// $add['ggw_type'] = $new_array['ggw_type'];
			// $add['yxd_name'] = $new_array['yxd_name'];
			// $add['title'] = $new_array['title'];
			// $add['fmzw_status'] = $new_array['fmzw_status'];
			// $add['zw_info'] = $new_array['zw_info'];
			// $add['ly_url'] = $new_array['ly_url'];
			// $add['start_time'] = strtotime($new_array['start_time']);
			// $add['over_time'] = strtotime($new_array['over_time']);
			// $add['sfyq'] = $new_array['sfyq'] =='' ? 0 : 1;
			// $add['dx_status'] = $new_array['dx_status'];
			// $add['dx_phone'] = $new_array['dx_phone'] ? $new_array['dx_phone'] : '';
			// $add['bz_info'] = $new_array['bz_info'];
			// $add['create_time'] = time();
			$this->create();
			$this->users_id = $id;
			$this->start_time = strtotime($new_array['start_time']);
			$this->over_time = strtotime($new_array['over_time']);
			$this->sfyq = $new_array['sfyq'] =='' ? 0 : 1;
			$this->dx_phone = $new_array['dx_phone'] ? $new_array['dx_phone'] : '';
			$this->create_time = time();
			return $this->add();
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
			$val = $this->where(array('users_id'=>$id))->getField('status',0);
			//已完成
			$ywc = 0;
			//拍单中
			$pdz = 0;
			//执行中
			$zxz = 0;
			//已拒单
			$yjd = 0;
			foreach($val as $value)
			{
				switch($value)
				{
					case 5:
						$ywc++;
					break;
					case 4:
						$zxz++;
					break;
					case 3:
						$yjd++;
					break;
					case 2:
						$pdz++;
					break;
					case 1:
						$pdz++;
					break;
					case 0:
						$pdz++;
					break;
				}
			}
			$sql = 'select count(b.generalize_id) as aid from app_generalize_weixin_order as a left join app_generalize_weixin_account as b on a.id = b.generalize_id where a.users_id="'.$id.'" group by b.generalize_id';
			$model = new Model();
			$caogao_arr = $model->query($sql);
			$caogao = 0;
			foreach($caogao_arr as $v)
			{
				if($v['aid']==0)
				{
					$caogao++;
				}
			}
			return array('ywc'=>$ywc,'pdz'=>$pdz,'zxz'=>$zxz,'yjd'=>$yjd,'caogao'=>$caogao);
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
				// $add['users_id'] = $arr['users_id'];
				// $add['tfpt_type'] = $arr['tfpt_type'];
				// $add['fslx_type'] = $arr['fslx_type'];
				// $add['ggw_type'] = $arr['ggw_type'];
				// $add['yxd_name'] = $arr['yxd_name'];
				// $add['title'] = $arr['title'];
				// $add['fmzw_status'] = $arr['fmzw_status'];
				// $add['zw_info'] = $arr['zw_info'];
				// $add['ly_url'] = $arr['ly_url'];
				// $add['start_time'] = $arr['start_time'];
				// $add['over_time'] = $arr['over_time'];
				// $add['sfyq'] = $arr['sfyq'];
				// $add['dx_status'] = $arr['dx_status'];
				// $add['dx_phone'] = $arr['dx_phone'];
				// $add['bz_info'] = $arr['bz_info'];
				// $add['all_price'] = $arr['all_price'];
				// $add['source_type'] = 1;
				// $add['create_time'] = time();
				$this->users_id = $arr['users_id'];
				$this->source_type = 1;
				$this->create_time = time();
				return $this->add();
			}
		}	
	}