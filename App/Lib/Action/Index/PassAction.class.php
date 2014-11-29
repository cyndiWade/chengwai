<?php

/**
 * 找回密码
 */
class PassAction extends IndexBaseAction {
	
	
	//每个类都要重写此变量
	protected  $is_check_rbac = false;		//是否需要RBAC登录验证
	
	protected  $not_check_fn = array();	//无需登录和验证rbac的方法名
	
	private $expired_time = 10;

	//控制器说明
	private $module_explain = '';
	
	//和构造方法
	public function __construct() {
		parent::__construct();
		parent::global_tpl_view(array('module_explain'=>$this->module_explain));
		$this->_init_data_();
		$this->date = date('Y-m-d H:i');
	}
	
	//初始化数据库连接
	protected  $db = array(
			'Users' => 'Users',
			'UserAdvertisement' => 'UserAdvertisement',
			'UserMedia' => 'UserMedia',
			'Verify' => 'Verify'
	);

	private function _init_data_ () {
		$this->two_urls();
	}
	
	//输出验证码
	public function verify()
	{
	    import('ORG.Util.Image');
	    Image::buildImageVerify();
	}
	
	public function index() {
		
		$this->display();
		
	}

	//检测用户名
	public function checkname()
	{
		$name = $this->_post('account');
		$type = $this->_post('type');
		$verify = $this->_post('verify');
		if(md5($verify)!=$_SESSION['verify'])
		{
			parent::callback(C('STATUS_OTHER'),'验证码错误!');
		}
		$id = $this->db['Users']->where(array('account'=>$name,'type'=>$type))->getField('id');
		if($id)
		{
			if($type == 1){
				$phone_number = $this->db['UserMedia']->where(array('users_id'=>$id))->getField('iphone');
			}else{
				$phone_number = $this->db['UserAdvertisement']->where(array('users_id'=>$id))->getField('contact_phone');
			}
			if($phone_number!='')
			{
				parent::callback(C('STATUS_SUCCESS'),'ok',array(),array('goto_url'=>U('Index/Pass/phone',array('name'=>$name,'usd'=>$id))));
			}else{
				parent::callback(C('STATUS_OTHER'),'您未预留手机号，请联系客服!');
			}
		}else{
			parent::callback(C('STATUS_OTHER'),'用户不存在!');
		}
	}

	public function phone()
	{
		$id = $this->_get('usd');
		if($id!='')
		{
			$account = $this->db['Users']->where(array('id'=>$id))->getField('account');
			parent::data_to_view(array(
				'account' => $account 
			));
			$this->display();
		}else{
			alertLocation('非法操作！',U('Index/Pass/index'));
		}
	}

	public function newpassword()
	{
		$id = $this->_get('usd');
		if($id!='')
		{
			$type = $this->db['Users']->where(array('id'=>$id))->getField('type');
			if($type==1)
			{
				$phone_number = $this->db['UserMedia']->where(array('users_id'=>$id))->getField('iphone');
			}else{
				$phone_number = $this->db['UserAdvertisement']->where(array('users_id'=>$id))->getField('contact_phone');
			}

			//发送短信
			$this->send_mall($phone_number);

			parent::data_to_view(array(
				'phone' => $phone_number 
			));
			$this->display();
		}else{
			alertLocation('非法操作!',U('Index/Pass/index'));
		}
	}

	//发送验证码调用
	private function send_mall($phone)
	{
		if($phone!='')
		{
			$this->telephonContrl($phone);
		}
	}

	public function ajax_mall()
	{
		$phone = $this->_post('telephone');
		if($phone!='')
		{
			$status = $this->telephonContrl($phone);
			if($status==true)
			{
				parent::callback(C('STATUS_SUCCESS'),'发送验证码成功！');
			}
		}
	}


	private function telephonContrl($phone)
	{
		$rand = mt_rand(111111,999999);
		$info = $rand.'，为您的账号修改密码验证码，请在'.$this->expired_time.'分钟内完成修改，如非本人修改，请忽略；'.$this->date.'。';
		$result =  parent::send_shp($phone, $info);
		if($result['status']==true)
		{
			$varif['telephone'] = $phone;
			$varif['expired'] = strtotime('+'.$this->expired_time.' minute',time());
			$varif['verify'] = $rand;
			$varif['type'] = 3;
			$varif['status'] = 0;
			$bool = $this->db['Verify']->add($varif);
			return $bool;
		}
	}


	public function checkpassword()
	{
		$telephone = $this->_post('telephone');
		$phone_verify = $this->_post('phone_verify');
		$password = $this->_post('password');
		$id = $this->_post('usd');
		$password_check = $this->_post('password_check');
		if($password!=$password_check) parent::callback(C('STATUS_OTHER'),'两次输入密码不一致！');
		$Verify = $this->db['Verify'];
		$ver_arr = $Verify->where(array('telephone'=>$telephone,'verify'=>$phone_verify))->field('id,expired,status')->find();
		if($ver_arr==false) parent::callback(C('STATUS_OTHER'),'验证码错误！');
		if($ver_arr['status']==1) parent::callback(C('STATUS_OTHER'),'验证码已经被使用！');
		if($ver_arr['expired'] - time() < 0 ) parent::callback(C('STATUS_OTHER'),'验证码已经超时！');
		if($id!='')
		{
			$update = array('password'=>pass_encryption($password));
			$now_bool = $this->db['Users']->where(array('id'=>$id))->save($update);
			if($now_bool)
			{
				$status = array('status'=>1);
				$Verify->where(array('telephone'=>$telephone,'verify'=>$phone_verify))->save($status);
				parent::callback(C('STATUS_SUCCESS'),'ok',array(),array('goto_url'=>U('Index/Pass/endpass')));
			}else{
				parent::callback(C('STATUS_OTHER'),'原始密码无变化！');
			}
		}
	}

	public function endpass()
	{
		$this->display();
	}
}

?>