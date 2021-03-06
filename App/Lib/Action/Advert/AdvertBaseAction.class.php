<?php

/**
 * 后台核心类--所有后台方法必须继承此类
 */
class AdvertBaseAction extends AppBaseAction {

	
	protected  $is_check_rbac = true;		//当前控制是否需要验证RBAC
	
	protected  $not_check_fn = array();	//登陆后无需登录验证方法
	
	
	//构造方法
	public function __construct() {
		parent:: __construct();			//重写父类构造方法
		
		//全局系统变量
		$this->global_system();
		
		//初始化用户数据
		$this->check_system_info();

	}
	
	
	/**
	 * 全局系统用到的数据
	 */
	private function global_system () {
		
		/* SESSION信息验证保存 */
		$session_userinfo = parent::get_session('user_info');
		if (!empty($session_userinfo)) {
			$this->is_login = true;
			$this->oUser = (object) $session_userinfo;					//转换成对象
		}
		
		$path = preg_replace("/[\\\+]/", "/",dirname($_SERVER['SCRIPT_NAME']));
	
		//剩余资金
		$money_info = D('UserAdvertisement')->getMoney($this->oUser->id);
		
		//'money' => $moneyget['money'],
		//'freeze_funds' => $moneyget['freeze_funds'],
		//'all_price' => $moneyget['money'] + $moneyget['freeze_funds']
		
		//全局模板变量
		parent::global_tpl_view(array(
				'button'=>array(
						'prve'=>C('PREV_URL')
				),
				'path'=>'http://'.$_SERVER['SERVER_NAME'].$path.''.'Public/'.GROUP_NAME.'/',
				
				'group_name' =>GROUP_NAME,
				
				'module_name'=>MODULE_NAME,
				
				'action_name'=>ACTION_NAME,
				
				//网站当前分组资源路径
				'Group_Resource_Path'=>C('LocalHost').'/'.APP_PATH.'Public/'.GROUP_NAME.'/',
				
				//模块级页面路径
				'Module_Resource_Path'=>C('LocalHost').'/'.APP_PATH.'Public/'.GROUP_NAME.'/Module/'.MODULE_NAME.'/',
		
				'user_info' => array(
					'account' => $this->oUser->account,
					'money' => $money_info['money'],
					'freeze_funds' => $money_info['freeze_funds'],
					'all_price' => $money_info['money'] + $money_info['freeze_funds'],
					//'money' => $this->oUser->money,
					//'freeze_funds' => $this->oUser->freeze_funds,
					//'all_price' => $this->oUser->all_price,
				),
			));

	}
	
	
	//验证方法
	private function check_system_info () {
		
		if ($this->global_system['web_status'] == 0) {
			echo '对不起，网站已关闭';
			exit;
		}

		$check_result = $this->init_check($this->oUser);
		if ($check_result['status'] == false) {
			$this->redirect('Advert/Account/login');
			//alertBack($check_result['message']);
		}
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
	 * 大分类
	 */
	protected function big_type_urls ($big_type) {
		
		$data[0] = array(0=>'nav_cur');
		$data[1] = array(1=>'nav_cur');
		$data[2] = array(2=>'nav_cur');
		$data[3] = array(3=>'nav_cur');
		$data[4] = array(4=>'nav_cur');
		$data[5] = array(5=>'nav_cur');
		$data[6] = array(6=>'nav_cur');
		parent::global_tpl_view(array(
			'big_type_class' => $data[$big_type],	//
			
				//{:U('Advert/News/news_list')}
			/*
			'big_type_urls'=>array(
				0 => U('Advert/News/news_list'),	
				1 => U('Advert/Weixin/celebrity_weixin'),	
				2 => U('Advert/Weibo/celebrity_weibo',array('pt_type'=>1)),
				3 => U('Advert/Help/index'),
				4 => U('Advert/Money/index'),
				5 => U('Advert/Money/record')
			),
			*/
			'big_type_urls'=>array(
				0 => U('Advert/News/news_list'),
				1 => U('Advert/Weixin/celebrity_weixin'),
				2 => U('Advert/Weibo/celebrity_weibo',array('pt_type'=>1)),	
				3 => U('Advert/News/generalize_activity'),
				4 => U('Advert/Money/record'),
				5 => U('Advert/Member/datum_edit'),
				6 => U('Advert/Money/index')
			),
		));

		
	}
	
	
	/**
	 * 微博二级分类URL集合
	 */
	protected function two_urls ($big_type) {
		
		//新闻媒体
		$data[0] = array(
			0 => U('/Advert/News/news_list'),
			1 => U('/Advert/News/add_generalize'),
			2 => U('/Advert/News/generalize_activity'),
		);
		//微信
		$data[1] = array(
			0 => U('/Advert/Weixin/celebrity_weixin'),	
			1 => U('/Advert/Weixin/weixin'),	
			2 => U('/Advert/WeixinOrder/add_generalize'),					
			3 => U('/Advert/WeixinOrder/generalize_activity'),
			4 => U('/Advert/WeixinOrder/intention_list'),
			//4 => U('/Advert/WeixinOrder/add_intention'),	
		);
		//微博
		$data[2] = array(
			0 => U('/Advert/Weibo/celebrity_weibo',array('pt_type'=>1)),	//新浪名人微博
			1 => U('/Advert/Weibo/caogen_weibo',array('pt_type'=>1)),		//新浪草根微博
			2 => U('/Advert/Weibo/celebrity_weibo',array('pt_type'=>2)),	//腾讯名人微博
			3 => U('/Advert/Weibo/caogen_weibo',array('pt_type'=>2)),		//腾讯草根微博
			4 => U('/Advert/WeiboOrder/add_generalize'),					
			5 => U('/Advert/WeiboOrder/generalize_activity'),
			6 => U('/Advert/WeiboOrder/intention_list'),	
			//6 => U('/Advert/WeiboOrder/add_intention'),		
		);
		parent::data_to_view(array(	
			//二级导航	
			'sidebar_two_url'=> $data[$big_type],//URL		
		));
	}
	
}


?>