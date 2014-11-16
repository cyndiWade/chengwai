<?php
// 帮助中心模型
class HelpModel extends MediaBaseModel 
{
	/**
     * 获取帮助信息列表
     * 
     * @param int $type 类型
     * 
     * @author lurongchang
     * @date   2014-10-13
     * @return bool
     */
	public function getList($type = 1)
	{
		$where = array(
            'type' => $type,
            'show_status' => 1,
            'is_del' => 0
        );
        $list = $this->field(true)->where($where)->select();
        return $list;
	}
	
	/**
     * 获取帮助信息
     * 
     * @param int $id 数据ID
     * 
     * @author lurongchang
     * @date   2014-11-16
     * @return bool
     */
	public function getInfo($id)
	{
		$where = array(
            'id' => $id,
        );
        $info = $this->field(true)->where($where)->find();
        return $info;
	}
}