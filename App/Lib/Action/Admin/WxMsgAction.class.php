<?php
/**
 * 语义分析系统
 */
class WxMsgAction extends AdminBaseAction {
	//控制器说明
	private $module_name = '图文消息管理';
	
	//初始化数据库连接
	protected  $db = array(
		'WxMsg'=>'WxMsg',		//消息表
	);
	
	
	//酒店星级
	private $msg_use_state = array(
		4=> array(
			'num'=>4,
			'explain'=>'不使用'
		),
		1=> array(
			'num'=>1,
			'explain'=>'特价酒店'
		),
		2=> array(
			'num'=>2,
			'explain'=>'预定送免房'
		),
		3=> array(
			'num'=>3,
			'explain'=>'订房返红包'
		),
	
	);

	//消息类别
	private $msg_type = array(
		1=> array(
			'num'=>1,
			'explain'=>'酒店'
		),
		2=> array(
			'num'=>2,
			'explain'=>'城市'
		)
	);

	/**
	 * 构造方法
	 */
	public function __construct() {
	
		parent::__construct();
	
		parent::global_tpl_view(array('module_name'=>$this->module_name));
		
	}
	
	
	
	//语义列表
	public function index () {		
		//连接数据库
		$WxMsg = $this->db['WxMsg'];
		$use_state = $this->_get('use_state');

		//数据列表
		if (!empty($use_state)) {
			$condition =array('use_state'=>$use_state);
			$msg_list = $WxMsg->seek_all_data($condition);
		}else{
			$msg_list = $WxMsg->seek_all_data();
		}

		if ($msg_list == true) {
			foreach ($msg_list as $key=>$val) {
				$msg_list[$key]['use_state']= $this->msg_use_state[$val['use_state']]['explain'];
				$msg_list[$key]['type']= $this->msg_type[$val['type']]['explain'];
			}
		}
		
		
	
		parent::global_tpl_view( array(
			'add_name' =>'添加消息',
			'action_name'=>'',
			'title_name'=>'所有消息',
		));
		
		$html['list'] = $msg_list;
		$this->assign('html',$html);
		$this->display();
	}
	
	private function upload(){
		
		$path = 'Public/Uploads/';

		//导入图片上传类  
		import("ORG.Net.UploadFile");  
		//实例化上传类  
		$upload = new UploadFile();  
		$upload->maxSize = 1048576;  
		//设置文件上传类型  
		$upload->allowExts = array('jpg','gif','png','jpeg');  
		//设置文件上传位置  
		$upload->savePath = $path; 
		//设置文件上传名(按照时间)  
		$upload->saveRule = "time";  
		if (!$upload->upload()){  
			 return  0;  
		}else{  
			//上传成功，获取上传信息  
			$info = $upload->getUploadFileInfo(); 
			$savename = $info[0]['savename'];
			$imgurl['path'] = $path;//这里是设置文件的url注意使用.不是+ 
			$imgurl['savename'] =$savename;
			return  $imgurl;
		}  
		
	}

	/*
	 * $url 图片名称
	 * $filepath 保存图片文件夹地址
	 */
	private function getimg($name,$path) {
		if ($name == '') {
			return false;
		}
		//判断路经是否存在
		!is_dir($path)?mkdir($path):null;

		$filename='cl'.$name;
		// 最大宽高
		$width = 200;
		$height = 200;
		// 获取图片宽高
		list($width_orig, $height_orig) = getimagesize($path.$name);
		// 改变大小。和上例一样。
		$image_p = imagecreatetruecolor($width, $height);
		$image = imagecreatefromjpeg($path.$name);
		imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);
		
		
		//写入图片到指定的文本
		imagejpeg($image_p, $path.$filename);
		return $path.$filename;
	}


	public function edit () {
		$act = $this->_get('act');						//操作类型
		$WxMsg = $this->db['WxMsg'];
		$msg_id = $this->_get('msg_id');  
		

		if ($act == 'add') {								//添加
			if ($this->isPost()) {
				$imgurl =$this ->upload();
				$imgurl_xiaotu=$this->getimg($imgurl['savename'],$imgurl['path']);
				$WxMsg->create();
				if($imgurl!='0'){
					$WxMsg->pic_url = $imgurl['path'].$imgurl['savename'];
					$WxMsg->pic_url_xiao = $imgurl_xiaotu;
				}
				$id = $WxMsg->add();
				$id ? $this->success('添加成功！',U('Admin/WxMsg/index')) : $this->error('添加失败请重新尝试！');
				exit;
			}
			//表单标题
			$title_name = '添加消息';
		
		} else if ($act == 'update') {			//修改
			if ($this->isPost()) {
				$imgurl =$this ->upload();
				$imgurl_xiaotu=$this->getimg($imgurl['savename'],$imgurl['path']);
				$WxMsg->create();
				if($imgurl!='0'){
					$WxMsg->pic_url = $imgurl['path'].$imgurl['savename'];
					$WxMsg->pic_url_xiao = $imgurl_xiaotu;
				}
				$WxMsg->save_one_data($msg_id) ? $this->success('修改成功！') : $this->error('没有做出任何修改！');
				exit;
			}
			//查找
			$info = $WxMsg->seek_one_data(array('id'=>$msg_id));

			if (empty($info)) $this->error('您编辑的消息不存在！');
			$title_name = $info['keyword'].'---编辑';
			$html = $info;
			
		
		} else if ($act == 'delete') {			//删除
			$WxMsg->del_one_data($msg_id) ? $this->success('删除成功！') : $this->error('删除失败，请稍后重试！');
			exit;
		}
		
		parent::global_tpl_view( array(
				'action_name'=>'编辑',
				'title_name'=>$title_name,
		));

		$html['msg_type'] = $this->msg_type;
		$html['msg_use_state']= $this->msg_use_state;
		$this->assign('html',$html);
		$this->display();
	}

	
	

    
}