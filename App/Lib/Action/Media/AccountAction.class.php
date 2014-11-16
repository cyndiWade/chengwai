<?php

/**
 * 媒体主表账号控制器
 */
class AccountAction extends MediaBaseAction {
	
	//每个类都要重写此变量
	protected  $is_check_rbac = true;		//是否需要RBAC登录验证
	
	protected  $not_check_fn = array('register', 'check_login', 'login', 'logout', 'register_accout', 'checkPhone', 'checkUserName','verify');	//无需登录和验证rbac的方法名
	
	//控制器说明
	private $module_explain = '媒体主';


	//初始化数据库连接
	protected  $db = array(
			'CategoryTags'=>'CategoryTags',
			'Users' => 'Users',
			'Verify'=>'Verify',
			'User_media' => 'UserMedia',
			'Blank' => 'Blank'
	);
	
	//和构造方法
	public function __construct() {
		parent::__construct();
		parent::global_tpl_view(array('module_explain'=>$this->module_explain));
	}


	public function user_system () {
		echo '1';
	}
	
	//输出验证码
	public function verify()
	{
		import('ORG.Util.Image');
		Image::buildImageVerify();
	}
	
	//验证注册的短信的模拟方法
	public function check_phone ($phone) {
		$phone_nub = $phone['phone'];
		$type = $phone['type'];	//1表示查询媒体主的注册，2表示查询广告主的注册，
		$phone_vr = $phone['phone_vr'];	//手机上的验证码
		//执行验证
		$phone_check_info = parent::check_verify($phone_nub,$type,$phone_vr);
		
		//验证结果
		//if ($phone_check_info['status'] == false) echo $phone_check_info['msg'];
		return $phone_check_info['status'] ? true : false ;
	}
	
	public function login () {
		$this->display();
	}
	
	//账号注册	
	public function register () {
        parent::data_to_view(array(
            'secondPosition' => '媒体主注册',
        ));
        $this->display();	
	}
	
	public function register_accout() {
		if ($this->isPost()) {
			import("@.Tool.Validate");
			$account = $this->_post('account');					//用户账号
			$password = $this->_post('password');				//用户密码
			$password_check = $this->_post('password_check');	//二次用户密码
			$iphone = $this->_post('iphone');
			$phone_verify= $this->_post('phone_verify');
			$Users = $this->db['Users'];
			if (Validate::checkNull($account)) parent::callback(C('STATUS_OTHER'),'账号不得为空');
			if ($Users->account_is_have($account)!='') parent::callback(C('STATUS_OTHER'),'会员名已存在');
			if (Validate::checkNull($password)) parent::callback(C('STATUS_OTHER'),'密码不得为空');
			if (Validate::checkNull($phone_verify)) parent::callback(C('STATUS_OTHER'),'手机验证码不得为空');
			if (!Validate::check_string_num($account)) parent::callback(C('STATUS_OTHER'),'账号密码只能输入英文或数字');
			if (!Validate::checkEquals($password,$password_check)) parent::callback(C('STATUS_OTHER'),'2次密码输入不一致');
			if (!Validate::checkPhone($iphone)) parent::callback(C('STATUS_OTHER'),'手机号码格式错误');
			$User_media = $this->db['User_media'];
			if ($User_media->iphone_is_have($iphone)!='') parent::callback(C('STATUS_OTHER'),'手机号已存在');
			$phone = array('phone'=>$iphone,'type'=> 1 ,'phone_vr'=>$phone_verify);
			 
			if(!$this->check_phone($phone)) parent::callback(C('STATUS_OTHER'),'手机验证码错误');
			
			$id = $Users->add_account($account,$password);
			if($id!='')
			{
				$media = array('users_id'=>$id,'iphone'=>$iphone);
				$User_media->add_account_list($media);
				//这里自定义设置session
				$db_data= array(
					'id'=>$id,
					'account'=>$account,
					'nickname' => '',
					'type' => 1
				);
				parent::set_session(array('user_info'=>$db_data));
				//$this->redirect('/Media/EventOrder/index');
				parent::callback(1,'数据有误！');
			}else{
				parent::callback(C('STATUS_OTHER'),'数据有误！');
			}
		} else {
			parent::callback(C('STATUS_OTHER'),'非法访问！');
		}
	}
	

