<?php

/**
 * 抽奖控制器
 */
class LuckyAction extends ApiBaseAction {
	
	/**
	 * 追加使用的数据表对象
	 * @var Array  当访问时，$this->db['Member']->query();
	 */
	protected $add_db = array(
		'Coupon'=>'Coupon',			//优惠券表
		'People' => 'People',			//中奖人表
		'CouponImg' => 'CouponImg'
	);
	
	/* 需要身份验证的方法名 */
	protected $Verify = array(
			//'apply',

	);
	
	
	public function __construct() {
		
		parent:: __construct();			//重写父类构造方法
		
		//$this->request['name'] = $this->_get('name');					//姓名
		//$this->request['phone'] = $this->_get('phone');				//手机号码
		//$this->request['coupon_id'] = $this->_get('coupon_id');		//奖项ID

		$this->request['name'] = $this->_post('name');					//姓名
		$this->request['phone'] = $this->_post('phone');				//手机号码	
		$this->request['coupon_id'] = $this->_post('coupon_id');		//奖项ID

	}
	
	
	
	//抽奖
	public function index () {

		if ($this->isPost() == false) {
			parent::callback(C('STATUS_ACCESS'),'非法访问！');
		}
		
		//链接数据库
		$Coupon = $this->db['Coupon'];
		$CouponImg = $this->db['CouponImg'];
		
		
		$Coupon_Status = C('Coupon_Status');
		$now_day = strtotime(date('Y-m-d',time()));

		$map['status'] = $Coupon_Status[0]['num'];
		$map['start_time'] = array('elt',$now_day);		//小于等于
		$map['over_time'] = array('egt',$now_day);		//大于等于
		$map['number'] = array('gt',0);		//大于
		
		$coupon_list = $Coupon->seek_all_data($map);

		if (empty($coupon_list)) parent::callback(C('STATUS_NOT_DATA'),'暂无奖项');
		
		//添加未中奖概率
		$probability = 100;
		foreach ($coupon_list AS $key=>$val){
			$probability -= $val['probability'];
		}
		array_push($coupon_list,array('id'=>0,'probability'=>$probability));


		foreach ($coupon_list as $key => $val) {
			$luck_arr[$val['id']] = $val['probability'];		//记录奖项的ID与中奖概率的关系
		}

		$rid = getRand($luck_arr); //根据概率获取奖项id

		$coupon_list = regroupKey($coupon_list,'id',true);

		$lucky_info = $coupon_list[$rid];		//中奖项目

		//抽中未中奖项目时
		if ($lucky_info['id'] == 0) parent::callback(C('STATUS_LUCKY_NO'),'没有中奖，再加把劲！');
		
		$lucky_img = $CouponImg->get_images(array('coupon_id'=>$lucky_info['id']));
		parent::public_file_dir($lucky_img,array('url'),'images/');

		$lucky_info['img'] = $lucky_img[0]['url'];

		if ($lucky_info['id'] != 1) {
			parent::callback(C('STATUS_LUCKY_YES'),'恭喜你中奖啦！',$lucky_info);
		} else {
			parent::callback(C('STATUS_LUCKY_NO'),'没有中奖，再加把劲！');
		}
	}
	

	/*
	public function index () {
		
		if ($this->isPost() == false) {
			//parent::callback(C('STATUS_ACCESS'),'非法访问！');
		}
		
		$Coupon = $this->db['Coupon'];
		$CouponImg = $this->db['CouponImg'];
		$Coupon_Status = C('Coupon_Status');
		$now_day = strtotime(date('Y-m-d',time()));

		$map['status'] = $Coupon_Status[0]['num'];
		$map['start_time'] = array('elt',$now_day);		//小于等于
		$map['over_time'] = array('egt',$now_day);		//大于等于
		$map['number'] = array('gt',0);		//大于

		$coupon_list = $Coupon->seek_all_data($map);

		$lucky_info = $coupon_list[mt_rand(0,count($coupon_list)-1)];
		
		if ($lucky_info) {
			$lucky_img = $CouponImg->get_images(array('coupon_id'=>$lucky_info['id']));
			parent::public_file_dir($lucky_img,array('url'),'images/');
			$lucky_info['img'] = $lucky_img[0]['url'];
			parent::callback(C('STATUS_LUCKY_YES'),'恭喜你中奖啦！',$lucky_info);
		} else {
			parent::callback(C('STATUS_LUCKY_NO'),'没有中奖，再加把劲！');
		}
		
	}
	*/



	//用户领奖
	public function get_lucky () {
		if ($this->isPost() == false) {
			parent::callback(C('STATUS_ACCESS'),'非法访问！');
		}
		$name = $this->request['name'];		//姓名
		$phone = $this->request['phone'];		//手机号码

		//验证
		if (empty($name)) parent::callback(C('STATUS_NOT_CHECK'),'姓名错误');
		if (!preg_match("/^1[358]\d{9}$/", $phone)) parent::callback(C('STATUS_NOT_CHECK'),'手机号码错误');


		//链接数据库
		$People = $this->db['People'];
		$Coupon = $this->db['Coupon'];

		//查询抽奖的奖项数据
	
		//写入数据
		$map2['name'] = $name;
		$map2['phone'] = $phone;
		$info = $People->get_one_people($map2);		
		if ($info == true) {
			parent::callback(C('STATUS_HAVE_DATA'),'您已经获取此奖品了！');
		} else {
			$People->create();
			$People->data($map2);
			$is_ok = $People->add_one_people();
			if ($is_ok == true) {
				parent::callback(C('STATUS_SUCCESS'),'奖品获取成功！');
			} else {
				parent::callback(C('STATUS_NOT_CHECK'),'获取失败请稍后重新尝试！');
			}
		}
		
	}



