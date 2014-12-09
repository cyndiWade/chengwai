<?php

/**
 * 社交帐号控制器
 */
class SocialAccountAction extends MediaBaseAction {
	
	//每个类都要重写此变量
	protected  $is_check_rbac = true;		//是否需要RBAC登录验证
	
	protected  $not_check_fn = array();	//无需登录和验证rbac的方法名
	
	//控制器说明
	private $module_explain = '帐号管理';


	//初始化数据库连接
	protected $db = array(
        'AccountNews'   => 'AccountNews',
        'AccountWeibo'  => 'AccountWeibo',
        'AccountWeixin' => 'AccountWeixin',
        'Region'        => 'Region',
        'CategoryTags'  => 'CategoryTags',
        'CeleprityindexWeibo'   => 'CeleprityindexWeibo',
        'CeleprityindexWeixin'  => 'CeleprityindexWeixin',
        'GrassrootsWeibo'   => 'GrassrootsWeibo',
        'GrassrootsWeixin'  => 'GrassrootsWeixin',
	);
	
	//和构造方法
	public function __construct() {
		parent::__construct();
		parent::global_tpl_view(array(
            'module_explain'    => $this->module_explain,
            'module_url'        => U('/Media/SocialAccount/manager')
        ));
	}
    
    /**
     * 社交帐号管理
     * 
     * @author lurongchang
     * @date   2014-09-19
     * @return void
     */
	public function manager()
    {
        $type = I('type', 0, 'intval');
        
        if (empty($type)) {
            redirect(U('/Media/SocialAccount/manager', array('type' => 1)));
        }
        
        $userInfos = parent::get_session('user_info');
        $where['users_id'] = &$userInfos['id'];
        $where['is_del']   = 0;
        // 微博
        $weiboList = $this->db['AccountWeibo']->where($where)->select();
        $sinaWeiboNums = 0;
        $tencentWeiboNums = 0;
        if ($weiboList) {
            foreach ($weiboList AS $val) {
                if ($val['pt_type'] == 1) {
                    $sinaWeiboNums += 1;
                } elseif ($val['pt_type'] == 2) {
                    $tencentWeiboNums += 1;
                }
            }
        }
        // 微信
        $weixinNums = $this->db['AccountWeixin']
            ->where(array_merge($where, array('pt_type' => 1)))->count();
        // 新闻媒体
        $newsNums = $this->db['AccountNews']->where($where)->count();
        
        $accountTypeList = C('MEDIA_ACCOUNT_TYPE');
        if ($accountTypeList) {
            foreach ($accountTypeList AS $key => $account) {
                if ($key == 1) {
                    $accountTypeList[$key]['nums'] = $sinaWeiboNums;
                } elseif ($key == 2) {
                    $accountTypeList[$key]['nums'] = $tencentWeiboNums;
                } elseif ($key == 3) {
                    $accountTypeList[$key]['nums'] = $weixinNums;
                } elseif ($key == 4) {
                    $accountTypeList[$key]['nums'] = $newsNums;
                }
            }
        }
        
		parent::data_to_view(array(
			'type'          => $type,
            'accountType'   => $accountTypeList,
		));
		$this->display();
	}
    
    /**
     * ajax获取社交帐号列表
     * 
     * @author lurongchang
     * @date   2014-09-20
     * @return json
     */
    public function getAccountList()
    {
        $start          = I('start', 0, 'intval');
        $pageSize       = I('limit', 20, 'intval');
        
        $filters        = I('filters', array());
        extract($filters);
        $type           = $type ? intval($type) : 0;
        $account        = $account ? setString($account) : '';
        $minfansnum     = $minfansnum ? intval($minfansnum) : '';
        $maxfansnum     = $maxfansnum ? intval($maxfansnum) : '';
        $minweekennum   = $minweekennum ? intval($minweekennum) : '';
        $maxweekennum   = $maxweekennum ? intval($maxweekennum) : '';
        $minmonthnum    = $minmonthnum ? intval($minmonthnum) : '';
        $maxmonthnum    = $maxmonthnum ? intval($maxmonthnum) : '';
        $pricetype      = $pricetype ? intval($pricetype) : 0;
        $minprice       = $minprice ? floatval($minprice) : '';
        $maxprice       = $maxprice ? floatval($maxprice) : '';
        $isfamous       = $isfamous ? intval($isfamous) : '';
        
        $userInfos = parent::get_session('user_info');
        $where['users_id'] = &$userInfos['id'];
        
        // 条件组合
        $account ? $where['account_name'] = array('LIKE', '%' . $account. '%') : '';
        $minfansnum ? $where['fans_num'] = array('egt', $minfansnum) : '';
        $maxfansnum ? $where['fans_num'] = array('elt', $maxfansnum) : '';
        $minweekennum ? $where['week_order_num'] = array('egt', $minweekennum) : '';
        $maxweekennum ? $where['week_order_num'] = array('elt', $maxweekennum) : '';
        $minmonthnum ? $where['month_order_nub'] = array('egt', $minmonthnum) : '';
        $maxmonthnum ? $where['month_order_nub'] = array('elt', $maxmonthnum) : '';
        
        $priceTypeColums = array('yg_zhuanfa', 'yg_zhifa', 'dj_money', 'rg_zhuanfa', 'rg_zhifa');
        $minprice ? $priceLimit[] = array('egt', $minprice) : '';
        $maxprice ? $priceLimit[] = array('elt', $maxprice) : '';
        $priceLimit ? $where[$priceTypeColums[$pricetype]] = $priceLimit : '';
        
        $isFamous ? $where['is_famous'] = $isFamous : '';
        
        $where['is_del']   = 0;
        
        $page = ceil(($start + 1) / $pageSize);
        $list = array('list' => 0, 'count' => 0);
        if ($type == 3) {
            // 微信公众号
            $where['pt_type'] = 1;
            $list = $this->db['AccountWeixin']->getLists($where, 'id DESC', $page, $pageSize);
        } elseif ($type == 2) {
            // 腾讯微博
            $where['pt_type'] = 2;
            $list = $this->db['AccountWeibo']->getLists($where, 'id DESC', $page, $pageSize);
        } elseif ($type == 1) {
            // 新浪微博
            $where['pt_type'] = 1;
            $list = $this->db['AccountWeibo']->getLists($where, 'id DESC', $page, $pageSize);
        } elseif ($type == 4) {
            $list = $this->db['AccountNews']->getLists($where, 'id DESC', $page, $pageSize);
        }
        
        ($list['count'] && $list['list']) ? $list['list'] = $this->bulidRowCell($type, $list['list']) : '';
        parent::callback(1, '成功获取数据', array(
            'start' => floor($start/$pageSize) * $pageSize,
            'limit' => $pageSize,
            'total' => $list['count'],
            'checkable' => 'false',
            'rows'  => $list['list'],
        ), array(
            'code' => 1000
        ));
        exit;
    }
    
