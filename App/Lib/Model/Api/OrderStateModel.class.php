<?php
class OrderStateModel extends ApiBaseModel{

      // 获得步骤
      public function get_step($wxuid){
	  
	       $step = $this->where(array('user_code'=>"$wxuid"))->getField('step');
	  
	       $step = empty($step) ? 0 : $step; 
		   
		   return $step;
	  }

      // 添加数据
	  public function add_step($data){
	  
	  
         $this->add($data);

	  }

	  public function get_hotel_id($wxuid){
	  
	      return $this->where(array('user_code'=>"$wxuid"))->getField('hotel_id');
	  
	  }
      //删除过期的数据
	  public function del_data(){
	  
	  
	     $this->where(array('endtime'=>array('LT',time()) ) )->delete(); 

	  }
      public function del_data_user($user_code){
	  
	  
	     $this->where(array('user_code'=>"$user_code" ) )->delete(); 

	  }
	  //获得入住时间
	  function get_in_time($wxuid){
	  
	       return $this->where(array('user_code'=>"$wxuid"))->getField('startrz');
	  
	  
	  }
	  public function get_order_info($wxuid){
	  
	  
	    $data = $this->where(array('user_code'=>"$wxuid") )->find();
	    
       // echo '<pre>';print_R($data);echo '</pre>';exit;
		$day = daysDiff($data['endlikai'],$data['startrz']);
        $HotelRoom = D('HotelRoom');  // 房型模型
	    $total_price = $HotelRoom->total_price($data['room_id'],date('Y-m-d',$data['startrz']),date('Y-m-d',$data['endlikai']),$data['pay_type']);//计算价格
	    //echo $HotelRoom->getLastSql();
	     
        $arr = array('total'=>$total_price);

		$this->where(array('user_code'=>"$wxuid"))->save($arr);
        $data['total'] = $total_price;
	    $result['str'] = '订单提交成功。您选的是：'.$data['hotel_name']."\n".
			   '房型是：'.$data['room_name']."\n".
			   '入住时间是：'.date('Y-m-d',$data['startrz'])."\n".
			   '离开时间是：'.date('Y-m-d',$data['endlikai'])."\n".
			   '总费用是：'.$total_price."元。\n".'请进入我的账户->我的订单 来查看订单。';
		$result['data'] = $data;
		return $result;
	  }

}