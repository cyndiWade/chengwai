<?php
/**
 * 新闻媒体账号
 */
class AccountNewsAction extends AdminBaseAction {
  	
	//控制器说明
	private $module_name = '新闻媒体账号';
	
	//初始化数据库连接
	protected  $db = array(
		'NowAccountObj'=>'AccountNews',
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
		$list = $this->db['NowAccountObj']->get_account_data_list();
		
		if ($list == true ) {
			foreach ($list as $key=>$val) {
				$list[$key]['pg_celebrity_explain'] =  $val['ac_is_celebrity'] == 0 ? '草根账号' : '名人';
				$list[$key]['pg_type_explain'] =  '新闻';
				$list[$key]['pg_fans_num'] =  ($val['ac_fans_num'] / 10000).'万';
				$list[$key]['pa_edit_url'] = U(GROUP_NAME.'/'.MODULE_NAME.'/edit',array('act'=>'edit','id'=>$val['ac_id']));
		
				$list[$key]['pg_status_explain'] = $this->Medie_Account_Status[$val['ac_status']]['explain'];
				
				$list[$key]['pg_pt_money'] = $this->global_finance['news_proportion'] + $val['ac_money'];
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
			
		$info = $this->db['NowAccountObj']->get_account_data_one($id);
		
		if($act == 'recommended') {
			$Help->show_status = $recommended_status;
			$is_up = $this->db['NowAccountObj']->where(array('id'=>$id))->save(array('recommended_status'=>$recommended_status));
			$is_up ? $this->success('修改成功！') : $this->error('修改失败！');
			exit;
		} elseif ($act == 'edit') {
			
			if ($this->isPost()) {
		
				$this->db['NowAccountObj']->create();
				if ($this->_post('area_id_sub') != ''){ 
					$this->db['NowAccountObj']->area_id = $this->_post('area_id_sub');
				}
				
				$this->db['NowAccountObj']->where(array('id'=>$id))->save();

				parent::newsDataprocess($id);	//同步方法
				
				//审核与未审核都要发送站内短信 add by bumtime 20141221
				$status = I("status", 0 ,'intval');
				
				$tipsInfo = C('MESSAGE_TYPE_MEDIA');
				$data['send_from_id']	=	$this->oUser->id;
				$data['send_to_id']		=	$info['ac_users_id'];
				$data['subject']		=	$tipsInfo[1]['subject'];
				$data['content']		=	$status == 1 ? sprintf($tipsInfo[1]['content'], "新闻", U('/Media/SocialAccount/manager/type/4'), $info['ac_account_name']) : sprintf($tipsInfo[2]['content'], "新闻", U('/Media/SocialAccount/manager/type/4'), $info['ac_account_name']);
				$data['message_time']	=	time();
				parent::sendMessageInfo($data);
				
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