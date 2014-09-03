<?php
class IndexAction extends HotelBaseAction{

	private $module_name = '客户管理';
    public function __construct() {
	
		parent::__construct();
	
		parent::global_tpl_view(array('module_name'=>$this->module_name));
	}
     protected $db = array(
        'WxUser'=>'WxUser'

     );
     
	  public function index(){
	  	$WxUser = $this->db['WxUser'];
	  	//$list = $WxUser->get_wx_user($this->oUser->id);
	 
	  	$list =$WxUser->get_all_wx_user($this->oUser->id);

//echo '<pre>';print_R($list);echo '</pre>';exit;
	  	$url = $_SERVER["REQUEST_URI"];
	  	$html = array('hover'=>parent::getAction($url),
	  		'list'=>$list,
	  	    'user_id'=>$this->oUser->id
	  	);
	    $this->assign('html',$html);
	    $this->display();
	  }
	  
	  
	  public function send_sms(){
	  	header("Content-type:text/html;charset=utf-8");
	    if(!$_SERVER["HTTP_REFERER"]){
	        $this->error('非法操作！');
	        exit;
        }
	  	import("@.Tool.SHP");
	  	$info = C('SHP');
	  	$SHP = new SHP($info['name'],$info['pwd']);
	  	//$SHP = new SHP('rikee','zyzylove2');
	  	if($this->isPost()){
           //备用IP地址为203.81.21.13
            $dst = $this->_post('dst');
            $msg = $this->_post('msg');
            echo $msg;exit;
            $arr = $SHP->send($dst,$msg,'',1);
            
			if($arr['status']==1 ){
				$this->success('发送成功！',U('Hotel/Index/index'));
			}else{
				$this->error('发送失败！');
			}
	  		
	  	}else{
	  		$html['phone'] = $this->_get('phone');
	  		$this->assign('html',$html);
	  		$this->display();
	  	}
	  	
	  	
	  }
	       


}