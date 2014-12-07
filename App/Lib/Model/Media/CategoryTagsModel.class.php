<?php
// 标签模型
class CategoryTagsModel extends MediaBaseModel 
{
	/**
     * 根据ID获取同级地区
     * 
     * @author lurongchang
     * @date   2014-10-29
     * @return void
     */
    public function getTagsList($parentId)
    {
        $where = array(
			'parent_id' 	=> array('IN', $parentId),
			'show_status' 	=> 1,
			'is_del' 		=> 0,
		);
		$tagList = $this->where($where)->field(true)->select();
        return $tagList;
    }
}