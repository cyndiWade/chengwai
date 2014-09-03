<?php
/**
 * 酒店房型每日价格管理
 */
class RoomScheduleAction extends AdminBaseAction {
  	
	private $module_name = '房型价格管理';
	
	//初始化数据库连接
	protected  $db = array(
		'HotelRoom' => 'HotelRoom',
		'RoomSchedule'=>'RoomSchedule',
	);
	
	
	/**
	 * 构造方法
	 */
	public function __construct() {
	
		parent::__construct();
	
		parent::global_tpl_view(array('module_name'=>$this->module_name));
	}
	
	
	//酒店房型列表
	public function index () {

		//获取参数
		$hotel_room_id = $this->_get('hotel_room_id');
		if (empty($hotel_room_id)) $this->error('此房型不存在！');
		
		//连接数据库
		$HotelRoom = $this->db['HotelRoom'];
		$hotel_room_info = $HotelRoom->get_one_data(array('id'=>$hotel_room_id),'id,title');
		if (empty($hotel_room_info)) $this->error('此房型不存在！');
	
		parent::global_tpl_view( array(
			'action_name'=>'酒店房型',
			'title_name'=> $hotel_room_info['title'],
			'add_name'=>'添加房型'
		));
		$html['hotel_room_id'] = $hotel_room_id;
		$this->assign('html',$html);
		$this->display();
	}
	
	
	//编辑日程
	public function Ajax_room_schedule_edit() {
		if ($this->isPost()) {
			
			$arr = array('hotel_room_id','spot_payment','prepay','room_num');
			$post_data = $this->_post();
			$int_start_time= strtotime($post_data['start_time']);		//开始日期
			$int_over_time = strtotime($post_data['over_time']);		//结束如期
			$now_data = strtotime(date('Y-m-d',time()));					//当天日期

			//时间验证
			if($int_start_time < $now_data) parent::callback(C('STATUS_NOT_CHECK'),'开始日期不得小于当天日期！');
			if($int_start_time > $int_over_time) parent::callback(C('STATUS_NOT_CHECK'),'开始日期不得大于结束日期！');
			
			//连接数据库
			$RoomSchedule = $this->db['RoomSchedule'];	

			//检测非法字段
			foreach ($arr AS $key) {
				if ($post_data[$key] == '') {
					parent::callback(C('STATUS_NOT_DATA'),$key.'不得为空！');
				}
			}

			//对只有一天数据的处理
			if ($int_start_time == $int_over_time) {
				$day = $int_start_time; 
				$RoomSchedule->create();
				$RoomSchedule->day = $day;
				$id = $RoomSchedule->seek_update_data(array('hotel_room_id'=>$post_data['hotel_room_id'],'day'=>$day));
				$id ? parent::callback(C('STATUS_SUCCESS'),'添加成功！',array('id'=>$id)) : parent::callback(C('STATUS_UPDATE_DATA'),'添加失败！');

			//对于多天数据的处理	
			} elseif ($int_over_time > $int_start_time) {
				
				$days_count = countDays($int_start_time,$int_over_time);		//计算相差天数
				$day = $int_start_time;	
				
				if ($days_count > 60) parent::callback(C('STATUS_OTHER'),'您一次只能修改60天内的数据！');
				
				//累计添加数据
				for ($i=0;$i<=$days_count;$i++) {
				
					//查找数据库是否有数据
					$RoomSchedule->create();
					$RoomSchedule->day = $day;
					$RoomSchedule->seek_update_data(array('hotel_room_id'=>$post_data['hotel_room_id'],'day'=>$day));
	
					$day = $day + 3600 * 24;		//每次写入数据库后，累加一天
				}
				parent::callback(C('STATUS_SUCCESS'),'添加成功！');
			} 
			
		} else {
			parent::callback(C('STATUS_ACCESS'),'非法访问！');
		}
	}
	

	
	/**
	 * 获取日程API
	 */
	public function AJAX_Get_Schedule() {
		
		if ($this->isPost()) {
			$RoomSchedule = $this->db['RoomSchedule'];	
			$hotel_room_id = $this->_post('hotel_room_id');
			if (empty($hotel_room_id)) {
				parent::callback(C('STATUS_NOT_DATA'),'请求客房不存在！');
			}	
			$data_list = $RoomSchedule->Seek_All_Schedule($hotel_room_id);
			
			$data_list ?  parent::callback(C('STATUS_SUCCESS'),'获取成功！',$data_list) : parent::callback(C('STATUS_NOT_DATA'),'暂无数据！') ;
		} else {
			parent::callback(C('STATUS_ACCESS'),'非法访问！');
		}
		
	}
	
	
	/**
	 * 删除日程
	 */
	public function AJAX_DEL_Schedule () {
		if ($this->isPost()) {
			$RoomSchedule = $this->db['RoomSchedule'];
			$id = $this->_post('id');
			if (empty($id)) {
				parent::callback(C('STATUS_NOT_DATA'),'非法操作！');
			}
			$del_status = $RoomSchedule->delete_data(array('id'=>$id));
			$del_status ?  parent::callback(C('STATUS_SUCCESS'),'删除成功！') : parent::callback(C('STATUS_NOT_DATA'),'删除失败！') ;
		} else {
			parent::callback(C('STATUS_ACCESS'),'非法访问！');
		}
	}
    
}