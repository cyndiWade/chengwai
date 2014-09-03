<?php

/**
 * 特价政策表
 */
class CommoditySpecialModel extends AdminBaseModel {
	
	public function get_last_one_data ($commodity_id) {
		$data =  $this->where(array('commodity_id'=>$commodity_id))->order('id desc')->find();
		return $data;
	}

	
	public function save_one_commodity_special ($commodity_id) {
		return $this->where(array('id'=>$commodity_id))->save();
	}
	
}

?>
