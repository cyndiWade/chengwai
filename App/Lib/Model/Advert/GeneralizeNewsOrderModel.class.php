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
			$sql = 'select count(b.generalize_id) as aid from app_generalize_news_order as a left join app_generalize_news_account as b on a.id = b.generalize_id where a.users_id="'.$id.'" group by b.generalize_id';
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