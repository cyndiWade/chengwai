<?php

//帮助
class HelpModel extends AdvertBaseModel {
	
	
	public function get_top_data ($parent_id) {
		return $this->where(array('parent_id'=>$parent_id, 'type' => 0,'show_status'=>1,'is_del'=>0))->select();
	}
}

?>
