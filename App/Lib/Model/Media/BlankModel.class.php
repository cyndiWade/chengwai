<?php
// 支付方式选择模型
class BlankModel extends MediaBaseModel 
{
	/**
     * 获取信息列表
     * 
     * @param array  $where     搜索条件
     * @param string $order     排序
     * @param int    $page      页数
     * @param int    $pagesize  每页数量
     * 
     * @author lurongchang
     * @date   2014-10-21
     * @return bool
     */
	public function getList($where, $order = 'id DESC', $page = 1, $pageSize = 20)
	{
        $list = $this->field(true)->where($where)->order($order)->page($page, $pageSize)->select();
        $total = $this->where($where)->count();
        return array('total' => $total, 'list' => $list);
	}
}