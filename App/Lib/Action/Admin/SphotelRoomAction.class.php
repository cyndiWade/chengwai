<?php
/**
 * 酒店房型管理
 */
class SphotelRoomAction extends AdminBaseAction {
  	
	private $module_name = '商家管理';
	
	//初始化数据库连接
	protected  $db = array(
		'Sphotel'=>'Sphotel',
		'SphotelRoom' => 'SphotelRoom',
		'SproomImg'	=> 'SproomImg'
	);
	
	
	//房型图片类型
	private $img_type = array(
			1 => array(
				'num'=>1,
				'explain'=>'房型360x200',
			),
			2 => array(
				'num'=>2,
				'explain'=>'房型200x200',
			),
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
		$hotel_id = $this->_get('hotel_id');
		if (empty($hotel_id)) $this->error('酒店不存在！');
		
		//连接数据库表
		$HotelRoom = $this->db['SphotelRoom'];		//酒店房型表
		$Hotel = $this->db['Sphotel'];				//酒店表
		$Roomspc   = C('Roomspc');
		//查询酒店
		$hotel_info = $Hotel->get_one_hotel(array('id'=>$hotel_id),'hotel_name');
		if (empty($hotel_info)) $this->error('酒店不存在！');
       
		//酒店房型列表
		$html['list'] = $HotelRoom->get_hotel_rooms($hotel_id);
		//echo '<pre>';print_R($html);echo'</pre>';
        foreach($html['list'] as $k=>$v ){
        	$html['list'][$k]['type']  = $Roomspc[$v['type']]['explain'];
        }
        //echo '<pre>';print_R($html);echo'</pre>';
		parent::global_tpl_view( array(
			'action_name'=>'酒店房型',
			'title_name'=> $hotel_info['hotel_name'].'--所有房型',
			'add_name'=>'添加房型'
		));
		$html['hotel_id'] = $hotel_id;
		$this->assign('html',$html);
		$this->display();
	}
	
	
	/**
	 * 房型编辑
	 */
	public function room_edit () {
		//获取参数
		$act = $this->_get('act');						//操作类型
		$hotel_id = $this->_get('hotel_id');		//酒店账号ID
		$hotel_room_id = $this->_get('hotel_room_id');		//酒店房型ID
		
		//连接数据库
		$Hotel     = $this->db['Sphotel'];						//酒店表
		$HotelRoom = $this->db['SphotelRoom'];		//酒店房型表
		
		
		if ($act == 'add') {								//添加
			if ($this->isPost()) {
				$HotelRoom->create();
				$HotelRoom->hotel_id = $hotel_id;
				$HotelRoom->add() ? $this->success('添加成功！',U('Admin/SphotelRoom/index',array('hotel_id'=>$hotel_id))) : $this->error('添加失败请重新尝试！');
				exit;
			}
			
			//查询酒店
			$hotel_info = $Hotel->get_one_hotel(array('id'=>$hotel_id),'hotel_name');
			if (empty($hotel_info)) $this->error('酒店不存在或已被删除！');
			
			//表单标题
			$title_name = $hotel_info['hotel_name'].'---添加房型';
			

		} else if ($act == 'update') {			//修改
			if ($this->isPost()) {
				$HotelRoom->create();
				$HotelRoom->save_one_data(array('id'=>$hotel_room_id)) ? $this->success('修改成功！') : $this->error('没有做出任何修改！');
				exit;
			}
			//查找房型
			$hotel_room_info = $HotelRoom->get_one_data(array('id'=>$hotel_room_id),'title,info,privilege_day,type,special');
			if (empty($hotel_room_info)) $this->error('您编辑的房型不存在！');
			$title_name = $hotel_room_info['title'].'---编辑';
		
			$html = $hotel_room_info;
			
		} else if ($act == 'delete') {			//删除
			$HotelRoom->del_one_data($hotel_room_id) ? $this->success('删除成功！') : $this->error('删除失败，请稍后重试！');
			exit;
		}
		
		
		parent::global_tpl_view( array(
				'action_name'=>'房型编辑',
				'title_name' => $title_name
		));
		$html['Roomspc']   = C('Roomspc');
//echo'<pre>';print_R($html);echo'</pre>';exit;
		$this->assign('html',$html);
		$this->display();
	}
	
	
	