    /**
     * 组装成前台使用格式
     * 
     * @author lurongchang
     * @date   2014-09-20
     * @return void
     */
    private function bulidRowCell($type, $data)
    {
        if (empty($data) || !is_array($data)) {
            return $data;
        }
        $newData = array();
        
        $accountTypeList = C('MEDIA_ACCOUNT_TYPE');
        
        $accountModel = '';
        if (in_array($type, array(1, 2))) {
            $accountModel = $this->db['AccountWeibo'];
        } elseif ($type == 3) {
            $accountModel = $this->db['AccountWeixin'];
        }
        
        // echo '<pre>';
        // print_r($data);
        
        foreach ($data AS $key => $val) {
            if ($accountModel) {
                $apiInfos = $accountModel->getInfosFromApi($val['account_name'], $type);
                
                if ($type == 1) {
                    $faceUrl = $apiInfos['profile_image_url'];
                    $url = $apiInfos['url'];
                } elseif ($type == 2) {
                    $faceUrl = $apiInfos['profile_image_url'];
                    $url = $apiInfos['url'];
                } elseif ($type == 3) {
                    $faceUrl = $val['head_img'];
                    $url = $apiInfos['url'];
                } elseif (type == 4) {
                    $faceUrl = $val['media_shot'];
                    $url = $apiInfos['url'];
                }
            }
            $temp = array(
                "id"    => $val['id'],
                "weibo_type" => $type,
                "cells" => array(
                    "account_id" => $val['id'],
                    "user_id" => $val['users_id'],
                    "face_url" => $faceUrl,
                    "url" => $url,
                    "weibo_type" => $type,
                    "weibo_id" => "1032732203",
                    "weibo_name" => $val['account_name'],
                    "cell_phone" => null,
                    "telephone" => null,
                    "qq" => null,
                    // 账号类型 草根 红人  媒体
                    "account_type" => 2,
                    "verification_info" => $val['company_name'],
                    // 蓝V
                    "is_bluevip" => 2,
                    // 黄V
                    "is_vip" => 1,
                    // 初级V
                    "is_daren" => 2,
                    "followers_count" => $val['fans_num'],
                    "is_private_message_enabled" => 2,
                    "is_activated" => 0,
                    "is_shield" => 2,
                    "is_sensitive" => 2,
                    // 是否接硬广
                    "is_extend" => $val['is_yg_status'],
                    "self_register" => 1,
                    // 是否审核
                    "is_verify" => $val['status'],
                    // 订单授权状态 1授权正常 2未授权 3即将过期  4授权过期
                    "is_auth" => 2,
                    "is_auth_ds" => 2,
                    "is_auto_send" => 2,
                    // 是否上架
                    "is_online" => $val['putaway_status'],
                    "is_price_open" => 0,
                    // 是否接单
                    "is_allow_order" => $val['receiving_status'],
                    "is_flowunit" => 2,
                    // 被加黑名单数
                    "company_black_num" => 0,
                    // 月订单
                    "orders_monthly" => $val['month_order_nub'],
                    // 周订单
                    "orders_weekly" => $val['week_order_num'],
                    // 月拒单率
                    "pass_order_monthly" => "0.00",
                    // 月流单率
                    "deny_order_monthly" => "0.00",
                    "posts_avgretweet_count" => "-1.000",
                    // 好评率
                    "good_rating_rate" => "暂无评价",
                    "rating_score" => 0,
                    // 被收藏数
                    "collection_num" => 0,
                    "is_contacted" => 1,
                    // 是否公开
                    "is_open" => 1,
                    // 是否支持预约
                    "is_famous" => 2,
                    "day_time" => null,
                    "tweet_price_change_count_for_day" => null,
                    "retweet_price_change_count_for_day" => null,
                    "content_price_change_count_for_day" => null,
                    "soft_tweet_price_change_count_for_day" => null,
                    "soft_retweet_price_change_count_for_day" => null,
                    "tweet_price_change_week_time" => null,
                    "retweet_price_change_week_time" => null,
                    "content_price_change_week_time" => null,
                    "soft_tweet_price_change_week_time" => null,
                    "soft_retweet_price_change_week_time" => null,
                    "content_price_change_count_for_week" => null,
                    "retweet_price_change_count_for_week" => null,
                    "tweet_price_change_count_for_week" => null,
                    "soft_tweet_price_change_count_for_week" => null,
                    "soft_retweet_price_change_count_for_week" => null,
                    // 行提示
                    "is_tip" => null,
                    // 行提示内容
                    "tip_content" => null,
                    // 普通账号 = 1,重点账号 > 1
                    "level" => 1,
                    "profit_rate" => 0.3,
                    "attribute_id" => 1,
                    "tactics_flag" => 1,
                    // 订单是否可带链接
                    "is_enable_micro_task" => $val['url_status'],
                    // 是否粉丝认证
                    "follower_be_identified" => 2,
                    "follower_be_identified_time" => null,
                    "order_settings" => 0,
                    "account_phone" => null,
                    "deadline" => null,
                    "tweet_price_d" => 5,
                    "tweet_price_u" => 1,
                    "retweet_price_d" => 5,
                    "retweet_price_u" => 1,
                    "content_price_d" => 5,
                    "content_price_u" => 1,
                    "soft_tweet_price_d" => 5,
                    "soft_tweet_price_u" => 1,
                    "soft_retweet_price_d" => 5,
                    "soft_retweet_price_u" => 1,
                    "admin_name" => $val['name'],
                    "admin_qq" => "800003455,1007",
                    // 是否接单
                    "leave" => $val['tmp_receiving_status'] ? true : false,
                    // 接单上限
                    "periodMax" => $val['receiving_num_status'] ? true : false,
                    // 接单上限
                    "orderMax" => $val['receiving_num'],
                    "weibo_type_logo" => $accountTypeList[$type]['icon'],
                    "rate" => 1
                )
            );
            if ($type == 3) {
                // 微信
                $diffPrice = array(
                    // 单图文报价
                    "single_graphic_price" => $val['dtb_money'],
                    // 多图文第一条报价
                    "multi_graphic_top_price" => $val['dtwdyt_money'],
                    // 多图文第二条报价
                    "multi_graphic_second_price" => $val['dtwdet_money'],
                    // 多图文其他位置报价
                    "multi_graphic_other_price" => $val['dtwqtwz_money'],
                    // 带号价
                    "content_price" => $val['dj_money'],
                    // 两周内单图文报价调整次数
                    'single_graphic_hard_price_raise_count_two_week' => 0,
                    // 两周内多图文第一条报价调整次数
                    'multi_graphic_hard_top_price_raise_count_two_week' => 0,
                    // 两周内多图文第二条报价调整次数
                    'multi_graphic_hard_second_price_raise_count_two_week' => 0,
                    // 两周内多图文其他位置报价次数
                    'multi_graphic_hard_other_price_raise_count_two_week' => 0,
                );
            } else {
                // 微博等
                $diffPrice = array(
                    // 硬广直发价格
                    "tweet_price" => $val['yg_zhifa'],
                    // 硬广转发价格
                    "retweet_price" => $val['yg_zhuanfa'],
                    // 带号价
                    "content_price" => $val['dj_money'],
                    // 软广直发价格
                    "soft_tweet_price" => $val['rg_zhifa'],
                    // 软广转发价格
                    "soft_retweet_price" => $val['rg_zhuanfa'],
                );
                if ($type == 4) {
                    $diffPrice['weekly_read_avg'] = $val['weekly_read_avg'];
                    $diffPrice['money'] = $val['money'];
                }
            }
            $temp['cells'] = array_merge($temp['cells'], $diffPrice);
            $newData[] = $temp;
        }
        return $newData;
    }
    
