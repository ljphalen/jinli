<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class Local_InformationController extends Api_BaseController {
	public $perpage = 10;
	
    /**
     * 资讯接口
     */
    public function indexAction() {
    	list($page,$gameId) = $this->_getGameIdInput();
    	list($total, $informations)  = $this->_indexList($page,$gameId);
    	$this->_getJsonData($page, $total, $informations);
    }
    
    private function _getGameIdInput() {
    	$gameId = intval($this->getInput('gameId'));
    	$page = intval($this->getInput('page'));
    
    	if(!$gameId) {
    		$this->localOutput(-1, 'not found game id', array());
    	}
    	
    	if ($page < 1) $page = 1;
    
    	return array($page, $gameId);
    }
    
    private  function _indexList($page, $gameId) {
    	$search['status'] = 1;
		$search['ntype'] = Client_Service_News::ARTICLE_TYPE_NEWS;
		$search['game_id'] = $gameId;
		$search['create_time']  = array('<=', Common::getTime());
		list($total, $informations) = Client_Service_News::getList($page, $this->perpage, $search, array('sort'=>'DESC','create_time'=>'DESC','id' =>'DESC'));
		return array($total, $informations);
    }
    
    private function _getJsonData($page, $total, $informations) {
    	$webroot = Common::getWebRoot();
    	$data = array();
    	foreach($informations as $key=>$value) {
    		if(empty($value['thumb_img'])) {
    			$img = '';
    		}else if((strpos($value['thumb_img'],'http://') !== false)){
    			$img = $value['thumb_img'];
    		} else {
    			$img = urldecode(Common::getAttachPath(). $value['thumb_img']);
    		}
    		$data[] = array(
    				'img'=>$img,
    				'title'=>html_entity_decode($value['title'],ENT_QUOTES),
    				'summary'=>html_entity_decode($value['resume'], ENT_QUOTES),
    				'id'=>$value['id'],
    				'timeStamp'=>$value['create_time']
    		);
    	}
    
    	$hasnext = (ceil((int) $total / $this->perpage) - $page) > 0 ? true : false;
    	$this->localOutput(0, '', array('list'=>$data, 'hasnext'=>$hasnext, 'curpage'=>$page, 'totalCount'=>$total));
    }
}