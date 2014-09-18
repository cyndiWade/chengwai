<?php

/**
 * 微博账号
 */
class WeiboAction extends AdvertBaseAction {
	
	
	//每个类都要重写此变量
	protected  $is_check_rbac = true;		//是否需要RBAC登录验证
	
	protected  $not_check_fn = array();	//无需登录和验证rbac的方法名
	
	//控制器说明
	private $module_explain = '微博账号';
	
	private $caogen_Top_Tags_ParentId = 293;	//草根分类顶层
	
	private $celebrity_Top_Tags_ParentId = 252;	//名人分类顶层
	
	private $now_classify_data = array();	//当前分类数据

	private $pt_type;	//平台类型
	
	private $big_type = 2;	//当前平台大分类
	
	private $pt_page_data;	//平台类型的数据
	
	//初始化数据库连接
	protected  $db = array(
			'CategoryTags'=>'CategoryTags',
			'Users' => 'Users',
			'AccountWeibo' => 'AccountWeibo',
			'GrassrootsWeibo' => 'GrassrootsWeibo',
			'CeleprityindexWeibo' => 'CeleprityindexWeibo',
			'BlackorcollectionWeibo' => 'BlackorcollectionWeibo'
	);
	
	//和构造方法
	public function __construct() {
		parent::__construct();
		
		$this->_init_data();
	}
	
	//初始化需要的数据
	private function _init_data () {
		parent::global_tpl_view(array(
			'module_explain'=>$this->module_explain,
		));
		
		parent::big_type_urls($this->big_type);		//大分类URL
		
		//初始化URL
		parent::two_urls($this->big_type);			//微博二级分类URL
		
		$this->pt_type = $this->_get('pt_type');	//平台类型
		
		$this->pt_page_data = array(
			1 => array(
				'pt_type'=>1,
				'pt_name'=> '新浪'
			),
			2 => array(
				'pt_type'=>2,
				'pt_name'=> '腾讯'
			),
		);
		parent::data_to_view(array(
			'pt_info'=>$this->pt_page_data[$this->pt_type]
		));
		
		
	}
	

	//名人微博
	public function celebrity_weibo () {
		
		$this->show_celebrity_category_tags();
		if ($this->pt_type == 1) {
			$show_num = 0;
		} elseif ($this->pt_type == 2) {
			$show_num = 2;
		}
		parent::data_to_view(array(
			//二级导航属性
			'sidebar_two'=>array($show_num=>'select',),//第一个加依次类推
		));
		$this->display();
	}
	
	
	//草根微博
	public function caogen_weibo() {
		//显示常见分类
		$this->show_caogen_category_tags();
		if ($this->pt_type == 1) {
			$show_num = 1;
		} elseif ($this->pt_type == 2) {
			$show_num = 3;
		}
		parent::data_to_view(array(
			//二级导航属性
			'sidebar_two'=>array($show_num=>'select'),//第一个加
		));
		$this->display();
	}
	
	//草根控制器
	//获得草根 新浪,腾讯微博 列表数据 0
	public function get_grassroots_list()
	{
		//草根 新浪 1 或者 腾讯 2
		$pt_type = intval($_POST['pt_type']);
		if(!empty($pt_type))
		{
			$list_new = $this->db['GrassrootsWeibo']->getPostArray($_POST,$pt_type,$this->oUser->id);
			parent::callback(1,'获取成功所有',array('list'=>$list_new['list'],'count'=>$list_new['count'],'p'=>$list_new['p']));
		}
	}

	//名人控制器
	//获得名人 新浪 腾讯微博 数据列表 1
	public function get_celeprity_list()
	{
		//名人 新浪 1 或者 腾讯 2
		$pt_type = intval($_POST['pt_type']);
		if(!empty($pt_type))
		{
			$list_new = $this->db['CeleprityindexWeibo']->getPostArry($_POST,$pt_type);
			parent::callback(1,'获取成功所有',array('list'=>$list_new['list'],'count'=>$list_new['count'],'p'=>$list_new['p']));
		}
	}

	//拉黑
	public function insert_blackorcollection()
	{
		$bool = $this->db['BlackorcollectionWeibo']->insertBlackorcollection($_POST,$this->oUser->id);
		if($bool)
		{
			if($_POST['or_type']==0)
			{
				parent::callback(1,'拉入黑名单成功!','ok');
			}else{
				parent::callback(1,'收藏成功!','ok');
			}
		}else{
			if($_POST['or_type']==0)
			{
				parent::callback(0,'拉入黑名单失败,数据已存在!','no');
			}else{
				parent::callback(0,'收藏失败,数据已存在!','no');
			}
		}
		
	}


	/**
	 * 草根导航分类数据
	 */
	private function show_caogen_category_tags () {
		$CategoryTags = $this->db['CategoryTags'];
		$this->now_classify_data = $CategoryTags->get_classify_data($this->caogen_Top_Tags_ParentId);
		
		$this->cjfl = $this->now_classify_data[295];
		$this->jg = $this->now_classify_data[296];
		$this->fans_num = $this->now_classify_data[297];
		$this->fans_sex = $this->now_classify_data[298];
		$this->zfjg_type = $this->now_classify_data[421];
		
		$data['cjfl'] = $this->cjfl;
		$data['jg'] = $this->jg;
		$data['fans_num'] = $this->fans_num;
		$data['fans_sex'] = $this->fans_sex;
		$data['zfjg_type'] = $this->zfjg_type;
		parent::data_to_view($data);
	}
	
	
	/**
	 * 名人导航分类数据
	 */
	private function show_celebrity_category_tags () {
		$CategoryTags = $this->db['CategoryTags'];
		$this->now_classify_data = $CategoryTags->get_classify_data($this->celebrity_Top_Tags_ParentId);
	
		$this->mrzy = $this->now_classify_data[21];	//名人职业
		$this->mtly = $this->now_classify_data[22];	//名人领域
		$this->ckbj_type = $this->now_classify_data[426];	//参考报价类型
		$this->jg = $this->now_classify_data[95];	//价格
		$this->dfmr_mt = $this->now_classify_data[106];		//地方名人媒体
		$this->xqbq = $this->now_classify_data[118];	//兴趣标签

		
		$data['mrzy'] = $this->mrzy;
		$data['mtly'] = $this->mtly;
		$data['ckbj_type'] = $this->ckbj_type;
		$data['jg'] = $this->jg;
		$data['dfmr_mt'] = $this->dfmr_mt;
		$data['xqbq'] = $this->xqbq;
		
		parent::data_to_view($data);
	}
	
	

    
}

?>