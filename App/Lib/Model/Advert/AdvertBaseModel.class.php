<?php 

/**
 * 后台管理模型类
 */

class AdvertBaseModel extends AppBaseModel {
	
	

	public function __construct() {
		parent::__construct();
		
	}
	
	
	
	protected function send_mall($telephone,$msg)
	{
		//短信属性
		$system_sms = D('SystemSms')->where(array('id'=>C('WEB_SYSTEM.sms_id')))->find();
		if ($system_sms == true) {
			$shp_type = $system_sms['sms_type']; 
			$shp_name = $system_sms['sms_account'];
			$shp_password = $system_sms['sms_pass'];
		} else {
			$shp_type = C('SHP.TYPE');
			$shp_name = C('SHP.NAME');
			$shp_password = C('SHP.PWD');		 
		}


		switch ($shp_type) {
			case 'SHP' :
				import("@.Tool.SHP");				//SHP短信发送类
				$SHP = new SHP($shp_name,$shp_password);			//账号信息
				$send = $SHP->send($telephone,$msg);		//执行发送
				break;
			case 'RD_SHP'	 :
				import("@.Tool.RD_SHP");		//RD_SHP短信发送类
				$SHP = new RD_SHP($shp_name,$shp_password);			//账号信息
				$send = $SHP->send($telephone,$msg);		//执行发送
				break;
			default:
				exit('illegal operation！');	
		}
		return $send;
	}
	
	
	
}
?>