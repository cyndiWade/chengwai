<?php
	//新闻媒体推广单表

	class GeneralizeNewsOrderModel extends AdminBaseModel
	{
		public function get_order_list ($where) {
			$result = $this->where($where)->order(array('id'=>'DESC'))->select();
			if ($result == true) {
				parent::set_all_time($result,array('start_time','create_time'));
				return $result;
			} else {
				return array();
			}
		}
		
	}