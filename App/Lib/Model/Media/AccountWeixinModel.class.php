<?php
// 微信公众号模型
class AccountWeixinModel extends MediaBaseModel 
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
            if (count($val) != 18) {
                continue;
            }
            $insertData[] = array(
                'users_id'              => intval($val[0]),
                'big_type'              => intval($val[1]),
                'pt_type'               => intval($val[2]),
                'account_name'          => trim($val[3]),
                'fans_num'              => intval($val[4]),
                'dtb_money'             => floatval($val[5]),
                'dtwdyt_money'          => floatval($val[6]),
                'dtwdet_money'          => floatval($val[7]),
                'dtwqtwz_money'         => floatval($val[8]),
                'dj_money'              => floatval($val[9]),
                'week_order_num'        => intval($val[10]),
                'month_order_nub'       => intval($val[11]),
                'audit_status'          => intval($val[12]),
                'receiving_status'      => intval($val[13]),
                'putaway_status'        => intval($val[14]),
                'tmp_receiving_status'  => intval($val[15]),
                'status'                => intval($val[16]),
                'is_del'                => intval($val[17]),
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
        return $apiInfos;
    }
}