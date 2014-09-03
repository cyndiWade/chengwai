<?php
/**
 * 报表功能
 */
class StatementAction extends HotelBaseAction {
    
	private $MODULE = '报表管理';
	
	//初始化数据库连接
	protected  $db = array(
		'WxUser' 		=> 'WxUser',
	    'HotelOrder'	=> 'HotelOrder'

	);
	
	/**
	 * 构造方法
	 */
	public function __construct() {
		parent::__construct();
		
		$this->assign('MODULE_NAME',$this->MODULE);
		
	}
	
	/**
	 * 生成报表
	 * @param String $name			//文件名
	 * @param String $content		//内容
	 */
	private function set_excel($name,$content) {
		
		header('Content-Type:text/html;charset=utf-8');
		header("Content-Type: application/force-download");
		header("Content-Type: text/csv");					//CSV文件
		header("Content-Disposition: attachment; filename=$name.csv");					//强制跳出下载对话框
		header('Cache-Control: no-cache, no-store, max-age=0, must-revalidate');
		header('Expires:0');
		header('Pragma:public');

		$content = (iconv( "UTF-8","gbk",$content)).',';
		echo $content;
		exit;
	}
	
	
	
   //客户数据报表
   public function down_wxuser() {
  	 	header('Content-Type:text/html;charset=utf-8');
   		$WxUser = $this->db['WxUser'];
   		
  	 	$wx_user_list = $WxUser->get_wx_user($this->oUser->id);
  	    $not_field = array('uid',
  	    'nickname','sex','city','country','province',
  	    'language','headimgurl','localimgurl','coupon','user_id','hotel_id');
   		
	   	//报列标题
	   	$title .= '是否关注,微信的uid,关注时间,姓名,手机号'."\n";

		if (!empty($wx_user_list)) {
			
			foreach ($wx_user_list as $key=>$val) {

				foreach ($wx_user_list[$key] as $k=>$v) {
					if (in_array($k,$not_field)) continue;
					//$str .= (iconv( "UTF-8","gbk",$val['oid'])).',';
					if($k=='subscribe') $v = $v==1 ? '已关注' : '已取消关注';
					if($k=='subscribe_time') $v = date('Y-m-d H:i:s');
					$str .= $v.',';
				}
				$str .= "\n";
			}		
		}

		$this->set_excel('客户报表',$title.$str);
   }
   
   //订单数据报表
   
   function dowload_order(){
   	  $PayType = C('PayType');
   	  $IsFrom  = C('IsFrom');
   	  $order_status   = C('ORDER_STATUS');
	  $dispose_status = C('DISPOSE_STATUS');

  $not_field = array('id','user_id','user_code','hotel_id','hotel_room_id','is_del','is_pay','dispose_status');
  $HotelOrder = $this->db['HotelOrder'];
  $orderlist = $HotelOrder->get_all_order($this->oUser->id);
  $title = '酒店名字,订单号,下单时间,入住人,联系人,客人要求,手机号,总价,房间数量,入住日期,离开日期,订单状态,意见,返回意见,来自,支付方式,房型'."\n";

	  foreach ($orderlist as $key=>$val){
	  	      foreach ($orderlist[$key] as $k=>$v) {
						if (in_array($k,$not_field)) continue;
						//$str .= (iconv( "UTF-8","gbk",$val['oid'])).',';
						if($k=='in_date' or $k=='out_date'or $k=='order_time' ) $v = date('Y-m-d H:i:s',$v);
						if($k=='is_from') $v = $IsFrom[$v]['explain'];
						if($k=='order_type') $v = $PayType[$v]['explain'];
						if($k=='order_status')$v = $order_status[$v];
						if($k=='dispose_status')$v = $dispose_status[$v];

					$str .= $v.',';
				}
				$str .= "\n";
	  	
	  } 
	 /* print_R($orderlist);	
	  exit;*/
   	$this->set_excel('订单报表',$title.$str);
   }
 
   
}