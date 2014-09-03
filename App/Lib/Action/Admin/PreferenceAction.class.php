<?php
/**
 * 特惠处理
 */
class PreferenceAction extends AdminBaseAction {
  	
	//控制器说明
	private $module_name = '特惠管理';
	
	//初始化数据库连接
	protected  $db = array(
			'HotelPreference' => 'HotelPreference'		//每日特惠数据库
	);

	/**
	 * 构造方法
	 */
	public function __construct() {
	
		parent::__construct();
	
		parent::global_tpl_view(array('module_name'=>$this->module_name));
	}
	
	
	/**
	 * 酒店每日特惠
	 */
	public function hotel_preference () {
		import('@.Tool.Tool');		//工具类
		header('Content-Type:text/html;charset=utf-8');
		
		$hotel_id = $this->_get('hotel_id');
		$HotelPreference = $this->db['HotelPreference'];
		
		$state = $HotelPreference->add_hotel_preference($hotel_id);
		if ($state == 0) {
			Tool::alertClose('已设置每日特惠！');
		} elseif ($state >0 ) {
			Tool::alertClose('设置成功！');
		} else {
			Tool::alertClose('设置失败！');
		}
		
		
		
		echo $hotel_id;
	}
	
}