    /**
     * 批量导入社交帐号
     * 
     * @author lurongchang
     * @date   2014-09-20
     * @return void
     */
    public function batchGuidesAccount()
    {
        if ($this->isPost()) {
            $accountType = I('account', 0, 'intval');
            
            $batchFile = &$_FILES['batchfile'];
            if ($batchFile['tmp_name']) {
                $fileInfo = pathinfo($batchFile['name']);
                if (strtolower($fileInfo['extension']) == 'csv') {
                    $temp = file($batchFile['tmp_name']);
                    // 循环文件内容
                    $len = count($temp) - 1;
                    
                    import("@.Tool.Validate");
                    
                    // 根据帐号类型插入不同的表
                    switch($accountType) {
                        // 新浪微博
                        case 1:
                        // 腾讯微博
                        case 2:
                            $weiboModel = $this->db['AccountWeibo'];
                            $categoryTagsModel = $this->db['CategoryTags'];
                            $categoryTagsList = $categoryTagsModel->getTagsList(array(295));
                             
                            for ($i = 1; $i <= $len; $i++) {
                                //通过循环得到EXCEL文件中每行记录的值
                                $temp_info = explode(',', iconv('GBK', 'UTF-8', $temp[$i]));
                                $accountName    = setString($temp_info[0]);
                                $price          = floatval($temp_info[1]);
                                //add by bumtime 20141128
                                $rgPrice        = floatval($temp_info[2]); 
                                $province       = setString(trim($temp_info[3]));
                                $city           = setString(trim($temp_info[4]));
                                $common         = setString(trim($temp_info[5]));
                                
                                if (empty($accountName) || empty($price) || empty($rgPrice)) {
                                    continue;
                                }
                                $where = array(
                                    'account_name'  => $accountName,
                                    'is_del'        => 0
                                );
                                $exists = $weiboModel->getAccountInfo($where, 'id');
                                if ($exists) {
                                    continue;
                                }
                                
                                $regionModel = $this->db['Region'];
                                $whereForArea = array(
                                    array(
                                        'region_type' => 1,
                                        'region_name' => array('LIKE', '%' . $province . '%')
                                    ),
                                    array(
                                        'region_type' => 2,
                                        'region_name' => array('LIKE', '%' . $city . '%')
                                    ),  
                                    '_logic' => 'OR'
                                );
                                $cityID = $regionModel->where($whereForArea)
                                    ->order('region_type DESC')->getField('region_id');
                                    
                                $commonID		= 0;                               
                                //行业分类转换
                                foreach ($categoryTagsList  as $value)
                                {
                                	if($common == $value['title'])
                                	{
	                                	$commonID = $value['val'];
	                                	break;
                                	}
                                }   
                                
                                $userInfos = parent::get_session('user_info');
                                $apiInfos = $weiboModel->getInfosFromApi($accountName, $accountType);
                                 
                                $priceRatio = C('WEIBO_PRICE_RATIO');
                                $insertData = array(
                                    'users_id'      => $userInfos['id'],
                                    'is_celebrity'  => intval($apiInfos['verified']),
                                    'pt_type'       => $accountType,
                                    'account_name'  => $accountName,
                                    'fans_num'      => intval($apiInfos['followers_count ']),
                                    'yg_zhuanfa'    => $price,//$price * $priceRatio['retweetPrice'],
                                    'yg_zhifa'      => $rgPrice,//$price * $priceRatio['tweetPrice'],
                                    'rg_zhuanfa'    => $price,//$price * $priceRatio['softRetweetPrice'],
                                    'rg_zhifa'      => $rgPrice,//$price * $priceRatio['softTweetPrice'],
                                    //'dj_money'      => $price * $priceRatio['contentPrice'],
                                    'create_time'   => $_SERVER['REQUEST_TIME'],
                                    'head_img'  	=> $apiInfos['avatar_large'],
                                    'area_id'		=> $cityID,
                                    'industries'	=> $commonID
                                );
                                
                                $insertId = $weiboModel->add($insertData);
                                if ($insertId) {
                                    // 更新索引表
                                    parent::weiboDataprocess($insertId);
                                }
                               
                            }
    
                            break;
                        case 3:
                            // 微信公众号
                            // $insertId = $this->db['AccountWeixin']->addBatchAccount($insertData);
                            //行业分类（常见分类）add by bumtime 20141127
                            $categoryTagsModel = $this->db['CategoryTags'];
                            $categoryTagsList = $categoryTagsModel->getTagsList(array(222));
                            $weixinModel = $this->db['AccountWeixin'];
                          
                            for ($i = 1; $i <= $len; $i++) {
                                //通过循环得到EXCEL文件中每行记录的值
                                $temp_info = explode(',', iconv('GBK', 'UTF-8', $temp[$i]));
                               
                                $accountName    = setString($temp_info[0]);
                                $weixinCode     = setString($temp_info[1]);
                                $fansNums       = intval($temp_info[2]);
                                $weeklyReadAvg  = intval($temp_info[3]);
                                $malePrecent    = floatval($temp_info[4]);
                                $femalePrecent  = floatval($temp_info[5]);
                                $price          = floatval($temp_info[6]);
                                $faceUrl        = setString(trim($temp_info[7]));
                                $qrCode         = setString(trim($temp_info[8]));
                                $fansShot       = setString(trim($temp_info[9]));
                                //add by bumtime 20141127
                                $province       = setString(trim($temp_info[10]));
                                $city           = setString(trim($temp_info[11]));
                                //$area           = setString(trim($temp_info[12]));
                                $common         = setString(trim($temp_info[12]));
                                $introduction   = setString(trim($temp_info[13])); 
                                $commonID		= 0;                               
                                
                                
                                //行业分类转换
                                foreach ($categoryTagsList  as $value)
                                {
                                	if($common == $value['title'])
                                	{
	                                	$commonID = $value['val'];
	                                	break;
                                	}
                                }
     
                                $malePrecent = ($malePrecent <= 0 ? 0 : ($malePrecent >= 101) ? 100 : $malePrecent);
                                $lessPrecent = 100 - $malePrecent;
                                $femalePrecent = $femalePrecent <= $lessPrecent ? $femalePrecent : $lessPrecent;
                                 
                                if (empty($accountName) || empty($price)  /*|| !Validate::checkAccount($weixinCode)*/)
                                {
                                    continue;
                                }
                              
                                if (!empty($faceUrl) && !Validate::checkUrl($faceUrl))
                                {
                                    continue;
                                }
                                if (!empty($qrCode) && !Validate::checkUrl($qrCode))
                                {
                                    continue;
                                } 
                                if (!empty($fansShot) && !Validate::checkUrl($fansShot))
                                {
                                    continue;
                                }
                                
                                $where = array(
                                    'weixinhao'  => $weixinCode,
                                    'is_del'        => 0
                                );
                               
                                $exists = $weixinModel->getAccountInfo($where, 'id');
                               
                                if ($exists) {
                                    continue;
                                }
                                $userInfos = parent::get_session('user_info');
                                //$apiInfos = $weixinModel->getInfosFromApi($accountName, $accountType);
                                $priceRatio = C('WEIXIN_PRICE_RATIO');
                                $fansNums = $fansNums;//$apiInfos['followers_count'] ? $apiInfos['followers_count'] : $fansNums;
                                
                                $regionModel = $this->db['Region'];
                                $whereForArea = array(
                                    array(
                                        'region_type' => 1,
                                        'region_name' => array('LIKE', '%' . $province . '%')
                                    ),
                                    array(
                                        'region_type' => 2,
                                        'region_name' => array('LIKE', '%' . $city . '%')
                                    ),  
                                    '_logic' => 'OR'
                                );
                                $cityID = $regionModel->where($whereForArea)
                                    ->order('region_type DESC')->getField('region_id');
                                    
                                $insertData = array(
                                    'users_id'          => $userInfos['id'],
                                    'is_celebrity'      => 0,//intval($apiInfos['verified']),
                                    'pt_type'           => 1,
                                    'weixinhao'         => $weixinCode,
                                    'account_name'      => $accountName,
                                    'fans_num'          => $fansNums,
                                    'weekly_read_avg'   => $weeklyReadAvg,
                                    'dtb_money'         => $price * $priceRatio['singleGraphicPrice'],
                                    'dtwdyt_money'      => $price * $priceRatio['multiGraphicTopPrice'],
                                    'dtwdet_money'      => $price * $priceRatio['multiGraphicSecondPrice'],
                                    'dtwqtwz_money'     => $price * $priceRatio['multiGraphicOtherPrice'],
                                    'dj_money'          => $price * $priceRatio['contentPrice'],
                                    'head_img'    		=> $faceUrl,
                                    'qr_code'           => $qrCode,
                                    'follower_shot'     => $fansShot,
                                    'male_precent'      => $malePrecent,
                                    'female_precent'    => $femalePrecent,
                                    'create_time'       => $_SERVER['REQUEST_TIME'],
                                     //add by bumtime 20141127
                                    'introduction'		=> $introduction,
                                    'area_id'			=> $cityID,
                                    'industries'		=> $commonID
                                );
                  
                                $insertId = $weixinModel->add($insertData);
                                if ($insertId) {
                                    // 更新索引表
                                    parent::weixinDataprocess($insertId);
                                }
                            }
                            break;
                        case 4:
                            // 新闻媒体
                            // $insertId = $this->db['AccountNews']->addBatchAccount($insertData);
                            $mediaNewsModel = $this->db['AccountNews'];
                            $categoryTagsModel = $this->db['CategoryTags'];
                            $categoryTagsList = $categoryTagsModel->getTagsList(array(295));
                            for ($i = 1; $i <= $len; $i++) {
                                //通过循环得到EXCEL文件中每行记录的值
                                $temp_info = explode(',', iconv('GBK', 'UTF-8', $temp[$i]));
                                $accountName    = setString(trim($temp_info[0]));
                                $channalName    = setString(trim($temp_info[1]));
                                $price          = floatval($temp_info[2]);
                                $currentUrl     = setString(trim($temp_info[3]));
                                $province       = setString(trim($temp_info[4]));
                                $city           = setString(trim($temp_info[5]));
                                $area           = setString(trim($temp_info[6]));
                                $entry          = setString(trim($temp_info[7]));
                                $newsSource     = intval(trim($temp_info[8]));
                                $included       = intval(trim($temp_info[9]));
                                $needSource     = intval(trim($temp_info[10]));
                                $exampleUrl     = setString(trim($temp_info[11]));
                                $pressWeekly    = setString(trim($temp_info[12]));
                                $link           = setString(trim($temp_info[13]));
                                $typeOfPortal   = setString(trim($temp_info[14]));
                                $mediaShot      = setString(trim($temp_info[15]));
                                $lenlimit      	= intval(trim($lenlimit[16]));
                                $industries     = setString(trim($temp_info[17]));
                                
                                if (empty($accountName) || empty($channalName) || empty($price)) {
                                    continue;
                                }
                                if (!empty($exampleUrl) && !Validate::checkUrl($exampleUrl)) 
                                {
                                    continue;
                                }                               
                                if (!empty($mediaShot) && !Validate::checkUrl($mediaShot)) 
                                {
                                    continue;
                                }                                 
                                $newsSource = in_array($newsSource, array(0, 1, 2, 3)) ? $newsSource : 0;
                                $included = in_array($included, array(0, 1, 2, 3, 4, 5, 6, 7, 8)) ? $included : 0;
                                $needSource = in_array($needSource, array(0, 1)) ? $needSource : 0;
                                $pressWeekly = in_array($pressWeekly, array(0, 1)) ? $pressWeekly : 0;
                                $link = in_array($link, array(0, 1, 2, 3, 4, 5)) ? $link : 0;
                                $typeOfPortal = in_array($typeOfPortal, array(0, 1, 2, 3, 4)) ? $typeOfPortal : 0;
                                $lenlimit = in_array($lenlimit, array(0, 1, 2, 3, 4)) ? $lenlimit : 0;
                                $commonID = 0;
                              
                                //$industries = in_array($industries, array(0, 1, 2, 3, 4)) ? $industries : 0;
                                 //行业分类转换
                                foreach ($categoryTagsList  as $value)
                                {
                                	if($industries == $value['title'])
                                	{
	                                	$commonID = $value['val'];
	                                	break;
                                	}
                                }

                                $where = array(
                                    'account_name'  => $accountName,
                                    'is_del'        => 0
                                );
                                $exists = $mediaNewsModel->getAccountInfo($where, 'id');
                                if ($exists) {
                                    continue;
                                }
                                
                                $regionModel = $this->db['Region'];
                                $whereForArea = array(
                                    array(
                                        'region_type' => 1,
                                        'region_name' => array('LIKE', '%' . $province . '%')
                                    ),
                                    array(
                                        'region_type' => 2,
                                        'region_name' => array('LIKE', '%' . $city . '%')
                                    ),
                                    array(
                                        'region_type' => 3,
                                        'region_name' => array('LIKE', '%' . $area . '%')
                                    ),
                                    '_logic' => 'OR'
                                );
                                $areaId = $regionModel->where($whereForArea)
                                    ->order('region_type DESC')->getField('region_id');
                               
                                $userInfos = parent::get_session('user_info');
                                $insertData = array(
                                    'users_id'          => $userInfos['id'],
                                    'pt_type'           => $typeOfPortal,
                                    // 默认待审核
                                    'status'      		=> 0,
                                    'account_name'      => $accountName,
                                    'web_type'          => $included,
                                    'money'             => $price,
                                    'weekly_read_avg'   => $weeklyReadAvg,
                                    'url_type'          => $newsSource,
                                    'url_status'        => $link,
                                    'media_shot'        => $mediaShot,
                                    'area_id'           => $areaId,
                                    'channel_name'      => $channalName,
                                    'entry'             => $entry,
                                    'title'             => $title,
                                    'press_weekly'      => $pressWeekly,
                                    'url'               => $exampleUrl,
                                    'is_need_source'    => $needSource,
                                    'lenlimit'    		=> $lenlimit,
                                    'industries'    	=> $commonID,
                                    'create_time'       => $_SERVER['REQUEST_TIME'],
                                    'currentUrl'		=> $currentUrl,
                                    'title'				=> ''
                                );
                                $insertId = $mediaNewsModel->add($insertData);
                                
                                if ($insertId) {
                                    // 更新索引表
                                    parent::newsDataprocess($insertId);
                                }
                            }
                            break;
                    }
                    
                    $insertId ? $this->success('上传成功!') : $this->error('上传失败, 请检查提交数据是否有问题!');
                } else {
                    $this->error('只能上传后缀为CSV的文件!');
                }
            } else {
                $this->error('请选择需要批量上传的CSV文件!');
            }
        } else {
            parent::data_to_view(array(
                'secondPosition' => '批量导入账号',
            ));
            $this->display();
        }
    }
    
