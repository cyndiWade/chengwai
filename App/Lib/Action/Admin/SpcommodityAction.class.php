<?php
/**
 * 产品控制器
 */
class SpcommodityAction extends AdminBaseAction {
	//控制器说明
	private $module_name = '产品管理';
	
	//初始化数据库连接
	protected  $db = array(
		'Commodity'=>'Commodity',		//产品表
		'Merchant' => 'Merchant',
		'CommoditySpecial' => 'CommoditySpecial'	//产品特价表
	);
	
	private $Commodity_Status;


	/**
	 * 构造方法
	 */
	public function __construct() {
	
		parent::__construct();
		
		$this->Commodity_Status = C('Commodity_Status');
	
		parent::global_tpl_view(array('module_name'=>$this->module_name));
		
	}
	
	
	
	//数据列表
	public function index () {	
		header("Content-type:text/html;charset=utf-8");	
		$type_id = $this->_get('type');
		$merchant_id = $this->_get('merchant_id');
		switch ($type_id){
			case 1 :
				echo '住';
				$this->redirect('Admin/Sphotel/index', array('cate_id' => $type_id,'merchant_id'=>$merchant_id),0, '页面跳转中...');
				break;
			case 2 :
				echo '研发中。。。。';
				break;
			case 3 :
				echo '研发中。。。。';
				break;
			default:
				break;
		}
		exit;
		//$this->error('研发中');
		//连接数据库
		$Commodity = $this->db['Commodity'];
		$Merchant = $this->db['Merchant'];
		$merchant_id = $this->_get('merchant_id');

		$con_info = $Merchant->seek_one_data(array('id'=>$merchant_id),'id,merchant_type');
		if (empty($con_info)) $this->error('你编辑的数据不存在');
		
		$commodity_list = $Commodity->seek_all_data(array('merchant_id'=>$merchant_id));
		if ($commodity_list == true) {
			
			foreach ($commodity_list as $key=>$val) {
				$commodity_list[$key]['commodity_type'] = $this->global_system->Merchant_Type[$val['commodity_type']]['explain'];
				 
				$val['strategy'] == 0 ? $strategy_explain = '普通策略' : $strategy_explain = '特价策略';
				$commodity_list[$key]['strategy'] = $strategy_explain;
			}
		}


		parent::global_tpl_view( array(
			'action_name'=>'产品首页',
			'title_name'=>'产品',
			'add_name' =>'添加产品',
		));
		
		$html['list'] = $commodity_list;
		$html['merchant_id'] = $merchant_id;
		$this->assign('html',$html);
		$this->display();
	}
	
	
	public function edit () {
		$act = $this->_get('act');						//操作类型
		$this->Commodity_Type = C('Commodity_Type');
		$merchant_id = $this->_get('merchant_id');		//商品店铺ID
		$commodity_id = $this->_get('commodity_id');	//产品ID
		
		$Merchant = $this->db['Merchant'];
		$Commodity = $this->db['Commodity'];
		$CommoditySpecial = $this->db['CommoditySpecial'];
		
		//数据验证是否存在
		$con_info = $Merchant->seek_one_data(array('id'=>$merchant_id),'id,	merchant_type');
		if (empty($con_info)) $this->error('你编辑的数据不存在');

		//对商家以后的产品类型进行编辑
		$tmp_merchant_type = explode(',',$con_info['merchant_type']);
		$Merchant_Type = array();
		foreach ($tmp_merchant_type AS $v) {
			if (!empty($this->global_system->Merchant_Type[$v]['num'])) {
				array_push($Merchant_Type,$this->global_system->Merchant_Type[$v]);
				//$str .= $this->global_system->Merchant_Type[$v]['explain'].',';
			}
		}

		//添加操作		
		if ($act == 'add') {								//添加
			if ($this->isPost()) {
	
				$Commodity->create();
				$Commodity->merchant_id = $merchant_id;
				$commodity_add_id = $Commodity->add_one_data();
				if ($commodity_add_id){
					$strategy = $this->_post('strategy');	//特价类型
				
					if ($strategy == 1) {
		
						$CommoditySpecial->create();
						$CommoditySpecial->commodity_id = $commodity_add_id;
						$CommoditySpecial->add() ? $this->success('添加成功！') : $this->error('添加失败请重新尝试！');
					}
					$this->success('添加成功！');

				} else {
					$this->error('添加失败请重新尝试！');
				}
					
				
				exit;
			}
			//表单标题
			$title_name = '添加产品';
		
		} else if ($act == 'update') {			//修改
			
			//特价政策数据
			$special_info = $CommoditySpecial->get_last_one_data($commodity_id);
			
			if ($this->isPost()) {
				//修改商品数据
				$Commodity->create();
				$Commodity->save_one_Commodity($commodity_id);
				
				//修改特价数据
				$strategy = $this->_post('strategy');	//特价类型
				if ($strategy == 1) {
					//get_last_one_data
					$CommoditySpecial->create();
					$CommoditySpecial->save_one_commodity_special($special_info['id']);
				}
				
				$this->success('修改成功！');
				exit;
			}
			//查找
			$info = $Commodity->seek_one_data(array('id'=>$commodity_id));
			if (empty($info)) $this->error('您编辑的数据不存在！');
			
			
		
			$html = $info;
			$html['special_info'] = $special_info;

			$title_name = $info['name'].'---编辑';
			
		} else if ($act == 'delete') {			//删除
			$Commodity->del_one_data($commodity_id) ? $this->success('删除成功！') : $this->error('删除失败，请稍后重试！');
			exit;
		} else {
			$this->error('非法操作！');
		}
		
		
		$html['Merchant_Type'] = $Merchant_Type;
		parent::global_tpl_view( array(
				'action_name'=>'编辑',
				'title_name'=>$title_name,
		));
		$this->assign('html',$html);
		$this->display();
	}

	
	

    
}