	//酒店图片编辑
	public function room_img () {
		$hotel_room_id = $this->_get('hotel_room_id');
		$HotelRoom = $this->db['SphotelRoom'];	//房型表
		$RoomImg = $this->db['SproomImg'];		//房型图片表		
			
		//检测房型
		if (empty($hotel_room_id)) $this->error('此房型不存在！');
		$hotel_room_info = $HotelRoom->get_one_data(array('id'=>$hotel_room_id),'id,title');
		if (empty($hotel_room_info)) $this->error('此房型不存在！');
		
	
		//获取酒店图片数据
		$photo_list = $RoomImg->get_hotel_images(array('hotel_room_id'=>$hotel_room_id),'id,type,url');
		if (!empty($photo_list)) {
			parent::public_file_dir($photo_list, array('url'), 'images/');		//组合访问地址
			$photo_type_list = regroupKey($photo_list,'type');						//按照图片类似分类
		}
	
	
		//注入模板
		$html['hotel_room_id'] = $hotel_room_info['id'];
		$html['img_type'] = $this->img_type;
		$html['photo_type_list'] = $photo_type_list;
		parent::global_tpl_view( array(
				'action_name'=>'房型图片',
				'title_name' => $hotel_room_info['title'].'--上传图片'
		));
		$this->assign('html',$html);
		$this->display();
	}
	
	
	
	/**
	 * AJAX处理上传车辆图片
	 */
	public function ajax_photo_upload() {
		header('Content-Type:text/html;charset=utf-8');
	
		if ($this->isPost()) {
			/* 上传文件目录 */
			$upload_dir = C('UPLOAD_DIR');
			$dir = $upload_dir['web_dir'].$upload_dir['image'];		//图片文件保存地址
			$RoomImg = $this->db['SproomImg'];		//酒店图片表
	
			/* 执行上传 */
			$file = $_FILES['photo_files'];					//上传的文件
			$hotel_room_id = $this->_post('hotel_room_id');				//车辆ID
			$type = $this->_post('type');						//图片类型
	
			/* 参数验证 */
			if (empty($hotel_room_id) || empty($type)) parent::callback(C('STATUS_DATA_LOST'),'参数错误！');
	
			/* 执行上传 */
			$result = parent::upload_file($file, $dir,5120000);
	
			/* 上传结果处理 */
			if ($result['status'] == true) {
				$RoomImg->hotel_room_id = $hotel_room_id;
				$RoomImg->type = $type;
				$RoomImg->url = $result['info'][0]['savename'];
				$hotel_img_id = $RoomImg->add();		//写入数据库
	
				if ($hotel_img_id) {
					$return['success'] = true;
					$return['info'] = '保存成功';
					echo json_encode($return);
				} else {
					$return['success'] = false;
					$return['info'] = '保存失败';
					echo json_encode($return);
				}
			} else {
				$return['success'] = false;
				$return['info'] = '上传失败';
				echo json_encode($return);
			}
	
		} else {
			parent::callback(C('STATUS_ACCESS'),'非法访问！');
		}
	
	}
	
	
	/**
	 * AJAX车辆删除图片
	 */
	public function ajax_photo_remove () {
		if ($this->isPost()) {
			$id = $this->_post('id');
			$RoomImg = $this->db['SproomImg'];		//酒店图片表
			$RoomImg->del_one_image($id) ? parent::callback(C('STATUS_SUCCESS'),'删除成功') : parent::callback(C('STATUS_UPDATE_DATA'),'删除失败') ;
		} else {
			parent::callback(C('STATUS_ACCESS'),'非法访问！');
		}
	}

    
}