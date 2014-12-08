<?php
if (!defined('THINK_PATH'))exit();

$db_config = require("config.inc.php");	//数据库配置

//其他系统配置
$system  = array(
		
	    'DB_PREFIX'             => 'app_',    // 数据库表前缀
	    'DB_FIELDTYPE_CHECK'    => false,       // 是否进行字段类型检查
	    'DB_FIELDS_CACHE'       => true,        // 启用字段缓存
	    'DB_CHARSET'            => 'utf8',      // 数据库编码默认采用utf8
	    'DB_DEPLOY_TYPE'        => 0, // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
	    'DB_RW_SEPARATE'        => false,       // 数据库读写是否分离 主从式有效
	    'DB_MASTER_NUM'         => 1, // 读写分离后 主服务器数量
	    'DB_SLAVE_NO'           => '', // 指定从服务器序号
	    'DB_SQL_BUILD_CACHE'    => false, // 数据库查询的SQL创建缓存
	    'DB_SQL_BUILD_QUEUE'    => 'file',   // SQL缓存队列的缓存方式 支持 file xcache和apc
	    'DB_SQL_BUILD_LENGTH'   => 20, // SQL缓存的队列长度
	    'DB_SQL_LOG'            => false, // SQL执行日志记录
	    
		/* SESSOIN配置 */
		'SESSION_AUTO_START'    => true,		//常开

		/* URL配置 */
		'URL_MODEL'             => 2,
		'URL_ROUTER_ON'   => false, 	//开启路由
		'URL_ROUTE_RULES' => array(
				//'join' => array('/Public/register'),    		 	 //注册
				//'index'=>array('?s=/Index/index'),			//功能介绍
				//'articles/:id'=>'home/Index/show',            //新闻详细页面
		),
		'PREV_URL' => $_SERVER["HTTP_REFERER"],
		
		/* 模板引擎设置 */
		//'DEFAULT_THEME' => 'default',
		//'TMPL_ACTION_SUCCESS' => 'public:success',
		//'TMPL_ACTION_ERROR' => 'public:error',
		'TMPL_EXCEPTION_FILE'   => THINK_PATH.'Tpl/think_exception.tpl',// 异常页面的模板文件
		'TMPL_DETECT_THEME'     => false,       // 自动侦测模板主题
		'OUTPUT_ENCODE'         =>  false, 			// 页面压缩输出

		//项目分组
		'APP_GROUP_LIST'        => 'Home,Admin,Api,Main,Media,Advert,Index,Service',  	// 项目分组设定,多个组之间用逗号分隔,例如'Home,Admin'
		'DEFAULT_GROUP'         => 'Index',  					// 默认分组
		'DEFAULT_MODULE' 		=> 'Index',
		'DEFAULT_ACTION'        => 'index', 					// 默认操作名称
		'APP_GROUP_MODE'        =>  0, 							 // 分组模式 0 普通分组 1 独立分组
		
		'APP_SUB_DOMAIN_DEPLOY' => false,  			 // 是否开启子域名部署
		'APP_SUB_DOMAIN_RULES'  => array(
			// 子域名部署规则
			//'192.168.1.100'    => array('Api/'),	//指向对应的分组。
		), 			
		'APP_SUB_DOMAIN_DENY'   => array(), 			//  子域名禁用列表
		

		//语言包
		'LANG_SWITCH_ON'=> true,				//开启语言包功能
		'LANG_AUTO_DETECT'=> false,			//是否自动检测语言
		'DEFAULT_LANG'=>'zh-cn',						//默认语言的文件夹是zh-cn
		'LANG_LIST'        => 'zh-cn,en-us',			 //允许切换的语言列表 用逗号分隔
		'VAR_LANGUAGE'     => '1',					 // 默认语言切换变量
		
		//表单安全配置
		//'TOKEN_ON'=>true,  							// 是否开启令牌验证
		//'TOKEN_NAME'=>'__hash__',    		// 令牌验证的表单隐藏字段名称		
		//'TOKEN_TYPE'=>'md5',  					//令牌哈希验证规则 默认为MD5	
		//'TOKEN_RESET'=>true,  					//令牌验证出错后是否重置令牌 默认为true
		
		//缓存配置
		//'DATA_CACHE_TYPE' =>'File',										//缓存类型
		//'DATA_CACHE_PATH' =>'Home/Runtime/Temp/',		//缓存文件目录
		//'DATA_CACHE_TIME'=>'60'	,										//缓存有效秒数	
		
		/** 静态缓存
		'HTML_CACHE_ON'=>true, // 开启静态缓存
		'HTML_FILE_SUFFIX'  =>  '.shtml', // 设置静态缓存后缀为.shtml
		//缓存规则
		'HTML_CACHE_RULES'=> array(
				//定义模块下的所有方法都缓存
				'Index:'            => array('{:module}/{$_SERVER.REQUEST_URI|md5}',5),
				//定义模块下某个方法缓存
				'Public:login'            => array('{:module}/{$_SERVER.REQUEST_URI|md5}', 2),
		)
		*/
		
		/* 时区设置 */
		'DEFAULT_TIMEZONE'=>'Asia/Shanghai', 	// 设置默认时区
		'DEFAULT_AJAX_RETURN' => '',		//默认AJAX返回值
		
);


