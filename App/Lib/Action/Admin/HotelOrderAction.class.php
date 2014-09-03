<?php
/**
 * 酒店订单管理
 */
class HotelOrderAction extends AdminBaseAction {
	//控制器说明
	private $module_name = '订单管理';
	
	//初始化数据库连接
	protected  $db = array(
		'HotelOrder'=>'HotelOrder',		//订单表
		'OrderLog' => 'OrderLog',			//订单日志表
		'RoomSchedule'=>'RoomSchedule', //酒店房型的价格
		'KfLog'		   => 'KfLog',
		'UsersHotel'=>'UsersHotel'
	);
	

	/**
	 * 构造方法
	 */
	public function __construct() {
	
		parent::__construct();
	
		parent::global_tpl_view(array('module_name'=>$this->module_name));
		
		import('@.Tool.Tool');		//工具类
	}
	
	
	
	//酒店列表
	public function index () {
		$get = $this->_get();
		unset($get['_URL_']);

		if (!empty($get)) {
			$condition['ho.dispose_status'] = $get['dispose_status'];
		} 
		

		//连接数据库
		$HotelOrder = $this->db['HotelOrder'];	
		$order_list = $HotelOrder->seek_order_list($condition);
        $PayType  = C('PayType');
		if ($order_list == true) {
			foreach ($order_list as $key=>$val) {
				$order_list[$key]['order_explain'] = $this->order_status[$val['order_status']]['explain'];
				$order_list[$key]['dispose_admin_explain'] = $this->dispose_status[$val['dispose_status']]['admin_explain'];
				$order_list[$key]['paytype']  = $PayType[$val['order_type']]['explain'];
				//订单状态是酒店无法处理，等待尊旅会协商处理的。
				if ($val['order_status'] == $this->order_status[2]['num']) {
					$order_list[$key]['dispose'] = true;
				}
			}
		}

		
		parent::global_tpl_view( array(
			'action_name'=>'订单列表',
			'title_name'=>'订单列表',
			'add_name' =>'人工预定',
		));
		
		$html['list'] = $order_list;
		$this->assign('html',$html);
		$this->display();
	}
	
	//人工预定
	public function edit (){
		$act = $this->_get('act');
		$HotelOrder = $this->db['HotelOrder'];

		$Hotel = new HotelModel();
		$html['hotle_list'] = $Hotel -> get_all_hotel_name();
		
		if ($act == 'add') {
			if ($this->isPost()) {
				$HotelOrder -> create();

				$order_time = strtotime(date('Y-m-d H:i:s'));
				$UsersHotel = new UsersHotelModel();
				$user_id = $UsersHotel->get_hotel_userid($_POST['hotel_id']);
				
				$HotelOrder->user_id = $user_id;
				$HotelOrder->order_time = $order_time;
				$HotelOrder->order_sn = date('Ymd').$order_time;
				$HotelOrder->user_code = '人工预定';
				$HotelOrder->in_date = strtotime($_POST['in_date']);
				$HotelOrder->out_date = strtotime($_POST['out_date']);
				$HotelOrder->order_type = 2;
				$id = $HotelOrder ->add();
				$id ? $this->success('添加成功！',U('Admin/HotelOrder/index')) : $this->error('添加失败请重新尝试！');
			
				exit;
			}

			$title_name = '人工预定';
		}


		parent::global_tpl_view( array(
				'action_name'=>'编辑',
				'title_name'=>$title_name,
		));
		$this->assign('html',$html);
		$this->display();
	}
	
