<?php
/**
 *	站内短消息管理
 *
 */
class MessageAction extends AdminBaseAction {
   
	private $module_name = '站内短消息管理';
	
	protected  $db = array(
		'Message' => 'Message'	
	);
	
	
	private $type;
	/**
	 * 构造方法
	 */
	public function __construct() {
		parent::__construct();
		
		parent::global_tpl_view(array('module_name'=>$this->module_name));
		
	}
	
	//列表	
	public function index () 
	{
		$messageModel = D('Media/Message');
		
		import('ORG.Util.Page');
		$count =  $messageModel->getCount();
		$Page  = new Page($count,100);
		$show   = $Page->show();
		
		$where = $userIDList =  array();
		$fields = "`messageid`, `send_from_id`, `send_to_id`, `type`, `subject`, `readed`, `read_time`, `status`, `message_time`";
		$messagelist = $messageModel->getList($where, $fields ,$Page->firstRow,$Page->listRows);
		
		foreach ($messagelist AS $key=>$val) 
		{	
			$userIDList[] = $val['send_from_id'];
			$userIDList[] = $val['send_to_id'];
		}
		if($userIDList)
		{
			$userWhere['id'] = array("in", array_unique($userIDList));
			$userList = M('users')->where($userWhere)->getField('id,account');
		}
		
		foreach ($messagelist AS $key=>$val) 
		{	
			$messagelist[$key]['send_from_name']	= $userList[$val['send_from_id']];
			$messagelist[$key]['send_to_name']		= $userList[$val['send_to_id']];
			$messagelist[$key]['read_time']			=  $val['read_time'] > 0 ? date("Y-m-d H:i:s", $val['read_time']) : 0;
			$messagelist[$key]['message_time']		=   date("Y-m-d H:i:s", $val['message_time']);
		}
		
		parent::global_tpl_view( array(
				'action_name'=>'账号管理',
				'title_name'=>'账号列表',
				'add_name' => '添加账号'
		));
		$this->assign('user_list', $messagelist);
		
		$data['page'] = $show ;
		parent::data_to_view($data);

		$this->display($page_name);
	}
	
	//详情	
	public function show () 
	{
		$id = I('id', 0, "intval");
		
		$messageInfo = D('Media/Message')->getInfo(array("messageid"=>$id ));
		if($messageInfo[0])
		{
			$messageInfo = $messageInfo[0];
			$userWhere['id'] = array("in", array($messageInfo['send_from_id'], $messageInfo['send_to_id']));
			$userList = M('users')->where($userWhere)->getField('id,account');
			
			$messageInfo['send_from_name']	= $userList[$messageInfo['send_from_id']];
			$messageInfo['send_to_name']	= $userList[$messageInfo['send_to_id']];
		}
	 
		parent::global_tpl_view(array(
				'action_name'=>'短消息详情',
				'title_name'=>"短消息详情",
				'add_name' => '短消息详情',
		));	

		$this->data_to_view($messageInfo);
	
		$this->display($page_name);
	}
	
	//删除
	public function delete () 
	{
		$id = I('id', 0, "intval");
		
		$messageInfo = D('Media/Message')->getInfo(array("messageid"=>$id ));
		if($messageInfo[0])
		{
			$where['messageid'] = $id; 
			$bool = D('Media/Message')->messageDelete($where);
			$bool ? $this->success('删除成功') : $this->error('删除失败');
		}
		else 
		{
			$this->error('删除失败');
		}
	}
	
	//发送	
	public function add() 
	{
		//提交
		if(IS_POST)
		{
			$subject = I('subject');
			$content = I('content');
			$send_id = I('user_id', 0, 'intval');

			if($subject && $content && $send_id)
			{
				$userInfo =  M('users')->where(array("id"=>$send_id))->getField('`account`');
				if($userInfo)
				{
					$data['send_from_id']	=	$this->oUser->id;
					$data['send_to_id']		=	$send_id;
					$data['subject']		=	$subject;
					$data['content']		=	$content;
					$data['message_time']	=	time();
					$bool = D('Media/Message')->messageAdd($data);
					
					$bool >0  ? $this->success("提交成功") : $this->error("提交失败");
				}
				else {
					$this->error("该会员不存在，请重新输入");
				}
			}
			else {
				$this->error("标题或内容或录入的会员不存在");
			}

		}
		
		parent::global_tpl_view(array(
				'action_name'=>'发送短消息',
				'title_name'=>"发送短消息",
				'add_name' => '短消息详情',
		));	

		$this->display($page_name);
	}
	
	 
	
	//取会员信息
	public function getUser()
	{
		$account = I('account');
		if($account)
		{
			$where['_string'] = "id ='".$account."' or account = '".$account."' or nickname= '".$account."'";
			$userInfo = M('users')->where($where)->field('`account`, `nickname`, `type`, id')->find();
			$userInfo['type_name'] = $userInfo['type'] == 1 ? "媒体主" : "广告主";
			parent::callback(1,"成功", $userInfo);
		}
		else 
		{
			parent::callback(0,"失败");
		}
	}
}