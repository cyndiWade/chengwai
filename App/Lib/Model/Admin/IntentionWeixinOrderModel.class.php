<?php
	//微信意向单表

	class IntentionWeixinOrderModel extends AdminBaseModel
	{
		public function get_order_list ($where) {
			$result = parent::get_spe_data($where);
			if ($result == true) {
				parent::set_all_time($result,array('start_time','create_time'));
				return $result;
			} else {
				return array();
			}
		}
		
	}