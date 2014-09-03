<?php

//订单日志类
class  OrderLogModel extends  AdminBaseModel {

	
		//添加日志
		public function add_order_log () {
			$this->addtime = time();
			return $this->add();
		}

		
		//获取所有订单列表
		public function seek_order_data ($order_id,$field='*')  {
			$data = $this->field($field)
			->table($this->prefix.'order_log AS ol')
			->join($this->prefix.'users AS u ON u.id=ol.user_id')
			->where(array('ol.order_id'=>$order_id))
			->select();
			parent::set_all_time($data, array('addtime'));
			return $data;
		}
}

?>