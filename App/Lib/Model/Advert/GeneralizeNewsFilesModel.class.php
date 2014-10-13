<?php
	
	//塞入图片信息
	class GeneralizeNewsFilesModel extends AdvertBaseModel
	{
		public function get_fiels_list (Array $where) {
			$where['is_del'] = 0;
			return $this->where($where)->order('id DESC')->select();
		}
		
	}