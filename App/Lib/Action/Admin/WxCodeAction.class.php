<?php
/**
 * 二维码管理
 */
class WxCodeAction extends AdminBaseAction {
	//控制器说明
	private $module_name = '二维码管理';
	
	//初始化数据库连接
	protected  $db = array(
		'WxCode' => 'WxCode',	//微信二维码
		'UsersHotel' => 'UsersHotel'		//酒店用户关系表
	);

	
	/**
	 * 构造方法
	 */
	public function __construct() {
	
		parent::__construct();

		parent::global_tpl_view(array('module_name'=>$this->module_name));
	}


	//已分配
	public function manage () {		

		import('@.ORG.Util.Page');
		
		//连接数据库表
		$WxCode = $this->db['WxCode'];
		
		//已分配酒店的二维码
		$condition = 'wc.hotel_id<>0'; 

		$search_data = $this->_POST('search_data');
		if(!empty($search_data)){
			$condition = $condition." and (wc.code_id='".$search_data."' or h.hotel_name like '%".$search_data."%')";
		}
		
		
		$result =  $WxCode->seek_hotel_codes($condition,'wc.id,wc.code_id,wc.code_url,wc.yuangong,wc.yuangong,wc.hotel_remarks,h.hotel_name');
		//<if condition="($vo['prepay'] neq 0)">
		//分页
		$Page = $result['obj'];
		$orderList = $result['data'];
		
		//设置分页样式
		$Page->setConfig('prev','<span class="pageAnthor">上一页</span>');
		$Page->setConfig('next','<span class="pageAnthor">下一页</span>');
		$Page->setConfig('first','<span class="pageAnthor" title="第一页">...</span>');
		$Page->setConfig('last','<span class="pageAnthor" title="最后一页">...</span>');
		$Page->setConfig('theme','%first% %upPage%　%linkPage% %downPage%　%end%　%nowPage%/%totalPage%页');

		parent::global_tpl_view( array(
				'action_name'=>'已分配',
				'title_name'=>'选择后重新分配',
		));
		
		$html['list'] = $orderList;					//数据列表
		$html['page'] = $Page->show();		//分页数据
		$this->assign('html',$html);
		$this->display();
	}
	
	

	//管理二维码
	public function allocation () {
		import('@.ORG.Util.Page');
		
		//二维码列表
		$WxCode = $this->db['WxCode'];

		$map['hotel_id'] = 0;
		$map['is_del'] = 0;
		
		//计算结果记录条数
		$code_count = $WxCode->where($map)->count();
		$Page = new Page($code_count,10);	//分页
		
		//计算查询结果集
		$orderList = $WxCode->where($map)->limit($Page->firstRow.','.$Page->listRows)->select();

		//设置分页样式
		$Page->setConfig('prev','<span class="pageAnthor">上一页</span>');
		$Page->setConfig('next','<span class="pageAnthor">下一页</span>');
		$Page->setConfig('first','<span class="pageAnthor" title="第一页">...</span>');
		$Page->setConfig('last','<span class="pageAnthor" title="最后一页">...</span>');
		$Page->setConfig('theme','%first% %upPage%　%linkPage% %downPage%　%end%　%nowPage%/%totalPage%页');
		
		$html['list'] = $orderList;
		$html['page'] = $Page->show();
		
		parent::global_tpl_view( array(
				'action_name'=>'待分配',
				'title_name'=>'选择后分配',
		));
		
		$this->assign('html',$html);
		$this->display();
	}
	
	
	
	//编辑二维码
	public function edit () {
		$act = $this->_get('act');						//操作类型
		$WxCode = $this->db['WxCode'];
		$code_id = $this->_get('code_id');

		
		if ($act == 'update') {			//修改
			if ($this->isPost()) {
				$WxCode->create();
				$WxCode->save_one_code($code_id) ? $this->success('修改成功！') : $this->error('没有做出任何修改！');
				exit;
			}
			//查找
			$info = $WxCode->seek_one_data(array('id'=>$code_id));
	
			if (empty($info)) $this->error('您编辑的二维码不存在！');
			$title_name = '编辑';
			$html = $info;
	
		} else if ($act == 'delete') {			//删除
			$WxCode->del_one_data($code_id) ? $this->success('删除成功！') : $this->error('删除失败，请稍后重试！');
			exit;
		}
	
		parent::global_tpl_view( array(
				'action_name'=>'编辑',
				'title_name'=>$title_name,
		));
		$this->assign('html',$html);
		$this->display();
	}
	
	
	
	//ajax设置二维码归属的酒店
	public function AJAX_set_code_from_hotel () {
		if ($this->isPost()) {
			$code_ids = $this->_post('code_ids');
			$hotel_id = $this->_post('hotel_id');
			
			if (empty($code_ids) || empty($hotel_id)) parent::callback(C('STATUS_DATA_LOST'),'上传数据丢失！');
			
			$WxCode = $this->db['WxCode'];
			$UsersHotel = $this->db['UsersHotel'];
			
			$code_ids = explode(',',$code_ids);
			
			$user_id = $UsersHotel->get_hotel_userid($hotel_id);
			
			if (is_array($code_ids)) {
				$is_save_ok = false;
				foreach ($code_ids as $val) {
					$WxCode->hotel_id = $hotel_id;
					$WxCode->user_id = $user_id;
					if ($WxCode->where(array('id'=>$val))->save()) $is_save_ok = true;
				}
				if ($is_save_ok == true) {
					parent::callback(C('STATUS_SUCCESS'),'分配成功！');
				} else {
					parent::callback(C('STATUS_UPDATE_DATA'),'没有数据被修改！');
				}
			} else {
				parent::callback(C('STATUS_DATA_LOST'),'上传数据丢失！');
			}
			
			
		} else {
			parent::callback(C('STATUS_ACCESS'),'非法访问！');
		}
	}
	

    
}