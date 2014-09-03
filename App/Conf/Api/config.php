<?php
if (!defined('THINK_PATH'))exit();
//$url = 'http://www.zunlvhui.com.cn';
$url = 'http://114.215.172.110';
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

		'Hotel_info_url' =>$url.'/zunlvhui/index.php?s=/Home/HotelList/get_hotel_info/hotel_id/',
        'Sphotel_info_url' =>$url.'/zunlvhui/index.php?s=/Home/SphotelList/get_hotel_info/hotel_id/',
		'logo_url'=>$url.'/zunlvhui/App/Public/Home/images/4.jpg',
        'Sphotel_more'=>$url.'/zunlvhui/index.php?s=/Home/SphotelList/index/hotel_cs/',
		'Hotel_more'=>$url.'/zunlvhui/index.php?s=/Home/HotelList/index/hotel_cs/',

		//'HOTELMAPIMAGES'=>'http://zunlvhui.com.cn/zunlvhui/App/Public/Home/images/city/',
		'HOTELMAPIMAGES'=>$url.'/zunlvhui/App/Public/Home/mapimages/',
		'COUPON_IMG'=>$url.'/files/zunlvhui/images/',// 优惠券图片的url;
		'COUPON_URL'=>$url.'/zunlvhui/index.php?s=/Home/HotelList/get_coupon/coupon_id/', //优惠券详情的url
		'HOTEL_MAP' => $url.'/zunlvhui/index.php?s=/Home/HotelList/map/hotel_cs/',
		//'SPHOTEL_MAP'=> 'http://www.zunlvhui.com.cn/zunlvhui/index.php?s=/Home/SphotelList/map/hotel_cs/',
		'ORDER_INFO'=>$url.'/zunlvhui/index.php?s=/Home/HotelList/order_info/order_id/',

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
        'TestCode'=>1,
        'TestCodeUser'=>array(
	        'o_kNsuDTFNH42UvcZIN7BH4mszPY', //唐吉荣
	        'o_kNsuHIAjL5eqqiWwzvH4eDQtig', //邹朝晖
            'o_kNsuBNiA-CTmQ8qegUQm4_Yc0w',
            'o_kNsuL4xBkN_nKLohAruUK6ACDo',
            'o_kNsuEXI_I2PzTgN4M3TKnU890Y',
            'o_kNsuLWb2-L297-4X-gK_v2Lc50',
            'o_kNsuCiF_jPfCkcT4Us6MygFbZs',
            'o_kNsuGgjuHEUfT69SkXqHTulIjo',
            'o_kNsuIsXzL4bje-I7thbB1Ppg7o',
            'o_kNsuFy_tcHlExuCQl8W96zPpPs',  //小杨
            'o_kNsuO-OezIQBhMGsSMlfAU7LRU',
            'o_kNsuJE-txf0sBuxjg56sB_10hw',
            'o_kNsuDQyzDesEIk8UmF_hmsRFtI'
        ),
		
);
?>