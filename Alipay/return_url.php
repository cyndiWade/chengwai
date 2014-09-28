<?php
require_once("alipay.config.php");
require_once("lib/alipay_notify.class.php");
?>
<!DOCTYPE HTML>
<html>
    <head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>支付宝即时到账交易接口</title>
	</head>
	<body>
	<?php
		//计算得出通知验证结果
		$alipayNotify = new AlipayNotify($alipay_config);
		$verify_result = $alipayNotify->verifyReturn();
		if($verify_result) {
		    if($_GET['trade_status'] == 'TRADE_FINISHED' || $_GET['trade_status'] == 'TRADE_SUCCESS') {
				//判断该笔订单是否在商户网站中已经做过处理
				//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
				//如果有做过处理，不执行商户的业务程序
		    	$out_trade_no 	= $_GET['out_trade_no'];
		    	$total_fee		= $_GET['total_fee'];
		    	$trade_no 		= $_GET['trade_no'];
		    	$subject 		= $_GET['subject'];
		    	$notify_time 	= $_GET['notify_time'];
		    	header('Location:http://zhuchencong.jsonlin.cn?s=/Advert/Money/index/okAlpay/'.$out_trade_no.'/total_fee/'.$total_fee.'/trade_no/'.$trade_no.'/subject/'.$subject.'/notify_time/'.$notify_time);
		    	exit;
		    }else{
		     	 echo "trade_status=".$_GET['trade_status'];
		      exit;
		    }
		}else {
		    echo "验证失败";
		}
	?>
	</body>
</html>