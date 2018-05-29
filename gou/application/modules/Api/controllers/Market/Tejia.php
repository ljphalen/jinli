<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * @author tiansh
 *
 */
class Market_TejiaController extends Api_BaseController {
	
	public $perpage = 12;
	
	public function tejiaAction() {
		$category = intval($this->getInput('category'));
		$version = intval($this->getInput('version'));
		$server_version = Gou_Service_Config::getValue('Channel_Goods_Version');
		$page = intval($this->getInput('page'));
		$perpage = $this->perpage;		 
		if ($page < 1) $page = 1;
		
		if ($version != $server_version){
			$search['status'] = 1;
			$search['start_time'] = array('<', time());
			$search['end_time'] = array('>', time());
			if($category) $search['goods_type'] = intval($category);
			
			$configs = Common::getConfig('tejiaConfig');
			
			list($total, $goodsList) = Client_Service_Channelgoods::getList($page, $perpage, $search, array('sort'=>'DESC', 'id'=>'DESC'));
			
			$data = array();
			if($goodsList) {
				foreach ($goodsList as $key=>$val){
				    $img = $val['img'];
				    if ($val['supplier'] == 2 && strpos(html_entity_decode($val['img']), 'www.yigw.net') !== false) {
				        $img = str_replace('www.yigw.net', 'img.ytaow.cn', html_entity_decode($val['img']));
				    }
					$data[] = array(
					'id'=>$val['id'],
					'title'=>Util_String::substr(html_entity_decode($val['title']), 0, 20, '', true),
					'category'=>$val['goods_type'],
					'from'=>$configs[$val['supplier']]['name'],
					'market_price'=>$val['market_price'],
					'sale_price'=>$val['sale_price'],
					'discount'=>round($val['sale_price']/$val['market_price'],2) * 10,
					'link'=>Client_Service_Channelgoods::getShortUrl(Stat_Service_Log::V_APK, $val),
					'img'=>$img
				);
				}
			}
			
			//category
			$goods_type_array = array();
			if($category == 0 && $page == 1){
			    $goods_type_list = Client_Service_Channelgoodscate::getAllCategory();
			    $goods_type_array[0] = array(
			            'id'=>0,
			            'name'=>'全部'
			    );
			    	
			    if (!empty($goods_type_list)) {
			        foreach ($goods_type_list as $val){
			            $goods_type_array[] = array(
			                    'id'=>$val['id'],
			                    'name'=>html_entity_decode($val['title'])
			            );
			        }
			    }
			}
		}
		
		$hasnext = (ceil((int) $total / $this->perpage) - ($page)) > 0 ? true : false;
		$this->output(0, '', array('list'=>$data, 'version'=>$server_version,
		        'category'=>$goods_type_array,
		        'curpage'=>$page,
		        'hasnext'=>$hasnext));
	}
}
