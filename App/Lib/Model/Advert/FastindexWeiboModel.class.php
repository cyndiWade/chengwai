<?php
	
	class FastindexWeiboModel extends AdvertBaseModel
	{

		//接受参数
		public function getPostArray($array)
		{
			//过滤数据
			$addvalue = array();
			foreach($array as $key=>$value)
			{
				$addvalue[$key] = addslashes($value);
			}
			if(!empty($addvalue))
			{
				$where = $this->getWhere($addvalue);
				//判断是分页提交还是分栏提交
				$limit = 10;
				if( ($addvalue['p']!='')  &&　($addvalue['count']!='') )
				{
					//总数
					$count = $addvalue['count'];
					//分页 p
					$p = $addvalue['p'];
					$p_limit = ($p - 1) * 10;
					$list = $this->where($where)
					->table('app_fastindex_weibo as w')
					->join('app_account_weibo as b on b.id = w.weibo_id')
					->limit($p_limit,$limit)->field('b.*')->select();
					return $new_list = array('list'=>$this->setList($list),'p'=>$p,'count'=>$count);
				}else{
					$count = $this->where($where)->count();
					$list = $this->where($where)
					->table('app_fastindex_weibo as w')
					->join('app_account_weibo as b on b.id = w.weibo_id')
					->limit(0,$limit)->field('b.*')->select();
					return $new_list = array('list'=>$this->setList($list),'p'=>1,'count'=>$count);
				}
			}
		}

		//生成HTML静态文件
		private function setList($array)
		{
			$str = '';
			foreach($array as $value)
			{
				$str .= '<tr>
					<td class="t1">
						<div class="weibopart"><input type="checkbox" class="check" />
							<img src="" class="wbimg" />
							<div class="grp fl">
								<h5 class="l"><b>全球头条新闻</b><span class="heart"></span><span class="heart"></span></h5>
								<div class="address l">'.$value['account_name'].'</div>
								<div class="desc l">简介：全球头条新闻全球头条新闻</div>
							</div>
						</div>
					</td>
					<td class="t2"><b class="red">'.$value['fans_num'].'万</b></td>
					<td class="t3">1.00</td>
					<td class="t4">'.$value['yg_zhuanfa'].'元</td>
					<td class="t5">'.$value['rg_zhuanfa'].'元</td>
					<td class="t6">'.$value['yg_zhifa'].'元</td>
					<td class="t7">'.$value['rg_zhuanfa'].'元</td>
					<td class="t8">'.$value['week_order_num'].'</td>
					<td class="t9"><div class="sex"><p>男：占20%</p><p>女：占20%</p></div></td>
					<td class="t10"><div class="date"><p class="select">09月10日</p><p>09月11日</p><p>09月12日</p></div></td>
					<td class="last"><div class="ctrl"><span>详情</span><span>收藏</span><span>拉黑</span></div></td>
			  	</tr>';
			}
			return $str;
		}


		//生成不同的WHERE子句
		private function getWhere($addslArray)
		{
			$wheres = array();
			//处理分类数据 常见分类
			if($addslArray['cjfl']!='')
			{
				$wheres['common'] = $addslArray['cjfl'];
			}
			//粉丝数量
			if($addslArray['fans_num']!='')
			{
				$wheres['fans_num'] = $this->getLeftRightstr($addslArray['fans_num'],'-');
			}
			//价格区间
			if($addslArray['zfjg_type']!='')
			{
				switch ($addslArray['zfjg_type']) {
					case 1:
						$wheres['yg_zhuanfa'] = $this->getLeftRightstr($addslArray['jg'],'-');
					break;
					case 2:
						$wheres['yg_zhifa'] = $this->getLeftRightstr($addslArray['jg'],'-');
					break;
					case 3:
						$wheres['rg_zhuanfa'] = $this->getLeftRightstr($addslArray['jg'],'-');
					break;
					case 4:
						$wheres['rg_zhifa'] = $this->getLeftRightstr($addslArray['jg'],'-');
					break;
				}
			}
			//性别区分
			if($addslArray['fans_sex']!='')
			{
				$wheres['fans_sex'] = $addslArray['fans_sex'];
			}
			return $wheres;
		}


		//对价格进行分区
		private function getLeftRightstr($string,$needle)
		{
			$str_array = array();
			$num = strpos($string,$needle);
			$left = substr($string,0,$num);
			$right = substr($string,$num+1);
			if($left!='' && $right=='')
			{
				$str_array = array('GT',$left);
			}else if($left=='' && $right!='')
			{
				$str_array = array('LT',$right);
			}else{
				$str_array = array('between',array($left,$right));
			}
			return $str_array;
		}

	}