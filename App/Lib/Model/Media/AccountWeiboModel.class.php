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
                    $list[$key] = array_merge($info, $newUserInfos[$info['users_id']]);
                }
            }
            unset($newUserInfos);
        }
        $count = $this->where($where)->count();
        return array('list' => $list, 'count' => $count);
    }
}