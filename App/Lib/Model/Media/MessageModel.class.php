<?php
// 站内短信
class MessageModel extends Model 
{
	/**
     * 添加站内短信
     * @param array	$arry   消息表数组
     * 
     * @author bumtime
     * @date   2014-12-19
     * @return bool
     */
	public function messageAdd($arry)
	{
		$id =  $this->add($arry);
	}
	
	
	/**
     * 取站内短信
     * @param array	$where  条件
     * 
     * @author bumtime
     * @date   2014-12-21
     * @return array 
     */
	public function getInfo($where, $field= "*")
	{
		return $this->field($field)->where($where)->select();
	}
	
	
	/**
     * 列表
     * 
     * @param array	$where  条件
     * @param array	$field  提取的字段
     * @param array	$start  起始值
     * @param array	$limmit  记录值
     * 
     * @author bumtime
     * @date   2014-12-19
     * 
     * @return array $list
     */
	public function getList($where = array(), $field="*", $start=0, $limit=10)
	{
		$list = $this->field($field)->where($where)->limit($start,$limit)->order("messageid desc")->select();

		return $list;
	}
	
	/**
     * 总记录数
     * 
     * @param array	$where  条件
     * 
     * @author bumtime
     * @date   2014-12-21
     * 
     * @return int $count
     */
	public function getCount($where = array())
	{
		$count = $this->where($where)->count();
		return $count;
	}
	
	
	/**
     * 删除记录
     * 
     * @param array	$where  条件
     * 
     * @author bumtime
     * @date   2014-12-21
     * 
     */	
	public function messageDelete($where)
	{
		if(empty($where))
			return false;

		return $this->where($where)->delete();		
	}

	/**
     *  设为已读
     * 
     * @param array	$where  条件
     * @param array	$array  数组
     * 
     * @author bumtime
     * @date   2014-12-21
     * 
     * @return bool $bool
     */	
	public function messageSave($where, $array)
	{
		if(empty($array))
			return false;
		$bool = $this->where($where)->save($array);
		return $bool;
	}
}