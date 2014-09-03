<?php
class SphotelListAction extends HomeBaseAction{
  
     
    //初始化数据库连接
	 protected  $db = array(
		'Sphotel'        => 'Sphotel',
	    'SphotelRoom'    => 'SphotelRoom',
	    'SproomSchedule' => 'SproomSchedule',
        'SphotelOrder'   => 'SphotelOrder',
	    'UsersHotel'   => 'UsersHotel',
		'Coupon'       => 'Coupon',
		'WxUser'       => 'WxUser',
        'OrderLog'     => 'OrderLog',
		'UserCoupon'   => 'UserCoupon',
		'Gift'         => 'Gift'
	 );
	 	/**
	 * 构造方法
	 */
		public function __construct() {
		
			parent::__construct();
		
			parent::global_tpl_view(array('module_name'=>$this->module_name));
		}
	
	
	  public function index(){
	     
	  	  $Hotel      = $this->db['Sphotel']; // 酒店
	  	  $HotelRoom  = $this->db['SphotelRoom'];
	  	  $Gift       = $this->db['Gift'];
	  	  $RoomSchedule = $this->db['SproomSchedule'];
		  $hotel_type  = $this->_get('hotel_type');
		  $city = $this->_get('hotel_cs');
		  
		  if(!empty($city) and $city !='all'){
		  	  $city = str_replace('ABCDE','%',$city);
		  	  $hotel_cs = passport_decrypt(urldecode($city),'hotel');
			  $where = array(
			  		'hotel_cs'=>"$hotel_cs"
			  );
		  }else{
		  	$allcity  = $Hotel->get_city(); 
		  	foreach($allcity as $key=>$val){
		  		$hc =  urlencode($val['hotel_cs']);
		  		$hc = passport_encrypt($hc,'hotel');
		  		$hc = str_replace('%','ABCDE',$hc);		
		  		$allcity[$key]['v']  = $hc;
		  	}
		  	//echo '<pre>';print_R($allcity);echo '</pre>';exit;
		  	$this->assign('hotel_type',$hotel_type);
		  	$this->assign('allcity',$allcity);
		  } 
		  
		  if($hotel_type !=0)$where['hotel_type'] = $hotel_type;
	      $list = $Hotel->get_hotels($where);
	      //echo '<pre>';print_R($list);echo '</pre>';exit; 
	      if($list == true){
	      	 $hotel_ids = getArrayByField($list,'id'); // 获得酒店的id
	      	 $rooms    = $HotelRoom->get_price_room(array('hotel_id'=>array('in',$hotel_ids) )); 
		     
	      	 $room_sort     = regroupKey($rooms,'hotel_id');  
	      	 
	         foreach ($list AS $key=>$val) {
	         	//$HotelRoom->get_hotel_room($val['hotel_id'],2);
	         	$list[$key]['lb']           = $Gift->all_data(array('id'=>array('in',$val['hotel_lp'])));
				$list[$key]['img']          = $Hotel->get_img($val['id'],4);
				$list[$key]['roomtype']     = $HotelRoom->get_hotel_room($val['id'],2);//$room_sort[$val['id']];
				$list[$key]['spot_payment'] = $list[$key]['roomtype'][0]['mspot_payment'];//!empty($room_sort[$val['id']][0]['spot_payment']) ? $room_sort[$val['id']][0]['prepay'] : C('NOT_PRICE'); // 微信支付
				$list[$key]['prepay']       = $list[$key]['roomtype'][0]['mprepay'];//!empty($room_sort[$val['id']][0]['prepay']) ? $room_sort[$val['id']][0]['prepay'] : C('NOT_PRICE') ;//到店支付
			 }
	      }
	      //echo '<pre>';print_R($list);echo '</pre>';exit;
	      $html = array(
			  'list'     => $list,
			  'hotel_cs' => str_replace('ABCDE','%',$city),//passport_encrypt($city,'hotel'),
	          'user_code'=> $this->_get('user_code')
		  );
		  
	      $this->assign('html',$html);
	      $this->display();
	  }
//单个酒店的详细信息
	  public function get_hotel_info(){
	  	
	     $Hotel  = $this->db['Sphotel'];
	     $HotelRoom  = $this->db['SphotelRoom'];
	     $Gift       = $this->db['Gift'];
	  	 $hotel_id = $this->_get('hotel_id');
	  	 $user_code = $this->_get('user_code');
	  	
	  	 
	  	 
	     $list = $Hotel->get_one_hotel(array('id'=>$hotel_id));
	     $data = $HotelRoom->get_hotel_room($hotel_id,2); // 获得房型
	     //echo '<pre>';print_R($list);echo'</pre>';
	     //echo '<pre>';print_R($arr_item);echo'</pre>';exit;
	     if($list == true){
	     	$list['hotel_lp']  = $Gift->seek_all_data(array('id'=>array('in',$list['hotel_lp']))) ; 
			$list['img']         = $Hotel->get_img($list['id'],4);
			$daytime   = $list['cut_off_day']==0 ? time() : strtotime($list['cut_off_day'].' day');
			$day = $list['cut_off_day']+1;
			$exit_time = $list['cut_off_day']==0 ?strtotime('1 day') : strtotime($day.' day');
	     }

	  	 $html = array(
	  	     'date'=>"{minDate:'".date('Y-m-d',$daytime)."'}",
	  	     'exit_date'=>"{minDate:'".date('Y-m-d',$exit_time)."'}",
		  	 'list'=>$list,
	  	     'hotel_lp'=>$list['hotel_lp'],
	  	     'roomtype' =>$data,
		  	 'user_code'=>$user_code,
			 'hotel_cs'=> passport_encrypt($list['hotel_cs'],'hotel')
	  	 );
	
	  	//echo '<pre>';print_R($html);echo '</pre>';exit;
		 
	     $this->assign('html',$html);
	  	 $this->display();
	  
	  }
	  
