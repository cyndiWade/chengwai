<?php
	
	class GeneralizeWeixinFilesModel extends AdvertBaseModel
	{
		//塞入注册图片路径
		public function insertImg($img_array)
		{
			foreach($img_array as $value)
			{
				$this->add($value);
			}
		}
	}