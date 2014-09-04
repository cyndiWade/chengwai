<?php
if (!defined('THINK_PATH'))exit();

return array(
		
	//	'DEFAULT_GROUP'         => 'Admin',  // 默认分组
	//	'DEFAULT_MODULE'        => 'Index', // 默认模块名称
	//	'DEFAULT_ACTION'        => 'index', // 默认操作名称

		/* 后台不需要验证的模块 */
		'USER_AUTH_ON' => false,							//是否开启
		'ADMIN_AUTH_KEY' => 'admin',					//管理员账号标识，不用认证的账号
		//'NOT_AUTH_GROUP'=> '',							//无需认证分组，多个用,号分割
		'NOT_AUTH_MODULE' => '', 						// 默认无需认证模块，多个用,号分割
		'NOT_AUTH_ACTION' => '', 							// 默认无需认证方法，多个用,号分割
		
		//客户端加密、解密钥匙
		'UNLOCAKING_KEY' => 'cheshencar',

		'Hotel_info_url' =>'http://yunqiserver.xicp.net/ftp/tjr/wxadmin/index.php?s=/Home/HotelList/get_hotel_info/hotel_id/',
		'logo_url'=>'http://yunqiserver.xicp.net/ftp/tjr/images/4.jpg',

		'Hotel_more'=>'http://yunqiserver.xicp.net/ftp/tjr/wxadmin/index.php?s=/Home/HotelList/index/hotel_cs/',

		'HOTEL_MAP_IMG'=>'http://yunqiserver.xicp.net/ftp/tjr/wxadmin/App/Public/Home/images/city/',
		'COUPON_IMG'=>'http://yunqiserver.xicp.net/files/zun/images/',// 优惠券图片的url;
		'COUPON_URL'=>'http://yunqiserver.xicp.net/ftp/tjr/wxadmin/index.php?s=/Home/HotelList/get_coupon/coupon_id/', //优惠券详情的url
		'HOTEL_MAP' => 'http://yunqiserver.xicp.net/ftp/tjr/wxadmin/index.php?s=/Home/HotelList/map/hotel_cs/',
		'ORDER_INFO'=>'http://yunqiserver.xicp.net/ftp/tjr/wxadmin/index.php?s=/Home/HotelList/order_info/order_id/',

		'PAY_TYPE' => array(
                    1=>array(
                       'num'=>1,
                       'explain' =>'预付(微信支付)'
                    ),
                    2=>array(
                       'num'=>2,
                       'explain' =>'现付(酒店前台支付)'
                    )
                ),
		
);
?>