	  public function order(){
	  	
	      $room_id     = $this->_get('room_id');
	      $pay_type    = $this->_get('pay_type');
	      $checkinday  = $this->_get('checkinday');
	      $checkoutday = $this->_get('checkoutday');
	      $user_code   = $this->_get('user_code');
	      $hotel_id    = $this->_get('hotel_id');
	      $countday = countDays($checkinday,$checkoutday,1);
	      
	      $user_code   = $this->_get('user_code');
	      $HotelRoom = $this->db['SphotelRoom'];
	      $total_price = $HotelRoom->total_price($room_id,$checkinday,$checkoutday,$pay_type);//计算价格
	     
	      $list = $HotelRoom->get_price_room(array('hr.id'=>$room_id));
	       
	      $price = $pay_type ==1 ? $list[0]['spot_payment']  : $list[0]['prepay'];
	      $html = array(
		      'room_id'    =>$room_id,
		      'pay_type'   =>$pay_type,
		      'checkinday' =>$checkinday,
		      'checkoutday'=>$checkoutday,
		      'user_code'  =>$user_code,
	          'price'      => $price,
	          'total_price'=> $total_price, 
	          'list'       =>$list[0],
	          'user_code'=>$user_code,
	          'hotel_id'=>$hotel_id,
	      );
	      //echo '<pre>';print_R($html);echo '</pre>';exit;
	      $this->assign('html',$html);
	      $this->display();
	  }
	  
	  //地图
	  
