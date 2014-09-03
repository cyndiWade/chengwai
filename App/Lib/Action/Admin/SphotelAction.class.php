<?php
/**
 * 酒店管理
 */
class SphotelAction extends AdminBaseAction {
  	
	//初始化数据库连接
	protected  $db = array(
			'Sphotel'=>'Sphotel',
			'Users' =>'Users',
			'UsersHotel' => 'UsersHotel',
			'SphotelImg' => 'SphotelImg'
	);
	
	//控制器说明
	private $module_name = '商家管理';
	
	//酒店星级
	private $hotel_xj_type = array(
		1=> array(
			'num'=>1,
			'explain'=>'一星级'
		),
		2=> array(
			'num'=>2,
			'explain'=>'二星级'
		),
		3=> array(
			'num'=>3,
			'explain'=>'三星级'
		),
		4=> array(
			'num'=>4,
			'explain'=>'四星级'
		),
		5=> array(
			'num'=>5,
			'explain'=>'五星级'
		)
	);

	//酒店类型
	private $hotel_type_value = array(
		4=> array(
			'num'=>4,
			'explain'=>'不使用'
		),
		1=> array(
			'num'=>1,
			'explain'=>'特价酒店'
		),
		2=> array(
			'num'=>2,
			'explain'=>'预定送免房'
		),
		3=> array(
			'num'=>3,
			'explain'=>'订房返红包'
		),
	
	);
	
	//酒店图片类型
	private $img_type = array(
// 		1 => array(
// 			'num'=>1,
// 			'explain'=>'房型360x200',
// 		),
// 		2 => array(
// 			'num'=>2,
// 			'explain'=>'房型200x200',
// 		),
		3 => array(
			'num'=>3,
			'explain'=>'外景360x200',
		),		
		4 => array(
			'num'=>4,
			'explain'=>'外景200x200',		
		),
		5 => array(
			'num'=>5,
			'explain'=>'酒店logo',
		)
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
		$user_id = $this->_get('user_id');
		$merchant_id = $this->_get('merchant_id');
		//连接数据库
		$Hotel = $this->db['Sphotel'];	
		$UsersHotel = $this->db['UsersHotel'];

		if (empty($merchant_id)) {
			$html['merchant_id'] = 0;
			$html['list'] = $Hotel->field('id,hotel_name,hotel_sf,hotel_cs,hotel_q,hotel_xj,hotel_pf,hotel_syq,hotel_dz,hotel_tel')->where(array('is_del'=>0))->select();
		} else {
			$html['merchant_id'] = $merchant_id;
			$html['list'] = $Hotel->field('id,hotel_name,hotel_sf,hotel_cs,hotel_q,hotel_xj,hotel_pf,hotel_syq,hotel_dz,hotel_tel')
			->where(array('is_del'=>0,'merchant_id'=>$merchant_id))->select();
		}
        
		parent::global_tpl_view( array(
			'action_name'=>'酒店管理',
			'title_name'=>'酒店列表',
		));
		$this->assign('html',$html);
		$this->display();
	}
	
	
	/**
	 * 编辑酒店
	 */
	public function hotel_edit () {
		$Users = $this->db['Users'];				//用户表
		$Hotel = $this->db['Sphotel'];				//酒店表
		$UsersHotel = $this->db['UsersHotel'];		//酒店用户关系表
		$act = $this->_get('act');						//操作类型
		$user_id = $this->_get('user_id');			//酒店账号ID
		$hotel_id = $this->_get('hotel_id');		//酒店ID
        $merchant_id = $this->_get('merchant_id');

		$this->Hotel_Lp = C('Hotel_Lp');

		$gitf=new GiftModel();
		$gift_list = $gitf->seek_all_data();

		if ($act == 'add') {								//添加
			if ($this->isPost()) {
				
				$Hotel->create();
				$hotel_lp = $this->_POST['hotel_lp'];
				$hotel_lp = implode(',',$hotel_lp);
				$Hotel->hotel_lp = $hotel_lp;
				$hotel_id = $Hotel->add();
				if ($hotel_id == true) {
					/*$UsersHotel->user_id = $user_id;
					$UsersHotel->hotel_id = $hotel_id;
					$UsersHotel->add() */
					$hotel_id ? $this->success('添加成功！',U('Admin/Sphotel/hotel_img',array('hotel_id'=>$hotel_id))) : $this->error('添加失败请重新尝试！');
				} else {
					$this->error('酒店添加失败，请重新尝试！');
				}
				exit;
			}
			
			//获取用户账号信息
			//$account_info = $Users->get_account(array('id'=>$user_id));
			if (empty($merchant_id)) $this->error('此酒店账号不存在或已被删除');
			$html['merchant_id'] = $merchant_id; // $account_info['account'];		//账号

			//模板标题
			$title_name = '添加酒店';
			
		} else if ($act == 'update') {			//修改
			if ($this->isPost()) {
				$hotel_lp = $this->_POST('hotel_lp');
				$hotel_lp = implode(',',$hotel_lp);
				$Hotel->create();
				$Hotel->hotel_lp = $hotel_lp;
				$Hotel->save_one_data(array('id'=>$hotel_id)) ? $this->success('修改成功！') : $this->error('没有做出任何修改！');
				exit;
			}
			
			//酒店是否存在验证
			if (empty($hotel_id)) $this->error('酒店不存在！');
			$hotel_info = $Hotel->get_one_hotel(array('id'=>$hotel_id),'*');
			if (empty($hotel_info)) $this->error('酒店不存在！');
			$hotel_lp = explode(',',$hotel_info['hotel_lp']);

			foreach ($gift_list AS $key=>$val) {
				if (in_array($val['id'],$hotel_lp)) {
					$gift_list[$key]['checked'] = 'checked="checked"';
				}
			}
			//模板标题
			$title_name = $hotel_info['hotel_name'].'--编辑';
			
			//模板注入内容
			$html = $hotel_info;
			
		} else if ($act == 'delete') {			//删除
			$is_del = $Hotel->del_one_hotel($hotel_id);
			if ($is_del == true) {
				//del_user_hotel
				$this->success('删除成功！');
			} else {
				$this->error('删除失败，请稍后重试！');
			}
			
			exit;
		}

		$html['Hotel_Lp'] = $gift_list;

		parent::global_tpl_view( array(
				'action_name'=>'酒店编辑',
				'title_name' => $title_name
		));
		$html['hotel_type_value']= $this->hotel_type_value;
		$html['hotel_xj_type'] = $this->hotel_xj_type; 
		$this->assign('html',$html);
		$this->display();
	}
	
	
	