    /**
     * 不能接单详情
     * 
     * @author lurongchang
     * @date   2014-09-22
     * @return void
     */
    public function notallow()
    {
        $accountId      = I('account_id', 0, 'intval');
        $accountType    = I('account_type', 0, 'intval');
        
        if (empty($accountId) || empty($accountType)) {
            $msg = '社交媒体帐号或类型错误,请联系网站客服人员!';
            parent::callback(1, '', array(
                'followers_count' => $msg
            ));
        }
       // 粉丝数不足
       $fansNum = $userAcountID =  0;
       $minFansNums = 1000;
       $where = array("id"=>$accountId);
       
       //当前登录会员ID
       $userInfos = parent::get_session('user_info');
       $uesr_id   = &$userInfos['id'];
       
        //不同平台取粉丝数
        switch ($accountType)
        {
        	case 1:
        	case 2:
        		 $weiboModel = $this->db['AccountWeibo'];
        		 $info = $weiboModel->getAccountInfo($where, '`users_id`, `fans_num`');
        		 $fansNum		= $info['fans_num'];
        		 $userAcountID	= $info['users_id'];
        		 break;
        	case 3:
        		 $weixinModel = $this->db['AccountWeixin'];
        		 $info = $weiboModel->getAccountInfo($where, '`users_id`, `fans_num`');
        		 $fansNum = $info['fans_num'];
        		 $userAcountID	= $info['users_id'];        		 
        		 break;
        	case 4:
        		 $newsModel = $this->db['AccountNews'];
        		 $info = $newsModel->getAccountInfo($where, '`users_id`, `fans_num`');
        		 $fansNum = $info['fans_num'];
        		 $userAcountID	= $info['users_id'];        		 
        		 break;       		
        }
        
        if ($uesr_id != $userAcountID) {
            $msg = '该账号不属于您的哦' ;
            parent::callback(1, '', array(
                'followers_count' => $msg
            ));
        }
       
        
        if ($fansNum < $minFansNums) {
            $msg = '该账号的粉丝不足' . $minFansNums;
            parent::callback(1, '', array(
                'followers_count' => $msg
            ));
        }
    }
    
    /**
     * 选择平台
     * 
     * @author lurongchang
     * @date   2014-09-24
     * @return void
     */
    public function chooseweibotype()
    {
        $datas = C('MEDIA_ACCOUNT_TYPE');
        foreach ($datas AS $key => $account) {
            // 能否重复账号名 1可以 2不可以
            $account['canRepeatAccountName']    = 2;
            $account['platformAccountNameImg']  = $account['accountNameImg'];
            $account['pid']                     = $account['id'];
            $account['platformIdImg']           = $account['idImg'];
            $account['platformUrlImg']          = $account['urlImg'];
            $account['platformIcon']            = $account['icon'];
            $account['platformName']            = $account['name'];
            $account['showB']                   = 1;
            $account['showC']                   = 1;
            $datas[$key] = $account;
        }
        $datas = array_values($datas);
        parent::callback(1, 'success', $datas, array(
            'code' => 1000
        ));
    }
    
    /**
     * 提交帐号信息
     * 
     * @author lurongchang
     * @date   2014-09-25
     * @return void
     */
    public function submitaccount()
    {
        if ($this->isPost()) {
            print_r($_POST);
            exit;
        } else {
            parent::callback(0, 'error');
        }
    }
    
