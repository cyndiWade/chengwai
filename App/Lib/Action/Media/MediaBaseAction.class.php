<?php

/**
 * 后台核心类--所有后台方法必须继承此类
 */
class MediaBaseAction extends AppBaseAction {

	
	protected  $is_check_rbac = true;		//当前控制是否需要验证RBAC
	
	protected  $not_check_fn = array();	//登陆后无需登录验证方法
	
	//构造方法
	public function __construct() {
		parent:: __construct();			//重写父类构造方法
		
		//全局系统变量
		$this->global_system();
		
		//初始化用户数据
		$this->check_system_info();
		
		$this->getHeaderInfo();
	}
	
	
	/**
	 * 全局系统用到的数据
	 */
	private function global_system () {
		$path = preg_replace("/[\\\+]/", "/",dirname($_SERVER['SCRIPT_NAME']));
		//全局模板变量
		parent::global_tpl_view(array(
				'button'=>array(
						'prve'=>C('PREV_URL')
				),
				'path'=>'http://'.$_SERVER['SERVER_NAME'].$path.''.'/Public/'.GROUP_NAME.'/',
				
				'group_name' =>GROUP_NAME,
				
				'module_name'=>MODULE_NAME,
				
				'action_name'=>ACTION_NAME,
				
				//网站当前分组资源路径
				'Group_Resource_Path'=> '/'. APP_PATH.'Public/'.GROUP_NAME.'/',
				
				//模块级页面路径
				'Module_Resource_Path'=> '/'. APP_PATH.'Public/'.GROUP_NAME.'/Module/'.MODULE_NAME.'/',
		));
	
	
		/* SESSION信息验证保存 */
		$session_userinfo = parent::get_session('user_info');
		
		if (!empty($session_userinfo)) {
			$this->is_login = true;
            parent::global_tpl_view(array(
                'oUser' => $session_userinfo
            ));
			$this->oUser = (object) $session_userinfo;					//转换成对象
		}
	}
	
	
	//验证方法
	private function check_system_info () {
		
		if ($this->global_system['web_status'] == 0) {
			echo '对不起，网站已关闭';
			exit;
		}
		
		$check_result = $this->init_check($this->oUser);
		if ($check_result['status'] == false) $this->error($check_result['message']);
	}
	


	
	/**
	 * 上传文件
	 * @param Array    $file  $_FILES['pic']	  上传的数组
	 * @param String   $type   上传文件类型    pic为图片
	 * @return Array	  上传成功返回文件保存信息，失败返回错误信息
	 */
	protected function upload_file($file,$dir,$size= 3145728,$type=array('jpg', 'gif', 'png', 'jpeg')) {
		import('@.ORG.Util.UploadFile');				//引入上传类
	
		$upload = new UploadFile();
		$upload->maxSize  =  $size;					// 设置附件上传大小
		$upload->allowExts  = $type;				// 上传文件的(后缀)（留空为不限制），，
		//上传保存
		$upload->savePath =  $dir;					// 设置附件上传目录
		$upload->autoSub = true;					// 是否使用子目录保存上传文件
		$upload->subType = 'date';					// 子目录创建方式，默认为hash，可以设置为hash或者date日期格式的文件夹名
		$upload->saveRule =  'uniqid';				// 上传文件的保存规则，必须是一个无需任何参数的函数名

		//执行上传
		$execute = $upload->uploadOne($file);
		//执行上传操作
		if(!$execute) {						// 上传错误提示错误信息
			return array('status'=>false,'info'=>$upload->getErrorMsg());
		}else{	//上传成功 获取上传文件信息
			return array('status'=>true,'info'=>$execute);
		}
	}
	
	
	/**
	 * 短信验证模块
	 * @param String $telephone		//验证的手机号码
	 * @param Number $type				//验证类型：1为注册验证
	 */
	protected function check_verify ($telephone,$type,$verify) {
	
		$Verify = $this->db['Verify'];
		$verify_code = $verify;		//短信验证码
	
		$shp_info = $Verify->seek_verify_data($telephone,$type);
	
		//手机验证码验证
		if (empty($shp_info)) {
			return array('status'=>false,'msg'=>'验证码不存在');
		} elseif ($verify_code != $shp_info['verify']) {
			return array('status'=>false,'msg'=>'验证码错误');
		} elseif ($shp_info['expired'] - time() < 0 ) {
			return array('status'=>false,'msg'=>'验证码已过期');
		}
		//把验证码状态变成已使用
		$Verify->save_verify_status($shp_info['id']);
		return array('status'=>true,'msg'=>'验证通过');
	}
	
	/**
	 * 获取header头部公共数据
	 * 
	 * @author lurongchang
	 * @date   2014-11-16
	 * @return void
	 */
	private function getHeaderInfo()
	{
		// 平台数量
		$where = array(
			'users_id' => $this->oUser->id
		);
		$newsNums = M('AccountNews')->where($where)->count();
		$weiboNums = M('AccountWeibo')->where($where)->count();
		$weixinNums = M('AccountWeixin')->where($where)->count();
		$this->typeNums = ($newsNums ? 1 : 0) + ($weiboNums ? 1 : 0) + ($weixinNums ? 1 : 0);
		
		// 昨日订单数
		$yesterday = strtotime(date('Y-m-d', $_SERVER['REQUEST_TIME'] - 86400));
		$where = array(
			'users_id' => $this->oUser->id,
			'create_time' => array('BETWEEN', array($yesterday, $yesterday +86399))
		);
		$newsOrderNums =  M('GeneralizeNewsOrder')->where($where)->count();
		$weiboOrderNums =  M('GeneralizeOrder')->where($where)->count();
		$weixinOrderNums =  M('GeneralizeWeixinOrder')->where($where)->count();
		$this->yesterdayOrder = $newsOrderNums + $weiboOrderNums + $weixinOrderNums;
	}
}