	/**
	 * 登陆验证
	 */
	public function check_login() {
	
		if ($this->isPost()) {
			
			$notcheck = explode(',', $this->_post('notcheck'));
			
			$Users = $this->db['Users'];						//系统用户表模型
			import("@.Tool.Validate");							//验证类
			$account = $this->_post('account');					//用户账号
			$password = $this->_post('password');					//用户密码
			$verify = $this->_post('verify');
			//数据过滤
			if (Validate::checkNull($account)){parent::callback(C('STATUS_OTHER'),'账号不能为空！!'); };
			if (Validate::checkNull($password)){parent::callback(C('STATUS_OTHER'),'密码不能为空！!'); };
			if (!Validate::check_string_num($account)){parent::callback(C('STATUS_OTHER'),'账号密码只能输入英文或数字!');};
			
			if (!in_array('verify',$notcheck)) {
				if (md5($verify)!=$_SESSION['verify']){parent::callback(C('STATUS_OTHER'),'验证码错误!');}
			}
			
			//$user_type = C('ACCOUNT_TYPE.Media');
			
			$user_type = 1;
			//读取用户数据
			$user_info = $Users->get_user_info(array('account'=>$account,'type'=>$user_type,'is_del'=>0));
			
			
			//验证用户数据
			if (empty($user_info)) {
				parent::callback(C('STATUS_OTHER'),'此用户不存在或被删除！');
			} else {
				$status_info = C('ACCOUNT_STATUS');
				//状态验证
				if ($user_info['status'] != $status_info[0]['status']) {
					parent::callback(C('STATUS_OTHER'),$status_info[$user_info['status']]['explain']);
					//alertBack($status_info[$user_info['status']]['explain']);
				}
				
				//验证密码
				if (md5($password) != $user_info['password']) {
					//$this->error("密码错误"); 
					parent::callback(C('STATUS_OTHER'),'密码错误！');
				} else {
					$tmp_arr = array(
						'id' =>$user_info['id'],
						'account' => $user_info['account'],
						'nickname' => $user_info['nickname'],
						'type'=>$user_info['type'],
					);
				}
				//写入SESSION
				parent::set_session(array('user_info'=>$tmp_arr));
				//更新用户信息
				$Users->up_login_info($user_info['id']);
				// $this->redirect('/Media/Account/user_system');
				//$this->redirect('/Media/EventOrder/index');
				parent::callback(C('STATUS_SUCCESS'),'ok',array(),array('goto_url'=>U('Media/EventOrder/index')));
				
			}
		} else {
			$this->redirect('/Media/Account/login');
		}
	}
	
	//验证用户名是否可注册
	public function checkUserName()
	{
		if ($this->isPost()) 
		{
			$account = $this->_post("account");
			$id = D('Users')->account_is_have($account);

			if($id !='')
			{
				parent::callback(0 ,'该用户已被注册！');
			}
			else
			{
				parent::callback(1 ,'该用户可注册！');
			}
		} 
		else 
		{
			$this->error('非法访问！');
		}
	}
	
	//验证手机号
	public function checkPhone()
	{
		if ($this->isPost()) 
		{
			$telephone = $this->_post("telephone");
			$id = D('UserMedia')->iphone_is_have($telephone);

			if($id !='')
			{
				parent::callback(0 ,'该手机已被使用！');
			}
			else
			{
				parent::callback(1 ,'该手机可以使用！');
			}
		} 
		else 
		{
			$this->error('非法访问！');
		}
	}
	
