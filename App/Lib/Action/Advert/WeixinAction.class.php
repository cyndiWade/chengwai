<?php

/**
 * 微信公众号
 */
class WeixinAction extends AdvertBaseAction {
	
	
	//每个类都要重写此变量
	protected  $is_check_rbac = false;		//是否需要RBAC登录验证
	
	protected  $not_check_fn = array();	//无需登录和验证rbac的方法名
	
	private $pt_type;	//平台类型
	
	private $big_type = 1;	//当前平台大分类
	
	private $caogen_Top_Tags_ParentId = 251;	//草根微信账号顶层ID
	
	private $celebrity_Top_Tags_ParentId = 445;	//名人分类顶层 
	
	private $now_classify_data;
	
	//控制器说明
	private $module_explain = '微信账号';
	
	//初始化数据库连接
	protected  $db = array(
			'CategoryTags'=>'CategoryTags',
			'Users' => 'Users',
			'CeleprityindexWeixin' => 'CeleprityindexWeixin',
			'GrassrootsWeixin' => 'GrassrootsWeixin',
			'BlackorcollectionWeixin' => 'BlackorcollectionWeixin'
	);
	
	//和构造方法
	public function __construct() {
		parent::__construct();
		
		$this->_init_data();
	}
	
	
	private function _init_data () {
		parent::global_tpl_view(array(
				'module_explain'=>$this->module_explain,
		));
		
		parent::big_type_urls($this->big_type);		//大分类URL
		
		//初始化URL
		parent::two_urls($this->big_type);			//微博二级分类URL
		
		//初始化URL
		//parent::weixin_urls();
	}
	
	
	//微信名人列表页
	public function celebrity_weixin () {
		$this->show_celebrity_category_tags();
		parent::data_to_view(array(
					
			//二级导航加样式
			'sidebar_two'=>array(
				0=>'select',//第一个加
			)
		));
		$this->display();
	}
	
	
	//微信
	public function weixin() {
		$this->show_caogen_category_tags();
		parent::data_to_view(array(
			
			//二级导航加样式
			'sidebar_two'=>array(
				1=>'select',//第一个加
			)
		));
		$this->display();
		
	}
	
	
	/**
	 * 草根导航分类数据
	 */
	private function show_caogen_category_tags () {
		$CategoryTags = $this->db['CategoryTags'];
		
		$tags_ids = C('Big_Nav_Class_Ids.weixin_caogen_tags_ids');

		$this->now_classify_data = $CategoryTags->get_classify_data($tags_ids['top_parent_id']);
	
		$data['cjfl'] = $this->now_classify_data[$tags_ids['cjfl']];	
		$data['zfjg_type'] = $this->now_classify_data[$tags_ids['zfjg_type']];
		$data['jg'] = $this->now_classify_data[$tags_ids['jg']];
		
		$data['fans_num'] = $this->now_classify_data[$tags_ids['fans_num']];
		$data['sprz'] = $this->now_classify_data[$tags_ids['sprz']];	
		
		$data['zhsfrz'] = $this->now_classify_data[$tags_ids['zhsfrz']];
		$data['szxb'] = $this->now_classify_data[$tags_ids['szxb']];
		$data['fsrzsj'] = $this->now_classify_data[$tags_ids['fsrzsj']];
		$data['zpjyds'] = $this->now_classify_data[$tags_ids['zpjyds']];
		parent::data_to_view($data);
	}
	
	/**
	 * 名人导航分类数据
	 */
	private function show_celebrity_category_tags () {
		$CategoryTags = $this->db['CategoryTags'];
		
		$tags_ids = C('Big_Nav_Class_Ids.weixin_celebrity_tags_ids');
		
		$this->now_classify_data = $CategoryTags->get_classify_data($tags_ids['top_parent_id']);
	
		$data['mrzy'] = $this->now_classify_data[$tags_ids['mrzy']];	//名人职业
		$data['mtly'] = $this->now_classify_data[$tags_ids['mtly']];	//名人领域
		$data['ckbj_type'] = $this->now_classify_data[$tags_ids['ckbj_type']];	//参考报价类型
		$data['jg'] = $this->now_classify_data[$tags_ids['jg']];		//价格
		$data['dfmr_mt'] = $this->now_classify_data[$tags_ids['dfmr_mt']];		//地方名人媒体
		$data['xqbq'] = $this->now_classify_data[$tags_ids['xqbq']];	//兴趣标签
	
		$data['mr_mtlb'] = $this->now_classify_data[$tags_ids['mr_mtlb']];	//名人/媒体类别
		$data['phd'] = $this->now_classify_data[$tags_ids['phd']];	//配合度
		$data['mr_fans_num'] = $this->now_classify_data[$tags_ids['mr_fans_num']];	//名人粉丝数
		$data['zhyc'] = $this->now_classify_data[$tags_ids['zhyc']];	//是否支持原创
		$data['mrxb'] = $this->now_classify_data[$tags_ids['mrxb']];	//名人性别
	
		parent::data_to_view($data);
	}

    	
    //微信草根 或者 名人 接口
    public function get_weixin_list()
    {
    	//判断是名人还是草根
		$is_celeprity = intval($_POST['is_celeprity']);
		if($is_celeprity==0)
		{
			$list_new = $this->db['GrassrootsWeixin']->getPostArray($_POST,$this->oUser->id);
			parent::callback(1,'获取成功所有',array('list'=>$list_new['list'],'count'=>$list_new['count'],'p'=>$list_new['p']));
		}else{
			$list_new = $this->db['CeleprityindexWeixin']->getPostArray($_POST,$this->oUser->id);
			parent::callback(1,'获取成功所有',array('list'=>$list_new['list'],'count'=>$list_new['count'],'p'=>$list_new['p']));
		}
    }

    //微信拉黑或者收藏
    public function get_weixin_borc()
    {
    	$bool = $this->db['BlackorcollectionWeixin']->insertBlackorcollection($_POST,$this->oUser->id);
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


}

?>