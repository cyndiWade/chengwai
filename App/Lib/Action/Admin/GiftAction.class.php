<?php
/**
 * 语义分析系统
 */
class GiftAction extends AdminBaseAction {
	//控制器说明
	private $module_name = '礼包管理';
	
	//初始化数据库连接
	protected  $db = array(
		'Gift'=>'Gift',		//礼包表
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
		$Gift = $this->db['Gift'];
		
		//所有数据列表
		
		$gift_list = $Gift->seek_all_data();

		

		parent::global_tpl_view( array(
			'add_name' =>'添加礼包',
			'action_name'=>'',
			'title_name'=>'所有礼包',
		));
		
		$html['list'] = $gift_list;
		$this->assign('html',$html);
		$this->display();
	}
	
	public function edit () {
		$act = $this->_get('act');						//操作类型
		$Gift = $this->db['Gift'];
		$gift_id = $this->_get('gift_id');
		
		if ($act == 'add') {								//添加
			if ($this->isPost()) {
				$Gift->create();
				$id = $Gift->add();
				$id ? $this->success('添加成功！',U('Admin/Gift/index')) : $this->error('添加失败请重新尝试！');
				exit;
			}
			//表单标题
			$title_name = '添加礼包';
		
		} else if ($act == 'update') {			//修改
			if ($this->isPost()) {
				$Gift->create();
				$Gift->save_one_data($gift_id) ? $this->success('修改成功！') : $this->error('没有做出任何修改！');
				exit;
			}
			//查找
			$info = $Gift->seek_one_data(array('id'=>$gift_id));

			if (empty($info)) $this->error('您编辑的礼包不存在！');
			$title_name = $info['keyword'].'---编辑';
			$html = $info;
			
		
		} else if ($act == 'delete') {			//删除
			$Gift->del_one_data($gift_id) ? $this->success('删除成功！') : $this->error('删除失败，请稍后重试！');
			exit;
		}
		
		
		parent::global_tpl_view( array(
				'action_name'=>'编辑',
				'title_name'=>$title_name,
		));
		$this->assign('html',$html);
		$this->display();
	}

	
	

    
}