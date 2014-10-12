<?php
	
	class IntentionWeixinFilesModel extends AdvertBaseModel
	{
		//塞入注册图片路径
		public function insertImg($img_array)
		{
			foreach($img_array as $value)
			{
				$this->add($value);
			}
		}

		//获得图片数据
		public function getImg($id)
		{
			if($id!='')
			{
				$value = $this->where(array('generalize_order_id'=>$id))->select();
				return $value;
			}
		}
	}