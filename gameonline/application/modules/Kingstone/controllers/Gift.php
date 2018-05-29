<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghua
 *
 */
class GiftController extends Kingstone_BaseController{
	
	public $actions = array(
		'listUrl' => '/kingstone/gift/index',
		'detailUrl' => '/kingstone/gift/detail/',
		'tjUrl' => '/kingstone/index/tj'
	);

	public $perpage = 10;
	/**
	 * 
	 * index page view
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		$intersrc = $this->getInput('intersrc');
		$sp = $this->getInput('sp');
		$imei = end(explode('_',$sp));
		$imei = crc32(trim($imei));
		
		if ($page < 1) $page = 1;
		//礼包列表
		$search = array('status' => 1, 'game_status'=>1);
		$search['effect_start_time'] = array('<=', Common::getTime());
		$search['effect_end_time'] = array('>=', Common::getTime());
		list($total, $gifts) = Client_Service_Gift::getList(1, $this->perpage,$search);
		$tmp = $imgs = array();
		foreach($gifts as $key=>$value){
			$logs = $remain_gifts = array();
			$imgs[$value['id']][] = Resource_Service_Games::getGameAllInfo(array('id'=>$value['game_id']));
			//礼包领取记录
    	    $logs = Client_Service_Giftlog::getByImeiGiftId($imei,$value['id']);
    	    //剩下的激活码数量
    	    $remain_gifts = Client_Service_Giftlog::getGiftlogByStatus(0,$value['id']); 
    	    $tmp[$value['id']][] = ($logs ? "已抢到" : ($remain_gifts ? "剩余：".$remain_gifts."个" :"大侠，礼包已经被抢完了"));
		}
		
		$hasnext = (ceil((int) $total / $this->perpage) - 1) > 0 ? true : false;
		$this->assign('hasnext', $hasnext);
		$this->assign('page', $page);
		$this->assign('gifts', $gifts);
		$this->assign('numlog', $tmp);
		$this->assign('imgs', $imgs);
		$this->assign('sp', $sp);
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
	    $sp = $this->getInput('sp');
		$imei = end(explode('_',$sp));
		$imei = crc32(trim($imei));
		//礼包列表
		$search = array('status' => 1, 'game_status'=>1);
		$search['effect_start_time'] = array('<=', Common::getTime());
		$search['effect_end_time'] = array('>=', Common::getTime());
		list($total, $gifts) = Client_Service_Gift::getList($page, $this->perpage,$search);
		$webroot = Common::getWebRoot();
		$temp = array();
		$intersrc = ($intersrc) ? $intersrc : 'olg_libao';
		foreach($gifts as $key=>$value) {
			$log = $remain_gifts = $not_gifts = $info = array();
			//礼包领取记录
			$log = Client_Service_Giftlog::getByImeiGiftId($imei,$value['id']);
			//剩下的激活码数量
			$remain_gifts = Client_Service_Giftlog::getGiftlogByStatus(0,$value['id']);
			
			$info = Resource_Service_Games::getGameAllInfo(array('id'=>$value['game_id']));
			
			$href = urldecode($webroot.$this->actions['detailUrl']. '?id=' . $value['game_id'].'&pc=2&intersrc=' . $intersrc . $value['id'] . '&t_bi='.$this->getSource());
			$temp[$key]['title'] = $value['name'];
			$temp[$key]['isGrab'] = ($log ? "true" : "false");
			$temp[$key]['giftNum'] = ($log ? "已抢到" : ($remain_gifts ? "剩余：".$remain_gifts."个" :"大侠，礼包已经被抢完了"));
			$temp[$key]['data-type'] = 2;
			$temp[$key]['img'] = urldecode($info['img']);
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
