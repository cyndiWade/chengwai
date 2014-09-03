<?php

//酒店房型模型表
class HotelRoomModel extends HomeBaseModel {
	
	//获取酒店下所有房型
	public function get_hotel_rooms ($hotel_id,$field = '*') {
		return $this->field($field)->where(array('hotel_id'=>$hotel_id,'is_del'=>0))->select();
	}
	
	//获取指定一条酒店房型数据
	public function get_one_data ($condition,$field = '*') {
		$con = array('is_del'=>0);
		array_add_to($con,$condition);

		return $this->where($con)->field($field)->find();
	}
	
    public function get_all_rooms ($condition,$field = '*') {
    	$con = array('is_del'=>0);
		array_add_to($con,$condition);
		return $this->field($field)->where($con)->select();
	}
	
    public function get_price_room ($condition,$field = '*') {
    	

    	$con = array('hr.is_del'=>0,'rs.is_del'=>0,'rs.day'=>strtotime(date('Y-m-d',time())));
		array_add_to($con,$condition);
    	$data = $this->field('*')
		->table($this->prefix.'hotel_room AS hr')
		->join($this->prefix.'room_schedule AS rs on rs.hotel_room_id = hr.id')
		->where($con)
		->select();

        foreach ($data as $key=>$val){
            $data[$key]['img'] = $this->get_img($condition['hotel_id'],2);//array('hotel_id'=>$hotel_id )
		}
		return $data;
	}

	//获得图片
	public function get_img($hotel_id,$type){
	  
	        

			 $data = $this->table($this->prefix.'hotel AS h')
			->join($this->prefix.'hotel_img AS hr ON h.id=hr.hotel_id')
			->where( array('hr.hotel_id'=>$hotel_id,'hr.is_del'=>0,'hr.type'=>$type) )
			->getField('url');

			 return C('PUBLIC_VISIT.domain').C('UPLOAD_DIR.image').$data;
	  
	  
	  }

	
	  
	  //获得酒店的房型
	  public function get_hotel_room($hotel_id=151,$type=1){

	  	$id = $this->room_putaway();  //下架的房型的id
	  	
	  	$where = array(
		  	'r.hotel_id'=>$hotel_id, //酒店的id
		  	'r.is_del'=>0,      //房型是否删除
		  	's.is_del'=>0,       //房型的价格是否删除
	  	    's.room_num' =>array('gt',0), //房间数量大于0
	  	    's.day'=>strtotime( date( 'Y-m-d',time() ) ), // 今天
	  	    //'r.id'=>array('not in',$id)
	  	);
	  	
	  	$data = $this->field('r.id as rid ,r.title,r.info,s.spot_payment,s.prepay,s.room_num,s.id as sid') 
	  	->table($this->prefix.'hotel_room AS r')
	  	->join($this->prefix.'room_schedule AS s on s.hotel_room_id = r.id')
	  	//->join($this->prefix.'room_img AS i on i.hotel_room_id = s.id')
	  	->where($where)->select();
	
	  	foreach($data as $key=>$val){
	  		if(in_array($val['rid'],$id)){ //判断是否下架
	  		    $data[$key]['is_cut_off'] = 0;
	  		}else{
	  			$data[$key]['is_cut_off'] = $val['rid'];
	  		}
	  		$data[$key]['url'] = $this->get_room_img($val['rid'],$type);
	  	}

	  	return $data;
	  	
	  }
	  //根据客人选择的日期获得价格
	 public function get_date_room_info($hotel_id,$type,$date){
	 	
	  	$datetime  = $this->room_date_putaway($hotel_id);  //某个酒店下架放房型的时间戳
	    if(is_array($date)){
	    	
	    	$days_count = countDays(strtotime($date['checkinday']),strtotime($date['checkoutday']));
	    	$day = strtotime($date['checkinday']);
	    	
	    	//累计添加数据
	    	for ($i=0;$i<=$days_count;$i++) {
	    		$checktime[]=$day;
	    		$day = $day + 3600 * 24;		//每次写入数据库后，累加一天
	    	}
	    	$flag = array_intersect($datetime,$checktime);
	    	//echo '<pre>';print_R($datetime);echo '</pre>';
	    	//echo '<pre>';print_R($checktime);echo '</pre>';
	    	//echo '<pre>';print_R($flag);echo '</pre>';
	    	$where = array(
	    			'r.hotel_id'=>$hotel_id, //酒店的id
	    			'r.is_del'=>0,      //房型是否删除
	    			's.is_del'=>0,       //房型的价格是否删除
	    			's.room_num' =>array('gt',0), //房间数量大于0
	    			's.day'=>strtotime( $date['checkoutday'] ), // 今天
	    			 
	    	);
	    	 
	    	$data = $this->field('r.id as rid ,r.title,r.info,s.spot_payment,s.prepay,s.room_num,s.id as sid')
	    	->table($this->prefix.'hotel_room AS r')
	    	->join($this->prefix.'room_schedule AS s on s.hotel_room_id = r.id')
	    	->where($where)->select();
	    	// echo $this->getLastSql();
	    	foreach($data as $key=>$val){
	    		if($flag){ //判断是否下架
	    			$data[$key]['is_cut_off'] = 0;
	    		}else{
	    			$data[$key]['is_cut_off'] = $val['rid'];
	    		}
	    		$data[$key]['url'] = $this->get_room_img($val['rid'],$type);
	    	}
	    }else{
	    	$id = $this->room_putaways($date);
			$where = array(
			  			'r.hotel_id'=>$hotel_id, //酒店的id
			  			'r.is_del'=>0,      //房型是否删除
			  			's.is_del'=>0,       //房型的价格是否删除
			  			's.room_num' =>array('gt',0), //房间数量大于0
			  			's.day'=>strtotime( $date ), // 今天
			  			 
			  	);
		  	
		  	$data = $this->field('r.id as rid ,r.title,r.info,s.spot_payment,s.prepay,s.room_num,s.id as sid')
		  	->table($this->prefix.'hotel_room AS r')
		  	->join($this->prefix.'room_schedule AS s on s.hotel_room_id = r.id')
		  	->where($where)->select();
		  	// echo $this->getLastSql();
		  	foreach($data as $key=>$val){
		  		if(in_array($val['rid'],$id)){ //判断是否下架
		  			$data[$key]['is_cut_off'] = 0;
		  		}else{
		  			$data[$key]['is_cut_off'] = $val['rid'];
		  		}
		  		$data[$key]['url'] = $this->get_room_img($val['rid'],$type);
		  	}
	    }
	  	//echo '<pre>';print_R($data);echo '</pre>';
	  	return $data;
	  	 
	  }
	  //获得房型图片
	  public function get_room_img($room_id,$type=1){	  	
	  	$where = array(
		  	'i.is_del'=>0,
		  	'i.hotel_room_id' =>$room_id,
	  	    'i.type'  =>$type
	  	);	  	
	  	$data = $this->table($this->prefix.'room_img as i')
	  	->where($where)->getField('url');	
	  	$img = empty($data) ? C('NO_PIC') : $data;  	
	  	return C('PUBLIC_VISIT.domain').C('UPLOAD_DIR.image').$img;
	  	
	  }
	  //获得下架的房型
	  
