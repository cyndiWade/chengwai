<?php

/**
 * 充值页面
 */
class MoneyAction extends AdvertBaseAction {
	
	private $https_verify_url = 'https://mapi.alipay.com/gateway.do?service=notify_verify&';
	/**
     * HTTP形式消息验证地址
     */
	private $http_verify_url = 'http://notify.alipay.com/trade/notify_query.do?';
	//签名方式 不需修改
	private $sign_type = 'MD5';
	//安全检验码，以数字和字母组成的32位字符
	private $key = 'e0jh3nxfrsbe69i5pdeebqgp9y93p5m9';
	private $partner = '2088111880928921';
	private $transport = 'http';
	//每个类都要重写此变量
	protected  $is_check_rbac = false;		//是否需要RBAC登录验证
	
	protected  $not_check_fn = array();	//无需登录和验证rbac的方法名
	
	private $big_type = 4;
	
	private $module_explain = '充值页面页面';
	
	//初始化数据库连接
	protected  $db = array(
		'Fund' => 'Fund',
		'UserAdvertisement' => 'UserAdvertisement'
	);
	
	//和构造方法
	public function __construct() {
		parent::__construct();
		
		$this->_init_data();
	}
	
	
	private function _init_data () {
		parent::global_tpl_view(array(
				'module_explain'=>$this->module_explain,
		));
		
		parent::big_type_urls($this->big_type);		//大分类URL

	}
	
	//充值首页
	public function index () {
	// 	充值订单 充值+UNIX前5位+用户ID+后五位
		$spnumber = 'CZ'.substr(time(),0,5).'U'.$this->oUser->id.'U'.substr(time(),5);
		parent::data_to_view(array(
				'spnumber'=>$spnumber,
				'spshow' => base64_encode(U('Advert/Money/index'))
		));
		$this->display();
	}
	
	
	//流水订单
	public function record () {
		$this->display();
	}

	public function okAlpay()
	{
		if($this->verifyReturn($_GET))
		{
			if($_GET['trade_status'] == 'TRADE_FINISHED' || $_GET['trade_status'] == 'TRADE_SUCCESS') {
				//插入流水
				$this->db['Fund']->instrtFund($_GET,$this->oUser->id,1);
				//修改用户信息表
				$bool = $this->db['UserAdvertisement']->update_user($_GET,$this->oUser->id);
				if($bool)
				{
					$this->redirect('Advert/Money/index');
				}else{
					parent::callback(C('STATUS_UPDATE_DATA'),'充值失败，请与管理员联系!');
				}
	    	}
    	}else{
    		parent::callback(C('STATUS_UPDATE_DATA'),'充值失败，请与管理员联系!');
    	}
	}
    

    public function verifyReturn($array)
    {
		if(empty($array)) {
			//判断GET来的数组是否为空
			return false;
		}else{
			//生成签名结果
			echo $isSign = $this->getSignVeryfy($array, $array["sign"]);
			exit;
			$responseTxt = 'true';
			if (! empty($array["notify_id"])) {
				$responseTxt = $this->getResponse($array["notify_id"]);
			}
			if (preg_match("/true$/i",$responseTxt) && $isSign) {
				return true;
			} else {
				return false;
			}
		}
    }

    /**
     * 获取返回时的签名验证结果
     * @param $para_temp 通知返回来的参数数组
     * @param $sign 返回的签名结果
     * @return 签名验证结果
     */
	private function getSignVeryfy($para_temp, $sign) {
		//除去待签名参数数组中的空值和签名参数
		$para_filter = paraFilter($para_temp);
		
		//对待签名参数数组排序
		$para_sort = argSort($para_filter);
		
		//把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
		$prestr = createLinkstring($para_sort);
		
		$isSgin = false;
		switch (strtoupper(trim(strtoupper($this->sign_type)))) {
			case "MD5" :
				$isSgin = md5Verify($prestr, $sign, $this->key);
				break;
			default :
				$isSgin = false;
		}
		
		return $isSgin;
	}

	 /**
	 * 获取远程服务器ATN结果,验证返回URL
	 * @param $notify_id 通知校验ID
	 * @return 服务器ATN结果
	 * 验证结果集：
	 * invalid命令参数不对 出现这个错误，请检测返回处理中partner和key是否为空 
	 * true 返回正确信息
	 * false 请检查防火墙或者是服务器阻止端口问题以及验证时间是否超过一分钟
	 */
	public function getResponse($notify_id) {
		$transport = strtolower(trim($this->transport));
		$partner = trim($this->partner);
		$veryfy_url = '';
		if($transport == 'https') {
			$veryfy_url = $this->https_verify_url;
		}
		else {
			$veryfy_url = $this->http_verify_url;
		}
		$veryfy_url = $veryfy_url."partner=" . $partner . "&notify_id=" . $notify_id;
		$responseTxt = getHttpResponseGET($veryfy_url, getcwd().'\\cacert.pem');
		
		return $responseTxt;
	}
}

?>