	  public function map(){
	  	  $id = $this->_get('hotel_id');
	  	  $user_code = $this->_get('user_code');
		  $hotel_cs  = passport_decrypt(urldecode($this->_get('hotel_cs')),'hotel');
	  	  if(!empty($id)){
	  	  	 $con = array('id'=>$id);
	  	  }else{
	  	  	 $con = array('hotel_cs'=>"$hotel_cs");
	  	  }
	  	  $Hotel  = $this->db['Sphotel'];  
	      $list = $Hotel->get_hotel($con);
	      //echo '<pre>';print_R($list);echo '</pre>';exit;
		  $list = regroupKey($list,'id',true);
			if($id){		//ID存在时
				$list[$id] = $list[$id];	
			} else {
			    $key = array_keys($list);
				$id = $key[1];
			}
	  	  $html = array(
	  	
		  	  'list'=>$list,
		  	  'hotel_id'=>$id,
	  	      'user_code'=>$user_code
	  	  );

		  //$lists = $Hotel->get_price(279);
	  	  //echo '<pre>';print_R($html);echo '</pre>';exit;
	  	  $this->assign('html',$html);
	  	  $this->display();
	  }
	  
	  
	  //ajax 更改价格
	  public function update_price(){
	  
	  	if($this->isPost()){
	  	  $room_id     = $this->_post('room_id');
	      $pay_type    = $this->_post('pay_type');
	      $checkinday  = $this->_post('checkinday');
	      $checkoutday = $this->_post('checkoutday');
	      $house = $this->_post('house');
	      //$countday = countDays($checkinday,$checkoutday,1);
	      $user_code   = $this->_post('user_code');
	      $HotelRoom = $this->db['SpotelRoom'];
	      $list = $HotelRoom->get_price_room(array('hr.id'=>$room_id));
	      $price = $pay_type ==1 ? $list[0]['spot_payment']  : $list[0]['prepay'];
	      $total_price = $HotelRoom->total_price($room_id,$checkinday,$checkoutday,$pay_type);//计算价格
	      $html = array(
		      'room_id'    =>$room_id,
		      'pay_type'   =>$pay_type,
		      'checkinday' =>$checkinday,
		      'checkoutday'=>$checkoutday,
		      'user_code'  =>$user_code,
	          'price'      => $price,
	          'total_price'=> $total_price*$house, 
	      );
	  	  parent::callback(C('STATUS_SUCCESS'),'处理成功！',$html);
	  	
	  	
	  	}else{
	  		parent::callback(C('STATUS_ACCESS'),'非法访问！');
	  	}
	 
	  
	  }
	  
	  
	  //订单处理
	  public function done_order(){

	  	  $room_id     = $this->_post('room_id');
	      $pay_type    = $this->_post('pay_type');
	      $checkinday  = $this->_post('checkinday');
	      $checkoutday = $this->_post('checkoutday');
	      $Inperson    = $this->_post('Inperson');
	      $telperson   = $this->_post('telperson');
	      $house       = $this->_post('house');
	      $order_total = $this->_post('order_total');
	      $tel         = $this->_post('tel');
	      $yq          = $this->_post('yq');
	      $hotel_id    = $this->_post('hotel_id');
	      $countday = countDays($checkinday,$checkoutday,1);
	      $user_code   = $this->_post('user_code');
          $UsersHotel = $this->db['UsersHotel'];
          $RoomSchedule = $this->db['SproomSchedule'];

          $user_id = $UsersHotel->get_user_id(array('hotel_id'=>$hotel_id));
	      $user_id = $user_id == true ? $user_id : 0;
          $data =array(
		      'order_sn'           => date('Ymd',time()).time(),
		      'order_time'         => time(),
		      'user_id'			   => $user_id,
		      'hotel_id' 		   => $hotel_id,
		      'hotel_room_id'      => $room_id,
		      'in_person'		   => $Inperson,
		      'contact_person'	   => $telperson,
	          'ask_for'   		   => $yq,
	          'user_code'          => $user_code,
	          'is_from'            => 1,
	          'phone'			   => $tel,
	          'total_price'        => $order_total,
	          'room_num'           => $house,
	          'in_date'            => strtotime($checkinday),
	          'out_date'           => strtotime($checkoutday),
	          'order_status'       => 0,
	          'dispose_status'     => 0,
	          'order_type'         =>$pay_type,
	          'is_pay'=>0,
	          'is_del'=>0      
	      );
	      
	      $HotelOrder = $this->db['SphotelOrder'];
	      $lastid = $HotelOrder->done_add($data);
	      
	      $where = array(
		      'day' =>array(
					array('egt',$data['in_date'] ),
					array('elt',$data['out_date'] )
			    ),
			  'hotel_room_id'=>$data['hotel_room_id'],
	      );

	      $RoomSchedule->Update_Room_num($where,$data['room_num']);
	      
	  	  if(!empty($lastid)){

	  	  	  header("location:?s=/Home/SphotelList/order_info/order_id/$lastid/showwxpaytitle/1");
	  	  }
	  }
	  
	  
	  //订单详情
	  public function order_info(){
	  	$PAY_TYPE    = C('PAY_TYPE');
	  	$IS_PAY      = C('IS_PAY');
	  	$order_id = $this->_get('order_id');
	  	$HotelOrder = $this->db['SphotelOrder'];
	  	$list = $HotelOrder->order_info(array('o.id'=>$order_id));
	  	$list['order_id'] = $order_id;
	  	$list['in_date'] = date('Y-m-d',$list['in_date']);
	  	$list['order_time'] = date('Y-m-d',$list['order_time']);
	  	$list['out_date'] = date('Y-m-d',$list['out_date']);
	  	$list['explain'] = $PAY_TYPE[$list['order_type']]['explain'];
	  	$list['is_pay']     = $IS_PAY[$list['is_pay']]['explain'];
		$list['in_person']  = empty($list['in_person']) ? '本人' : $list['in_person'];
        $list['contact_person']  = empty($list['contact_person']) ? '本人' : $list['contact_person'];
		//$list['total_price'] = str_replace('.','',$list['total_price']);
		$list['total_prices'] = str_replace('.','',$list['total_price']);
		$list['total_price'] =str_replace('.00','',$list['total_price']);
		//echo '<pre>';print_R($list);echo '</pre>';exit;
	  	$this->assign('html',$list);
	  	$this->display();
	  }

