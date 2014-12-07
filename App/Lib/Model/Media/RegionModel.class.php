<?php
// 地区模型
class RegionModel extends MediaBaseModel 
{
	/**
     * 根据ID获取同级地区
     * 
     * @author lurongchang
     * @date   2014-09-20
     * @return void
     */
    public function getAreaById($areaId)
    {
        $areaList = $this->where(array('parent_id' => $areaId))
            ->field(true)->order('region_id ASC')->select();
        return $areaList;
    }
}