	//退出登陆
    public function logout () {
    	if (session_start()) {
    		parent::del_session('user_info');
    		$this->success('退出成功',U(GROUP_NAME.'/Account/login'));
    		//$this->success('退出成功',U(GROUP_NAME.'/Login/login'));
    	} 
    }

    
    /**
     * 用户信息
     * 
     * @author lurongchang
     * @date   2014-09-30
     * @return void
     */
    public function userInfo()
    {
        $userInfos = parent::get_session('user_info');
        if ($this->isPost()) {
            $truename   = I('post.truename', '', 'addslashes');
            $campany    = I('post.campany', '', 'addslashes');
            $iphone     = I('post.phone', '', 'addslashes');
            $tel        = I('post.tel', '', 'addslashes');
            $email      = I('post.email', '', 'addslashes');
            $qq         = I('post.qq', 0, 'intval');
            $msn        = I('post.msn', '', 'addslashes');
            
            import("@.Tool.Validate");
            if (!Validate::checkPhone($iphone)) {
                parent::callback(C('STATUS_OTHER'), '手机号码格式错误');
            }
            if (!Validate::checkQQ($qq)) {
                parent::callback(C('STATUS_OTHER'), 'QQ号码错误');
            }
            if ($email && !Validate::checkemail($email)) {
                parent::callback(C('STATUS_OTHER'), 'Email格式错误');
            }
            $datas = array(
                'name'          => $truename,
                'company_name'  => $campany,
                'iphone'        => $iphone,
                'tel_phone'     => $tel,
                'email'         => $email,
                'qq'            => $qq,
                'msn'           => $msn,
                'email'         => $email,
            );
            $where['users_id'] = $userInfos['id'];
            $userMediaModel = $this->db['User_media'];
            $isHave = $userMediaModel->account_is_have($userInfos['id']);
            if ($isHave) {
                $status = $userMediaModel->saveAccount($where, $datas);
            } else {
                $datas['users_id'] = $userInfos['id'];
                $status = $userMediaModel->addAccount($datas);
            }
            if ($status) {
                parent::callback(1, 'success');
            } else {
                parent::callback(0, '数据保存错误, 请重新提交', array($status));
            }
        } else {
            if ($userInfos) {
                $userMediaModel = $this->db['User_media'];
                $userMediaInfo = $userMediaModel->getInfoByIds(array($userInfos['id']));
                if ($userMediaInfo) {
                    $userInfos = array_merge($userInfos, reset($userMediaInfo));
                }
            }
            parent::data_to_view(array_merge($userInfos, array(
                //二级导航
                'secondSiderbar' => array(
                    '用户信息' => array('select' => true, 'url' => U('/Media/Account/userInfo')),
                    '修改密码' => array('select' => false, 'url' => U('/Media/Account/changepw')),
                    '支付信息' => array('select' => false, 'url' => U('/Media/Account/payinfo')),
                ),
                'secondPosition' => '用户信息',
            )));
            $this->display();
        }
    }
    
    /**
     * 修改密码
     * 
     * @author lurongchang
     * @date   2014-09-30
     * @return void
     */
    function changepw()
    {
        parent::data_to_view(array(
            //二级导航
            'secondSiderbar' => array(
                '用户信息' => array('select' => false, 'url' => U('/Media/Account/userInfo')),
                '修改密码' => array('select' => true, 'url' => U('/Media/Account/changepw')),
                '支付信息' => array('select' => false, 'url' => U('/Media/Account/payinfo')),
            ),
            'secondPosition' => '修改密码',
        ));
        $this->display();
    }
    
    /**
     * 支付信息
     * 
     * @author lurongchang
     * @date   2014-09-30
     * @return void
     */
    function payinfo()
    {
        parent::data_to_view(array(
            //二级导航
            'secondSiderbar' => array(
                '用户信息' => array('select' => false, 'url' => U('/Media/Account/userInfo')),
                '修改密码' => array('select' => false, 'url' => U('/Media/Account/changepw')),
                '支付信息' => array('select' => true, 'url' => U('/Media/Account/payinfo')),
            ),
            'secondPosition' => '支付信息',
        ));
        $this->display();
    }
    
