<?php
/**
 * 优惠券
 */
class CouponAction extends AdminBaseAction {
	//控制器说明
	private $module_name = '优惠管理';
	
	//初始化数据库连接
	protected  $db = array(
		'Coupon'=>'Coupon',					//优惠管理
		'CouponImg' => 'CouponImg',		//优惠图片
	);
	
	//优惠券类型
	private $coupon_type;
	
	//上下架状态类型
	private $coupon_status;
	
	//图片类型
	private $coupon_img_type;
	
	
	

	/**
	 * 构造方法
	 */
	public function __construct() {
	
		parent::__construct();
	
		parent::global_tpl_view(array('module_name'=>$this->module_name));
		
		$this->coupon_type = C('Coupon_Type');
		$this->coupon_status = C('Coupon_Status');
		$this->coupon_img_type = C('Coupon_Img_Type');
		
		
	}
	
	//验证
	private function check_data() {
		import('@.Tool.Validate');
	
		$start_time = $this->_post('start_time');
		$over_time = $this->_post('over_time');
		$now_data = date('Y-m-d',time());
	
		if(Validate::check_date_differ($start_time,$over_time)) $this->error('开始日期不得大于结束日期');
		if(Validate::check_date_differ($now_data,$start_time)) $this->error('开始日期不得小于当天日期');
	
	}
	
	
	//优惠券列表
	public function index () {
		$get = $this->_get();
		$Coupon = $this->db['Coupon'];
		$list = $Coupon->seek_all_data();
		
		if ($list == true) {
			foreach ($list as $key=>$val) {
				$list[$key]['type'] = $this->coupon_type[$val['type']]['explain'];
				$list[$key]['status'] = $this->coupon_status[$val['status']]['explain'];
			}
		}

		parent::global_tpl_view( array(
			'action_name'=>'优惠券列表',
			'title_name'=>'优惠券列表',
		));
		
		$html['list'] = $list;
		$this->assign('html',$html);
		$this->display();
	}
	
	
	//优惠券编辑
	public function coupon_edit () {
		$act = $this->_get('act');						//操作类型
		$Coupon = $this->db['Coupon'];
		$coupon_id = $this->_get('coupon_id');		
		
		if ($act == 'add') {								//添加
			if ($this->isPost()) {
				$this->check_data();
				$Coupon->create();
				$id = $Coupon->add_one_coupon();
				$id ? $this->success('添加成功！',U('Admin/Coupon/coupon_img',array('coupon_id'=>$id))) : $this->error('添加失败请重新尝试！');
				exit;
			}

			//表单标题
			$title_name = '添加优惠';
		
		} else if ($act == 'update') {			//修改
			if ($this->isPost()) {
				$Coupon->create();
				$Coupon->save_one_coupon($coupon_id) ? $this->success('修改成功！') : $this->error('没有做出任何修改！');
				exit;
			}
			//查找房型
			$info = $Coupon->seek_one_coupon(array('id'=>$coupon_id));
			if (empty($info)) $this->error('您编辑的优惠券不存在！');
			$title_name = $info['title'].'---编辑';
			$html = $info;
				
		} else if ($act == 'delete') {			//删除
			$Coupon->del_one_data($coupon_id) ? $this->success('删除成功！') : $this->error('删除失败，请稍后重试！');
			exit;
		}
		
		
		$html['coupon_type'] = $this->coupon_type;
		$html['coupon_status'] = $this->coupon_status;
		parent::global_tpl_view( array(
				'action_name'=>'编辑',
				'title_name'=>$title_name,
		));
		$this->assign('html',$html);
		$this->display();
	}
	
	
	
	//优惠券图片
	public function coupon_img () {
		$coupon_id = $this->_get('coupon_id');
		$Coupon = $this->db['Coupon'];
		$CouponImg = $this->db['CouponImg'];	

		//检测
		if (empty($coupon_id)) $this->error('非法操作！');
		$info = $Coupon->seek_one_coupon(array('id'=>$coupon_id),'id,title');
		if (empty($info)) $this->error('此优惠券不存在！');
		
		//获取酒店图片数据
		$photo_list = $CouponImg->get_hotel_images(array('coupon_id'=>$coupon_id),'id,type,url');
		if (!empty($photo_list)) {
			parent::public_file_dir($photo_list, array('url'), 'images/');		//组合访问地址
			$photo_type_list = regroupKey($photo_list,'type');						//按照图片类似分类
		}
		
		
		//注入模板
		$html['coupon_id'] = $info['id'];
		$html['img_type'] = $this->coupon_img_type;
		$html['photo_type_list'] = $photo_type_list;

		parent::global_tpl_view( array(
				'action_name'=>'优惠券图片',
				'title_name' => $info['title'].'--上传图片'
		));
		
		$this->assign('html',$html);
		$this->display();
	}
	
	
	/**
	 * AJAX处理上传图片
	 */
	public function ajax_photo_upload() {
		header('Content-Type:text/html;charset=utf-8');
	
		if ($this->isPost()) {
			/* 上传文件目录 */
			$upload_dir = C('UPLOAD_DIR');
			$dir = $upload_dir['web_dir'].$upload_dir['image'];		//图片文件保存地址
			$CouponImg  = $this->db['CouponImg'];		//酒店图片表
	
			/* 执行上传 */
			$file = $_FILES['photo_files'];					//上传的文件
			$coupon_id = $this->_post('coupon_id');				
			$type = $this->_post('type');						//图片类型
	
			/* 参数验证 */
			if (empty($coupon_id) || empty($type)) parent::callback(C('STATUS_DATA_LOST'),'参数错误！');
	
			/* 执行上传 */
			$result = parent::upload_file($file, $dir,5120000);
	
			/* 上传结果处理 */
			if ($result['status'] == true) {
				$CouponImg->coupon_id = $coupon_id;
				$CouponImg->type = $type;
				$CouponImg->url = $result['info'][0]['savename'];
				$hotel_img_id = $CouponImg->add();		//写入数据库
	
				if ($hotel_img_id) {
					$return['success'] = true;
					$return['info'] = '保存成功';
					echo json_encode($return);
				} else {
					$return['success'] = false;
					$return['info'] = '保存失败';
					echo json_encode($return);
				}
			} else {
				$return['success'] = false;
				$return['info'] = '上传失败';
				echo json_encode($return);
			}
	
		} else {
			parent::callback(C('STATUS_ACCESS'),'非法访问！');
		}
	
	}
	
	
	/**
	 * AJAX删除图片
	 */
	public function ajax_photo_remove () {
		if ($this->isPost()) {
			$id = $this->_post('id');
			$CouponImg = $this->db['CouponImg'];		//酒店图片表
			$CouponImg->del_one_image($id) ? parent::callback(C('STATUS_SUCCESS'),'删除成功') : parent::callback(C('STATUS_UPDATE_DATA'),'删除失败') ;
		} else {
			parent::callback(C('STATUS_ACCESS'),'非法访问！');
		}
	}

}