    /**
     * 创建帐号信息
     * 
     * @author lurongchang
     * @date   2014-09-25
     * @return void
     */
    public function create()
    {
        if ($this->isPost()) {
            $weiboType  = I('post.weibo_type', 0, 'intval');
            $weiboId    = I('post.weibo_id', '', 'setString');
            
            if (empty($weiboType) || empty($weiboId)) {
                parent::callback(0, '提交数据错误,请检查填写数据是否正确');
            }
            
            import("@.Tool.Validate");
            $viewPath = C('PUBLIC_VISIT');
            
            if (in_array($weiboType, array(1, 2))) {
                // 新浪微博
                $retweetPrice	= I('retweet_price', 0, 'floatval');
				
                $accountType	= I('accountType', 0, 'intval');
				
				// 名人职业
                $occupation		= I('occupation', 0, 'intval');
				// 媒体领域
                $field       	= I('field', 0, 'intval');
				// 地方名人/媒体
                $cirymedia		= I('cirymedia', 0, 'intval');
				// 兴趣标签
                $interest       = I('interest', 0, 'intval');
				// 配合度
                $coordination	= I('coordination', 0, 'intval');
				// 是否支持原创
                $originality	= I('originality', 0, 'intval');
				
				// 常见分类
                $common	= I('common', 0, 'intval');
				// 地区
                $sex	= I('sex', 0, 'intval');
				
				
				
                if (empty($retweetPrice)) {
                    parent::callback(0, '价格错误');
                }
                
                $weiboModel = $this->db['AccountWeibo'];
                $apiInfos = $weiboModel->getInfosFromApi($weiboId, $weiboType);
                
                // if ($apiInfos['followers_count'] <= 499) {
                    // parent::callback(0, '粉丝数至少为500!', array(), array(
                        // 'code'  => 900
                    // ));
                // }
                
				$weiboPriceRatio = C('WEIBO_PRICE_RATIO');
                $userInfos = parent::get_session('user_info');
                $datas = array(
                    // 默认待审核
                    'status'  => 0,
                    'users_id'      => $userInfos['id'],
                    'is_celebrity'  => intval($apiInfos['verified']),
                    'pt_type'       => ($weiboType == 1) ? 1: 2,
                    'account_name'  => $weiboId,
                    'fans_num'      => intval($apiInfos['followers_count']),
                    'yg_zhuanfa'    => $retweetPrice * $weiboPriceRatio['retweetPrice'],
                    'yg_zhifa'      => $retweetPrice * $weiboPriceRatio['tweetPrice'],
                    'rg_zhuanfa'    => $retweetPrice * $weiboPriceRatio['softRetweetPrice'],
                    'rg_zhifa'      => $retweetPrice * $weiboPriceRatio['softTweetPrice'],
                    'create_time'   => $_SERVER['REQUEST_TIME'],
                );
                $insertId = $weiboModel->add($datas);
                if ($insertId) {
					
					if ($accountType == 1) {
						// 名人
						$indexDatas = array(
							'occupation'	=> $occupation,
							'field'			=> $field,
							'ck_price'		=> $retweetPrice,
							'yc_price'		=> $retweetPrice,
							'cirymedia'		=> $cirymedia,
							'interest'		=> $interest,
							'coordination'	=> $coordination,
							'originality'	=> $originality,
							'fansnumber'	=> $datas['fans_num'],
							'weibo_id'		=> $insertId,
						);
						$celeprityindexWeiboModel = $this->db['CeleprityindexWeibo'];
						$celeprityindexWeiboModel->add($indexDatas);
					} else {
						// 草根
						$indexDatas = array(
							'common'		=> $common,
							'cirymedia'		=> $cirymedia,
							'fans_num'		=> $datas['fans_num'],
							'yg_zhuanfa'    => $retweetPrice * $weiboPriceRatio['retweetPrice'],
							'yg_zhifa'      => $retweetPrice * $weiboPriceRatio['tweetPrice'],
							'rg_zhuanfa'    => $retweetPrice * $weiboPriceRatio['softRetweetPrice'],
							'rg_zhifa'      => $retweetPrice * $weiboPriceRatio['softTweetPrice'],
							'sex'			=> $sex,
							'weibo_id'		=> $insertId,
						);
						$grassrootsWeiboModel = $this->db['GrassrootsWeibo'];
						$grassrootsWeiboModel->add($indexDatas);
					}
					
					parent::weiboDataprocess($insertId);
                    parent::callback(1, '增加帐号成功!', array(), array(
                        'code'  => 1000
                    ));
                } else {
                    parent::callback(0, '增加帐号失败!');
                }
            } elseif ($weiboType == 3) {
                // 微信
                $weiboName          = I('weibo_name', '', 'setString');
                $weibo_id 			= I('weibo_id', '', 'setString');
                $followersCount     = I('followers_count', 0, 'intval');
                $weeklyReadAvg      = I('weekly_read_avg', 0, 'intval');
                $malePrecent        = I('gender_distribution_male', 0, 'floatval');
                $femalePrecent      = I('gender_distribution_female', 0, 'floatval');
                $price              = I('single_graphic_price', 0, 'floatval');
                $hardPrice          = I('single_graphic_hard_price', '', 'floatval');
                $topPrice           = I('multi_graphic_top_price', '', 'floatval');
                $hardTopPrice       = I('multi_graphic_hard_top_price', '', 'floatval');
                $secondPrice        = I('multi_graphic_second_price', '', 'floatval');
                $hardSecondPrice    = I('multi_graphic_hard_second_price', '', 'floatval');
                $otherPrice         = I('multi_graphic_other_price', '', 'floatval');
                $hardOtherPrice     = I('multi_graphic_hard_other_price', '', 'floatval');
                $contentPrice       = I('content_price', '', 'floatval');
                $uploadImgAvatar    = I('uploadImgAvatar', '', 'setString');
                $uploadImgQrCode    = I('uploadImgQrCode', '', 'setString');
                $uploadImgFollowers = I('uploadImgFollowers', '', 'setString');
				
				$accountType		= I('accountType', 0, 'intval');
				
				// 名人职业
                $occupation			= I('occupation', 0, 'intval');
				// 媒体领域
                $field       		= I('field', 0, 'intval');
				// 地方名人/媒体
                $cirymedia			= I('cirymedia', 0, 'intval');
				// 兴趣标签
                $interest       	= I('interest', 0, 'intval');
				// 配合度
                $coordination		= I('coordination', 0, 'intval');
				// 是否支持原创
                $originality		= I('originality', 0, 'intval');
				
				
				// 常见分类
                $common	= I('common', 0, 'intval');
				// 地区
                $sex	= I('sex', 0, 'intval');
                
                $userInfos = parent::get_session('user_info');
                $datas = array(
                    'users_id'          => $userInfos['id'],
                    'pt_type'           => 1,
                    // 默认待审核
                    'status'      => 0,
                    'account_name'      => $weiboName,
                    'weixinhao'      	=> $weibo_id,
                    'fans_num'          => $followersCount,
                    'dtb_money'         => $price,
                    'dtwdyt_money'      => $topPrice,
                    'dtwdet_money'      => $secondPrice,
                    'dtwqtwz_money'     => $otherPrice,
                    'dj_money'          => $contentPrice,
                    'weekly_read_avg'   => $weeklyReadAvg,
                    'male_precent'      => $malePrecent,
                    'female_precent'    => $femalePrecent,
                    'account_avatar'    => $viewPath['domain'] . $viewPath['dir'].'images/'. $uploadImgAvatar,
                    'qr_code'           => $viewPath['domain'] . $viewPath['dir'].'images/' . $uploadImgQrCode,
                    'follower_shot'     => $viewPath['domain'] . $viewPath['dir'].'images/' . $uploadImgFollowers,
                    'create_time'       => $_SERVER['REQUEST_TIME'],
                );
                
                $weixinModel = $this->db['AccountWeixin'];
                $insertId = $weixinModel->add($datas);
                if ($insertId) {
					
					if ($accountType == 1) {
						$indexDatas = array(
							'occupation'	=> $occupation,
							'field'			=> $field,
							'ck_price'		=> $price,
							'yc_price'		=> $price,
							'cirymedia'		=> $cirymedia,
							'interest'		=> $interest,
							'coordination'	=> $coordination,
							'originality'	=> $originality,
							'fansnumber'	=> $datas['fans_num'],
							'weibo_id'		=> $insertId,
						);
						$celeprityindexWeixinModel = $this->db['CeleprityindexWeixin'];
						$celeprityindexWeixinModel->add($indexDatas);
					} else {
						// 草根
						$indexDatas = array(
							'common'		=> $common,
							'cirymedia'		=> $cirymedia,
							'fans_number'	=> $datas['fans_num'],
							'dtb_money'		=> $price,
							'dtwdyt_money'  => $topPrice,
							'dtwdet_money'  => $secondPrice,
							'dtwqtwz_money' => $otherPrice,
							'audience_man'	=> $malePrecent,
							'audience_women'=> $femalePrecent,
							'read_number'	=> $weeklyReadAvg,
							'weibo_id'		=> $insertId,
						);
						$grassrootsWeixinModel = $this->db['GrassrootsWeixin'];
						$grassrootsWeixinModel->add($indexDatas);
					}
					
                    parent::weixinDataprocess($insertId);
                    parent::callback(1, '增加帐号成功!', array(), array(
                        'code'  => 1000
                    ));
                } else {
                    parent::callback(0, '增加帐号失败!');
                }
            } elseif ($weiboType == 4) {
                // 新闻媒体
                $retweetPrice       = I('retweet_price', 0, 'floatval');
                $channelName        = I('channel_name', '', 'setString');
                $title              = I('account_title', '', 'setString');
                $areaId             = I('area_id', '', 'setString');
                $entry              = I('account_entry', '', 'setString');
                $isNewsSource       = I('is_news_source', '', 'intval');
                $isWebSiteIncluded  = I('is_web_site_included', '', 'intval');
                $isNeedSource       = I('is_need_source', '', 'intval');
                $url                = I('url', '', 'setString');
                $isPressWeekly      = I('is_press_weekly', '', 'intval');
                $isTextLink      	= I('is_text_link', '', 'intval');
                $typeOfPortal       = I('type_of_portal', '', 'intval');
                $imgPath            = I('uploadImgMedia', '', 'setString');
                
                if (Validate::checkNull($retweetPrice)) {
                    parent::callback(0, '价格错误');
                }
                if (Validate::checkNull($isNewsSource)) {
                    parent::callback(0, '请选择新闻源方式');
                }
                if (Validate::checkNull($isWebSiteIncluded)) {
                    parent::callback(0, '请选择网址收录方式');
                }
                if (Validate::checkNull($isNeedSource)) {
                    parent::callback(0, '是否需要来源?');
                }
                if (Validate::checkNull($isPressWeekly)) {
                    parent::callback(0, '周末能否发稿?');
                }
                if (Validate::checkNull($isTextLink)) {
                    parent::callback(0, '请选择文本链接方式');
                }
                if (Validate::checkNull($typeOfPortal)) {
                    parent::callback(0, '请选择门户类型');
                }
                
                $userInfos = parent::get_session('user_info');
                $where = array(
                    'users_id'      => $userInfos['id'],
                    'account_name'  => $weiboId
                );
                $accountNewsModel = $this->db['AccountNews'];
                $accountInfo = $accountNewsModel->getAccountInfo($where);
                if ($accountInfo) {
                    parent::callback(0, '该帐号已存在!');
                } else {
                    $datas = array(
                        'users_id'      => $userInfos['id'],
                        // 'big_type'      => 1,
                        'pt_type'       => $typeOfPortal,
                        // 默认待审核
                        'status'  => 0,
                        'account_name'  => $weiboId,
                        'web_type'      => $isWebSiteIncluded,
                        'money'         => $retweetPrice,
                        'url_type'      => $isNewsSource,
                        'url_status'    => $isTextLink,
                        'media_shot'    => $viewPath['domain'] . $viewPath['dir'] . $imgPath,
                        'area_id'       => $areaId,
                        'channel_name'  => $channelName,
                        'entry'         => $entry,
                        'title'         => $title,
                        'press_weekly'  => $isPressWeekly,
                        'url'           => $url,
                        'is_need_source'=> $isNeedSource,
                        'create_time'   => $_SERVER['REQUEST_TIME'],
                    );
                    $insertId = $accountNewsModel->add($datas);
                    if ($insertId) {
                        parent::newsDataprocess($insertId);
                        parent::callback(1, '增加帐号成功!', array(), array(
                            'code'  => 1000
                        ));
                    } else {
                        parent::callback(0, '增加帐号失败!');
                    }
                }
            }
        } else {
            parent::callback(C('STATUS_ACCESS'), 'error');
        }
    }
    
