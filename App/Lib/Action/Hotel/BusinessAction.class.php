<?php
class BusinessAction extends HotelBaseAction{


    private $module_name = '商家管理';
	
 //   private $no_logo  =array('url'=>'App/Public/media/hotel.jpg');
    private $no_logo  =array('url'=>'App/Public/Hotel/image/no_log.jpg');

	
	//初始化数据库连接
	protected  $db = array(
		'Hotel'=>'Hotel',
		'Users' =>'Users',
		'UsersHotel' => 'UsersHotel',
	    'RoomSchedule' =>'RoomSchedule'	
	);
	
	
	/**
	 * 构造方法
	 */
	public function __construct() {
	
		parent::__construct();
	
		parent::global_tpl_view(array('module_name'=>$this->module_name));
	}
	
	
	
	//酒店列表
	public function index () {
        $UserHotel = $this->db['UsersHotel']; //
        $HotelRoom = D('HotelRoom');//$this->db['HotelRoom'];  //酒店房型
        $HotelImg  = D('HotelImg');
        $userId =  $this->oUser->id; //用户id
        $list =  $UserHotel->get_user_hotels($userId);

		if ($list == true) {
		        $hotel_ids = getArrayByField($list,'id');
		        $imgs  = $HotelImg->get_hotel_images(array('hotel_id'=>array('in',$hotel_ids),'type'=>array('eq',3) ),'hotel_id,type,url');
				$rooms = $HotelRoom->get_room_type( array('hotel_id'=>array('in',$hotel_ids) ));
		
				if ($imgs == true or $rooms == true) {
					//组合图片访问地址
					parent::public_file_dir($imgs,'url','images/');
		

				   //public_file_dir
					$imgs_sort  = regroupKey($imgs,'hotel_id');
					$rooms_sort = regroupKey($rooms,'hotel_id');
				
					foreach ($list AS $key=>$val) {

						$list[$key]['logo'] = empty($imgs_sort[$val['id']]) ? $this->no_logo :  $imgs_sort[$val['id']][0];
						$list[$key]['room'] = $rooms_sort[$val['id']];
					}
	
					
				}
					
		}
		
		$html['list']   = $list; 
		parent::global_tpl_view( array(
			'action_name'=>'酒店管理',
			'title_name'=>'酒店列表',
		));
		$url = $_SERVER["REQUEST_URI"];
	    $html['hover'] = parent::getAction($url); //激活菜单
		$this->assign('html',$html);
		//echo'<pre>';print_R($html);echo '</pre>';
		$this->display();
	}
	
	
	/**
	 * 编辑酒店
	 */
	public function hotel_edit () {
		$Users = $this->db['Users'];				//用户表
		$Hotel = $this->db['Hotel'];				//酒店表
		$UsersHotel = $this->db['UsersHotel'];		//酒店用户关系表
		$act = $this->_get('act');						//操作类型
		$user_id = $this->_get('user_id');			//酒店账号ID
		
		$account_info = $Users->get_account(array('id'=>$user_id));
		if (empty($account_info)) $this->error('此酒店账号不存在或已被删除');

		if ($act == 'add') {								//添加
			if ($this->isPost()) {
				$Hotel->create();
				$hotel_id = $Hotel->add();
				if ($hotel_id == true) {
					$UsersHotel->user_id = $user_id;
					$UsersHotel->hotel_id = $hotel_id;
					$UsersHotel->add() ? $this->success('添加成功！') : $this->error('添加失败请重新尝试！');
				} else {
					
				}
				exit;
			}
			
			$html['account'] = $account_info['account'];
		
		} else if ($act == 'update') {			//修改
			if ($this->isPost()) {
				exit;
			}

		} else if ($act == 'delete') {			//删除
		
			exit;
		}
		
		
		parent::global_tpl_view( array(
				'action_name'=>'酒店编辑',
				'title_name' => '编辑信息'
		));

		$html['hotel_xj'] = $this->hotel_xj; 
		$this->assign('html',$html);
		$this->display();
	}
	
	public function ajax_info(){
	  
		if($this->_get('id')){
		    $HotelRoom = D('HotelRoom');		
		    $result = $HotelRoom->get_room_type(array('id'=>$this->_get('id')),'info');
		    foreach($result as $key=>$val){
		       $info = $val['info'];
		    }
		}else{
		       $info = '没有详情信息';
		}
		die($info);

	}
}