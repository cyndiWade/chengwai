<?php
// 媒体主信息模型
class UserMediaModel extends AppBaseModel  
{
	//判断信息是否存在
	public function add_account_list($array)
	{
		if($this->account_is_have($array['users_id'])=='')
		{
			$this->add($array);
		}else{
			
		}
	}
	
	//通过账号验证账号是否存在
	public function account_is_have($id) 
	{
		return $this->where(array('users_id'=>$id))->getField('id');
	}

	//检车手机号是否存在
	public function iphone_is_have ($iphone) {
		return $this->where(array('iphone'=>$iphone))->getField('id');
	}
    
    /**
     * 根据用户ID获取用户信息
     * 
     * @param array $ids 用户ID
     * 
     * @author lurongchang
     * @date   2014-09-23
     * @return void
     */
    public function getInfoByIds($ids)
    {
        if (empty($ids)) {
            return array();
        }
        // 不采用联表已兼容往后数据量大产生查询缓慢
        $where = array(
            'id'  => array('IN', $ids),
            'is_del'    => 0
        );
        $ids = M('Users')->where($where)->getField('id', true);
        $lists = $this->field(true)->where(array('users_id' => array('IN', $ids)))->select();
        return $lists;
    }
    
    /**
     * 更新媒体主信息
     * 
     * @param array $condition 条件
     * @param array $array 数据
     * 
     * @author lurongchang
     * @date   2014-09-30
     * @return void
     */
    public function saveAccount($condition, $data)
    {
        $status = $this->where($condition)->save($data);
        return $status === false ? false : true;
    }
    
    /**
     * 新建媒体主信息
     * 
     * @param array $datas 数据
     * 
     * @author lurongchang
     * @date   2014-09-30
     * @return void
     */
    public function addAccount($datas)
    {
        if (empty($datas['users_id']) || empty($datas['iphone'])
        || empty($datas['qq']) || empty($datas['company_name'])) {
            return false;
        }
        return $this->add($datas);
    }
    
	//塞入价格
	public function insertPirce($user_id,$price,$adverttype,$generalizeid)
	{
		$money = $this->where(array('users_id'=>$user_id))->field('money')->find();
		$update['money'] = $money['money'] + $price;
		$this->where(array('users_id'=>$user_id))->save($update);
		$add['users_id'] = $user_id;
		$add['shop_number'] = 'SL'.time();
		$add['money'] = $price;
		$add['type'] = 5;
		$add['adormed'] = 1;
		$add['member_info'] = '收入';
		$add['admin_info'] = '收入';
		$add['time'] = time();
		//$add['adverttype'] = $adverttype;
		//$add['generalizeid'] = $generalizeid;
		$add['status'] = 1;
		D('Fund')->add($add);
	}
	
	/**
     * 根据用户ID获取用户信息
     * 
     * @param int $id 用户ID
     * @param array $field 查找的字段
     * 
     * @author bumtime
     * @date   2014-12-15
     * @return void
     */
    public function getInfoById($id, $field)
    {
        if (empty($id)) {
            return array();
        }
        $info = $this->field($field)->where(array('users_id' =>$id))->find();
        return $info;
    }
}