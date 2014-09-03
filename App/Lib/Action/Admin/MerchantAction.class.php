<?php
/**
 * 商家控制器
 */
class MerchantAction extends AdminBaseAction {
	//控制器说明
	private $module_name = '每日特惠';
	
	//初始化数据库连接
	protected  $db = array(
		'Merchant'=>'Merchant',		//语义表
	);
	
	private $Merchant_Status;


	/**
	 * 构造方法
	 */
	public function __construct() {
	
		parent::__construct();
		
		$this->Merchant_Status = C('Merchant_Status');
	
		parent::global_tpl_view(array('module_name'=>$this->module_name));
		
	}
	
	
	
	//语义列表
	public function index () {		

		//连接数据库
		$Merchant = $this->db['Merchant'];
		
		//所有数据列表
		$list = $Merchant->seek_all_data();

		
		if ($list == true) {
			foreach ($list as $key=>$val) {
				$list[$key]['status'] = $this->Merchant_Status[$val['status']]['explain'];
				$str = '';
				$tmp_merchant = explode(',',$val['merchant_type']);	
				foreach ($tmp_merchant AS $v) {
					if (!empty($this->global_system->Merchant_Type[$v]['explain'])) {
						$str .= $this->global_system->Merchant_Type[$v]['explain'].',';
					}
				}
				$list[$key]['merchant_type'] = $str;
				unset($str);
			}
		}

		parent::global_tpl_view( array(
			'add_name' =>'添加商家',
			'action_name'=>'商家首页',
			'title_name'=>'所有商家',
		));
		
		$html['list'] = $list;
		$this->assign('html',$html);
		$this->display();
	}
	
	
	public function edit () {
		$act = $this->_get('act');						//操作类型
		$Merchant = $this->db['Merchant'];
		$merchant_id = $this->_get('merchant_id');
		$this->Merchant_Type = C('Merchant_Type');
		

		if ($act == 'add') {								//添加
			if ($this->isPost()) {
				//组合商家类型
				$merchant_type = $this->_post('merchant_type');
				$merchant_type = implode(',',$merchant_type);
				$Merchant->create();
				$Merchant->merchant_type = $merchant_type;
				$id = $Merchant->add();
				$id ? $this->success('添加成功！',U('Admin/Merchant/index')) : $this->error('添加失败请重新尝试！');
				exit;
			}
			//表单标题
			$title_name = '添加商铺';
		
		} else if ($act == 'update') {			//修改
			if ($this->isPost()) {
				$merchant_type = $this->_post('merchant_type');
				$merchant_type = implode(',',$merchant_type);
				$Merchant->create();
				$Merchant->merchant_type = $merchant_type;
				$Merchant->save_one_merchant($merchant_id) ? $this->success('修改成功！') : $this->error('没有做出任何修改！');
				exit;
			}
			//查找
			$info = $Merchant->seek_one_data(array('id'=>$merchant_id));

			if (empty($info)) $this->error('您编辑的数据不存在！');
			
			$merchant_type = explode(',',$info['merchant_type']);

			foreach ($this->global_system->Merchant_Type AS $key=>$val) {
				if (in_array($val['num'],$merchant_type)) {
					$this->global_system->Merchant_Type[$key]['checked'] = 'checked="checked"';
				}
			}
			
			$title_name = $info['name'].'---编辑';
			$html = $info;
		
		} else if ($act == 'delete') {			//删除
			$Merchant->del_one_data($merchant_id) ? $this->success('删除成功！') : $this->error('删除失败，请稍后重试！');
			exit;
		} else {
			$this->error('非法操作！');
		}
		
		//语音类型
		$html['Merchant_Type'] = $this->global_system->Merchant_Type;
		$html['Merchant_Status'] = $this->Merchant_Status;

		parent::global_tpl_view( array(
				'action_name'=>'编辑',
				'title_name'=>$title_name,
		));
		$this->assign('html',$html);
		$this->display();
	}

	
	

    
}