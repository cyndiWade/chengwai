<?php
/**
 * 房间下架管理
 */
class SproomPutawayAction extends AdminBaseAction {
  	
	private $module_name = '房间下架';
	
	//初始化数据库连接
	protected  $db = array(
		'SphotelRoom' => 'SphotelRoom',
		'SproomPutaway' => 'SproomPutaway'
	);
	
	
	/**
	 * 构造方法
	 */
	public function __construct() {
	
		parent::__construct();
	
		parent::global_tpl_view(array('module_name'=>$this->module_name));
	}
	
	
	//验证
	private function check_data() {
		import('@.Tool.Validate');
		
		$start_time = $this->_post('start_time');
		$over_time = $this->_post('over_time');
		$now_data = date('Y-m-d',time());
		
		if(Validate::check_date_differ($start_time,$over_time)) $this->error('开始日期不得大于结束日期');
		if(Validate::check_date_differ($now_data,$start_time)) $this->error('开始日期不得小于当天日期');

	}
	
	
	//酒店房型列表
	public function index () {

		//获取参数
		$hotel_room_id = $this->_get('hotel_room_id');	//房型ID
		$hotel_id = $this->_get('hotel_id');		//酒店ID
		if (empty($hotel_room_id)) $this->error('此房型不存在！');
		
		//连接数据库
		$HotelRoom = $this->db['SphotelRoom'];
		
		//检测房型
		$hotel_room_info = $HotelRoom->get_one_data(array('id'=>$hotel_room_id),'id,title');
		if (empty($hotel_room_info)) $this->error('此房型不存在！');
		
		//连接数据库表
		$RoomPutaway = $this->db['SproomPutaway'];		//酒店房型表
		
		//获取下架日期列表
		$rutaway_list = $RoomPutaway->seek_all_data(array('hotel_room_id'=>$hotel_room_id));

		parent::global_tpl_view( array(
			'action_name'=>'下架时间段',
			'title_name'=> $hotel_room_info['title'],
			'add_name'=>'添加下架时间'
		));
		
		$html['hotel_id'] = $hotel_id;
		$html['hotel_room_id'] = $hotel_room_id;
		$html['list'] = $rutaway_list;
		$this->assign('html',$html);
		$this->display();
	}
	
	
	/**
	 * 房型编辑
	 */
	public function putaway_edit () {
		//获取参数
		$act = $this->_get('act');						//操作类型
		$room_putaway_id = $this->_get('room_putaway_id');		//下架记录条数ID
		$hotel_room_id = $this->_get('hotel_room_id');		//酒店房型ID
		
		//连接数据库
		$HotelRoom = $this->db['SphotelRoom'];		//酒店房型表
		$RoomPutaway = $this->db['SproomPutaway'];		//下架日期表

		
		if ($act == 'add') {								//添加
			if ($this->isPost()) {
				$this->check_data();
				$RoomPutaway->create();
				$RoomPutaway->hotel_room_id = $hotel_room_id;
				$RoomPutaway->add_one_data() ? $this->success('添加成功！',U('Admin/SproomPutaway/index',array('hotel_room_id'=>$hotel_room_id))) : $this->error('添加失败请重新尝试！');
				exit;
			}
			
			//检测房型
			$hotel_room_info = $HotelRoom->get_one_data(array('id'=>$hotel_room_id),'id,title');
			if (empty($hotel_room_info)) $this->error('此房型不存在！');
			
			//表单标题
			$title_name = $hotel_room_info['title'].'--添加下架日期';

		} else if ($act == 'update') {			//修改
			if ($this->isPost()) {
				$this->check_data();
				$RoomPutaway->create();
				$RoomPutaway->save_data($room_putaway_id) ? $this->success('修改成功！') : $this->error('没有做出任何修改！');
				exit;
			}
			
			//查找房型
			$putaway_info = $RoomPutaway->seek_one_data(array('id'=>$room_putaway_id));
			if (empty($putaway_info)) $this->error('您编辑的房型不存在！');
			$title_name = '编辑';
			$html = $putaway_info;
			
		} else if ($act == 'delete') {			//删除
			$RoomPutaway->del_one_data($room_putaway_id) ? $this->success('删除成功！') : $this->error('删除失败，请稍后重试！');
			exit;
		}
		
		
		parent::global_tpl_view( array(
				'action_name'=>'编辑下架日期',
				'title_name' => $title_name
		));

		$this->assign('html',$html);
		$this->display();
	}
	
	
	
	

    
}