<?php

//区域表
class RegionModel extends AdvertBaseModel {
	
	
	//获取父集下的数据
	public function get_parent_list ($parent_id) {
		return parent::get_spe_data(array('parent_id'=>$parent_id));
	}
	
}

?>
