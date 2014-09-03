<?php
if (!defined('THINK_PATH'))exit();

return array(
		
		'DEFAULT_GROUP'         => 'Admin',  // 默认分组
		'DEFAULT_MODULE'        => 'Login', // 默认模块名称
		'DEFAULT_ACTION'        => 'login', // 默认操作名称


		/* 后台不需要验证的模块 */
		'USER_AUTH_ON' => true,						//是否开启用户权限验证
		'RBAC_NODE_TABLE' => 'node',				//节点表（系统所有资源）
		'RBAC_GROUP_TABLE' => 'group',			//组表
		'RBAC_GROUP_NODE_TABLE'	=>'group_node',	//组与节点关系表
		'RBAC_GROUP_USER_TABLE'	=>'group_user',	//组与用户关系表
		
		'ADMIN_AUTH_KEY' => 'admin',				//管理员账号标识，不用认证的账号
		//'NOT_AUTH_GROUP'=> '',						//无需认证分组，多个用,号分割
		'NOT_AUTH_MODULE' => 'Login', 			// 默认无需认证模块，多个用,号分割
	//	'NOT_AUTH_ACTION' => 'login', 						// 默认无需认证方法，多个用,号分割

		'VAR_PAGE' => 'page',		//分页标识
		'USERFROM'   =>array(
			0 => array(
						'num' => 0,
						'explain' => '账号搜索',
				),
	        1 => array(
						'num' => 1,
						'explain' => '扫描二维码',
				),
			2 => array(
						'num' => 2,
						'explain' => '关注后扫描',
				),
			3 => array(
						'num' => 3,
						'explain' => '关注后扫描',
				),
             ),
		 'SUBSCRIBE'=>array(
				1  => array(
						'num' => 1,
						'explain' =>'已关注'
		         ),
		 		2  => array(
		 				'num' => 2,
		 				'explain' =>'已取消关注'
		 		),
			),
		 'Roomspc'=>array(
			    0  => array(
						'num' => 0,
						'explain' =>'无优惠 	',
			            'text'=>'<font color=red>(填写 0)</font>'
		         ),
		 		1  => array(
		 				'num' => 1,
		 				'explain' =>'打折',
		 				'text'=>'<font color=red>(填写 0.9 就是大九折)</font>'
		 		),
		 		2  => array(
						'num' => 2,
						'explain' =>'立减 ',
						'text'=>'<font color=red>(填写 1 就是减1元)</font>'
		         ),
		 		3  => array(
		 				'num' => 3,
		 				'explain' =>'送N间夜 ',
		 				'text'=>'<font color=red>(填写 3/1 就是入住三天送一天)</font>'
		 		),
			),
	     'KF'=>array(
			1  => array(
						'num' => 1,
						'explain' =>'正在跟进'		
		         ),
		    2  => array(
						'num' => 2,
						'explain' =>'已联系酒店'		
		         ),
		    3  => array(
						'num' => 3,
						'explain' =>'已联系客人'		
		         ),
		    4  => array(
						'num' => 4,
						'explain' =>'酒店确认订单'		
		         ),
		    5  => array(
						'num' => 5,
						'explain' =>'订单处理完毕'		
		         ),

			)

);
?>