    /**
     * 支付帐号列表
     * 
     * @author lurongchang
     * @date   2014-10-24
     * @return void
     */
    function blankLists()
    {
        $start          = I('start', 0, 'intval');
        $pageSize       = I('limit', 20, 'intval');
        
        $filters        = I('filters', array());
        extract($filters);
        
        $userInfos = parent::get_session('user_info');
        $blankModel = $this->db['Blank'];
        $where = array(
            'user_id' => $userInfos['id'],
        );
        $page = ceil(($start + 1) / $pageSize);
        $lists = array('list' => 0, 'count' => 0);
        $lists = $blankModel->getList($where, 'id DESC', $page, $pageSize);
        $newList = array();
        if ($lists) {
            $cardType = array('支付宝', '银行卡');
            foreach ($lists['list'] AS $key => $info) {
                $info['typename'] = $cardType[$info['cardtype']];
                $info['date'] = date('Y-m-d H:i:s', $info['addtime']);
                $info['account'] = $info['card'];
                $temp = array(
                    'id' => $info['id'],
                    'cells' => $info,
                );
                $lists['list'][$key] = $temp;
                // $lists['list'][$key]['typename'] = $cardType[$info['cardtype']];
                // $lists['list'][$key]['date'] = date('Y-m-d H:i:s', $info['addtime']);
            }
        }
        
        parent::callback(1, '成功获取数据', array(
            'start' => floor($start/$pageSize) * $pageSize,
            'limit' => $pageSize,
            'total' => $lists['total'],
            'rows'  => $lists['list'],
        ), array(
            'code' => 1000
        ));
        exit;
    }
    
    /**
     * 增加支付帐号
     * 
     * @author lurongchang
     * @date   2014-10-24
     * @return void
     */
    function addBlank()
    {
        $id 		= I('id', 0, 'intval');
        $truename 	= I('truename', '', 'setString');
        $cardtype 	= I('cardtype', 0, 'intval');
        $account 	= I('account', '', 'setString');
        $bank 		= I('bank', '', 'setString');
        
        if (empty($truename)) {
            parent::callback(0, '真实姓名错误，请填写正确的真实姓名!');
        }
        
        import("@.Tool.Validate");
        $check = ($cartype == 0) ? Validate::checkEmail($account) : (strlen($account) == 16);
        if (empty($account) || !$check) {
            parent::callback(0, '支付帐号错误，支付宝请填写正确的邮箱，银行卡请填写16位号码!');
        }
        
        $userInfos = parent::get_session('user_info');
        $blankModel = $this->db['Blank'];
        if ($id) {
            $where = array(
                'id' => $id,
                'user_id' => $userInfos['id']
            );
            $datas = array(
                'cardtype'  => $cardtype,
                'card'      => $account,
                'truename'  => $truename,
                'bank'  	=> $bank,
            );
            $status = $blankModel->where($where)->save($datas);
            $status = ($status !== false) ? true : false;
        } else {
            $datas = array(
                'user_id'   => $userInfos['id'],
                'cardtype'  => $cardtype,
                'card'      => $account,
                'bank'  	=> $bank,
                'truename'  => $truename,
                'addtime'   => $_SERVER['REQUEST_TIME'],
            );
            $status = $blankModel->add($datas);
        }
        if ($status) {
            parent::callback(1, '提交成功', array(), array(
                'code'  => 1000
            ));
        } else {
            parent::callback(0, '数据保存错误, 请重新提交', array($status));
        }
    }
    
    /**
     * 删除支付帐号
     * 
     * @author lurongchang
     * @date   2014-10-24
     * @return void
     */
    function delBlank()
    {
        $id = I('id', 0, 'intval');
        
        if (empty($id)) {
            parent::callback(0, '帐号ID错误!');
        }
        
        $userInfos = parent::get_session('user_info');
        $where = array(
            'id'   => $id,
            'user_id'   => $userInfos['id'],
        );
        $blankModel = $this->db['Blank'];
        $status = $blankModel->where($where)->delete();
        if ($status) {
            parent::callback(1, 'success', array(), array(
                'code'  => 1000
            ));
        } else {
            parent::callback(0, '数据保存错误, 请重新提交', array($status));
        }
    }
}