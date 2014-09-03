<?php
class SphotelModel extends ApiBaseModel{



      public function get_all_sphotel(){

	        //if(empty($hotel_cs))$hotel_cs ='青岛';
			//$h = passport_encrypt($hotel_cs,'hotel');
			$data =  
			$this->table($this->prefix.'sphotel AS h')
			->field('h.id,h.hotel_name , h.hotel_syq, h.hotel_pf')
			->where(array('h.is_del'=>0))
			->order('h.sort DESC')
			->select();
			$datas = $this->table($this->prefix.'sphotel AS h')
			->field('h.id,h.hotel_name , h.hotel_syq, h.hotel_pf ,rs.spot_payment,rs.prepay,hr.privilege_day')
			->join($this->prefix.'sphotel_room AS hr ON h.id=hr.hotel_id')
			->join($this->prefix.'sproom_schedule AS rs on rs.hotel_room_id = hr.id')
			->where(array('h.is_del'=>0,'rs.day'=>strtotime( date('Y-m-d',time())) )  )
			->order('h.sort DESC')
			->select();
			$datas = regroupKey($datas,id);

			foreach($data as $k=>$v){
		
				$data[$k]['spot_payment'] = $datas[$v['id']][0]['spot_payment'];
				$data[$k]['prepay'] = $datas[$v['id']][0]['prepay'];
				$data[$k]['privilege_day'] = $datas[$v['id']][0]['privilege_day'];
			}

			$arr = array();
            /*$arr[0] = array(
						'Title'=>'酒店地图',
						'Description'=>'',
						'Picurl' =>$this->get_map("$hotel_cs"),//C('logo_url'),
						'Url'    =>C('SPHOTEL_MAP').urlencode($h),
						);*/
			$i = 0 ;
			foreach($data as $key=>$val){
			    if($i>9)break;
			     $spot_payment = $val['spot_payment']==0 ? '':   '预付 ￥'.$val['spot_payment'];
			     //$prepay = $val['prepay']==0 ? '':   '现付 ￥'.$val['prepay'];
			     $type = $i==0 ? 3 : 4;
			     $arr[$i] = array(
						'Title'=>$val['hotel_name']."\n". $val['privilege_day']."\n".$spot_payment,//.$spot_payment.$prepay,
						'Description'=>'',
						'Picurl' =>$this->get_img($val['id'],$type),//C('logo_url'),
						'Url'    =>C('Sphotel_info_url').$val['id'],
						);
			    
			  $i++;
			}
			if(count($data)>9){
				$arr[10] = array(
							'Title'=>'更多酒店',
							'Description'=>'',
							'Picurl' =>C('logo_url'),
							'Url'    =>C('Sphotel_more'),//.urlencode($h),
							);
			}
            return empty($data) ? array() : $arr;
			//return $arr;
	  
	  }
	  
	  
	  //根据客人说的酒店名
	  
	  public function get_Hotel($hotel_name){
	  
	  	    if(empty($hotel_name))$hotel_name ='上海中环';
			$data = $this->field('h.id,h.hotel_name , hr.title, h.hotel_pf ,rs.spot_payment,rs.prepay')
			->table($this->prefix.'sphotel AS h')
			->join($this->prefix.'sphotel_room AS hr ON h.id=hr.hotel_id')
			->join($this->prefix.'sproom_schedule AS rs on rs.hotel_room_id = hr.id')
			->where(array('hotel_name'=>array('like',"%$hotel_name%"),'h.is_del'=>0,'rs.day'=>strtotime( date('Y-m-d',time())) )  )
			->select();
			$arr = array();
			$i = 1 ;
			foreach($data as $key=>$val){
			    if($i>10)break;
			    $arr[] = array(
						'Title'=>$val['hotel_name']."\n". $val['title']."\n".$val['hotel_pf'].'分 预付 ￥'.$val['spot_payment'].' 现付 ￥'.$val['prepay'],
						'Description'=>'',
						'Picurl' =>$this->get_room_img($val['id'],2),//C('logo_url'),
						'Url'    =>C('Hotel_info_url').$val['id'],
						);
			   $hotel_id =$val['id'];
			   $hotel_name = $val['hotel_name'];
			    
			  $i++;
			}
			$result['list']       = $arr;
			$result['hotel_id']   = $hotel_id; 
			$result['hotel_name'] = $hotel_name;
			return $result; 
	  
	  
	  }

