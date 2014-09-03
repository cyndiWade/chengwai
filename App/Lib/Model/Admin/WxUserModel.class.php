<?php
set_time_limit(0);
//微信关注的用户
class  WxUserModel extends  AdminBaseModel {

		//获取所有订单列表
		public function seek_all_data ($field='*')  {
			$remarks  = $this->get_code_exp('code_id,hotel_remarks,yuangong');
		    
			$data = $this->field($field)->select();
		    $USERFROM = C('USERFROM');
		    $SUBSCRIBE = C('SUBSCRIBE');
			foreach($data as $k=>$v){
				$data[$k]['hotel_remarks']=$remarks['hotel_remarks'][$v['code_id']];
				$data[$k]['yuangong']     =$remarks['yuangong'][$v['code_id']];
				$data[$k]['userfrom']=$USERFROM[$v['is_from']]['explain'];
				$data[$k]['subscribe']=$SUBSCRIBE[$v['subscribe']]['explain'];
			}

			parent::set_all_time($data, array('subscribe_time'));		
			return $data;
		}
		
		
		function get_code_exp($field){
			
			$where = array(
					'is_del'=>0,
					'hotel_id'=>array('neq',0)
			);
			$data = $this->field($field)
			->table($this->prefix.'wx_code')
			->where($where)->select();
			$hotel_remarks = array();
			$yuangong = array();
			foreach ($data as $k=>$v){
				$hotel_remarks[$v['code_id']]= $v['hotel_remarks'];
				$yuangong[$v['code_id']]=$v['yuangong'];
			}
			return array('hotel_remarks'=>$hotel_remarks,'yuangong'=>$yuangong);
			
		}

		public function admin_get_wx_user(){
	   
			$data = $this->field('u.*,c.hotel_name')
			->table($this->prefix.'wx_user as u')
			->join($this->prefix.'hotel as c on u.hotel_id = c.id')
			->select();
			
			$remarks  = $this->get_code_exp('code_id,hotel_remarks,yuangong');
			foreach($data as $k=>$v){
				$data[$k]['hotel_remarks']=$remarks['hotel_remarks'][$v['code_id']];
				$data[$k]['yuangong']     =$remarks['yuangong'][$v['code_id']];
			}
			return $data;
		}
}

?>