	//人工预定--查找房型ajax方法
	public function findHotelRomm(){
		$result = array();
		$hotel_id =$_POST['hotel_id'];

		$HotelRoom = new HotelRoomModel();
		$result = $HotelRoom->get_hotel_rooms($hotel_id);
		$this->ajaxReturn($result,"JSON");
		
	}
	//人工预定--查找价格ajax方法
	public function findRoomPrice(){
		$Prices = array();
		$data = array();
		$result = array('room_num'=>99999,'total_price'=>0);

		$data['hotel_room_id'] = $_POST['room_id'];
		$data['in_date'] = strtotime($_POST['in_date']);
		$data['out_date'] = strtotime($_POST['out_date']);

		$RoomSchedule = new RoomScheduleModel();
		$Prices = $RoomSchedule->Seek_Data_Schedule($data);
		
		foreach($Prices as $key=>$val){
			 if($val['room_num']>0){
				 if($result['room_num'] > $val['room_num']){
					 $result['room_num'] = $val['room_num'];
				 }

				 $result['total_price'] += $val['prepay'];
			 }else{
				$result['room_num'] = 0;
				$result['total_price'] = 0;
			 }
		}
		
		$this->ajaxReturn($result,"JSON");
	}
	//订单处理
	public function order_dispose () {
		header('Content-Type:text/html;charset=utf-8');
		
		$hotel_order_id = $this->_get('hotel_order_id');
		$HotelOrder = $this->db['HotelOrder'];
		
		if (empty($hotel_order_id)) $this->error('非法操作！');
		$order_info = $HotelOrder->seek_one_hotel(array('id'=>$hotel_order_id),'id,order_sn,dispose_content,feedback,order_status');
		if (empty($order_info)) $this->error('此订单不存在！');		
		
		
		if ($order_info['order_status'] == $this->order_status[1]['num']) Tool::alertClose('此订单已处理，请勿重复操作！');
		
	
		//修改订单
		if ($this->isPost()) {
			$HotelOrder->create();
			$dispose_status = $this->_post('dispose_status');		//处理状态
			
			$HotelOrder->order_status = $this->order_status[1]['num'];		//订单状态为已处理
			$order_id = $HotelOrder->save_one_data(array('id'=>$hotel_order_id));		//修改订单
		
			if ($order_id == true) {
				$this->add_data_order_log($hotel_order_id,$this->dispose_status[$dispose_status]['admin_explain']);		//记录订单日志
				Tool::alertClose('成功');
				//$this->success('成功');
			} else {
				Tool::alertClose('失败');
				//$this->error('失败');
			}

			exit;		
		}
		
		parent::global_tpl_view( array(
				'action_name'=>'订单处理',
				'title_name'=>'订单号：'.$order_info['order_sn'],
		));
		
		$html['order_info'] = $order_info;
		$html['dispose_status'] = $this->dispose_status;
		$this->assign('html',$html);
		$this->display();
	}
	
	
	
	//订单日志
	public function order_log () {
		$hotel_order_id = $this->_get('hotel_order_id');
		$HotelOrder = $this->db['HotelOrder'];
		$OrderLog = $this->db['OrderLog'];
		$KfLog	  = $this->db['KfLog'];
		
		if (empty($hotel_order_id)) $this->error('非法操作！');
		$order_info = $HotelOrder->seek_one_hotel(array('id'=>$hotel_order_id),'id,order_sn');
		if (empty($order_info)) $this->error('此订单不存在！');
		
		//日志列表
		$log_list = $OrderLog->seek_order_data($hotel_order_id,'ol.*,u.account');
        $kf_list = $KfLog->get_all_data($hotel_order_id,'ol.*,u.account');
        //echo'<pre>';print_R($log_list);echo'</pre>';
		//echo'<pre>';print_R($kf_list);echo'</pre>';
        parent::global_tpl_view(array(
				'action_name'=>'订单日志',
				'title_name'=>'订单：'.$order_info['order_sn'].'-日志',
		));

		$html['log_list'] = $log_list;
		$html['kf_list'] = $kf_list;
		$this->assign('html',$html);
		$this->display();
	}
	
	
	//订单中每日的价格
	public function order_price_info(){
		$hotel_order_id = $this->_get('hotel_order_id');
		$HotelOrder = $this->db['HotelOrder'];
		$RoomSchedule = $this->db['RoomSchedule'];
		if (empty($hotel_order_id)) $this->error('非法操作！');
		$order_info = $HotelOrder->seek_one_hotel(array('id'=>$hotel_order_id),'id,hotel_room_id,in_date,out_date');
		if (empty($order_info)) $this->error('此订单不存在！');
		
		$data = array(
				'hotel_room_id' => $order_info['hotel_room_id'],
				'in_date'       => $order_info['in_date'],
				'out_date'      => $order_info['out_date']
		);
		
		$order_price_list = $RoomSchedule->Seek_Data_Schedule($data);
	
		
		parent::global_tpl_view(array(
				'action_name'=>'订单每日的价格',
				'title_name'=>'订单：'.$order_info['order_sn'].'-价格',
		));
		
		$html['list'] = $order_price_list;
		$this->assign('html',$html);
		
		$this->display();
	}
	
	//添加订单日志
	private function add_data_order_log ($order_id,$msg) {
		$OrderLog = $this->db['OrderLog'];
		$OrderLog->user_id = $this->oUser->id;
		$OrderLog->order_id = $order_id;
		$OrderLog->msg = $msg;
		return $OrderLog->add_order_log();
	}


