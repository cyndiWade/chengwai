<?php
	//微博数据
	class AccountWeiboModel extends AdvertBaseModel
	{

		//获得总数和直取10条
		public function getListTen()
		{
			$count = $this->count();
			$list = $this->limit('10')->select();
			$returnArray = array('count'=>$count,'list'=>$this->setList($list));
			return $returnArray;
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
	}