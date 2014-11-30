<?php
	
	//广告位
	function adGet($id)
	{
		switch($id)
		{
			case 1:
				return '单图文';
			break;
			case 2:
				return '多图文第一条';
			break;
			case 3:
				return '多图文第二条';
			break;
			case 4:
				return '多图文第3-N条';
			break;
		}
	}

	//平台类型
	function getPt($id)
	{
		switch($id)
		{
			case 1:
				return '新浪';
			break;
			case 2:
				return '腾讯';
			break;
			case 3:
				return '微信';
			break;
		}
	}

	//发送类型
	function getFsT($id)
	{
		switch($id)
		{
			case 1:
				return '直发';
			break;
			case 2:
				return '转发';
			break;
			case 3:
				return '带号';
			break;
			case 4:
				return '分享';
			break;
		}
	}

	//订单状态
	function getHdT($id)
	{
		switch($id)
		{
			case 0:
				return '确认中';
			break;
			case 1:
				return '已确认待执行';
			break;
			case 2:
				return '待支付';
			break;
			case 3:
				return '拒绝';
			break;
			case 4:
				return '已支付待执行';
			break;
		}
	}
	
	//订单账号订单状态
	function getAccountStatus($id)
	{  
		switch($id)
		{
			case 0:
				return '待确认';
			break;
			case 1:
				return '审核通过';
			break;
			case 2:
				return '审核失败';
			break;
			case 3:
				return '支付成功';
			break;
			case 4:
				return '媒体主拒绝';
			break;
			case 5:
				return '执行中';
			break;
			case 6:
				return '执行完成';
			break;
			case 7:
				return '确认完成';
			break;
			case 8:
				return '已流单';
			break;
			case 9:
				return '已取消';
			break;			
			case 10:
				return '不合格';
			break;			
			
		}
	}

	//软硬广
	function getRyG($id)
	{
		switch($id)
		{
			case 1:
				return '硬广';
			break;
			case 2:
				return '软广';
			break;
		}
	}
	
	//转发语类型
	function  getZfyType($id)
	{
		switch($id)
		{
			case 1:
				return '指定转发语';
			break;
			case 2:
				return '博主自拟转发语';
			break;
			
			case 3:
				return '无转发语';
			break;
		}
	}
	
	//直发内容类型
	function  getZfnrType($id)
	{
		switch($id)
		{
			case 1:
				return '指定直发内容';
			break;
			case 2:
				return '博主自拟直发内容';
			break;
		}
	}
	
	//意向订单状态
	function getIntentionAccountStatus($id)
	{  
		$status =  C('Account_Order_Status');
		switch($id)
		{
			case 0:
				return $status[0]['explain_yxd'];
			break;
			case 1:
				return   $status[1]['explain_yxd'];
			break;
			case 2:
				return   $status[2]['explain_yxd'];
			break;
			case 3:
				return   $status[3]['explain_yxd'];
			break;
		}
	}

	//评论来源
	function  getPltype($id)
	{
		switch($id)
		{
			case 1:
				return '新闻平台';
			break;
			case 2:
				return '微信平台';
			break;
			case 3:
				return '微博平台';
			break;
		}
	}

	//封面显示正文 延期 短信通知
	function getFenm($id)
	{
		switch ($id) {
			case 0:
				return '否';
			break;
			case 1:
				return '是';
			break;
		}
	}

	//转发语类型
	function getZhuanf($id)
	{
		switch ($id) {
			case 1:
				return '指定转发语';
			break;
			case 2:
				return '博主自拟转发语';
			break;
			default:
				return '无转发语';
			break;
		}
	}

	//文案是否包含链接
	function wenAn($id)
	{
		switch ($id) {
			case 1:
				return '包含';
			break;
			default:
				return '不包含';
			break;
		}
	}

	//直发内容类型
	function zfNeir($id)
	{
		switch ($id) {
			case 1:
				return '指定直发内容';
			break;
			case 2:
				return '博主自拟直发内容';
			break;
			default:
				return '无';
			break;
		}
	}

	//record状态
	function recordStatus($id)
	{
		switch ($id) {
			case 0:
				return '操作失败';
			break;
			case 1:
				return '操作成功';
			break;
		}
	}

	//record 分类
	function recordType($id)
	{
		switch ($id) {
			case 1:
				return '充值';
			break;
			case 2:
				return '退款';
			break;
			case 3:
				return '消费';
			break;
			case 4:
				return '冻结';
			break;
			case 4:
				return '收入';
			break;
		}
	}

	//支付宝账户 系统账户
	function recordAdmin($id)
	{
		switch($id)
		{
			case 1:
				return '支付宝';
			break;
			case 2:
				return '系统账户';
			break;
			default:
				return '';
			break;
		}
	}

	//链接
	function recordUrl($type,$id)
	{
		switch($type)
		{
			case 1:
				return '<a href="'. U('Advert/News/generalize_detail',array('order_id'=>$id)) .'">新闻订单</a>';
			break;
			case 2:
				return '<a href="'. U('Advert/WeixinOrder/generalize_detail',array('order_id'=>$id)) .'">微信订单</a>';
			break;
			case 3:
				return '<a href="'. U('Advert/WeiboOrder/generalize_detail',array('order_id'=>$id)) .'">微博订单</a>';
			break;
			default:
				return '其他';
			break;
		}
	}
?>