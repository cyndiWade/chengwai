<?php
/**
 * 标签类别管理
 */
class CategoryTagsAction extends AdminBaseAction {
  	
	//控制器说明
	private $module_name = '类别标签管理';
	
	//初始化数据库连接
	protected  $db = array(
		'CategoryTags'=>'CategoryTags',
	);
	
	private $parent_id;

	/**
	 * 构造方法
	 */
	public function __construct() {
	
		parent::__construct();
	
		parent::global_tpl_view(array('module_name'=>$this->module_name));
		
		$this->parent_id = $this->_get('parent_id');
		empty($this->parent_id) ? $this->parent_id = 0 : $this->parent_id;
	}
	
	
	
	//数据列表
	public function index () {
	
		//连接数据库
		$CategoryTags= $this->db['CategoryTags'];	

		$data['list']  = $CategoryTags->get_spe_data(array('parent_id'=>$this->parent_id,'is_del'=>0));
		
		parent::global_tpl_view( array(
			'action_name'=>'数据列表',
			'title_name'=>'数据列表',
			'add_name'=>'添加类别'	
		));
		$data['parent_id'] = $this->parent_id;
		
		parent::data_to_view($data);
		$this->display();
	}
	
	
	public function edit () {
		$act = $this->_get('act');						//操作类型
		$id = $this->_get('id');						//上一页地址
		$prve_url = $this->_post('prev_url');			//上一页地址
		$CategoryTags= $this->db['CategoryTags'];		//标签类型表
		
		
		if ($act == 'add') {
			if ($this->isPost()) {
				$CategoryTags->create();
				$CategoryTags->parent_id = $this->parent_id;
				$CategoryTags->add() ? $this->success('保存成功！',$prve_url) : $this->error('保存失败请稍后重新尝试！');
				exit;
			}
			$title_name = '添加标签';
		} elseif ($act == 'update') {
			if ($this->isPost()) {
				$CategoryTags->create();
				$CategoryTags->save_one_data(array('id'=>$id)) ? $this->success('修改成功！',$prve_url) : $this->error('修改失败请稍后重新尝试！');
				exit;
			}
			$data = $CategoryTags->get_one_data(array('id'=>$id,'is_del'=>0));
			$title_name = $data['title'].'---编辑';
			parent::data_to_view($data);
			
		} elseif ($act == 'delete') {
			$CategoryTags->delete_data(array('id'=>$id)) ? $this->success('删除成功') : $this->error('删除错误');
			exit;
		} else {
			$this->error('非法操作');
			exit;
		}
			
		parent::global_tpl_view( array(
				'action_name'=>'编辑',
				'title_name' => $title_name
		));
		
		
		$this->display();
	}
	
	
	
    
}