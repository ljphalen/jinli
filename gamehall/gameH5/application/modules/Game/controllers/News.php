<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghua
 *
 */
class NewsController extends Game_BaseController{
	
	public $actions = array(
	                'listUrl' => '/News/index',
	                'listAjax' => '/News/listAjax',
	                'detailUrl' => '/News/detail/',
	                'indexlUrl' => '/index/detail/',
	                'tjUrl' => '/index/tj'
	);

	public $perpage = 10;
	public $out_link = "http://wap.fwan.cn/info/infos/";

	private static $type = array(
	                Client_Service_News::ARTICLE_TYPE_NEWS => '资讯',
	                Client_Service_News::ARTICLE_TYPE_ACTIVITY => '活动'
	);
	
	/**
	 * 
	 * index page view
	 */
	public function indexAction() {
		$data = $this->getNewsData();
		$this->assign('data', json_encode($data));
	}
	
	public function listAjaxAction() {
	    $data = $this->getNewsData();
	    $this->ajaxOutput($data);
	}
	
	private function getNewsData() {
	    $page = $this->getPageInput();
	    list($total, $news) = Client_Service_News::getUseNews($page, $this->perpage, array('ntype' => array('IN',array(1,3)), 'create_time' => array('<=', Common::getTime()), 'status'=>1));
	    $attachPath = Common::getAttachPath();
	    $list = array();
	    foreach ($news as $value) {
	        $item = array();
	        $typeName = self::$type[$value['ntype']];
	        $item['name'] =  "【{$typeName}】".$value['title'];
	        $item['date'] = date('Y-m-d', $value['create_time']);
	        $item['href'] = $this->actions['detailUrl'].'?id='.$value['id'];
	        if(empty($value['thumb_img'])) {
	            $thumbImg = '';
	        } elseif((strpos($value['thumb_img'],'http://') !== false)){
	            $thumbImg = $value['thumb_img'];
	        } else{
	            $thumbImg = $attachPath . $value['thumb_img'];
	        }
	        $item['imgUrl'] = $thumbImg;
	        $item['info'] = $value['resume'];
	        $list[] = $item;
	    }
	    $hasNext = (ceil((int) $total / $this->perpage) - ($page)) > 0 ? true : false;
	    $data = array(
	                    'list' => $list,
	                    'hasNext' => $hasNext,
	                    'curPage' => $page,
	                    'ajaxUrl' => $this->actions['listAjax']
	    );
	    return $data; 
	}
	
	public function detailAction(){
		$id = intval($this->getInput('id'));
		$info = Client_Service_News::getNews($id);
		$data = array();
        $data['name'] =  $info['title'];
        $data['original'] =  $info['fromto'];
        $data['date'] = date('Y-m-d', $info['create_time']);
        if ($info['ctype'] != 2) {
        	$data['href'] = $this->out_link.$info['out_id'].'.shtml';
        }
        $data['href'] = $this->actions['detailUrl'].'?id='.$info['id'];
        $data['info'] = html_entity_decode($info['content']);
        $this->assign('data', json_encode($data));
	}
}
