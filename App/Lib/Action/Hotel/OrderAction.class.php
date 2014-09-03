<?php
class OrderAction extends HotelBaseAction{

    private $module_name = '订单管理';
	
	//初始化数据库连接
	protected  $db = array(
		'Hotel'        => 'Hotel',
		'Users'        => 'Users',
		'UsersHotel'   => 'UsersHotel',
	    'RoomSchedule' => 'RoomSchedule',	
	    'HotelOrder'   => 'HotelOrder',
	    'HotelRoom'    => 'HotelRoom',
	    'OrderLog'     => 'OrderLog'
	);
	
	
	/**
	 * 构造方法
	 */
	public function __construct() {
	
		parent::__construct();
	
		parent::global_tpl_view(array('module_name'=>$this->module_name));
	}
	
	public function index(){
	
		$HotelOrder = $this->db['HotelOrder'];
		$HotelRoom  = $this->db['HotelRoom'];
		$Hotel      = $this->db['Hotel'];
		$userId =  $this->oUser->id;
	
		$list = $HotelOrder->get_all_order($userId);
		$order_status = C('ORDER_STATUS');
		$dispose_status = C('DISPOSE_STATUS');
		if($list == true){
			$pay_type =  C('PAY_TYPE');
			$is_from  =  C('IS_FROM');
			foreach ($list as $key=>$val){
			    $list[$key]['order_time']            = date('Y-m-d H:i:s',$val['order_time']);
				$list[$key]['in_date']               = date('Y-m-d',$val['in_date']);
				$list[$key]['out_date']               = date('Y-m-d',$val['out_date']);
				$list[$key]['order_type']            = $pay_type[$val['order_type']];
                $list[$key]['order_status']          = $order_status[$val['order_status']];
                $list[$key]['dispose']          = $dispose_status[$val['dispose_status']];
				$list[$key]['is_from']               = $is_from[$val['is_from']];
			
			}
		}
		//echo '<pre>';print_r($list);echo '</pre>';exit;
		$hover = parent::getAction($_SERVER["REQUEST_URI"]);
		$html  = array(
		         'list'=>$list,
		         'hover'=>$hover
		) ;
	    $this->assign('html',$html);
	    $this->display();
	}
	
	public function Ajax_order_status_edit(){
	    
		if ($this->isPost()){
			$id = $this->_post('id');			
			$HotelOrder = $this->db['HotelOrder'];	
			$log        = $this->db['OrderLog'];	
			$order_status = C('ORDER_STATUS');
			$dispose_status= C('DISPOSE_STATUS');
			$arr = array(
				'order_status'  =>$order_status['YCL'],
				'dispose_status'=>$dispose_status['QR']
			);
		    $result = $HotelOrder->update_order($id,$arr);
		    if($result!==false){
		       $arr = array(
		       'user_id'=>$this->oUser->id,
		       'order_id'=>$id,
		       'msg'=>'同意订单'
		       );
		       $log->orderlog($arr);
		       parent::callback(C('STATUS_SUCCESS'),'处理成功！');
		    }else{
			   parent::callback(C('STATUS_NOT_DATA'),'处理失败！');
		    }
		}else{
			parent::callback(C('STATUS_ACCESS'),'非法访问！');
		}
	
	
	
	}
	
	//处理拒绝的订单
	public function noagree(){
	    $id = $this->_get('id');
	    $hover = parent::getAction($_SERVER["REQUEST_URI"]);
		$html  = array(
		         'id'=>$id,
		         'list'=>$list,
		         'hover'=>$hover
		) ;
	  $this->assign('html',$html);
	  $this->display();
	}
	public function Ajax_order_jujue_status(){
	
	
	      if ($this->isPost()){
			$id = $this->_post('id');
			$con = $this->_post('con');			
			$HotelOrder = $this->db['HotelOrder'];
			$OrderLog   = $this->db['OrderLog'];		
			$order_status = C('ORDER_STATUS');
			$dispose_status= C('DISPOSE_STATUS');
			$order = array(
				'order_status'   =>$order_status['CLZ'],
				'dispose_status' =>$dispose_status['NQR'],
			    'dispose_content'=>$con
			);
		    $arr =array(
		       'user_id'=>$this->oUser->id,
		       'order_id'=>$id,
		       'msg'=>'拒绝订单');
			
		    $result = $HotelOrder->update_order($id,$order);
		    
		    if($result!==false){
		       $OrderLog->orderlog($arr);
		       parent::callback(C('STATUS_SUCCESS'),'处理成功！');
		    }else{
			   parent::callback(C('STATUS_NOT_DATA'),'处理失败！');
		    }
		}else{
			parent::callback(C('STATUS_ACCESS'),'非法访问！');
		}
	
	
	
	
	
	
	
	
	
	
	}

}