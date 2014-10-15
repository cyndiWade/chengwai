<?php
/**
 * 微信媒体账号
 */
class AccountWeixinAction extends AdminBaseAction {
  	
	//控制器说明
	private $module_name = '微信媒体账号';
	
	//初始化数据库连接
	protected  $db = array(
		'NowAccountObj'=>'AccountWeixin',
	);
	

	/**
	 * 构造方法
	 */
	public function __construct() {
	
		parent::__construct();
	
		parent::global_tpl_view(array('module_name'=>$this->module_name));
		
	}
	
	
	
	//数据列表
	public function index () {
		
		$list = $this->db['NowAccountObj']->where(array('is_del'=>0))->select();
			
		if ($list == true ) {
			foreach ($list as $key=>$val) {
				$list[$key]['celebrity_explain'] =  $val['is_celebrity'] == 0 ? '草根账号' : '名人';
			}
		}
		
		$data['list'] = $list;
		
		parent::global_tpl_view( array(
			'action_name'=>'数据列表',
			'title_name'=>'数据列表',
		));
	
		parent::data_to_view($data);
		$this->display();
	}
	
	
	public function edit () {
		$act = $this->_get('act');						//操作类型
		$id = $this->_get('id');						//上一页地址
		$recommended_status = $this->_get('recommended_status');		//推荐状态
	
		if($act == 'recommended') {
			$Help->show_status = $show_status;
			$is_up = $this->db['NowAccountObj']->where(array('id'=>$id))->save(array('recommended_status'=>$recommended_status));
			$is_up ? $this->success('修改成功！') : $this->error('修改失败！');
			exit;
		} else {
			$this->error('非法操作');
			exit;
		}
			
	}
	
	
	
    
}