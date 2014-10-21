<?php
// 支付方式选择模型
class BlankModel extends MediaBaseModel 
{
	/**
     * 获取信息列表
     * 
     * @param array $where     搜索条件
     * @param int   $page      页数
     * @param int   $pagesize  每页数量
     * 
     * @author lurongchang
     * @date   2014-10-21
     * @return bool
     */
	public function getList($where, $page = 1, $pageSize = 20)
	{
		$list = $this->where($where)->page($page, $pageSize)->order('id DESC')->select();
        $total = $this->where($where)->count();
        return array('total' => $total, 'list' => $list);
	}
}