    /**
     * 检查帐号是否存在
     * 
     * @author lurongchang
     * @date   2014-09-26
     * @return void
     */
    public function accountExists()
    {
        if ($this->isGet()) {
            $weiboType      = I('post.weibo_type', 0, 'intval');
            $weiboId        = I('post.weibo_id', 0, 'intval');
            
            if (empty($weiboType) || empty($weiboId)) {
                parent::callback(0, 'error');
            }
            
            $accountDB = array(
                1 => 'AccountWeibo',
                2 => 'AccountWeibo',
                3 => 'AccountWeixin',
                4 => 'AccountNews',
            );
            $accountModel = $this->db[$accountDB[$weiboType]];
            $where = array(
                'account_name'  => $weiboId,
                'is_del'        => 0
            );
            $hasId = $accountModel->where($where)->getField('id');

            // {"code":1000,"msg":"success","data":[false]}
            if ($hasId) {
                echo json_encode(array(
                    'code'  => 1000,
                    'msg'   => '帐号已存在',
                    'data'  => array(false)
                ));
            } else {
                echo json_encode(array(
                    'code'  => 1000,
                    'msg'   => '帐号不存在',
                    'data'  => array(true)
                ));
            }
            exit;
        } else {
            parent::callback(C('STATUS_ACCESS'), 'error');
        }
    }
    
    /**
     * 获取地区分级
     * 
     * @author lurongchang
     * @date   2014-09-26
     * @return void
     */
    public function getlowerarea()
    {
        if ($this->isGet()) {
            $areaId     = I('get.area_id', 0, 'intval');
            
            $datas = $this->db['Region']->getAreaById($areaId);
            foreach ($datas AS $key => $value) {
                $datas[$key] = array(
                    'id'    => $value['region_id'],
                    // 'id'    => $value['parent_id'],
                    'name'  => $value['region_name']
                );
            }
            
            parent::callback(1, 'success', $datas, array(
                'code' => 1000
            ));
        } else {
            parent::callback(C('STATUS_ACCESS'), 'error');
        }
    }
    
    /**
     * 查看审核状态
     * 
     * @author lurongchang
     * @date   2014-10-01
     * @return void
     */
    public function review()
    {
        if ($this->isGet()) {
            $accountId      = I('get.account_id', 0, 'intval');
            $accountType    = I('get.account_type', 0, 'intval');
            
            if (empty($accountId) || empty($accountType)) {
                parent::callback(C('STATUS_DATA_LOST'), '提交数据错误,请检查填写数据是否正确');
            }
            
            $accountDB = array(
                1 => 'AccountWeibo',
                2 => 'AccountWeibo',
                3 => 'AccountWeixin',
                4 => 'AccountNews',
            );
            $accountModel = $this->db[$accountDB[$accountType]];
            $status = $accountModel->where(array('id' => $accountId))->getField('status');
            $statusName = array('待审核', '审核通过', '审核失败');
            echo json_encode(array(
                'error' => $statusName[$status]
            ));
            exit;
        } else {
            parent::callback(C('STATUS_ACCESS'), 'error');
        }
    }
    
    /**
     * 是否暂离 获取HTML代码
     * 
     * @author lurongchang
     * @date   2014-10-01
     * @return void
     */
    public function leavedetails()
    {
       /* if ($this->isGet()) {
            $accountId      = I('get.accountId', 0, 'intval');
            $accountType    = I('get.accountType', 0, 'intval');
            
            if (empty($accountId) || empty($accountType)) {
                die('提交数据错误,请检查填写数据是否正确');
            }
            // 根据帐号获取暂离时间
            
            $accountDB = array(
                1 => 'AccountWeibo',
                2 => 'AccountWeibo',
                3 => 'AccountWeixin',
                4 => 'AccountNews',
            );
            $accountModel = $this->db[$accountDB[$accountType]];
            $leave = $accountModel->where(array('id' => $accountId))->getField('tmp_receiving_status');
            
            parent::data_to_view(array(
                'leave'         => $leave,
                'accountId'     => $accountId,
                'accountType'   => $accountType,
            ));
            $this->display();
            exit;
        } else {
            die('ERROR');
        }*/
    }
    
    /**
     * 设置是否暂离
     * 
     * @author lurongchang
     * @date   2014-10-01
     * @return void
     */
    public function setleave()
    {
        if ($this->isPost()) {
            $accountId      = I('post.account_id', 0, 'intval');
            $accountType    = I('post.account_type', 0, 'intval');
            $leave          = I('post.leave', 0, 'intval');
            $processall     = I('post.processall', 0, 'intval');
            
            if (empty($accountId) || empty($accountType)) {
                echo json_encode(array(
                    'status' => false,
                    'message' => '提交数据错误,请检查填写数据是否正确',
                ));
                exit;
            }
            // 根据帐号获取暂离时间
            
            $accountDB = array(
                1 => 'AccountWeibo',
                2 => 'AccountWeibo',
                3 => 'AccountWeixin',
                4 => 'AccountNews',
            );
            $accountModel = $this->db[$accountDB[$accountType]];
            if ($processall) {
                $userInfos = parent::get_session('user_info');
                $where = array(
                    'users_id'  => $userInfos['id'],
                    'is_del'    => 0,
                );
            } else {
                $where = array('id' => $accountId);
            }
            $status = $accountModel->where($where)->save(array('tmp_receiving_status' => $leave));
            if ($status === false) {
                echo json_encode(array(
                    'status' => false,
                    'message' => '设置失败！',
                ));
            } else {
                echo json_encode(array(
                    'status' => true,
                    'message' => 'success',
                ));
            }
            exit;
        } else {
            parent::callback(C('STATUS_ACCESS'), 'error');
        }
    }
    