	/**
	//用户领奖
	public function get_lucky () {
		if ($this->isPost() == false) {
			parent::callback(C('STATUS_ACCESS'),'非法访问！');
		}
		$name = $this->request['name'];		//姓名
		$phone = $this->request['phone'];		//手机号码
		$coupon_id = $this->request['coupon_id'];	//想想ID

		//验证
		if (empty($name)) parent::callback(C('STATUS_NOT_CHECK'),'姓名错误');
		if (!preg_match("/^1[358]\d{9}$/", $phone)) parent::callback(C('STATUS_NOT_CHECK'),'手机号码错误');
		if (!is_numeric($coupon_id)) parent::callback(C('STATUS_NOT_CHECK'),'奖项错误，刷新后再尝试！');

		//链接数据库
		$People = $this->db['People'];
		$Coupon = $this->db['Coupon'];

		//查询抽奖的奖项数据
		$Coupon_Status = C('Coupon_Status');
		$now_day = strtotime(date('Y-m-d',time()));
		$map['status'] = $Coupon_Status[0]['num'];
		$map['start_time'] = array('elt',$now_day);		//小于等于
		$map['over_time'] = array('egt',$now_day);		//大于等于
		$map['number'] = array('gt',0);					//大于0
		$map['id'] = $coupon_id;
		$coupon_info = $Coupon->seek_one_coupon($map);
		if (empty($coupon_info)) parent::callback(C('STATUS_NOT_DATA'),'此奖项已下架！');

		//写入数据
		$map2['name'] = $name;
		$map2['phone'] = $phone;
		$map2['coupon_id'] = $coupon_id;
		$info = $People->get_one_people($map2);		
		if ($info == true) {
			parent::callback(C('STATUS_HAVE_DATA'),'您已经获取此奖品了！');
		} else {
			$People->create();
			$People->data($map2);
			$is_ok = $People->add_one_people();
			if ($is_ok == true) {
				$Coupon->where(array('id'=>$coupon_id))->setDec('number'); 
				parent::callback(C('STATUS_SUCCESS'),'奖品获取成功！');
			} else {
				parent::callback(C('STATUS_NOT_CHECK'),'获取失败请稍后重新尝试！');
			}
			
		}
		
	}
	*/
	
	

	//用于合成宝石类的
	private function luck_one () {
		//初始化数组
		$stone_arr = array(
				array( 'num' => 1, 'prob' => '50%' ),
				array( 'num' => 2, 'prob' => '16%' ),
				array( 'num' => 3, 'prob' => '2%' )
		);
		//随机获得一个幸运数字
		$luck_num = mt_rand( 0, 99 );
		//初始化几率区间和最终宝石生产数目
		$lucky_range = $made_num = 0;
		
		foreach( $stone_arr as $sa ){
			$prob = intval( $sa['prob'] );		//取最大值
			if( $luck_num >= $lucky_range && $luck_num < $lucky_range + $prob ){
				$made_num = $sa['num'];
				break;
			}
			else{
				$lucky_range += $prob;
			}
		}
		
		for( $i = 0; $i < $made_num; $i++ ){
				//生产宝石的逻辑
		}
	}
	
	
	
	//抽奖
	private function luck_two() {
		$prize_arr = array(
				'0' => array('id'=>1,'min'=>1,'max'=>29,'prize'=>'一等奖','v'=>1),
				'1' => array('id'=>2,'min'=>302,'max'=>328,'prize'=>'二等奖','v'=>2),
				'2' => array('id'=>3,'min'=>242,'max'=>268,'prize'=>'三等奖','v'=>5),
				'3' => array('id'=>4,'min'=>182,'max'=>208,'prize'=>'四等奖','v'=>7),
				'4' => array('id'=>5,'min'=>122,'max'=>148,'prize'=>'五等奖','v'=>10),
				'5' => array('id'=>6,'min'=>62,'max'=>88,'prize'=>'六等奖','v'=>25),
				'6' => array('id'=>7,'min'=>array(32,92,152,212,272,332),
				'max'=>array(58,118,178,238,298,358),
				'prize'=>'七等奖','v'=>50)
		);
		
		foreach ($prize_arr as $key => $val) {
			$arr[$val['id']] = $val['v'];		//记录奖项的ID与中奖概率的关系
		}
	
		$rid = $this->getRand($arr); //根据概率获取奖项id

		//中奖项
		$res = $prize_arr[$rid-1];
		$min = $res['min'];
		$max = $res['max'];
		if($res['id']==7){ //七等奖
			$i = mt_rand(0,5);
			$result['angle'] = mt_rand($min[$i],$max[$i]);
		}else{
			$result['angle'] = mt_rand($min,$max); //随机生成一个角度
		}
		$result['prize'] = $res['prize'];
		
		dump($result);
		echo json_encode($result);

	}
	
	

	
}

?>