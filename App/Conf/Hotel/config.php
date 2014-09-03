<?php
if (!defined('THINK_PATH'))exit();

return array(
		
		'DEFAULT_GROUP'         => 'Hotel',  // 默认分组
		'DEFAULT_MODULE'        => 'Login', // 默认模块名称
		'DEFAULT_ACTION'        => 'login', // 默认操作名称


		/* 后台不需要验证的模块 */
		'USER_AUTH_ON' => false,						//是否开启用户权限验证
		'RBAC_NODE_TABLE' => 'node',				//节点表（系统所有资源）
		'RBAC_GROUP_TABLE' => 'group',			//组表
		'RBAC_GROUP_NODE_TABLE'	=>'group_node',	//组与节点关系表
		'RBAC_GROUP_USER_TABLE'	=>'group_user',	//组与用户关系表
		
		'ADMIN_AUTH_KEY' => 'admin',				//管理员账号标识，不用认证的账号
		//'NOT_AUTH_GROUP'=> '',						//无需认证分组，多个用,号分割
		'NOT_AUTH_MODULE' => 'Login,Statement', 	// 默认无需认证模块，多个用,号分割
		'NOT_AUTH_ACTION' => 'login,down_all_wxuser', 						// 默认无需认证方法，多个用,号分割
		

        'PAY_TYPE'          => array(
                                  '1'=>'预付',         // 微信支付
		                          '2'=>'现付',          // 到酒店前台支付
		                          'YF'=>1,
                                  'DF'=>2
                              ),
        'IS_FROM'      =>array(   
                                  '1'=>'来自网页',        
		                          '2'=>'来自微信',        
		                          'WY'=>1,
                                  'WX'=>2),
        'ORDER_STATUS' => array(
                              '0'=>'未处理',
                              '1'=>'已处理',
                              '2'=>'处理中',
                              'WCL'=>0,
                              'YCL'=>1,
                              'CLZ'=>2
                              
                              ),
         'DISPOSE_STATUS'=> array(
                              '0'=>'新订单',
                              '1'=>'确认',
                              '2'=>'无法确认',
                              '3'=>'客户同意',
                              '4'=>'客户拒绝',
                              'QR'=>1,
                              'NQR'=>2,
                              'TY'=>3,
                              'JJ'=>4
                              ),
		'SHP'=>array(
				'name'=>'zunlvhui',
				'pwd'=>'zunlvhui88'
				/* 'name'=>'rikee',
				'pwd'=>'zyzylove2' */
		)
		

);
?>