    /**
     * 是否接硬广
     * 
     * @author lurongchang
     * @date   2014-10-01
     * @return void
     */
    public function setextend()
    {
        if ($this->isPost()) {
            $accountId      = I('post.accountId', 0, 'intval');
            $accountType    = I('post.accountType', 0, 'intval');
            $status         = I('post.isExtendRadio', 0, 'intval');
            $processall     = I('post.selectAllCheckbox', 0, 'intval');
            
            if (empty($accountId) || empty($accountType)) {
                echo json_encode(array(
                    'status' => false,
                    'message' => '提交数据错误,请检查填写数据是否正确',
                ));
                exit;
            }
            
            if ($accountType == 3) {
                echo json_encode(array(
                    'status' => false,
                    'message' => '微信不能设置是否接硬广',
                ));
                exit;
            }
            
            $accountDB = array(
                1 => 'AccountWeibo',
                2 => 'AccountWeibo',
                3 => 'AccountWeixin',
                4 => 'AccountNews',
            );
            $accountModel = $this->db[$accountDB[$accountType]];
            if ($processall) {
                $userInfos = parent::get_session('user_info');
                $where = array(
                    'users_id'  => $userInfos['id'],
                    'is_del'    => 0,
                );
            } else {
                $where = array('id' => $accountId);
            }
            $status = $accountModel->where($where)->save(array('is_yg_status' => $status));
            if ($status === false) {
                echo json_encode(array(
                    'status' => false,
                    'message' => '设置失败！',
                ));
            } else {
                echo json_encode(array(
                    'status' => true,
                    'message' => 'success',
                ));
            }
            exit;
        } else {
            parent::callback(C('STATUS_ACCESS'), 'error');
        }
    }
    
    /**
     * 接单上限
     * 
     * @author lurongchang
     * @date   2014-10-01
     * @return void
     */
    public function setperiod()
    {
        if ($this->isPost()) {
            $accountId      = I('post.accountId', 0, 'intval');
            $accountType    = I('post.accountType', 0, 'intval');
            $toSet          = I('post.isOnlineRadio', 0, 'intval');
            $processall     = I('post.selectAllCheckbox', 0, 'intval');
            $maxOrder       = I('post.orderMaxInput', 0, 'intval');
            
            if (empty($accountId) || empty($accountType) || empty($maxOrder)) {
                echo json_encode(array(
                    'status' => false,
                    'message' => '提交数据错误,请检查填写数据是否正确',
                ));
                exit;
            }
            
            if ($accountType == 3) {
                echo json_encode(array(
                    'status' => false,
                    'message' => '微信不能设置接单上限',
                ));
                exit;
            }
            
            $accountDB = array(
                1 => 'AccountWeibo',
                2 => 'AccountWeibo',
                3 => 'AccountWeixin',
                4 => 'AccountNews',
            );
            $accountModel = $this->db[$accountDB[$accountType]];
            
            if ($processall) {
                $userInfos = parent::get_session('user_info');
                if ($toSet) {
                    $where = array(
                        'users_id'  => $userInfos['id'],
                        'is_del'    => 0,
                    );
                    $data = array(
                        'receiving_num_status' => 1,
                        'receiving_num' => $maxOrder
                    );
                } else {
                    $where = array('users_id'  => $userInfos['id']);
                    $data = array('receiving_num_status' => 0);
                }
            } else {
                if ($toSet) {
                    $where = array('id' => $accountId);
                    $data = array(
                        'receiving_num_status' => 1,
                        'receiving_num' => $maxOrder
                    );
                } else {
                    $where = array('id' => $accountId);
                    $data = array('receiving_num_status' => 0);
                }
            }
            $status = $accountModel->where($where)->save($data);
            if ($status === false) {
                echo json_encode(array(
                    'status' => false,
                    'message' => '设置失败！',
                ));
            } else {
                echo json_encode(array(
                    'status' => true,
                    'message' => 'success',
                ));
            }
            exit;
        } else {
            parent::callback(C('STATUS_ACCESS'), 'error');
        }
    }
    
    /**
     * 订单是否可带链接
     * 
     * @author lurongchang
     * @date   2014-10-01
     * @return void
     */
    public function setisenablemicrotask()
    {
        if ($this->isPost()) {
            $accountId      = I('post.accountId', 0, 'intval');
            $accountType    = I('post.accountType', 0, 'intval');
            $urlStatus      = I('post.isEnableMicroTask', 0, 'intval');
            $selectAll      = I('post.isSelectAll', 0, 'intval');
            
            if (empty($accountId) || empty($accountType)) {
                echo json_encode(array(
                    'status' => false,
                    'message' => '提交数据错误,请检查填写数据是否正确',
                ));
                exit;
            }
            
            if ($accountType == 3) {
                echo json_encode(array(
                    'status' => false,
                    'message' => '微信不能设置订单是否可带链接',
                ));
                exit;
            }
            
            $accountDB = array(
                1 => 'AccountWeibo',
                2 => 'AccountWeibo',
                3 => 'AccountWeixin',
                4 => 'AccountNews',
            );
            $accountModel = $this->db[$accountDB[$accountType]];
            
            if ($selectAll) {
                $userInfos = parent::get_session('user_info');
                $where = array(
                    'users_id'  => $userInfos['id'],
                    'is_del'    => 0,
                );
            } else {
                $where = array('id' => $accountId);
            }
            $status = $accountModel->where($where)->save(array('url_status' => $urlStatus));
            if ($status === false) {
                echo json_encode(array(
                    'status' => false,
                    'message' => '设置失败！',
                ));
            } else {
                echo json_encode(array(
                    'status' => true,
                    'message' => 'success',
                ));
            }
            exit;
        } else {
            parent::callback(C('STATUS_ACCESS'), 'error');
        }
    }
    
    /**
     * 修改价格
     * 
     * @author lurongchang
     * @date   2014-10-01
     * @return void
     */
    public function changeamount()
    {
        if ($this->isPost()) {
            $accountId      = I('post.accountId', 0, 'intval');
            $accountType    = I('post.accountType', 0, 'intval');
            $accountPhone   = I('post.accountPhone', '', 'setString');
            $newPrice       = I('post.newRetweetPrice', 0, 'floatval');
            $priceType      = I('post.priceType', '', 'setString');
            
            $priceTypes = array(
                'accountPhone',
                'retweetprice'      => 'yg_zhuanfa', 
                'tweetprice'        => 'yg_zhifa',
                'softretweetprice'  => 'rg_zhuanfa',
                'softtweetprice'    => 'rg_zhifa',
                'contentprice'      => 'dj_money',
                );
            if (empty($accountId) || empty($accountType) || !array_key_exists($priceType, $priceTypes)) {
                echo json_encode(array(false, '提交数据错误,请检查填写数据是否正确'));
                exit;
            }
            
            if (!in_array($accountType, array(1, 2))) {
                echo json_encode(array(false, '此功能只支持微博'));
                exit;
            }
            
            if ($priceType == 'accountPhone') {
                echo json_encode(array(false, '修改号码功能暂时暂停使用!'));
                exit;
            }
            
            $accountModel = $this->db['AccountWeibo'];
            $where = array('id' => $accountId);
            $datas[$priceTypes[$priceType]] = $newPrice;
            $status = $accountModel->where($where)->save($datas);
            if ($status === false) {
                echo json_encode(array(false, '设置失败！'));
            } else {
                // 更新搜索表
                parent::weiboDataprocess($accountId);
                echo json_encode(array(true, '资料修改成功!'));
            }
            exit;
        } else {
            parent::callback(C('STATUS_ACCESS'), 'error');
        }
    }
    
    /**
     * 新闻媒体修改价格
     * 
     * @author lurongchang
     * @date   2014-10-03
     * @return void
     */
    public function updateprice()
    {
        if ($this->isGet()) {
            $money          = I('get.price_type', '', 'setString');
            $newPrice       = I('get.price_value', 0, 'floatval');
            $rowFields      = I('get.row');
            
            $accountId      = intval($rowFields['account_id']);
            $accountType    = intval($rowFields['weibo_type']);
            $userId         = intval($rowFields['user_id']);
            
            $userInfos = parent::get_session('user_info');
            
            if (empty($accountId) || empty($accountType) || ($userId != $userInfos['id'])
            || ('money' != $money)) {
                echo json_encode(array(false, '提交数据错误,请检查填写数据是否正确'));
                exit;
            }
            
            $accountModel = $this->db['AccountNews'];
            $where = array(
                'id'        => $accountId,
                'users_id'  => $userInfos['id']
            );
            $datas['money'] = $newPrice;
            $status = $accountModel->where($where)->save($datas);
            if ($status === false) {
                echo json_encode(array(
                    'result'    => false,
                    'message' => '设置失败！'
                ));
            } else {
                // 更新搜索表
                parent::newsDataprocess($accountId);
                echo json_encode(array(
                    'result'  => true,
                    'message' => '资料修改成功!'
                ));
            }
            exit;
        } else {
            parent::callback(C('STATUS_ACCESS'), 'error');
        }
    }
    
