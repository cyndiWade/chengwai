<?php

/**
 * 广告主账号控制器
 */
class MemberAction extends AdvertBaseAction {
	
	
	//每个类都要重写此变量
	protected  $is_check_rbac = true;		//是否需要RBAC登录验证
	
	protected  $not_check_fn = array();	//无需登录和验证rbac的方法名
	
	private $big_type = 5;
	
	//控制器说明
	private $module_explain = '我是广告主';
	
	//和构造方法
	public function __construct() {
		parent::__construct();
		parent::global_tpl_view(array('module_explain'=>$this->module_explain));
		//用户所有的信息都保存在$this->oUser 中，对象方式调用
		$this->_user_id =$this->oUser->id;
		parent::data_to_view(array('account'=>$this->oUser->account));
		
		parent::big_type_urls($this->big_type);		//大分类URL
	}
	
	//初始化数据库连接
	protected  $db = array(
			'CategoryTags'=>'CategoryTags',
			'Users' => 'Users',
			'Verify'=>'Verify',
			'UserAdvertisement' => 'UserAdvertisement',
			'Discss'	=>	'Discss',
			'ExportOrder' => 'ExportOrder'
	);
	
	
	//资料编辑
	public function datum_edit() {
		if($this->isPost())
		{
			$bool = $this->db['UserAdvertisement']->save_account_list($_POST,$this->_user_id);

			if ($bool == true) {
				parent::callback(C('STATUS_SUCCESS'),'','',array('info'=>'提交成功'));
			} else {
				parent::callback(C('STATUS_UPDATE_DATA'),'','',array('info'=>'请重新尝试下'));
			}
		}
		//选中样式
		$this->data_to_view(array('member_sidebar_datumEdit_class'=>'class="on"',));
		$big_list = $this->db['UserAdvertisement']->select_account_list($this->_user_id);
		parent::data_to_view($big_list);
		$this->display();
	}
	
	
	//密码修改
    public function pass_save() {
    	//选中样式
    	$this->data_to_view(array('member_sidebar_passSave_class'=>'class="on"'));
    	if($this->isPost())
    	{
    		$bool = $this->db['Users']->checkUserNew($_POST,$this->oUser->id);
    		switch ($bool) {
    			case '1':
    				parent::callback(C('STATUS_SUCCESS'),'','',array('info'=>'密码修改成功!'));
    			break;
    			case '2':
    				parent::callback(C('STATUS_UPDATE_DATA'),'','',array('info'=>'密码修改失败!'));
    			break;
    			case '3':
    				parent::callback(C('STATUS_UPDATE_DATA'),'','',array('info'=>'原始密码错误!'));
    			break;
    			case '4':
    				parent::callback(C('STATUS_UPDATE_DATA'),'','',array('info'=>'新密码不一致'));
    			break;
    		}
    	}
    	$this->display();
    }
   
    
    //评价list
    public function evaluate () {
    	//选中样式
    	$this->data_to_view(array(
    			'member_sidebar_evaluate_class'=>'class="on"',
    	));
    	$searchname = trim($_REQUEST['searchname']);
    	$pinfen = intval($_REQUEST['pinfen']);
    	$map['users_id'] = $this->oUser->id;
    	if($searchname!='')
    	{
    		$map['name'] = array('like','%'.$searchname.'%');
    	}
    	if($pinfen!='')
    	{
    		$map['pinfen'] = array('eq',$pinfen);
    	}
    	$Discss = $this->db['Discss'];
    	import('ORG.Util.Page');
    	$all      = $Discss->where($map)->count();
    	$c_all      = $Discss->where(array('users_id'=>$this->oUser->id,'pinfen'=>array('eq','1')))->count();
    	$z_all      = $Discss->where(array('users_id'=>$this->oUser->id,'pinfen'=>array('between',array('2','3'))))->count();
    	$h_all      = $Discss->where(array('users_id'=>$this->oUser->id,'pinfen'=>array('gt','3')))->count();
    	$Page       = new Page($all,10);
    	$show       = $Page->show();
    	$list = $Discss->where($map)->limit($Page->firstRow.','.$Page->listRows)->order('id desc')->select();
    	
    	if (!empty($list)) {
    		foreach ($list as $key=>$val) {
    			$list[$key]['show_pinlun'] = msubstr($val['pinlun'],0,15);
    		}
    	}
    	
    	parent::data_to_view(array(
				'page' => $show ,
				'list' => $list,
				'all'=> $all,
				'c_all'=>$c_all,
				'z_all'=>$z_all,
				'h_all'=>$h_all,
				'pinfen'=>$_REQUEST['pinfen'],
				'searchname'=>$_REQUEST['searchname']
		));
    	$this->display();
    }
    

    //删除评论
    public function delpl()
    {
    	$id = $this->_post('id');
    	if($id!='')
    	{
    		$bool = $this->db['Discss']->where(array('id'=>$id))->delete();
    		if($bool)
			{
				parent::callback(C('STATUS_SUCCESS'),'删除评论成功!');
			}else{
				parent::callback(C('STATUS_UPDATE_DATA'),'删除评论失败!');
			}
    	}
    }
    
    
    //ajax接口
    public function ajax_export_order () {
    	
    	if ($this->isPost()) {
    		$post_data = $this->_post();	
    		$status = $this->db['ExportOrder']->add_data($this->oUser->id);
    		
    		parent::callback(C('STATUS_SUCCESS'),'成功');
    	
    		//$post_data
    	}
    }
    
    
    public function export_order () {
    	import('ORG.Util.Page');
    	
    	$where['users_id'] = $this->oUser->id;
    	$count = $this->db['ExportOrder']->get_count($where);
    	
    	$Page    = new Page($count,10);
    	$show     = $Page->show();
    	
    	$list = $this->db['ExportOrder']->get_list($where,$Page->firstRow.','.$Page->listRows);
    	//$list = $Discss->where($map)->limit($Page->firstRow.','.$Page->listRows)->order('id desc')->select();
    	//dump($list);
    	$this->data_to_view(array(
    		'member_sidebar_export_order_class'=>'class="on"',
    		'page' => $show ,
    		'list' => $list,
    	));
    	$this->display();
    }
    
}

?>