<?php

//订单日志类
class  OrderLogModel extends  AppBaseModel {

	
		//添加日志
		public function add_order_log ($data) {
			
			$this->data($data)->add();
		}

		
	
}

?>