      //获取酒店的图片
	  public function get_img($hotel_id,$type){
	  
	        

			 $data = $this->table($this->prefix.'sphotel AS h')
			->join($this->prefix.'sphotel_img AS hr ON h.id=hr.hotel_id')
			->where( array('hr.hotel_id'=>$hotel_id,'hr.is_del'=>0,'hr.type'=>$type) )
			->getField('url');

			 return C('PUBLIC_VISIT.domain').C('UPLOAD_DIR.image').$data;
	  
	  
	  }
	  //获得房型图片
	  public function get_room_img($room_id,$type=1){
	  	$where = array(
	  			'i.is_del'=>0,
	  			'i.hotel_room_id' =>$room_id,
	  			'i.type'  =>$type
	  	);
	  	$data = $this->table($this->prefix.'sproom_img as i')
	  	->where($where)->getField('url');
	  	$img = empty($data) ? C('NO_PIC') : $data;
	  	return C('PUBLIC_VISIT.domain').C('UPLOAD_DIR.image').$img;
	  
	  }

	  public function get_map($hotel_cs){
	  
	    $data =  $this->where(array('is_del'=>0))->field('hotel_cs')->group('hotel_cs')->select();
	   // echo '<pre>';print_R($data);echo '</pre>';
	    $arr = array();
	    foreach ($data as $key=>$val){
	    	 
	    	$arr[$val['hotel_cs']]=$key.'.png';
	    }
	    return C('HOTELMAPIMAGES').$arr[$hotel_cs];

	  }


	  //获得每日特惠的酒店的id
	  public function get_id(){
	  
	  
	      $data = $this->field('hotel_id')
	      ->table($this->prefix.'sphotel_preference ')->where(  array('is_del'=>0,'time'=>strtotime( date('Y-m-d',time())) )   )->select();

		  foreach($data as $key=>$val){

			   $arr[] = $val['hotel_id'];

		  }
		  return $arr;
	   
	  
	  }

    //获得每日优惠的酒店
	  public function get_hotel_p(){

	        $hotel_ids = $this->get_id();
		
			$data = $this->field('h.id,h.hotel_name , h.hotel_syq, h.hotel_pf ,rs.spot_payment,rs.prepay')
			->table($this->prefix.'sphotel AS h')
			->join($this->prefix.'sphotel_room AS hr ON h.id=hr.hotel_id')
			->join($this->prefix.'sproom_schedule AS rs on rs.hotel_room_id = hr.id')
			->where(array('h.id'=>array('in',$hotel_ids),'h.is_del'=>0,'rs.day'=>strtotime( date('Y-m-d',time())) )  )
			->select();
			$arr = array();
			$i = 1 ;
			foreach($data as $key=>$val){
			    if($i>8)break;
			    $arr[$i] = array(
						'Title'=>$val['hotel_name']."\n". $val['hotel_syq']."\n".$val['hotel_pf'].'分 预付 ￥'.$val['spot_payment'].' 现付 ￥'.$val['prepay'],
						'Description'=>'',
						'Picurl' =>$this->get_img($val['id'],4),//C('logo_url'),
						'Url'    =>C('Hotel_info_url').$val['id'],
						);
			    
			  $i++;
			}
			      
			return $arr;
	  
	  }

      public function get_hotel_price($hotel_id){
      	$data = $this
			->field('h.id,h.hotel_name , h.hotel_syq, h.hotel_pf ,rs.spot_payment,rs.prepay')
			->join($this->prefix.'sphotel_room AS hr ON h.id=hr.hotel_id')
			->join($this->prefix.'sproom_schedule AS rs on rs.hotel_room_id = hr.id')
			->where(array('h.id'=>$hotel_id,'h.is_del'=>0,'rs.day'=>strtotime( date('Y-m-d',time())) )  )
			->order('h.sort DESC')
			->select();
			//echo $this->getLastSql();
			return $data;
      }


}