	  // 优惠券
	  public function get_coupon(){
	     
		   $coupon_id = $this->_get('coupon_id');
		   $user_code = $this->_get('user_code'); //
		   $type      = $this->_get('type');
		   $Coupon = $this->db['Coupon'];
		   $WxUser = $this->db['WxUser'];
	       $html = $Coupon->get_coupon($coupon_id);
		   $html['img']    =  $Coupon->get_img($coupon_id,3);
		   $html['phone']  =  $WxUser->The_existence_of_phone($user_code);
           
		   $html['create_time'] = date('Y-m-d',$html['create_time']);
		   $html['start_time']  = date('Y-m-d',$html['start_time']);
		   $html['over_time']   = date('Y-m-d',$html['over_time']);
		   $html['user_code']   = $user_code;
		   
		   $html['type'] = empty($type) ? '' : $type;
		   //echo '<pre>';print_r($html);echo '</pre>';exit;
		   $this->assign('html',$html);
		   $this->display(); 
	  }
	  // 取消优惠券
	  public function qx_coupon(){
	       $UserCoupon = $this->db['UserCoupon'];
	       $coupon_id = $this->_post('coupon_id');
		   $user_code = $this->_post('user_code'); //
	       $result  = $UserCoupon->qx_coupon(array('coupon_id'=>$coupon_id,'wxid'=>"$user_code"));
	       if($result === false){
				   parent::callback(C('STATUS_UPDATE_DATA'),'取消失败');
			     
			}else{
			      parent::callback(C('STATUS_SUCCESS'),'取消成功！');
		    } 
	  
	  }

	  //发送短信

	  function ajax_send(){
	     import('@.ORG.Util.Sms');
		 $smsclient = new SMSClient('961958','admin','DULKN1');
		 $phone = $this->_post('phone');
		 $youhui  = $this->_post('youhui');
		 $sent_ret = $smsclient->sendSMS("$phone",$youhui.'【尊旅会】');
		 if($sent_ret['errorno']==0){
		  
		  parent::callback(C('STATUS_SUCCESS'),'处理成功！');
		 
		 }else{
			 print_R($sent_ret);
		 }

	  }

