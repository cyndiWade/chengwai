<?php
/**
 * 二维码管理
 */
class WxCodeAction extends HotelBaseAction {
	//控制器说明
	private $module_name = '二维码';
	
	//初始化数据库连接
	protected  $db = array(
		'WxCode' => 'WxCode',		//微信二维码
		'Hotel' => 'Hotel'				//酒店列表
	);

	
	/**
	 * 构造方法
	 */
	public function __construct() {
	
		parent::__construct();

		parent::global_tpl_view(array('module_name'=>$this->module_name));
	}

	
	
	//已分配
	public function index () {	
		
		
		//连接数据库表
		$WxCode = $this->db['WxCode'];
		$Hotel = $this->db['Hotel'];
		$hotel_id = $this->_get('hotel_id');
		//验证
		if ($hotel_id == false) parent::close_windows('此酒店不存在！');
		$hotel_info = $Hotel->get_one_hotel(array('id'=>$hotel_id),'id,hotel_name');

		
		if (empty($hotel_info)) {
			parent::close_windows('此酒店不存在！');
		}

		//获取二维码列表
		$code_list =  $WxCode->seek_hotel_codes(array('hotel_id'=>$hotel_id));

		$html = array(
				'list' => $code_list,				//数据列表
		);
		parent::global_tpl_view( array(
				'action_name'=>'已分配',
				'title_name'=>$hotel_info['hotel_name'],
		));
		//echo '<pre>';print_r($html);echo'</pre>';exit;
		$this->assign('html',$html);
		$this->display();
	}
	

	//编辑二维码
	public function edit () {
		$act = $this->_get('act');						//操作类型
		$WxCode = $this->db['WxCode'];
		$code_id = $this->_get('code_id');
		
		switch ($act) {
			case 'update' :
				if ($this->isPost()) {
					$WxCode->create();
					$WxCode->save_one_code($code_id) ? $this->success('修改成功！') : $this->error('没有做出任何修改！');
					exit;
				}
				//查找
				$info = $WxCode->seek_one_data(array('id'=>$code_id));
				//echo '<pre>';print_r($html);echo'</pre>';exit;
				if (empty($info)) $this->error('您编辑的二维码不存在！');
				$title_name = '编辑';
				$html = $info;
				
				break;
				
			default:
				$this->erros('非法操作！');
		}
			
		parent::global_tpl_view( array(
				'action_name'=>'编辑',
				'title_name'=>$title_name,
		));
		$this->assign('html',$html);
		$this->display();
	}
	
	
	

    
}