/* 自定设置 */
$custom= array (		
		'SESSION_DOMAIN' => 'chengwai',	//项目session域
		
		//用户类型
		'ACCOUNT_TYPE' => array (
			'ADMIN' => 0,			//管理员
			'Media' => 1,			//媒体主
			'Advert' => 2,			//广告主
			'Salesman'=>3,			//销售员
		),

		//上传文件目录
		'UPLOAD_DIR' => array(
				'web_dir' => $_SERVER['DOCUMENT_ROOT'].'/',
				'image' => 'files/chengwai/images/',		//图片地址
		),
		
		//外部文件访问地址(用来填写专用的文件服务器)
		'PUBLIC_VISIT' => array(
 				//'domain' =>	'http://'.$_SERVER['SERVER_NAME'].'/',
 				'domain' =>	'http://local_cwq.com/',
				'dir' => 'files/chengwai/',							//项目文件目录
		),

		//短信平台账号
		'SHP' => array(
// 			'TYPE' => 'SHP',	//使用哪种短信接口
//  				'NAME'=>'cheshen_gd',
//  				'PWD'=>'cheshen801'

// 				'NAME'=>'rikee',
// 				'PWD'=>'zyzylove2'	
				
			'TYPE' => 'RD_SHP',				//使用哪种短信接口
				'NAME'=>'gztzwl',
				'PWD'=>'gztzwl'
		),
		
		//系统配置属性
		'WEB_SYSTEM' => array(
			'base_id'=>1,		//system_base 表ID
			'finance_id'=>1, 	//system_finance 表ID	
			'sms_id'=>1,    	  //system_sms 表ID
		),
		
		'Medie_Account_Status' => array(
				0 => array(
						'status' => 0,
						'explain' => '待审核',
				),
				1 => array(
						'status' => 1,
						'explain' => '审核通过',
				),
		),
		'ACCOUNT_STATUS' => array(
				0 => array(
						'status' => 0,
						'explain' => '正常',
				),
				1 => array(
						'status' => 1,
						'explain' => '审核中',
				),
				2 => array(
						'status' => 2,
						'explain' => '禁用',
				),
		),
		
		//主订单状态
		'Order_Status' => array(
			0=>array(
				'status'=>0,
				'explain'=>'待派单'
			),	
			1=>array(
				'status'=>1,
				'explain'=>'待审核'
			),
			2=>array(
				'status'=>2,
				'explain'=>'审核通过'
			),
			3=>array(
				'status'=>3,
				'explain'=>'审核失败，重新提交'
			),
			4=>array(
				'status'=>4,
				'explain'=>'已支付，待执行'
			),
			5=>array(
				'status'=>5,
				'explain'=>'执行完成'
			),
			6=>array(
				'status'=>6,
				'explain'=>'强制取消'
			),
		),
		
		
		//关系表订单状态
		'Account_Order_Status' => array(
			//新建立的订单
			0=>array(
				'status'=>0,
				'explain'=>'新订单',
				'explain_yxd'=>'预约中',
				'other' =>	'等待城外圈审核!'
			),
			1=>array(				
				'status'=>1,			//城外圈审核通过即可支付
				'explain'=>'审核通过',
				'explain_yxd'=>'已接受',
				'other' => '无'
			),
			2=>array(
				'status'=>2,			//城外圈审核失败
				'explain'=>'审核失败',
				'explain_yxd'=>'已拒绝',
				'other' => '城外圈审核失败!'
			),
			3=>array(
				'status'=>3,			//客户支付成功的状态
				'explain'=>'支付成功',
				'explain_yxd'=>'需要修改',
				'other' => '支付成功,等待媒体主执行!'
			),
			4=>array(
				'status'=>4,
				'explain'=>'媒体主拒绝',		//这里拒绝后，要把钱从冻结资金中打回客户的资金池中
				'explain_yxd'=>'媒体主拒绝',
				'other' => '媒体拒绝执行!'
			),
			5=>array(
				'status'=>5,			//媒体主确认后，订单的状态
				'explain'=>'执行中',
				'explain_yxd'=>'媒体主确认',
				'other' => '媒体主确认,正在执行!'
			),
			6=>array(
				'status'=>6,			//媒体主执行完成后的状态
				'explain'=>'执行完成',
				'other' => '媒体主执行完成!'
			),
			7=>array(
				'status'=>7,			//确认完成后的状态
				'explain'=>'确认完成',
				'other' => '确认执行完成！'
			),	
			8=>array(
				'status'=>8,
				'explain'=>'已流单',
				'explain_yxd'=>'已创建推广单',//意向单不可再次点击
				'other' => '已创建推广单!'
			),
			
			9=>array(
				'status'=>9,
				'explain'=>'已取消',	//订单过期
				'explain_yxd'=>'已过期',
				'other' => '订单过期!'
			),
			10=>array(
				'status'=>10,
				'explain'=>'不合格',	//订单执行不合格
				'other' => '订单执行不合格!'
			),			
			//增加投诉流程 订单状态 add by chenchao 2014-12-06
            11=>array(
				'status'=>11,
				'explain'=>'投诉中'
			),
            12=>array(
				'status'=>12,
				'explain'=>'申诉中'
			),
            13=>array(
				'status'=>13,
				'explain'=>'投诉成功'
			),
            14=>array(
				'status'=>14,
				'explain'=>'申诉成功'
			),
            //增加投诉流程 订单状态 add by chenchao 2014-12-06	
		),
	
		//大导航分类ID集合
		'Big_Nav_Class_Ids' => array(
			//新浪、腾讯名人分类ID集合
			'celebrity_tags_ids' => array(
				'top_parent_id'=>252,//新浪、腾讯名人分类最顶层ID
				'mrzy' => 21,			//名人职业
				'mtly' => 22,			//名人领域
				'ckbj_type' => 426,		//参考报价类型
				'jg' => 95,				//价格
				'dfmr_mt' => 106,		//地方名人媒体
				'xqbq' => 118,			//兴趣标签
				'mr_mtlb' => 150,		//名人/媒体类别
				'phd' => 154,			//配合度
				'mr_fans_num' => 159,	//名人粉丝数
				'zhyc' => 169			//是否支持原创					
			),
			
			//新浪、腾讯草根分类ID集合
			'caogen_tags_ids' => array(
				'top_parent_id'=>293,	//新浪、腾讯草根分类最顶层ID
				'cjfl' => 295,			//常见分类
				'jg' => 296,			//价格
				'dfmr_mt' => 711,		//地方名人媒体(地区)
				'fans_num' => 297,		//粉丝量
				'fans_sex' => 298,		//粉丝性别
				'zfjg_type' => 421,		//转发价格
			),
				
			//微信草根ids
			'weixin_caogen_tags_ids' => array(
				'top_parent_id'=>251,	//微信草根分类最顶层ID
				'cjfl' => 222,		//常见分类
				'zfjg_type' => 436,	//转发价格类型
				'jg' => 253,		//价格
				'dfmr_mt' => 723,	//地方名人媒体(地区)
				'fans_num' => 262,	//粉丝量
				'sprz' => 273,		//视频认证
				'zhsfrz' => 277,	//账号是否认证
				'szxb' => 278,		//受众性别
				'fsrzsj' => 279,	//粉丝量认证时间
				'zpjyds' => 431,	//周平均阅读数	
			),	
				
			//微信名人IDS
			'weixin_celebrity_tags_ids' => array(
				'top_parent_id'=>445,		//微信名人分类最顶层ID
				'mrzy' => 453,				//名人职业
				'mtly' => 452,				//名人领域
				'ckbj_type' => 451,			//参考报价类型
				'jg' => 447,				//价格
				'dfmr_mt' => 448,			//地方名人媒体
				'xqbq' => 449,				//兴趣标签
					
				'mr_mtlb' => 577,			//名人/媒体类别
				'phd' => 578,				//配合度
				'mr_fans_num' => 579,		//名人粉丝数
				'zhyc' => 580,				//是否支持原创
				'mrxb' => 581,				//名人性别		
			), 

			//新闻媒体
			'xinwen_tags_ids' => array(
				'top_parent_id'=>652,		//分类最顶层ID
				'hyfl'=>653,				//行业分类
				'dqsx'=> 659,				//地区筛选
				'yhzq'=>660,				//优惠专区
				'jg'=>666,					//价格
				'mh_type'=>667,				//门户类型
				'sfxwy'=>672,				//是否新闻源
				'dljzk'=>677,				//带链接情况			
			),
				
		),
		
		
		/* 错误类型 */
		'STATUS_SUCCESS' => '0',					//没有错误
		'STATUS_NOT_LOGIN'	=> '1002',			//未登录
		'STATUS_UPDATE_DATA'	=> '2001',		//没有成功修改数据
		'STATUS_HAVE_DATA' => '2002',			//数据已存在
		'STATUS_NOT_DATA'	=> '2004',			//没有数据
		'STATUS_RBAC' => '3001',						//RBAC权限不通过
		'STATUS_ACCESS' => '4001',				//非法访问
		'STATUS_DATA_LOST' => '5001',			//上传数据丢失
		'STATUS_OTHER' => '9999',					//其他错误
		'STATUS_NOT_CHECK'=>'6001',			//验证不通过
		'STATUS_LUCKY_YES' => '700',					//中奖了
		'STATUS_LUCKY_NO' => '701',					//没中奖
		
);

return array_merge($db_config,$system,$custom);


/*		系统常量 (手册附录)
 echo __SELF__  . '<br />';					//当前URL所有参数
echo __URL__  . '<br />';						//当前模块地址(控制器地址)
echo __APP__	. '<br />';						//当前项目入口文件
echo __ACTION__  . '<br />';				//当前模块控制器地址 (当前模块控制器地址)
echo ACTION_NAME . '<br />'; 			//当前方法名称

echo '<br />';

echo APP_PATH . '<br />'; 					//当前项目目录
echo APP_NAME . '<br />'; 					//当前项目名称
echo APP_TMPL_PATH . '<br />'; 		//当前项目模板路径
echo APP_PUBLIC_PATH . '<br />'; 	//项目公共文件目录
echo CACHE_PATH . '<br />'; 				//当前项目编译缓存文件

*/
?>
