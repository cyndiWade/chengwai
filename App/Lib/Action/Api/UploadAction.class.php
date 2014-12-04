<?php

/**
 * 短信发送类
 */
class UploadAction extends ApiBaseAction {
	
	
	protected  $is_check_rbac = false;		//当前控制是否需要验证RBAC
	
	protected  $not_check_fn = array();		//登陆后无需登录验证方法
	
	private $Arr_file;
	
	//和构造方法
	public function __construct() {
		parent::__construct();
		
		$this->Void_init_data();
	}
	
	//初始化数据库连接
	protected  $db = array(
	
	);
	
	
	private function Void_init_data () {
		
		
		$this->Arr_file = $_FILES['upload'];
	}
	
	
	//上传图片
	public function image () {
		//上传地址
		$upload_dir = C('UPLOAD_DIR');
		$dir = $upload_dir['web_dir'].$upload_dir['image'];	
		
		//域名、文件目录
		$public_file_dir =  C('PUBLIC_VISIT.domain').C('PUBLIC_VISIT.dir').'images/';			
		$_ckefn = $_GET['CKEditorFuncNum'];//ckeditor 图片接收
		
		$Arr_result = parent::upload_file($this->Arr_file,$dir,5120000);
		if ($Arr_result['status'] == true) {
			$_path =  $public_file_dir.$Arr_result['info'][0]['savename'];
			echo "<script type='text/javascript'>window.parent.CKEDITOR.tools.callFunction($_ckefn,\"$_path\",'图片上传成功!');</script>";
		} else {
			alertBack('请再重试一次');
		}

	}
	
	
}

?>