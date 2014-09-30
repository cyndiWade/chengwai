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
            $this->redirect(U('/Media/SocialAccount/', array('type' => 1)));
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
        $priceTypeColums = array('yg_zhuanfa, yg_zhifa, dj_money, rg_zhuanfa, rg_zhifa');
        $minprice ? $where[$priceTypeColums[$pricetype]] = array('egt', $minprice) : '';
        $maxprice ? $where[$priceTypeColums[$pricetype]] = array('elt', $maxprice) : '';
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
        
        // parent::callback(1, '成功获取数据', array(
            // 'list'  => $list['list'],
            // 'count' => $list['count'],
            // 'p'     => $page
        // ));
        
        ($list['count'] && $list['list']) ? $list['list'] = $this->bulidRowCell($list['list']) : '';
        parent::callback(1, '成功获取数据', array(
            'start' => floor($start/$pageSize) * $pageSize,
            'limit' => $pageSize,
            'total' => $list['count'],
            'checkable' => 'false',
            'rows'  => $list['list'],
        ), array(
            'code' => 1000
        ));
    }
    
    /**
     * 组装成前台使用格式
     * 
     * @author lurongchang
     * @date   2014-09-20
     * @return void
     */
    private function bulidRowCell($data)
    {
        if (empty($data) || !is_array($data)) {
            return $data;
        }
        $newData = array();
        
        
        // echo '<pre>';
        // print_r($data);
        
        foreach ($data AS $key => $val) {
            $newData[] = array(
                "id"    => $val['id'],
                "cells" => array(
                    "account_id" => $val['id'],
                    "user_id" => 105153,
                    "face_url" => "http://tp4.sinaimg.cn/1032732203/50/40044194886/1",
                    "url" => "http://weibo.com/u/1032732203",
                    "weibo_type" => $val['pt_type'],
                    "weibo_id" => "1032732203",
                    "weibo_name" => $val['account_name'],
                    "cell_phone" => null,
                    "telephone" => null,
                    "qq" => null,
                    "account_type" => 2,
                    "verification_info" => $val['company_name'],
                    "is_bluevip" => 2,
                    "is_vip" => 1,
                    "is_daren" => 2,
                    "followers_count" => $val['fans_num'],
                    "tweet_price" => $val['yg_zhifa'],
                    "retweet_price" => $val['yg_zhuanfa'],
                    "content_price" => $val['yg_zhifa'],
                    "soft_tweet_price" => $val['rg_zhifa'],
                    "soft_retweet_price" => $val['rg_zhuanfa'],
                    "is_private_message_enabled" => 2,
                    "is_activated" => 0,
                    "is_shield" => 2,
                    "is_sensitive" => 2,
                    // 是否接硬广
                    "is_extend" => $val['is_yg_status'],
                    "self_register" => 1,
                    // 是否审核
                    "is_verify" => $val['audit_status'],
                    "is_auth" => 2,
                    "is_auth_ds" => 2,
                    "is_auto_send" => 2,
                    // 是否上架
                    "is_online" => $val['putaway_status'],
                    "is_price_open" => 0,
                    // 是否接单
                    "is_allow_order" => $val['receiving_status'],
                    "is_flowunit" => 2,
                    "company_black_num" => 0,
                    "orders_monthly" => 0,
                    "orders_weekly" => 0,
                    "pass_order_monthly" => "0.00",
                    "deny_order_monthly" => "0.00",
                    "posts_avgretweet_count" => "-1.000",
                    "good_rating_rate" => "暂无评价",
                    "rating_score" => 0,
                    "collection_num" => 0,
                    "is_contacted" => 1,
                    "is_open" => 1,
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
                    "is_tip" => null,
                    "tip_content" => null,
                    "level" => 1,
                    "profit_rate" => 0.3,
                    "attribute_id" => 1,
                    "tactics_flag" => 1,
                    "is_enable_micro_task" => 1,
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
                    "orderMax" => $val['receiving_num_status'] ? $val['receiving_num'] : '',
                    "weibo_type_logo" => "http://img.weiboyi.com/img/uploadimg/platform_img/sina.jpg",
                    "rate" => 1
                )
            );
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
                    $len = count($temp);
                    for ($i = 0; $i < $len; $i++) {
                        //通过循环得到EXCEL文件中每行记录的值
                        $insertData[] = explode(',', $temp[$i]);
                    }
                    
                    // 根据帐号类型插入不同的表
                    switch($accountType) {
                        case 1:
                            // 新闻媒体
                            $status = $this->db['AccountNews']->addBatchAccount($insertData);
                            break;
                        case 2:
                            // 新浪、腾讯微博
                            $status = $this->db['AccountWeibo']->addBatchAccount($insertData);
                            break;
                        case 3:
                            // 微信公众号
                            $status = $this->db['AccountWeixin']->addBatchAccount($insertData);
                            break;
                    }
                    
                    $status ? $this->success('上传成功!') : $this->error('上传失败!');
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
        $accountType    = I('type', 0, 'intval');
        
        if (empty($accountId) || empty($accountType)) {
            $msg = '社交媒体帐号或类型错误,请联系网站客服人员!';
            parent::callback(1, '', array(
                'followers_count' => $msg
            ));
        }
        // 粉丝数不足
        $fansNum = 0;
        $minFansNums = 2000;
        if ($fansNum < $minFansNums) {
            $msg = '该账号的粉丝不足' . $minFansNums . '，<a href="/auth/powerful" target="_blank">找号带粉</a>';
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
            
            $viewPath = C('PUBLIC_VISIT');
            
            if (in_array($weiboType, array(1, 2))) {
                // 新浪微博
                $retweetPrice       = I('retweet_price', 0, 'floatval');
                if (empty($retweetPrice)) {
                    parent::callback(0, '价格错误');
                }
                
                $datas['source'] = $weiboId;
                $datas['screen_name'] = $weiboId;
                import("@.ORG.Util.CurlRequest");
                $sinaUrl = 'http://api.weibo.com/2/users/show.json?' . http_build_query($datas);
                $tencentUrl = 'http://api.weibo.com/2/users/show.json?' . http_build_query($datas);
                $url = ($weiboType == 1) ? $sinaUrl : $tencentUrl;
                $ch = new CurlRequest($url);
                $apiInfos = $ch->get();
                if ($apiInfos['followers_count'] <= 499) {
                    parent::callback(0, '粉丝数至少为500!', array(), array(
                        'code'  => 900
                    ));
                }
                
                $userInfos = parent::get_session('user_info');
                $datas = array(
                    'users_id'      => $userInfos['id'],
                    'is_celebrity'  => $apiInfos['verified'],
                    'pt_type'       => ($weiboType == 1) ? 1: 2,
                    'account_name'  => $weiboId,
                    'fans_num'      => $apiInfos['followers_count'],
                    'yg_zhuanfa'    => $retweetPrice,
                    'yg_zhifa'      => $retweetPrice,
                    'rg_zhuanfa'    => $retweetPrice * 0.7,
                    'rg_zhifa'      => $retweetPrice * 0.7,
                    'create_time'   => $_SERVER['REQUEST_TIME'],
                );
                
                $weiboModel = $this->db['AccountWeibo'];
                $insertId = $weiboModel->add($datas);
                if ($insertId) {
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
                
                $userInfos = parent::get_session('user_info');
                $datas = array(
                    'users_id'          => $userInfos['id'],
                    'pt_type'           => 1,
                    'account_name'      => $weiboName,
                    'fans_num'          => $followersCount,
                    'dtb_money'         => $price,
                    'dtwdyt_money'      => $topPrice,
                    'dtwdet_money'      => $secondPrice,
                    'dtwqtwz_money'     => $otherPrice,
                    'weekly_read_avg'   => $weeklyReadAvg,
                    'male_precent'      => $malePrecent,
                    'female_precent'    => $femalePrecent,
                    'account_avatar'    => $viewPath['domain'] . $viewPath['dir'] . $uploadImgAvatar,
                    'qr_code'           => $viewPath['domain'] . $viewPath['dir'] . $uploadImgQrCode,
                    'follower_shot'     => $viewPath['domain'] . $viewPath['dir'] . $uploadImgFollowers,
                    'create_time'       => $_SERVER['REQUEST_TIME'],
                );
                
                $weixinModel = $this->db['AccountWeixin'];
                $insertId = $weixinModel->add($datas);
                if ($insertId) {
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
                $isNewsSource       = I('is_news_source', 0, 'intval');
                $isWebSiteIncluded  = I('is_web_site_included', 0, 'intval');
                $isNeedSource       = I('is_need_source', 0, 'intval');
                $url                = I('url', '', 'setString');
                $isPressWeekly      = I('is_press_weekly', 0, 'intval');
                $isTextLink         = I('is_text_link', 0, 'intval');
                $imgPath            = I('uploadImgMedia', '', 'setString');
                
                if (empty($retweetPrice)) {
                    parent::callback(0, '价格错误');
                }
                if (empty($isNewsSource)) {
                    parent::callback(0, '是否新闻源?');
                }
                if (empty($isWebSiteIncluded)) {
                    parent::callback(0, '是否网址收录?');
                }
                if (empty($isNeedSource)) {
                    parent::callback(0, '是否需要来源?');
                }
                if (empty($isPressWeekly)) {
                    parent::callback(0, '周末能否发稿?');
                }
                if (empty($isTextLink)) {
                    parent::callback(0, '能否带文本链接?');
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
                        'big_type'      => 1,
                        'pt_type'       => 1,
                        'account_name'  => $weiboId,
                        'web_type'      => $isWebSiteIncluded,
                        'money'         => $retweetPrice,
                        'url_type'      => $isNewsSource,
                        'url_status'    => $isTextLink,
                        'media_shot'    => $viewPath['domain'] . $viewPath['dir'] . $imgPath,
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
            parent::callback(0, 'error');
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
            
            
            // {"code":1000,"msg":"success","data":[false]}
            parent::callback(1, 'success', array(false), array(
                'code' => 1000
            ));
        } else {
            parent::callback(0, 'error');
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
            parent::callback(0, 'error');
        }
    }
	
}