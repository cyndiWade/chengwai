<?php
/**
 * 语义分析系统
 */
class SiriAction extends AdminBaseAction {
	//控制器说明
	private $module_name = '语义分析';
	
	//初始化数据库连接
	protected  $db = array(
		'Siri'=>'Siri',		//语义表
	);
	

	/**
	 * 构造方法
	 */
	public function __construct() {
	
		parent::__construct();
	
		parent::global_tpl_view(array('module_name'=>$this->module_name));
		
	}
	
	
	
	//语义列表
	public function index () {		
		//连接数据库
		$Siri = $this->db['Siri'];
		
		//所有数据列表
		$type['type'] = array('neq',1);
		$siri_list = $Siri->seek_all_data($type);

		if ($siri_list == true) {
			foreach ($siri_list as $key=>$val) {
				$siri_list[$key]['type'] =  $this->global_system->siri_type[$val['type']]['explain'];
			}
		}


		parent::global_tpl_view( array(
			'action_name'=>'所有语义',
			'title_name'=>'所有语义',
		));
		
		$html['list'] = $siri_list;
		$this->assign('html',$html);
		$this->display();
	}
	
	public function edit () {
		$act = $this->_get('act');						//操作类型
		$Siri = $this->db['Siri'];
		$siri_id = $this->_get('siri_id');
		
		if ($act == 'add') {								//添加
			if ($this->isPost()) {
				$Siri->create();
				$id = $Siri->add();
				$id ? $this->success('添加成功！',U('Admin/Siri/index')) : $this->error('添加失败请重新尝试！');
				exit;
			}
			//表单标题
			$title_name = '添加关键字';
		
		} else if ($act == 'update') {			//修改
			if ($this->isPost()) {
				$Siri->create();
				$Siri->save_one_siri($siri_id) ? $this->success('修改成功！') : $this->error('没有做出任何修改！');
				exit;
			}
			//查找
			$info = $Siri->seek_one_data(array('id'=>$siri_id));

			if (empty($info)) $this->error('您编辑的语义不存在！');
			$title_name = $info['keyword'].'---编辑';
			$html = $info;
		
		} else if ($act == 'delete') {			//删除
			$Siri->del_one_data($siri_id) ? $this->success('删除成功！') : $this->error('删除失败，请稍后重试！');
			exit;
		}
		
		//语音类型
		$html['siri_type'] = $this->global_system->siri_type;
		parent::global_tpl_view( array(
				'action_name'=>'编辑',
				'title_name'=>$title_name,
		));
		$this->assign('html',$html);
		$this->display();
	}

	
	

    
}