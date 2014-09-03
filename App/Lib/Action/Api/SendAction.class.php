<?php

/**
 * 短信发送类
 */
class SendAction extends ApiBaseAction {
	
	private $telephone;			//目标手机号码
	private $verify;					//短信验证码
	private $msg;					//短信内容
	private $date;					//发送时间
	private $send_status;		//发送状态
	private $expired_time = 30;		//分钟

	
	/**
	 * 追加使用的数据表对象
	 * @var Array  当访问时，$this->db['Member']->query();
	 */
	protected $add_db = array(
		'Member' => 'Member',
	);
	
	//需要身份验证的模块
	protected $aVerify = array(
		'store_send'
	);

	//构造方法
	public function __construct() {
		parent::__construct();
		
		//短信内容
		$this->verify =  mt_rand(111111,999999);		//生成随机验证码
		$this->date = date('Y-m-d H:i');						//日期
		
		$this->request['telephone'] = $this->_post('telephone');
	}
	
	
	
	//短信发送
	private function _send () {
		//请求数据
		if (empty($this->telephone)) {
			parent::callback(C('STATUS_OTHER'),'电话号码不存在');
		} elseif (!preg_match("/^1[358]\d{9}$/", $this->telephone)) {
			parent::callback(C('STATUS_OTHER'),'电话号码格式错误');
		}
	
		//执行发送短信
		$result =  parent::send_shp($this->telephone, $this->msg);
		$this->send_status = $result['status'];		//发送后的状态

	}
	
	
	//保存到数据库
	private function _add_data ($type) {
		$this->_send();	//发送短信
	
		//对发送结果进行处理
		if ($this->send_status == true) {		//发送成功后
			//写入数据库
			$Verify = M('Verify');										//短信表
			$Verify->telephone = $this->telephone;			//电话号码
			$Verify->verify = $this->verify;						//验证码
			$Verify->expired = strtotime('+'.$this->expired_time.' minute',time());				//过期时间设置为30分钟后
			$Verify->type = $type;														//过期时间设置为30分钟后
			//写入数据库
			$Verify->add() ? parent::callback(C('STATUS_SUCCESS'),'发送成功') : parent::callback(C('STATUS_UPDATE_DATA'),'发送超时');
			//失败处理
		} else {
			parent::callback(C('STATUS_OTHER'),'发送失败，请检查网络');
		}
	}
	
	
	
	//注册发送短信
	public function register_send () {

		//手机号码
		if ($this->isPost()) {
			$this->telephone = $this->request['telephone'];		//电话号码
			$this->msg = $this->verify.'，为您的账号注册验证码，请在'.$this->expired_time.'分钟内完成注册，如非本人注册，请忽略；'.$this->date.'。';
			$this->_add_data(1);
			exit;
		}
		
		$this->assign('name','telephone');
		$this->display('Login:sendSHP');
	}
	
	
	
	
	
}

?>