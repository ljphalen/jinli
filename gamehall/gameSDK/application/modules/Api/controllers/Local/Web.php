<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class Local_WebController extends Api_BaseController {
	public $perpage = 10;
	
	public function webInfoAction() {
		//广告位 intersrc
		//$adsrc = ($intersrc) ? $intersrc . '_adn12' : 'olg_adn12';
// 		list(, $ad) = Client_Service_Ad::getCanUseNormalAds(1, 6, array('ad_type'=>1,'status'=>1,'hits'=>2));
		$ad = Game_Api_WebRecommendBanner::getOldVersionBannerData();
		foreach($ad as $key=>$value){
			$topad = array();
			$topad = Client_Service_Ad::getDataAd($value['ad_ptype'],$value['title'],$value['link'],$value['img'],'','');
			if($topad['viewType']){
			    $tmp[] = array(
						'viewType'=>$topad['viewType'],
						'title'=>html_entity_decode($topad['title']),
						'imageUrl'=>$topad['imageUrl'],
						'param'=>$topad['param'],
			    		'ad_id' => $value ['id'],
				);
			}
		}
		$temp['slideItems'] = $tmp;
		
		$webroot = Common::getWebRoot();
		$href = urldecode($webroot.'/Api/Local_Web/webList');
		$temp['listGameUrl'] = $href;
	
		$data = $this->_webList(1);
		header("Content-type:text/json");
		$webList = json_encode(array(
				'success' => $data  ? true : false ,
				'msg' => '',
				'sign' => 'GioneeGameHall',
				'data' => $data,
		));
		$temp['listData'] = $webList;
		$temp['totalCount'] = $data['totalCount'];
		$this->localOutput('','',$temp);
	}
    
	/**
	 * 网游列表
	 */
    public function webListAction() {
    	$page = intval($this->getInput('page'));
    	$intersrc = $this->getInput('intersrc');
    	$data = $this->_webList($page, $intersrc);
    	$this->localOutput('','',$data);
    }
    
    private  function _webList($page, $intersrc = '') {
    	
		if ($page < 1) $page = 1;
		$webroot = Common::getWebRoot();
		
		list($total, $web_games) = Client_Service_Channel::getList($page, $this->perpage, array('ctype'=>2, 'game_status'=>1), array('sort'=>'DESC','game_id'=>'DESC'));
		//判断游戏大厅版本
		$checkVer = $this->checkAppVersion();
		$tmp = Resource_Service_Games::getClientGameData($web_games, $intersrc, $checkVer, 1);
		$hasnext = (ceil((int) $total / $this->perpage) - ($page)) > 0 ? true : false;
		$data = array('list'=>$tmp, 'hasnext'=>$hasnext, 'curpage'=>$page, 'totalCount'=>$total);
		return $data;
    }
}