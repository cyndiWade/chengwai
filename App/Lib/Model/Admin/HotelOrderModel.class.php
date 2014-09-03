<?php

//酒店订单模型表
class HotelOrderModel extends AdminBaseModel {
	
	
	//获取一条数据
	public function seek_one_hotel ($condition,$field = '*') {
		$con = array('is_del'=>0);
		array_add_to($con,$condition);

		return $this->where($con)->field($field)->find();
	}
	
	
	//删除一条数据
	public function del_one_hotel ($id) {
		return $this->where(array('id'=>$id))->data(array('is_del'=>-2))->save();
	}
	
	
	//获取酒店列表
	public function seek_order_list ($condition = array()) {
		$data = $this->field('ho.*,u.nickname,h.hotel_name,hr.title')
		->table($this->prefix.'hotel_order AS ho')
		->join($this->prefix.'users AS u ON u.id = ho.user_id')
		->join($this->prefix.'hotel AS h ON h.id = ho.hotel_id')
		->join($this->prefix.'hotel_room AS hr ON hr.id = ho.hotel_room_id')
		->where($condition)
		->order('ho.order_time DESC')
		->select();
		parent::set_all_time($data, array('order_time'));
		parent::set_all_time($data, array('in_date','out_date'),'Y-m-d');
		return $data;
	}
	
	public function check_order(){
		$result = array('new_orders' => 0,'msg'=>'', 'new_paid' => 0);
		$where = array(
			'order_time'=>array('egt',$_SESSION[last_check]),
		    'order_status'=>0
		);
		$result['new_orders'] = $this->where($where)->count('*');
        if($result['new_orders']>0){
        	$result['msg'] = '新订单';
        } 
		die(json_encode($result));	
	}
	

	
}

?>