	//酒店图片编辑
	public function hotel_img () {
		$hotel_id = $this->_get('hotel_id');
		$Hotel = $this->db['Sphotel'];				//酒店表
		$HotelImg = $this->db['SphotelImg'];	//酒店图片表
		
		//酒店是否存在验证
		if (empty($hotel_id)) $this->error('酒店不存在！');
		$hotel_info = $Hotel->get_one_hotel(array('id'=>$hotel_id),'id,hotel_name');
		if (empty($hotel_info)) $this->error('酒店不存在！');
		
		//获取酒店图片数据
		$photo_list = $HotelImg->get_hotel_images(array('hotel_id'=>$hotel_id),'id,type,url');
		//echo '<pre>';print_R($photo_list);echo '</pre>';
		if (!empty($photo_list)) {
			parent::public_file_dir($photo_list, array('url'), 'images/');		//组合访问地址
			$photo_type_list = regroupKey($photo_list,'type');						//按照图片类似分类
		}

		
		//注入模板
		$html['hotel_id'] = $hotel_info['id'];
		$html['img_type'] = $this->img_type;
		$html['photo_type_list'] = $photo_type_list;
		parent::global_tpl_view( array(
				'action_name'=>'酒店图片',
				'title_name' => $hotel_info['hotel_name'].'--上传图片'
		));
		$this->assign('html',$html);
		$this->display();
	}
	

	
	/**
	 * AJAX处理上传图片
	 */
	public function ajax_photo_upload() {
		header('Content-Type:text/html;charset=utf-8');

		if ($this->isPost()) {
			/* 上传文件目录 */
			$upload_dir = C('UPLOAD_DIR');
			$dir = $upload_dir['web_dir'].$upload_dir['image'];		//图片文件保存地址
			$HotelImg = $this->db['SphotelImg'];		//酒店图片表
	
			/* 执行上传 */
			$file = $_FILES['photo_files'];					//上传的文件
			$hotel_id = $this->_post('hotel_id');				//酒店ID
			$type = $this->_post('type');						//图片类型
	
			/* 参数验证 */
			if (empty($hotel_id) || empty($type)) parent::callback(C('STATUS_DATA_LOST'),'参数错误！');
				
			/* 执行上传 */
			$result = parent::upload_file($file, $dir,5120000);
	
			/* 上传结果处理 */
			if ($result['status'] == true) {
				$HotelImg->hotel_id = $hotel_id;
				$HotelImg->type = $type;
				$HotelImg->url = $result['info'][0]['savename'];
				$hotel_img_id = $HotelImg->add();		//写入数据库
	
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
	 * AJAX删除图片
	 */
	public function ajax_photo_remove () {
		if ($this->isPost()) {
			$id = $this->_post('id');
			$HotelImg = $this->db['SphotelImg'];		//酒店图片表
			$HotelImg->del_one_image($id) ? parent::callback(C('STATUS_SUCCESS'),'删除成功') : parent::callback(C('STATUS_UPDATE_DATA'),'删除失败') ;
		} else {
			parent::callback(C('STATUS_ACCESS'),'非法访问！');
		}
	}
	
	
	//显示酒店，供分配二维码时使用
	public function show_hotel_list () {
		$Hotel = $this->db['Sphotel'];
		
		$html['list'] = $Hotel->field('id,hotel_name,hotel_sf,hotel_cs,hotel_q,hotel_xj,hotel_pf,hotel_syq,hotel_dz,hotel_tel')->where(array('is_del'=>0))->select();
		
		$this->assign('html',$html);
		$this->display();
	}
	
	/*
	 * 微信端酒店地图生成
	 */
	
	public function get_map(){
		if(!$_SERVER["HTTP_REFERER"]){
			$this->error('非法操作！');
			exit;
		}
	   define('MAP_PATH',str_replace('Lib/Action/Admin/HotelAction.class.php','',str_replace('\\','/',__FILE__)));

	   $Hotel = $this->db['Hotel'];
	   $Mapdata = $Hotel->get_hotel_city();  //根据酒店的城市分组找到所有的城市   
	   $Map = array();
	   $Mar = array(0,1,2,3,4,5,6,7,8,9,'a','b','c','d','e','f','g','h','i','j','k'); //标记地图的点
	   foreach ($Mapdata as $key=>$val){
	   	  $Map[$key]['city'] = $val['hotel_cs'];
	   	  $Map[$key]['map']  = $Hotel->get_hotel(array('hotel_cs'=>$val['hotel_cs']),10,'hotel_location_x,hotel_location_y');
	   	  
	   }
	   foreach ($Map as $k1=>$v1){
	   	    $num = count($v1['map']);
	   	    $i = 0; 
	   	    $mar = '';
	   	    $mars = '';
	   	    foreach($v1['map'] as $k2=>$v2){
	   	    	$m = $num-1 > $i ?  '|' : '';
				$arr = array(
				  'markers'=>$v2['hotel_location_x'].','.$v2['hotel_location_y'],
				  'markerStyle'=>'l,'.$Mar[$i],
				  'm' => $m,
				);
				$mar  .= $v2['hotel_location_x'].','.$v2['hotel_location_y'].$m;
				$mars .= 'l,'.$Mar[$i].$m;				
				$Map[$k1]['mar'][$i] = $arr;
				unset($arr);
				$i++;
	   	    }
	   	    $Map[$k1]['center'] =$Map[$k1]['mar'][0]['markers'];
	   	    $Map[$k1]['markers'] =  $mar;
	   	    $Map[$k1]['markerStyle'] =  $mars;
	   	    unset($mar);unset($mars);

	   }
	   foreach ($Map as $k1=>$v1){
	   
		   	$url = 'http://api.map.baidu.com/staticimage?width=360&height=200'."&center=".$v1['center'].'&zoom=11&markers='.$v1['markers'].'&markerStyles='.$v1['markerStyle'];
		   if(file_exists(MAP_PATH.C('UPLOAD_DIR.mapimage').$k1.'.png')){		
				@unlink(MAP_PATH.C('UPLOAD_DIR.mapimage').$k1.'.png');
				echo Dowload_code2($url,MAP_PATH.C('UPLOAD_DIR.mapimage').$k1.'.png').'</br>';
			}else{
				echo Dowload_code2($url,MAP_PATH.C('UPLOAD_DIR.mapimage').$k1.'.png').'</br>';
			}
		    //echo Dowload_code2($url,MAP_PATH.C('UPLOAD_DIR.mapimage').$k1.'.png').'</br>';
	   }
	   //echo '<pre>';print_R($Map);echo '</pre>';
		
	}
	
	
    
}