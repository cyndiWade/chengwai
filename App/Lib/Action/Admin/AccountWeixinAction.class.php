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
		'GrassrootsWeixin' => 'GrassrootsWeixin', //微信草根索引表
		'CeleprityindexWeixin' => 'CeleprityindexWeixin' //微信名人索引表	
	);
	

	/**
	 * 构造方法
	 */
	public function __construct() {
	
		parent::__construct();
	
		parent::global_tpl_view(array('module_name'=>$this->module_name));
		
		$this->Medie_Account_Status = C('Medie_Account_Status');
	}
	
	
	
	//数据列表
	public function index () {

		import('ORG.Util.Page');
		$count =  $this->db['NowAccountObj']->get_account_count(array('is_del'=>0));	
		$Page  = new Page($count,100);
		$show   = $Page->show();

		$list = $this->db['NowAccountObj']->get_account_data_list($Page->firstRow,$Page->listRows);
		
		if ($list == true ) {
			foreach ($list as $key=>$val) {
				$list[$key]['pg_celebrity_explain'] =  $val['ac_is_celebrity'] == 0 ? '草根账号' : '名人';
				$list[$key]['pg_type_explain'] =  '微信';
				$list[$key]['pg_fans_num'] =  ($val['ac_fans_num'] / 10000).'万';
				$list[$key]['pa_edit_url'] = U(GROUP_NAME.'/'.MODULE_NAME.'/edit',array('act'=>'edit','id'=>$val['ac_id']));
		
				$list[$key]['pg_status_explain'] = $this->Medie_Account_Status[$val['ac_status']]['explain'];
				
				$list[$key]['pg_ac_dtb_money'] = ($this->global_finance['weixin_proportion'] * $val['ac_dtb_money']) + $val['ac_dtb_money'];
			}
			
		}
		
		
		$data['list'] = $list;
		
		$data['page'] = $show ;
		
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
	
		$info = $this->db['NowAccountObj']->get_account_data_one($id);
		
		if($act == 'recommended') {
			$Help->show_status = $recommended_status;
			$is_up = $this->db['NowAccountObj']->where(array('id'=>$id))->save(array('recommended_status'=>$recommended_status));
			$is_up ? $this->success('修改成功！') : $this->error('修改失败！');
			exit;
		} elseif ($act == 'edit') {

			if ($this->isPost()) {
				
				//微信基础表
				$this->db['NowAccountObj']->create();
				if ($this->_post('area_id_sub') != ''){
					$this->db['NowAccountObj']->area_id = $this->_post('area_id_sub');
				}
				$this->db['NowAccountObj']->where(array('id'=>$id))->save();
				
				//微信草根索引表
				if ($this->_post('is_celebrity') == 0) {
					$this->db['GrassrootsWeixin']->create();
					$this->db['GrassrootsWeixin']->where(array('weixin_id'=>$id))->save();
				
				//微信名人索引表
				} else if ($this->_post('is_celebrity') == 1) {
					$this->db['CeleprityindexWeixin']->create();
					$this->db['CeleprityindexWeixin']->where(array('weixin_id'=>$id))->save();
				}
							
				parent::weixinDataprocess($id);	//同步方法
				
				$this->redirect(GROUP_NAME.'/'.MODULE_NAME.'/edit',array('act'=>$act,'id'=>$id));
			}
			
		} else {
			$this->error('非法操作');
			exit;
		}
		
		parent::global_tpl_view( array(
				'action_name'=>'账号详情',
				'title_name'=>'账号详情',
		));
		
		$this->data_to_view($info);
		$this->display();
			
	}
	
	
	
    
}