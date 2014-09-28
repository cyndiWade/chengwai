<?php
/**
 * 后台订单管理
 */
class OrderAction extends AdminBaseAction {
  	
	//控制器说明
	private $module_name = '订单管理';
	
	//初始化数据库连接
	protected  $db = array(
		//新闻媒体订单
		'GeneralizeNewsOrder'=>'GeneralizeNewsOrder',	
		'GeneralizeNewsAccount'=>'GeneralizeNewsAccount',
		
	);
	
	private $parent_id;

	/**
	 * 构造方法
	 */
	public function __construct() {
	
		parent::__construct();
	
		parent::global_tpl_view(array('module_name'=>$this->module_name));

	}
	
	
	
	//新闻媒体数据列表
	public function news_generalize () {
	
		$GeneralizeNewsOrder = $this->db['GeneralizeNewsOrder'];
		$where['status'] = 1;
		$list = $GeneralizeNewsOrder->get_order_list($where);
	
		$data['list'] = $list;
		parent::global_tpl_view( array(
			'action_name'=>'新闻媒体推广单',
			'title_name'=>'新闻媒体推广单',
			'add_name'=>'添加类别'	
		));
	
		parent::data_to_view($data);
		$this->display();
	}
	
	public function news_generalize_edit() {
		$id = $this->_get('id');
		$act = $this->_get('act');						//操作类型
		if ($act == 'update') {
				
		}
	}
	
	
	
	
	//微博推广单列表
	public function weibo_generalize () {
	
		$GeneralizeNewsOrder = $this->db['GeneralizeNewsOrder'];
		$where['status'] = 1;
		$list = $GeneralizeNewsOrder->get_order_list($where);
	
		$data['list'] = $list;
		parent::global_tpl_view( array(
				'action_name'=>'微博推广单',
				'title_name'=>'微博推广单',
				'add_name'=>'添加类别'
		));
	
		parent::data_to_view($data);
		$this->display();
	}
	
	//微博推广单编辑
	public function weibo_generalize_edit() {
		$id = $this->_get('id');
		$act = $this->_get('act');						//操作类型
		if ($act == 'update') {
	
		}
	}
	
	
	
	
	//微博意向单列表
	public function weibo_intention () {
	
		$GeneralizeNewsOrder = $this->db['GeneralizeNewsOrder'];
		$where['status'] = 1;
		$list = $GeneralizeNewsOrder->get_order_list($where);
	
		$data['list'] = $list;
		parent::global_tpl_view( array(
				'action_name'=>'微博意向单',
				'title_name'=>'微博意向单',
				'add_name'=>'添加类别'
		));
	
		parent::data_to_view($data);
		$this->display();
	}
	
	//微博推广单编辑
	public function weibo_intention_edit() {
		$id = $this->_get('id');
		$act = $this->_get('act');						//操作类型
		if ($act == 'update') {
	
		}
	}
	
	
	

	//微信推广单列表
	public function weixin_generalize () {
	
		$GeneralizeNewsOrder = $this->db['GeneralizeNewsOrder'];
		$where['status'] = 1;
		$list = $GeneralizeNewsOrder->get_order_list($where);
	
		$data['list'] = $list;
		parent::global_tpl_view( array(
				'action_name'=>'微信推广单',
				'title_name'=>'微信推广单',
				'add_name'=>'添加类别'
		));
	
		parent::data_to_view($data);
		$this->display();
	}
	//微信推广单编辑
	public function weixin_generalize_edit() {
		$id = $this->_get('id');
		$act = $this->_get('act');						//操作类型
		if ($act == 'update') {
	
		}
	}
	
	
	
	//微信意向单
	public function weixin_intention () {
		$GeneralizeNewsOrder = $this->db['GeneralizeNewsOrder'];
		$where['status'] = 1;
		$list = $GeneralizeNewsOrder->get_order_list($where);
		
		$data['list'] = $list;
		parent::global_tpl_view( array(
				'action_name'=>'微信意向单',
				'title_name'=>'微信意向单',
				'add_name'=>'添加类别'
		));
		
		parent::data_to_view($data);
		$this->display();
	}
	//微信推广单编辑
	public function weixin_intention_edit() {
		$id = $this->_get('id');
		$act = $this->_get('act');						//操作类型
		if ($act == 'update') {
	
		}
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	public function edit () {
		$act = $this->_get('act');						//操作类型
		$id = $this->_get('id');						//上一页地址
		$show_status = $this->_get('show_status');		//显示状态
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
		} elseif ($act == 'init_data') {
			
			//$data = '不限,男女,泛';
	
			$title_array = explode(',', $data);
			foreach ($title_array as $key=>$val) {
				$CategoryTags->parent_id = $this->parent_id;
				$CategoryTags->title = $val;
				//$CategoryTags->add();
			}
			$this->success('成功');
			exit;
		} elseif ($act == 'cp_data') {
			//$mubiao_id = 666;
			
			//获取当前父级下的数据
			$list = $CategoryTags->get_spe_data(array('parent_id'=>$this->parent_id,'is_del'=>0));
			
			foreach ($list as $key=>$val) {
				$CategoryTags->parent_id = $mubiao_id;
				$CategoryTags->title = $val['title'];
				$CategoryTags->val = $val['val'];
				$CategoryTags->field = $val['field'];
				//$CategoryTags->add();
			}
			$this->success('成功');
			exit;
		//是否显示	
		}elseif($act == 'is_show') {
			$CategoryTags->show_status = $show_status;
			$is_up = $CategoryTags->save_one_data(array('id'=>$id));
			$is_up ? $this->success('修改成功！') : $this->error('修改失败！');
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