	//处理状态
	public function dispose_status () {
		$hotel_order_id = $this->_get('hotel_order_id');
		$HotelOrder = $this->db['HotelOrder'];
		$this->global_system->DISPOSE_STATUS = C('DISPOSE_STATUS');

		if (empty($hotel_order_id)) $this->error('非法操作！');
		$order_info = $HotelOrder->seek_one_hotel(array('id'=>$hotel_order_id),'id,order_sn,dispose_status');
		if (empty($order_info)) $this->error('此订单不存在！');
		
		$dispose_status = $order_info['dispose_status'];
		foreach ($this->global_system->DISPOSE_STATUS AS $key=>$val) {
				if ($val['num'] <= $dispose_status) {
					$this->global_system->DISPOSE_STATUS[$key]['checked'] = 'checked="checked"';
					$this->global_system->DISPOSE_STATUS[$key]['disabled'] = 'disabled="disabled"';
				}
		}
		if ($this->isPost()) {			//修改
			$HotelOrder->create();
			$dispose_status = end($this->_post('dispose_status'));//处理状态
			$HotelOrder->dispose_status = $dispose_status;
			$HotelOrder->order_status = $this->order_status[1]['num'];		//订单状态为已处理
			$order_id = $HotelOrder->save_one_data(array('id'=>$hotel_order_id));		//修改订单
		
			if ($order_id == true) {
				$this->add_data_order_log($hotel_order_id,$this->dispose_status[$dispose_status]['admin_explain']);		//记录订单日志
				Tool::alertClose('chenggong');
				//$this->success('成功');
			} else {
				Tool::alertClose('shibai');
				//$this->error('失败');
			}

			exit;
				
		}

		parent::global_tpl_view(array(
				'action_name'=>'订单状态',
				'title_name'=>'订单：'.$order_info['order_sn'].'-状态',
		));
		$html['DISPOSE_STATUS'] = $this->global_system->DISPOSE_STATUS;
		
		$this->assign('html',$html);
		$this->display();
	}
	
	//客服日志
	public function kefu_log(){
	       $KfLogs = $this->db['KfLog'];
	       $OrderLog = $this->db['OrderLog'];
	      
		   if($this->isPost()){
		      $kf_id  =  $this->_Post('kf_id');
		      $order_id = $this->_Post('order_id');
		      $user_id  = $this->oUser->id;
		      $addtime  = time();
		      $data = array(
			      'user_id'=>$user_id,
			      'order_id'=>$order_id,
			      'addtime'=>$addtime,
			      'kf_id'=>$kf_id,
		     // 'msg'=>
		      );
		      //判断有没有操作
		      $existdata = $KfLogs->get_exist($kf_id,$order_id);
		      if(!empty($existdata))parent::callback(C('STATUS_SUCCESS'),'已经处理了！',array());
		      $user = $KfLogs->add_log($data);
		      if($user){
		      	$html='处理成功';
		      }
		      parent::callback(C('STATUS_SUCCESS'),'处理成功！',$html);
		     exit;
		   }
		   $html['order_id'] = $this->_get('hotel_order_id');
		   $donedata = $KfLogs->get_done($this->_get('hotel_order_id'));
		   //echo '<pre>';print_R($donedata);echo'</pre>';
		   $KF =array();
		   foreach(C('KF') as  $k=>$v){
		   	  $KF[$k]['num'] = $v['num'] ;
		   	  $KF[$k]['explain'] = $v['explain'];
		   	  if(in_array($v['num'],$donedata))$KF[$k]['disabled'] = 1;
		   	  
		   }
		   $html['KF'] = $KF;
		   //echo '<pre>';print_R($KF);echo'</pre>';
	       $this->assign('html',$html);
		   $this->display();
	}
	
    private function add_data_kefu_log ($order_id,$msg) {
		$OrderLog = $this->db['OrderLog'];
		$OrderLog->user_id = $this->oUser->id;
		$OrderLog->order_id = $order_id;
		$OrderLog->msg = $msg;
		return $OrderLog->add_order_log();
	}
	
	//自动检测订单
	public function check_order(){
		$HotelOrder = $this->db['HotelOrder'];
		if (empty($_SESSION['last_check']))
	    {
	        $_SESSION['last_check'] = time();
	        //make_json_result('', '', array('new_orders' => 0, 'new_paid' => 0));
	    }
        $HotelOrder->check_order();
        exit;
    /* 新订单 */
    $sql = 'SELECT COUNT(*) FROM ' . $ecs->table('order_info').
    " WHERE add_time >= '$_SESSION[last_check]'";
    $arr['new_orders'] = $db->getOne($sql);

    /* 新付款的订单 */
    $sql = 'SELECT COUNT(*) FROM '.$ecs->table('order_info').
    ' WHERE pay_time >= ' . $_SESSION['last_check'];
    $arr['new_paid'] = $db->getOne($sql);

    $_SESSION['last_check'] = gmtime();

    if (!(is_numeric($arr['new_orders']) && is_numeric($arr['new_paid'])))
    {
        make_json_error($db->error());
    }
    else
    {
        make_json_result('', '', $arr);
    }
	}
	
    
}