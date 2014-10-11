<?php
// 新浪、腾讯微博模型
class AccountWeiboModel extends MediaBaseModel 
{
	/**
     * 批量导入社交帐号
     * 
     * @author lurongchang
     * @date   2014-09-20
     * @return void
     */
    public function addBatchAccount($data)
    {
        if (empty($data)) {
            return false;
        }
        foreach ($data AS $val) {
            if (count($val) != 23) {
                continue;
            }
            $insertData[] = array(
                'users_id'              => intval($val[0]),
                'is_celebrity'          => intval($val[1]),
                'pt_type'               => intval($val[2]),
                'account_name'          => trim($val[3]),
                'fans_num'              => intval($val[4]),
                'yg_zhuanfa'            => floatval($val[5]),
                'yg_zhifa'              => floatval($val[6]),
                'rg_zhuanfa'            => floatval($val[7]),
                'rg_zhifa'              => floatval($val[8]),
                'dj_money'              => floatval($val[9]),
                'week_order_num'        => intval($val[10]),
                'month_order_nub'       => intval($val[11]),
                'receiving_status'      => intval($val[12]),
                'putaway_status'        => intval($val[13]),
                'tmp_receiving_status'  => intval($val[14]),
                'is_yg_status'          => intval($val[15]),
                'receiving_num_status'  => intval($val[16]),
                'receiving_num'         => intval($val[17]),
                'url_status'            => intval($val[18]),
                'trusteeship_status'    => intval($val[19]),
                'integral'              => intval($val[20]),
                'status'                => intval($val[21]),
                'is_del'                => intval($val[22]),
                'create_time'           => $_SERVER['REQUEST_TIME'],
            );
        }
        return $insertData ? $this->addAll($insertData) : false;
    }
    
    /**
     * 获取帐号列表
     * 
     * @author lurongchang
     * @date   2014-09-22
     * @return void
     */
    public function getLists($where, $order = 'id DESC', $page = 1, $pageSize = 20)
    {
        $list = $this->field(true)->where($where)->order($order)->page($page, $pageSize)->select();
        if ($list) {
            $userIds = array();
            foreach ($list AS $info) {
                $userIds[] = $info['users_id'];
            }
            // 获取用户信息
            $userInfos = D('UserMedia')->getInfoByIds(array_unique($userIds));
            $newUserInfos = array();
            // 用户ID为键名
            foreach ($userInfos AS $key => $info) {
                $newUserInfos[$info['users_id']] = $info;
            }
            unset($userInfos);
            foreach ($list AS $key => $info) {
                if (isset($newUserInfos[$info['users_id']])) {
                    $userMediaInfo = &$newUserInfos[$info['users_id']];
                    $list[$key]['iphone']       = $userMediaInfo['iphone'];
                    $list[$key]['tel_phone']    = $userMediaInfo['tel_phone'];
                    $list[$key]['email']        = $userMediaInfo['email'];
                    $list[$key]['qq']           = $userMediaInfo['qq'];
                    $list[$key]['msn']          = $userMediaInfo['msn'];
                    $list[$key]['name']         = $userMediaInfo['name'];
                    $list[$key]['company_name'] = $userMediaInfo['company_name'];
                    $list[$key]['website']      = $userMediaInfo['website'];
                    $list[$key]['is_dls']       = $userMediaInfo['is_dls'];
                }
            }
            unset($newUserInfos);
        }
        $count = $this->where($where)->count();
        return array('list' => $list, 'count' => $count);
    }
    
    /**
     * 获取某帐号数据
     * 
     * @param array  $where 条件
     * @param string $field 字段 默认true为提取所有字段
     * 
     * @author lurongchang
     * @date   2014-10-02
     * @return void
     */
    public function getAccountInfo($where, $field = true)
    {
        $info = $this->where($where)->field($field)->find();
        return $info;
    }
    
    /**
     * 通过接口获取用户数据
     * 
     * @param string $weiboId   微博昵称或ID
     * @param int    $weiboType 1新浪微博 OR 2腾讯微博
     * 
     * @author lurongchang
     * @date   2014-10-02
     * @return void
     */
    public function getInfosFromApi($weiboId, $weiboType)
    {
        if (empty($weiboId) || empty($weiboType)) {
            return array();
        }
        if ($weiboType == 1) {
            $datas = array(
                'source'        => 645773571,
                'screen_name'   => $weiboId,
            );
            $url = 'http://api.weibo.com/2/users/show.json?' . urldecode(http_build_query($datas));
        } else {
            $datas = array(
            );
            $url = 'http://api.weibo.com/2/users/show.json?' . urldecode(http_build_query($datas));
        }
        
        import("@.ORG.Util.CurlRequest");
        $ch = new CurlRequest($url);
        $apiInfos = json_decode($ch->get());
        $apiInfos = $apiInfos ? objectToArray($apiInfos) : array();
        return $apiInfos;
    }

 /**
     * 获取帐号列表
     * 
     * @param  int 	$user_id 会员ID
     * @param  string 	$pt_type 平台标识， all为所有，1为新浪，2为腾讯
     * @param  array 	$where 其他条件
     * 
     * @author bumtime
     * @date   2014-10-02

     * @return array	
     */
    public function getListsByUserID($user_id, $pt_type = 'all', $where = array())
    {
    	$where["users_id"] = $user_id ;
    	$pt_type != "all" ? $where['pt_type'] = $pt_type : "" ;
    	$list = $this->where($where)->getField("id, account_name");
    	return $list;
    }
    
    /**
     * 检查账号是否属于该会员
     * 
     * @param  int 	$account_id 媒体账号ID
     * @param  int 	$user_id 	会员ID
     * 
     * @author bumtime
     * @date   2014-10-04

     * @return bool	
     */
    public function checkAccountByUserId($account_id, $user_id)
    {
    	$users_id_new = $this->where(array("id"=>$account_id))->getField("users_id");
    	return $users_id_new == $user_id ? true : false;
    }
}