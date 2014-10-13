<?php
	
	//塞入图片信息
	class GeneralizeFilesModel extends AdvertBaseModel
	{


		//塞入注册图片路径
		public function insertImg($img_array)
		{
			foreach($img_array as $value)
			{
				$this->add($value);
			}
		}

		//塞入微信图片数据
		public function insertImgs($order_id,$img_array)
		{
			if($order_id!='' && $img_array!='')
			{
				$new_array  = array();
				foreach($img_array as $value)
				{
					$new_array['users_id'] = $value['users_id'];
					$new_array['generalize_order_id'] = $order_id;
					$new_array['type'] = $value['type'];
					$new_array['url'] = $value['url'];
					$this->add($new_array);
				}
			}
		}
		
		public function get_fiels_list (Array $where) {
			$where['is_del'] = 0;
			return $this->where($where)->order('id DESC')->select();
		}
		
	}