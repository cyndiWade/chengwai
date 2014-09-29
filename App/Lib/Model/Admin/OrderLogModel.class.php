<?php
	//订单日志表

	class OrderLogModel extends AdminBaseModel
	{
		
		//插入订单日志
		public function add_order_log ($user_id,$order_id,$type) {
			$this->user_id = $user_id;
			$this->order_id = $order_id;
			$this->type = $type;
			$this->create_time = time();
			return $this->add();
			//dump($this->oUser)
		}
		
		
		//获取订单日志列表
		public function get_order_list ($where) {//)->order('score desc')->
			$result = $this->where($where)->order('id DESC')->select();
			if ($result == true) {
				parent::set_all_time($result,array('create_time'));
				return $result;
			} else {
				return array();
			}
		}
		
		
		
		
	}