    /**
     * 详情
     * 
     * @author lurongchang
     * @date   2014-10-03
     * @return void
     */
    public function detail()
    {
        if ($this->isGet()) {
            $accountId      = I('get.account_id', 0, 'intval');
            $accountType    = I('get.account_type', 0, 'intval');
            
            $userInfos = parent::get_session('user_info');
            
            if (empty($accountId) || empty($accountType)) {
                parent::callback(
                    C('STATUS_ACCESS'),
                    '提交数据错误,请检查填写数据是否正确', 
                    array(),
                    array('code' => 1000)
                );
            }
            
            $accountDB = array(
                1 => 'AccountWeibo',
                2 => 'AccountWeibo',
                3 => 'AccountWeixin',
                4 => 'AccountNews',
            );
            $accountModel = $this->db[$accountDB[$accountType]];
            $where = array(
                'id'        => $accountId,
                'users_id'  => $userInfos['id']
            );
            $accountInfos = $accountModel->getAccountInfo($where);
            
            $returnExampleArray = array(
                'account_advantage' => "",
                'account_id' => $accountInfos['id'],
                'account_type' => null,
                // 转评值
                'active_score' => "0.00",
                'age' => 0,
                'areaFont' => "",
                'area_id' => isset($accountInfos['area_id']) ? $accountInfos['area_id'] : -1,
                // 被加黑名单数
                'company_black_num' => 0,
                // 账号分类
                'content_categories' => null,
                // 月流单率
                'deny_order_monthly' => "0.00",
                'edu_degree' => null,
                // 是否粉丝认证
                'follower_be_identified' => 0,
                'follower_be_identified_time' => null,
                'followers_count' => $accountInfos['fans_num'],
                'friend_desc' => "",
                'gender' => 2,
                'hand_tags' => "",
                'isEditable' => true,
                // 是否支持预约
                'is_famous' => $accountInfos['is_famous'],
                // 是否公开
                'is_open' => null,
                // 账号标签
                'keywords' => null,
                // 月合格率
                'order_yield' => "0.00",
                // 月拒单率
                'pass_order_monthly' => "0.00",
                'profession_type' => 0,
                'true_name' => "",
                'type' => 1,
                'weibo_id' => "1670431414",
                'weibo_link' => "",
                'weibo_name' => $accountInfos['account_name'],
                'weibo_type' => $accountType,
            );
            
            if ($accountType == 3) {
                $unknowPrecent = 100 - $accountInfos['male_precent'] - $accountInfos['female_precent'];
                $weixinFields = array(
                    // 是否粉丝认证
                    'follower_be_identified'    => 0,
                    // 性别分布: 男
                    'gender_distribution_male'  => $accountInfos['male_precent'],
                    // 性别分布: 女
                    'gender_distribution_female'  => $accountInfos['female_precent'],
                    // 性别分布: 未知
                    'gender_distribution_unknown'  => $unknowPrecent,
                    // 性别分布认证
                    'gender_distribution_identified' => 0,
                    // 性别分布认证 只有通过认证才有 认证时间
                    'gender_distribution_identified_time' => array(
                        'date' => null
                    ),
                    // 微信号
                    'weibo_id'  => $accountInfos['weixinhao'],
                    // 头像
                    'screen_portrait' => $accountInfos['head_img'],
                    // 二维码
                    'screen_shot_qr_code' => $accountInfos['qr_code'],
                    // 粉丝截图
                    'screen_shot_followers' => $accountInfos['follower_shot'],
                    // 趋势截图
                    'screen_shot_info' => ''
                );
                $returnExampleArray = array_merge($returnExampleArray, $weixinFields);
            }
            
            parent::callback(C('STATUS_ACCESS'), 'success', $returnExampleArray, array('code' => 1000));
            
            // echo json_encode(array(
                // 'code' => 1000,
                // 'data' => $returnExampleArray,
                // 'msg' => "success"
            // ));
            // exit;
        } else {
            parent::callback(C('STATUS_ACCESS'), 'error');
        }
    }
    
    /**
     * 上传图片并修改数据
     * 
     * @author lurongchang
     * @date   2014-10-04
     * @return url
     */
    public function modifyreview()
    {
        if ($this->isPost()) {
            $accountId      = I('account_id', 0, 'intval');
            $accountType    = I('account_type', 0, 'intval');
            $keyField       = I('from', '', 'setString');
            $fansNums       = I('followers_count', 0, 'intval');
            
            $keyProxy = array(
                // 趋势截图
                // 'info' => '',
                // 二维码
                'qr_code' => 'qr_code',
                // 头像
                'avatar' => 'account_avatar',
                // 帐号头像
                'accountAvatar' => 'account_avatar',
                // 真人头像
                'personAvatar' => 'account_avatar',
            );
            
            
            if (empty($accountId) || empty($accountType)) {
                parent::callback(
                    C('STATUS_ACCESS'),
                    '提交数据错误,请检查填写数据是否正确', 
                    array(),
                    array(
                        'url' => '',
                        'success' => 0,
                    )
                );
            }
            
            $url = '';
            $uploadDir = C('UPLOAD_DIR');
            $viewDir = C('PUBLIC_VISIT');
            $files = $_FILES['qqfile'];
            $dir = $uploadDir['web_dir'] . $uploadDir['image'];
            $info = parent::upload_file($files, $dir, 5120000);
            if ($info['status']) {
                $url = $info['info'][0]['savename'];
                
                $userInfos = parent::get_session('user_info');
                $accountModel = $this->db['AccountWeixin'];
                $where = array(
                    'id' => $accountId,
                    'users_id' => $userInfos['id'],
                );
                $datas = array(
                    $keyProxy[$keyField] => $url
                );
                $status = $accountModel->where($where)->save($datas);
                $trueUrl = $viewDir['domain'] . $viewDir['dir'] . $url;
                if ($status !== false) {
                    echo json_encode(array(
                        'url' => $trueUrl,
                        'msg' => '图片上传成功',
                        'success'  => true
                    ));
                } else {
                    echo json_encode(array(
                        'url' => $trueUrl,
                        'msg' => '数据保存错误',
                        'success'  => false
                    ));
                }
            } else {
                parent::callback(
                    C('STATUS_ACCESS'),
                    $info['info'], 
                    array(),
                    array(
                        'url' => '',
                        'success' => 0,
                    )
                );
            }
            exit;
        } else {
            parent::callback(C('STATUS_ACCESS'), 'error', array(), array(
                'url' => '',
                'success' => 0,
            ));
        }
    }
    /**
     * 粉丝图
     * 
     * @author lurongchang
     * @date   2014-10-04
     * @return url
     */
    public function claimfollowers()
    {
        if ($this->isPost()) {
            $accountId      = I('account_id', 0, 'intval');
            $accountType    = I('account_type', 0, 'intval');
            $isUpload       = I('js_followersUploaded', 0, 'intval');
            $uploadUrl      = I('uploadimg', '', 'setString');
            $fansNums       = I('followers_count', 0, 'intval');
            
            import("@.Tool.Validate");
            $msg = '';
            if (empty($accountId) || empty($accountType) || empty($fansNums)) {
                $msg = '提交数据错误,请检查填写数据是否正确';
            } elseif (empty($isUpload) || !Validate::checkUrl($uploadUrl)) {
                $msg = '图片地址错误';
            }
            if ($msg) {
                parent::callback(
                    C('STATUS_ACCESS'), $msg, array(), array(
                        'url' => '',
                        'success' => 0,
                    )
                );
                exit;
            }
            
            $userInfos = parent::get_session('user_info');
            $accountModel = $this->db['AccountWeixin'];
            $where = array(
                'id' => $accountId,
                'users_id' => $userInfos['id'],
            );
            $datas = array(
                'follower_shot' => $uploadUrl,
                'fans_num'      => $fansNums
            );
            $status = $accountModel->where($where)->save($datas);
            if ($status !== false) {
                echo json_encode(array(
                    'url' => $uploadUrl,
                    'msg' => '图片上传成功',
                    'success'  => true
                ));
            } else {
                echo json_encode(array(
                    'url' => $uploadUrl,
                    'msg' => '数据保存错误',
                    'success'  => false
                ));
            }
            exit;
        } else {
            parent::callback(C('STATUS_ACCESS'), 'error', array(), array(
                'url' => '',
                'success' => 0,
            ));
        }
    }
	
    /**
     * 获取标签
     * 
     * @author lurongchang
     * @date   2014-10-29
     * @return void
     */
    public function getTags()
    {
        if ($this->isGet()) {
            $tagId = I('get.id', '', 'addslashes');
			$tagIds = explode(',', $tagId);
            
            $lists = $this->db['CategoryTags']->getTagsList($tagIds);
			$datas = array();
            foreach ($lists AS $key => $value) {
                $datas[$value['parent_id']][] = array(
                    'id'    => $value['id'],
                    'val'   => $value['val'],
                    'name'  => $value['title']
                );
            }
            
            parent::callback(1, 'success', $datas, array(
                'code' => 1000
            ));
        } else {
            parent::callback(C('STATUS_ACCESS'), 'error');
        }
    }
	
}