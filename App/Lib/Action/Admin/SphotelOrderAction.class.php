<?php
/**
 * 酒店订单管理
 */
class SphotelOrderAction extends AdminBaseAction {
	//控制器说明
	private $module_name = '订单管理';
	
	//初始化数据库连接
	protected  $db = array(
		'SphotelOrder'=>'SphotelOrder',		//订单表
		'OrderLog' => 'OrderLog',			//订单日志表
		'SproomSchedule'=>'SproomSchedule' //酒店房型的价格
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
		$HotelOrder = $this->db['SphotelOrder'];
			
		$order_list = $HotelOrder->seek_order_list($condition);
		//echo $HotelOrder->getLastSql();exit;
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
		));
		
		$html['list'] = $order_list;
		//echo '<pre>';print_R($html);echo'</pre>';
		$this->assign('html',$html);
		$this->display();
	}
	
	
	//订单处理
	public function order_dispose () {
		header('Content-Type:text/html;charset=utf-8');
		
		$hotel_order_id = $this->_get('hotel_order_id');
		$HotelOrder = $this->db['SphotelOrder'];
		
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
		$HotelOrder = $this->db['SphotelOrder'];
		$OrderLog = $this->db['OrderLog'];
		
		if (empty($hotel_order_id)) $this->error('非法操作！');
		$order_info = $HotelOrder->seek_one_hotel(array('id'=>$hotel_order_id),'id,order_sn');
		if (empty($order_info)) $this->error('此订单不存在！');
		
		//日志列表
		$log_list = $OrderLog->seek_order_data($hotel_order_id,'ol.*,u.account');

		parent::global_tpl_view(array(
				'action_name'=>'订单日志',
				'title_name'=>'订单：'.$order_info['order_sn'].'-日志',
		));
		
		$html['list'] = $log_list;
		$this->assign('html',$html);
		$this->display();
	}
	
	
	//订单中每日的价格
	public function order_price_info(){
		$hotel_order_id = $this->_get('hotel_order_id');
		$HotelOrder = $this->db['SphotelOrder'];
		$RoomSchedule = $this->db['SproomSchedule'];
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
    
}