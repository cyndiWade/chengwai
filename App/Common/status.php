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
	
	//订单状态
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
	
?>