	  public function ajax_get(){
	  
	     $coupon_id = $this->_post('coupon_id');
		 $user_code = $this->_post('user_code');
         $UserCoupon = $this->db['UserCoupon'];
		 //$data = $UserCoupon->get_coupon(4);
        // $data = $UserCoupon->get_coupon_id("o_kNsuDTFNH42UvcZIN7BH4mszPY");
		 $state = $UserCoupon->add_user_coupon($user_code,$coupon_id);
		 if ($state == 0) {
			 parent::callback(C('STATUS_UPDATE_DATA'),'您已获取过了！');
		 }elseif ($state >0){
			 parent::callback(C('STATUS_SUCCESS'),'获取成功！请到 私人定制-> 我的优惠券中查看');
		 }else {
			parent::callback(C('STATUS_UPDATE_DATA'),'获取失败，请稍后尝试！');
		 }
		 
	  
	  }
//订单的取消
	  public function quxiao_dingdan(){
	  
	        $order_id   =  $this->_post('order_id');
	        $HotelOrder = $this->db['SphotelOrder'];
	        $OrderLog   = $this->db['OrderLog'];
	        if($order_id){
			  $mes = $HotelOrder->quxiao_dingdan($order_id);
			 // print_R($mes);
			  if($mes === false){
				   parent::callback(C('STATUS_UPDATE_DATA'),'取消失败');
			     
			  }else{
			  	 $data = array(
			  	 		'order_id'=> $order_id,
			  	 		'addtime' => time(),
			  	 		'msg'     => '订单取消'
			  	 );
			  	  $OrderLog->add_order_log($data);
			      parent::callback(C('STATUS_SUCCESS'),'取消成功！');
			  } 
			
			}else{
			
			   parent::callback(C('STATUS_UPDATE_DATA'),'没有此订单号！');
			}
	  
	  
	  }
	  //判断房间 数量是否足够
	  public function room_num_enough(){
	  	$RoomSchedule = $this->db['SproomSchedule'];
	  	$data = $this->_post();
	  	$room_num = $data['house'];
	   /* $data = array(
	    	'room_id'=>289,
	        'house'=>2,
		    'checkinday'=>'2014-03-19',
		    'checkoutday'=>'2014-03-20'
	    );*/
	  	if($data){
	  			
	  	    $arr = $RoomSchedule->room_num_enough($data);
	  	    if(!empty($arr)){
	  	    	$data = $RoomSchedule->get_hotel($data['room_id']);
	  	    	$msg = '';
	  	    	$msg .=$data['hotel_name']."\n";
	  	    	foreach($arr as $key=>$val){
	  	    		$str[]= date('Y-m-d',$val['day']).'号房间数量为'.$val['room_num'];
	  	    		$msg .= date('Y-m-d',$val['day']).'号房间数量为'.$val['room_num']."\n";
	  	    	}
	  	    	SendMail("guestservice@zunlvhui.com.cn","房间不足",$msg);
	  	    	parent::callback(C('STATUS_UPDATE_DATA'),'房间数量不够',$str);
	  	    	
	  	    }else{
	  	    	parent::callback(C('STATUS_SUCCESS'),'');
	  	    }
	  	}else{
	  		parent::callback(C('STATUS_UPDATE_DATA'),'没有此订单号！');
	  	}
	  	
	  	
	  }
	  //根据客人选择的日期获得当天的价格
	  public function ajax_get_date_info(){
	  	 
	  	$Hotel  = $this->db['Spotel'];
	  	$HotelRoom  = $this->db['SpotelRoom'];
	  	if($this->_post()){
	  		
	  		$hotel_id = $this->_post('hotel_id');
	  		$date = $this->_post('checkinday');
	  		//$list = $Hotel->get_one_hotel(array('id'=>$hotel_id));
	  		$data = $Hotel->get_hotel_room($hotel_id,2,$date); // 获得房型
	  		$html = array(
	  				'roomtype' =>$data,
	  				//'user_code'=>$user_code,
	  				//'hotel_cs'=> passport_encrypt($list['hotel_cs'],'hotel')
	  		);
	  		$this->assign('html',$html);
	  		$this->display();
	  	}else{
	  		
	  	}
 
	  }
	  
	  //选择城市显示该城市下面的酒店
	  public function allcity(){
	    
	  	   
	      $Hotel      = $this->db['Sphotel']; // 酒店
	  	  $HotelRoom  = $this->db['SphotelRoom'];
	  	  $Gift       = $this->db['Gift'];
	  	  $RoomSchedule = $this->db['SproomSchedule'];
		  $city         =  $this->_post('city');
	  	  $hotel_type   =  $this->_post('hotel_type');
		  $where =array(
			  'hotel_type'=>$hotel_type,
			  'hotel_cs'=>$city,
		  );
	      $list = $Hotel->get_hotels($where);
	      
	      if($list == true){
	      	 $hotel_ids = getArrayByField($list,'id'); // 获得酒店的id
	      	 $rooms    = $HotelRoom->get_price_room(array('hotel_id'=>array('in',$hotel_ids) )); 
		     
	      	 $room_sort     = regroupKey($rooms,'hotel_id');  
	      	 
	         foreach ($list AS $key=>$val) {
	         	//$HotelRoom->get_hotel_room($val['hotel_id'],2);
	         	$list[$key]['lb']           = $Gift->all_data(array('id'=>array('in',$val['hotel_lp'])));
				$list[$key]['img']          = $Hotel->get_img($val['id'],4);
				$list[$key]['roomtype']     = $HotelRoom->get_hotel_room($val['id'],2);//$room_sort[$val['id']];
				$list[$key]['spot_payment'] = $list[$key]['roomtype'][0]['mspot_payment'];//!empty($room_sort[$val['id']][0]['spot_payment']) ? $room_sort[$val['id']][0]['prepay'] : C('NOT_PRICE'); // 微信支付
				$list[$key]['prepay']       = $list[$key]['roomtype'][0]['mprepay'];//!empty($room_sort[$val['id']][0]['prepay']) ? $room_sort[$val['id']][0]['prepay'] : C('NOT_PRICE') ;//到店支付
			 }
	      }else{
	          return  array();
	      }
	      //echo '<pre>';print_R($list);echo '</pre>';exit;
	      $html = array(
			  'list'     => $list,
		  );
		  
	      $this->assign('html',$html);
	      $this->display();
	  }

}