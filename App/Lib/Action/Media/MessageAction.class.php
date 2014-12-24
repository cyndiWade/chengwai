<?php

/**
 * 站内短信控制器
 */
class MessageAction extends MediaBaseAction {
	
	//每个类都要重写此变量
	protected  $is_check_rbac = true;		//是否需要RBAC登录验证
	
	protected  $not_check_fn = array();	//无需登录和验证rbac的方法名
	
	//控制器说明
	private $module_explain = '站内短信';


	//初始化数据库连接
	protected  $db = array(
			'Message'=>'Message',
			'Users' => 'Users',
	);
	
	//和构造方法
	public function __construct() {
		parent::__construct();
		parent::global_tpl_view(array('module_explain'=>$this->module_explain));
	}
	
	/**
	 * 列表
	*/
	public function index()
	{
		$list = $where = $send_to_id = $userList =  array();
		$count_info = $count_isReaded = $count_isRead = $count = 0;
		$type 		= I('status', 0, 'intval');

        $messageModel = $this->db['Message'];
        
        //条件
/*        if($type >0)
        {
        	$where['readed'] = ($type == 1) ?  1 : 0 ;
        }*/
        
       $fields = array('`messageid`, `send_from_id`, `type`, `subject`, `status`, `message_time`, readed');
        //全部
        $where_all['send_to_id'] = $this->oUser->id;
		$count 	= $messageModel->getCount($where_all);
		
		//已读
		$where_read['send_to_id']	= $this->oUser->id;
		$where_read['readed']  		= 1;
		$count_isReaded = $messageModel->getCount($where_read);
		
		//未读
		$count_isRead   = $count - $count_isReaded;
		
        import('ORG.Util.Page');   
        switch ($type)
        {
        	//全部
        	case 0 :	
        		$where = $where_all;
        		$count_info		= $count;
        		break;
        		
        	//已读
        	case 1:
        		$where= $where_read;
        		$count_info		= $count_isReaded;
        		break;
        		
        	//未读
        	case 2:
        		$where['send_to_id']	= $this->oUser->id;
				$where['readed']  		= 0;
				
        		$count_info		= $count_isRead;
        		break;      		
        }  
        

        $Page       = new Page($count_info,10);
		$show       = $Page->show();
	
        $list = $messageModel->getList($where, $fields, $Page->firstRow, $Page->listRows);

        //关联会员
        /*if ($list) {
            foreach ($list AS $info) {
                $send_to_id[] = $info['send_to_id'];
            }
        }
        if($send_to_id)
        {
	        $whereUser['id'] = array("in", $send_to_id);
	        $userList = $this->db['Users']->where($whereUser)->getField('`id`, `account`, `nickname`') ;
        }
		
        if($list)
        {
        	foreach ($list as $key=>$value)
        	{
        		$list[$key]['send_name'] = $userList[$value['send_from_id']]['account'];
        	}
        }*/
       
		parent::data_to_view(array(
			'status'    => $type,
            'list'      => $list,
            'count'		=> array('count'=>$count, 'count_isRead'=>$count_isRead, 'count_isReaded'=>$count_isReaded)
		));
		$this->display();
	}
	
	/**
	 * 详细页
	*/
	function show()
	{
		$id = I('id', 0, 'intval');
		if($id)
		{
			$info = $this->db['Message']->field('`subject`, `content`')->where(array("messageid"=>$id))->find();
			if($info)
			{
				$where['messageid']	= $id;
				$data['readed']		= 1;
				$data['read_time']	= time();
				$bool = D('Media/Message')->messageSave($where, $data);			
			}
			
		    parent::callback(1, 'success', $info, array('code' => 1000));
		}
		else {
			parent::callback(0, 'fail', '该记录不存在！', array('code' => 1000));
		}
	}
	
	/**
	 * 操作页(删除或标记已读)
	*/
	function operate()
	{
		$temp	= I('temp');
		$id		= I('id', 0, 'intval');
		$type	= I("type", 0, 'intval');
		$bool   = 0;
		if($id || $temp)
		{	
			switch ($type)
			{
				//删除
				case 1:
					$bool = $this->mDelete($temp, $id);
					break;
					
				//标记下	
				case 2: 	
					$bool = $this->mReaded($temp, $id);
					break;
			}
			$bool > 0 ? $this->success("操作成功！") : $this->error("操作失败");;
		}
		else {
			$this->error("该记录不存在");
		}
	}
	
	//删除
	private function mDelete($temp, $id)
	{
		if($temp)
		{
			//检查是否本人
			$where_temp['messageid']  = array("in", $temp);
			$list	= M('message')->where($where_temp)->getField('messageid,send_to_id');
			foreach ($list as $key=>$value) 
			{
				if( $value == $this->oUser->id)
				{
					$arrayList[] = $key;
				}
			}
			if($arrayList)
				$where['messageid'] = array("in", $arrayList);
			
		}
		else {
			$where  = array("messageid"=> $id);
			$info	= M('message')->where($where)->getField('send_to_id');
			//检查是否本人
			if( $info != $this->oUser->id)
			{
				$this->error("操作错误！");
			}
		}
		
		$bool = $this->db['Message']->messageDelete($where);

		return $bool;	
	}
	
	//标为已读
	private function mReaded($temp, $id)
	{
		if($temp)
		{
			$arrayList = explode(',', $temp);
			foreach ($arrayList as $key=>$value) 
			{
				$where['messageid']  		= $value;
				$data['readed']		= 1;
				$data['read_time']	= time();
				$bool = $this->db['Message']->messageSave($where, $data);
			}	 
		}
		else 
		{
			$where['messageid']	= $id;
			$data['readed']		= 1;
			$data['read_time']	= time();
			$bool = $this->db['Message']->messageSave($where, $data);
		}
		
		
		return $bool;	
	}
	
}