<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class Local_SingleController extends Api_BaseController {
	public $perpage = 10;
	
	public function singleInfoAction() {
		//广告位 intersrc
		//$adsrc = ($intersrc) ? $intersrc . '_adp13' : 'pcg_adp13';
		list(, $ad) = Client_Service_Ad::getCanUseNormalAds(1, 4, array('ad_type'=>1,'status'=>1,'hits'=>3));
		$topad = array();
		foreach($ad as $key=>$value){
			$topad = Client_Service_Ad::getDataAd($value['ad_ptype'],$value['title'],$value['link'],$value['img']);
		    $tmp[] = array(
					'viewType'=>$topad['viewType'],
					'title'=>html_entity_decode($topad['title']),
					'imageUrl'=>$topad['imageUrl'],
					'param'=>$topad['param'],
		    		'ad_id' => $value ['id'],
			);
		}
		
		
		
		$webroot = Common::getWebRoot();
		$href = urldecode($webroot.'/Api/Local_Single/singleList');
		
		$temp['slideItems'] = $tmp;
		$temp['listGameUrl'] = $href;
		
	
		$data = $this->_signleList(1);
		header("Content-type:text/json");
		$signleList = json_encode(array(
				'success' => $data  ? true : false ,
				'msg' => '',
				'sign' => 'GioneeGameHall',
				'data' => $data,
		));
		$temp['listData'] = $signleList;
		$temp['totalCount'] = $data['totalCount'];
		$this->localOutput('','',$temp);
	}
    
	/**
	 * 单机列表
	 */
    public function singleListAction() {
    	$page = intval($this->getInput('page'));
    	$intersrc = $this->getInput('intersrc');
    	$data = $this->_signleList($page, $intersrc);
    	$this->localOutput('','',$data);
    }
    
    private  function _signleList($page, $intersrc ='') {
    	
		if ($page < 1) $page = 1;
		$webroot = Common::getWebRoot();
		
		list($total, $signle_games) = Client_Service_Channel::getList($page, $this->perpage, array('ctype'=>1, 'game_status'=>1), array('sort'=>'DESC','game_id'=>'DESC'));
		//判断游戏大厅版本
		$checkVer = $this->checkAppVersion();
		
		$tmp = Resource_Service_Games::getClientGameData($signle_games, $intersrc, $checkVer, 1);
		$hasnext = (ceil((int) $total / $this->perpage) - ($page)) > 0 ? true : false;
		
		//第一页
		if($page == 1){
			list(, $ad) = Client_Service_Ad::getCanUseNormalAds(1, 6, array('ad_type'=>1,'status'=>1,'hits'=>3));
			foreach($ad as $key=>$value){
				$topad = array();
				$topad = Client_Service_Ad::getDataAd($value['ad_ptype'],$value['title'],$value['link'],$value['img'],'','');
				if($topad['viewType']){
					$temp_data[] = array(
							'viewType'=>$topad['viewType'],
							'title'=>html_entity_decode($topad['title']),
							'imageUrl'=>$topad['imageUrl'],
							'param'=>$topad['param'],
							'ad_id' => $value ['id'],
					);
				}
			}
			$slide_data['slideItems'] = $temp_data;
			$data = array('list'=>$tmp, 'hasnext'=>$hasnext, 'curpage'=>$page, 'totalCount'=>$total,'slideData'=>json_encode($slide_data));
		}else{
			$data = array('list'=>$tmp, 'hasnext'=>$hasnext, 'curpage'=>$page, 'totalCount'=>$total);
		}

		return $data;
    }
}