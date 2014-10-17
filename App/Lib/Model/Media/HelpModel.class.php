<?php
// 帮助中心模型
class HelpModel extends MediaBaseModel 
{
	/**
     * 获取帮助信息列表
     * 
     * @param array $where     搜索条件
     * @param int   $page      页数
     * @param int   $pagesize  每页数量
     * 
     * @author lurongchang
     * @date   2014-10-13
     * @return bool
     */
	public function getList($parentId = 0, $type = 1)
	{
		$where = array(
            'type' => $type,
            // 'parent_id' => $parentId,
            'show_status' => 1,
            'is_del' => 0
        );
        $list = $this->field(true)->where($where)->select();
        return $list;
	}
}