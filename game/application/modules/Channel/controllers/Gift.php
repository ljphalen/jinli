<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghua
 *
 */
class GiftController extends Client_BaseController{
	
	public $actions = array(
		'listUrl' => '/channel/gift/index',
		'detailUrl' => '/channel/gift/detail/',
		'tjUrl' => '/channel/index/tj'
	);

	public $perpage = 10;
	/**
	 * 
	 * index page view
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		$intersrc = $this->getInput('intersrc');
		if(!$imei){
			$sp = $this->getInput('sp');
			$imei = array_shift(explode('_',$sp));
			Util_Cookie::set("imei", $imei, true, Common::getTime() + strtotime("+1 days"));
		} else {
			$imei = Util_Cookie::get('imei', true);
		}
		
		if(!$imei) exit;
		
		if ($page < 1) $page = 1;
		//礼包列表
		list($total, $gifts) = Client_Service_Gift::getList($page, $this->perpage,array('status'=>1,'game_status'=>1));
		$tmp = $imgs = array();
		foreach($gifts as $key=>$value){
			$logs = $remain_gifts = array();
			$imgs[$value['id']][] = Resource_Service_Games::getGameAllInfo(array('id'=>$value['game_id']));
			//礼包领取记录
    	    $logs = Client_Service_Giftlog::getByImeiGiftId(crc32(trim($imei)),$value['id']);
    	    //剩下的激活码数量
    	    $remain_gifts = Client_Service_Giftlog::getGiftlogByStatus(0,$value['id']); 
    	    $tmp[$value['id']][] = ($logs ? "已抢到" : (count($remain_gifts) ? "剩余：".count($remain_gifts)."个" :"大侠，礼包已经被抢完了"));
		}
		
		$hasnext = (ceil((int) $total / $this->perpage) - 1) > 0 ? true : false;
		$this->assign('hasnext', $hasnext);
		$this->assign('page', $page);
		$this->assign('gifts', $gifts);
		$this->assign('numlog', $tmp);
		$this->assign('imgs', $imgs);
		$this->assign('source', $this->getSource());
		$this->assign('intersrc', $intersrc);		
	}
	
	/**
	 * get game list as more
	 */
	public function moreAction() {
	    $page = intval($this->getInput('page'));
	    $intersrc = $this->getInput('intersrc');
		if ($page < 1) $page = 1;
		$imei = Util_Cookie::get('imei', true);
		if(!$imei) exit;
		//礼包列表
		list($total, $gifts) = Client_Service_Gift::getList($page, $this->perpage,array('status'=>1,'game_status'=>1));
		$webroot = Common::getWebRoot();
		$temp = array();
		foreach($gifts as $key=>$value) {
			if(!$intersrc) $intersrc = 'libao_olg'.$value['id'];
			
			$log = $remain_gifts = $not_gifts = $info = array();
			//礼包领取记录
			$log = Client_Service_Giftlog::getByImeiGiftId(crc32(trim($imei)),$value['id']);
			//剩下的激活码数量
			$remain_gifts = Client_Service_Giftlog::getGiftlogByStatus(0,$value['id']);
			
			$info = Resource_Service_Games::getGameAllInfo(array('id'=>$value['game_id']));
			
			$href = urldecode($webroot.$this->actions['detailUrl']. '?id=' . $value['game_id'].'&intersrc='.$intersrc.'&t_bi='.$this->getSource());
			$temp[$key]['title'] = $value['name'];
			$temp[$key]['isGrab'] = ($log ? "true" : "false");
			$temp[$key]['giftNum'] = ($log ? "已抢到" : (count($remain_gifts) ? "剩余：".count($remain_gifts)."个" :"大侠，礼包已经被抢完了"));
			$temp[$key]['data-type'] = 2;
			$temp[$key]['link'] = Common::tjurl($this->actions['tjUrl'], $value['id'], $intersrc, $href);
			$temp[$key]['img'] = urldecode(Common::getAttachPath().$info['img']);
			$temp[$key]['data-infpage'] = $value['name'].','.$href.','.$value['game_id'].','.$value['id'];
			
		}
		
		$hasnext = (ceil((int) $total / $this->perpage) - ($page)) > 0 ? true : false;
		$this->output(0, '', array('list'=>$temp, 'hasnext'=>$hasnext, 'curpage'=>$page));
		$this->assign('hasnext', $hasnext);
		$this->assign('page', $page);
	}
	
	public function detailAction() {
		$this->assign('content', '礼包页面');
	}
}
