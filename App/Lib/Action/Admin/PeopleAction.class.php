<?php
/**
 * 中奖人名单
 */
class PeopleAction extends AdminBaseAction {
	//控制器说明
	private $module_name = '获奖名单';
	
	//初始化数据库连接
	protected  $db = array(
		'People'=>'People',		//订单表
	);
	
	private $people_status;	//领奖状态
	

	/**
	 * 构造方法
	 */
	public function __construct() {
	
		parent::__construct();
	
		parent::global_tpl_view(array('module_name'=>$this->module_name));
		
		$this->people_status = C('People_Status');
		
		import('@.Tool.Tool');		//工具类
	}
	
	

	public function index () {
		//连接数据库
		$People = $this->db['People'];	

		$map['p.status'] = $this->_get('status');
		$list = $People->seek_all_data($map,'p.*,c.title');

		if ($list == true) {
			foreach ($list as $key=>$val) {
				$list[$key]['status_info'] =  $this->people_status[$val['status']]['explain'];
				if ($val['status'] == $this->people_status[0]['num']) {
					$list[$key]['bool'] = true;
				} else {
					$list[$key]['bool'] = false;
				}
			}
		}
		
		parent::global_tpl_view( array(
			'action_name'=>'名单列表',
			'title_name'=>'名单列表',
		));	
		$html['list'] = $list;
		$this->assign('html',$html);
		$this->display();
	}
	
		
	//确认领奖
	public function confirm_receive () {
		import('@.Tool.Tool');		//工具类
		header('Content-Type:text/html;charset=utf-8');
		
		$People = $this->db['People'];	
		$people_id = $this->_get('people_id');


		if (empty($people_id)) $this->error('非法操作！');
		
		$state = $People->where(array('id'=>$people_id))->data(array('status'=>1))->save();
		if ($state == 0) {
			$this->success('已设置过了！');
		} elseif ($state >0 ) {
			$this->success('设置成功！');
		} else {
			$this->success('设置失败！');
		}

		//if (empty($people_id)) Tool::alertClose('非法操作！');
		
		//$state = $People->where(array('id'=>$people_id))->data(array('status'=>1))->save();
		//if ($state == 0) {
		//	Tool::alertClose('已设置成功！');
		//} elseif ($state >0 ) {
		//	Tool::alertClose('设置成功！');
		//} else {
		//	Tool::alertClose('设置失败！');
		//}
	
	}
    
}