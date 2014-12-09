<?php
if (!defined('THINK_PATH'))exit();

return array(
		
	//	'DEFAULT_GROUP'         => 'Admin',  // 默认分组
	//	'DEFAULT_MODULE'        => 'Index', // 默认模块名称
	//	'DEFAULT_ACTION'        => 'index', // 默认操作名称


    /* 后台不需要验证的模块 */
    'USER_AUTH_ON' => false,					//是否开启用户权限验证
    'RBAC_NODE_TABLE' => 'node',				//节点表（系统所有资源）
    'RBAC_GROUP_TABLE' => 'group',			//组表
    'RBAC_GROUP_NODE_TABLE'	=>'group_node',	//组与节点关系表
    'RBAC_GROUP_USER_TABLE'	=>'group_user',	//组与用户关系表
    
    'ADMIN_AUTH_KEY' => 'admin',				//管理员账号标识，不用认证的账号
    'NOT_AUTH_GROUP'=> '',						//无需认证分组，多个用,号分割
    'NOT_AUTH_MODULE' => 'Public,Index', // 默认无需认证模块，多个用,号分割
    'NOT_AUTH_ACTION' => '', 						// 默认无需认证方法，多个用,号分割

	'MEDIA_ACCOUNT_TYPE'    => array(
        '1' => array(
            'id'                => 1, 
            'name'              => '新浪微博',
            'listSort'          => 1,
            // 帐号名称截图
            'accountNameImg'    => '',
            // 媒体图标
            'icon'              => '/App/Public/Media/images/sina.jpg',
            // 帐号ID截图
            'idImg'             => '',
            // 提取帐号信息流程图片
            'urlImg'            => '',
        ),
        '2' => array(
            'id'                => 2,
            'name'              => '腾讯微博',
            'listSort'          => 2,
            
            // 帐号名称截图
            'accountNameImg'    => '',
            // 媒体图标
            'icon'              => '/App/Public/Media/images/tetent.jpg',
            // 帐号ID截图
            'idImg'             => '',
            // 提取帐号信息流程图片
            'urlImg'             => '',
        ),
        '3' => array(
            'id'                => 3,
            'name'              => '微信公众号',
            'listSort'          => 3,
            
            // 帐号名称截图
            'accountNameImg'    => '/App/Public/Media/images/weixin1.jpg',
            // 媒体图标
            'icon'              => '/App/Public/Media/images/weixin.jpg',
            // 帐号ID截图
            'idImg'             => '/App/Public/Media/images/add_show_weixin_id.jpg',
            // 提取帐号信息流程图片
            'urlImg'            => '/App/Public/Media/images/weixin2.gif',
        ),
        '4' => array(
            'id'                => 4,
            'name'              => '新闻媒体',
            'listSort'          => 4,
            
            // 帐号名称截图
            'accountNameImg'    => '',
            // 媒体图标
            'icon'              => '/App/Public/Media/images/xinwen.jpg',
            // 帐号ID截图
            'idImg'             => '',
            // 提取帐号信息流程图片
            'urlImg'            => '',
        ),
    ),
    
    // 微博报价比率
    'WEIBO_PRICE_RATIO' => array(
        // 硬广转发报价
        'retweetPrice'      => 1,
        // 硬广直发报价
        'tweetPrice'        => 1,
        // 软广转发报价
        'softRetweetPrice'  => 0.7,
        // 软广直发报价
        'softTweetPrice'    => 0.7,
        // 带号价
        'contentPrice'      => 0.45,
    ),
    
    // 微信图文价格比率
    'WEIXIN_PRICE_RATIO' => array(
        // 单图报价
        'singleGraphicPrice'        => 1.3,
        // 多图文第一条报价
        'multiGraphicTopPrice'      => 1,
        // 多图文第二条报价
        'multiGraphicSecondPrice'   => 0.7,
        // 多图文其他位置报价
        'multiGraphicOtherPrice'    => 0.4,
        // 带号价
        'contentPrice'              => 0.4,
    ),
);
?>