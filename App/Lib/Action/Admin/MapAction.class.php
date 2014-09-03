<?php
/**
 * 地图管理
 */
class MapAction extends AdminBaseAction {
  	
	//初始化数据库连接
	protected  $db = array(
			'Hotel'=>'Hotel',
	);
	
	//控制器说明
	private $module_name = '地图管理';
	

	/**
	 * 构造方法
	 */
	public function __construct() {
	
		parent::__construct();
	
		parent::global_tpl_view(array('module_name'=>$this->module_name));
	}
	
	

	/**
	 * 编辑酒店
	 */
	public function hotel_map() {
		$Hotel = $this->db['Hotel'];				//酒店表
		$hotel_id = $this->_get('hotel_id');		//酒店ID
		
		//酒店是否存在验证
		if (empty($hotel_id)) $this->error('酒店不存在！');
		$hotel_info = $Hotel->get_one_hotel(array('id'=>$hotel_id),'*');
		if (empty($hotel_info)) $this->error('酒店不存在！');

	
		parent::global_tpl_view( array(
				'action_name'=>'酒店地图',
				'title_name' => $title_name
		));
		
		$html['hotel_xj_type'] = $this->hotel_xj_type; 
		$this->assign('html',$html);
		$this->display();
	}
	
	

    
}