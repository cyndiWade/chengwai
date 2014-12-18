<?php
/**
 * 微博媒体账号
 */
class AccountWeiboAction extends AdminBaseAction {
  	
	//控制器说明
	private $module_name = '微博媒体账号';
	
	//初始化数据库连接
	protected  $db = array(
		'NowAccountObj'=>'AccountWeibo',
		'CategoryTags'=>'CategoryTags',
		'CeleprityindexWeibo'=>'CeleprityindexWeibo',
		'GrassrootsWeibo'=>'GrassrootsWeibo'
	);
	
	private $type_explain = array(
		1 => '新浪',
		2 => '腾讯'
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
				$list[$key]['pg_type_explain'] =  $this->type_explain[$val['ac_pt_type']];
				$list[$key]['pg_fans_num'] =  ($val['ac_fans_num'] / 10000).'万';
				$list[$key]['pa_edit_url'] = U(GROUP_NAME.'/'.MODULE_NAME.'/edit',array('act'=>'edit','id'=>$val['ac_id']));
				
				$list[$key]['pg_status_explain'] = $this->Medie_Account_Status[$val['ac_status']]['explain'];
				
				$list[$key]['pg_ac_yg_zhuanfa'] = ($this->global_finance['weibo_proportion'] * $val['ac_yg_zhuanfa']) + $val['ac_yg_zhuanfa'];
			}
		}
		
		//dump($list);
		
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

		$Big_Nav_Class_Ids = C('Big_Nav_Class_Ids');

		$CategoryTags = $this->db['CategoryTags'];

		if($info['ac_is_celebrity']==1)
		{
			$id_info = $this->db['CeleprityindexWeibo']->where(array('weibo_id'=>array('eq',$info['ac_id'])))->find();
			if($id_info!='')
			{
				$info['id_info'] = $id_info;
			}
			$info['mrzy'] = $CategoryTags->get_classify_data($Big_Nav_Class_Ids['celebrity_tags_ids']['mrzy'])[$Big_Nav_Class_Ids['celebrity_tags_ids']['mrzy']];
			$info['mtly'] = $CategoryTags->get_classify_data($Big_Nav_Class_Ids['celebrity_tags_ids']['mtly'])[$Big_Nav_Class_Ids['celebrity_tags_ids']['mtly']];
			$info['dfmr_mt'] = $CategoryTags->get_classify_data($Big_Nav_Class_Ids['celebrity_tags_ids']['dfmr_mt'])[$Big_Nav_Class_Ids['celebrity_tags_ids']['dfmr_mt']];
			$info['phd'] = $CategoryTags->get_classify_data($Big_Nav_Class_Ids['celebrity_tags_ids']['phd'])[$Big_Nav_Class_Ids['celebrity_tags_ids']['phd']];
			$info['zhyc'] = $CategoryTags->get_classify_data($Big_Nav_Class_Ids['celebrity_tags_ids']['zhyc'])[$Big_Nav_Class_Ids['celebrity_tags_ids']['zhyc']];
		}else{
			$id_info = $this->db['GrassrootsWeibo']->where(array('weibo_id'=>array('eq',$info['ac_id'])))->find();
			if($id_info!='')
			{
				$info['id_info'] = $id_info;
			}
			$info['cjfl'] = $CategoryTags->get_classify_data($Big_Nav_Class_Ids['caogen_tags_ids']['cjfl'])[$Big_Nav_Class_Ids['caogen_tags_ids']['cjfl']];
			$info['dfmr_mt'] = $CategoryTags->get_classify_data($Big_Nav_Class_Ids['caogen_tags_ids']['dfmr_mt'])[$Big_Nav_Class_Ids['caogen_tags_ids']['dfmr_mt']];
			$info['fans_sex'] = $CategoryTags->get_classify_data($Big_Nav_Class_Ids['caogen_tags_ids']['fans_sex'])[$Big_Nav_Class_Ids['caogen_tags_ids']['fans_sex']];
		}

		if($act == 'recommended') {
			$Help->show_status = $show_status;
			$is_up = $this->db['NowAccountObj']->where(array('id'=>$id))->save(array('recommended_status'=>$recommended_status));
			$is_up ? $this->success('修改成功！') : $this->error('修改失败！');
			exit;
		} elseif ($act == 'edit') {
			
			if ($this->isPost()) {
				$this->db['NowAccountObj']->create();
				$this->db['NowAccountObj']->where(array('id'=>$id))->save();

				$CeleprityindexWeibo = $this->db['CeleprityindexWeibo'];

				if($CeleprityindexWeibo->where(array('weibo_id'=>array('eq',$id)))->find())
				{
					$CeleprityindexWeibo->create();
					$CeleprityindexWeibo->where(array('weibo_id'=>array('eq',$id)))->save();

				}else{
					$CeleprityindexWeibo->create();
					$CeleprityindexWeibo->where(array('weibo_id'=>array('eq',$id)))->add();
				}

				$GrassrootsWeibo = $this->db['GrassrootsWeibo'];

				if($GrassrootsWeibo->where(array('weibo_id'=>array('eq',$id)))->find())
				{
					$GrassrootsWeibo->create();
					$GrassrootsWeibo->where(array('weibo_id'=>array('eq',$id)))->save();

				}else{
					$GrassrootsWeibo->create();
					$GrassrootsWeibo->where(array('weibo_id'=>array('eq',$id)))->add();
				}
				
				//parent::weiboDataprocess($id);	//同步方法
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