	  public function room_putaway(){
	  	$where = array(
		  	'p.is_del'=>0,
		  	'p.start_time'=>array('elt',strtotime( date( 'Y-m-d',time() ) )),
		  	'p.over_time'=>array('egt',strtotime( date( 'Y-m-d',time() ) ))
	  	);
	  	
	  	$data = $this->field('p.hotel_room_id')
	  	->table($this->prefix.'room_putaway as p')
	  	->where($where)->select();
	  	foreach($data as $key=>$val){
	  		$arr[] = $val['hotel_room_id'];
	  	}
	  	return array_unique($arr);  // 去除重复的值
	  }
	  //根据日期获得下架的酒店的id
	  public function room_putaways($date){
	  	$where = array(
	  			'p.is_del'=>0,
	  			'p.start_time'=>array('elt',strtotime( $date)),
	  			'p.over_time'=>array('egt',strtotime( $date ))
	  	);
	  
	  	$data = $this->field('p.hotel_room_id')
	  	->table($this->prefix.'room_putaway as p')
	  	->where($where)->select();
	  	foreach($data as $key=>$val){
	  		$arr[] = $val['hotel_room_id'];
	  	}
	  	return array_unique($arr);  // 去除重复的值
	  }
	  //获得下架酒店的日期
	  public function room_date_putaway($hotel_room_id){
		  	$where = array(
		  		      'p.hotel_room_id'=>$hotel_room_id,
		  			  'p.over_time'=>array('EGT',strtotime( date( 'Y-m-d',time() ) )),
		  			  'p.is_del'=>0  // 过滤删除掉的
	  	            );	
		  	$data = $this->field('p.start_time,p.over_time')
		  	->table($this->prefix.'room_putaway as p')
		  	->where($where)->select();
	        foreach ($data as $k=>$v){
	                $days_count = countDays($v['start_time'],$v['over_time']);		//计算相差天数
					$day = $v['start_time'];		
					//累计添加数据
					for ($i=0;$i<=$days_count;$i++) {
					    $arr[]=$day;
						$day = $day + 3600 * 24;		//每次写入数据库后，累加一天
					}
	        }
	  	return  array_unique($arr);
	 }

	  
	  // 计算价格
	  public function total_price($hotel_room_id,$starttime,$endtime,$type){
	  	
	  	$filed = $type == 1 ? 'spot_payment' : 'prepay';
	  	$where = array(
		  	's.is_del'=>0,
		  	's.day' =>array(
				array('egt',strtotime($starttime) ),
				array('lt',strtotime($endtime) )
		    ),
	  	    's.hotel_room_id'=>$hotel_room_id,
		  	
	  	);
	  	
	  	$data = $this->field('*')
	  			->table($this->prefix.'room_schedule as s')
	  			->where($where)
	  			//->select();
	  			->sum($filed);
	  	return $data;
	  
	  	
	  }
	
}

?>
