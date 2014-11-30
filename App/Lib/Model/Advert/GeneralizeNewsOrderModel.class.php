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