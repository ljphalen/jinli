<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class StrategyController extends Api_BaseController {
	public $perpage = 10;
	/**
	 * subject list
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
	    $id = $this->getInput('id');
	    $sp = $this->getInput('sp');
	    //判断游戏大厅版本
	    $checkVer = $this->checkAppVersion();
		if ($page < 1) $page = 1;
	    
		$search['status'] = 1;
		$search['ntype'] = 4;
		$search['game_id'] = $id;
		$search['create_time']  = array('<=', Common::getTime());
		list($total, $strategys) = Client_Service_News::getList($page, $this->perpage, $search, array('sort'=>'DESC','create_time'=>'DESC','id' =>'DESC'));
		$webroot = Common::getWebRoot();
		$temp = array();
		foreach($strategys as $key=>$value) {
			$intersrc = 'detail'.$id.'_strategy'.$value['id'];
			$href = urldecode($webroot. '/client/strategy/detail/?id=' . $value['id'].'&intersrc='.$intersrc.'&t_bi='.$this->getSource().'&sp='.$sp);
			$temp[$key]['id'] = $value['id'];
			$temp[$key]['title'] = $value['title'];
			$temp[$key]['resume'] = $value['resume'];
			$temp[$key]['link'] = Common::tjurl($webroot.'/client/index/tj', $value['id'], $intersrc, $webroot.'/strategy/detail/?id='.$value['id'].'&intersrc='.$intersrc.'&sp='.$sp);
			$temp[$key]['img'] = ($value['thumb_img'] ?  urldecode(Common::getAttachPath().$value['thumb_img']) : '');
			$temp[$key]['create_time'] = date('Y-m-d',$value['create_time']);
			$temp[$key]['data-infpage'] = '攻略详情,'.$href;
			if($checkVer >= 2){
				$temp[$key]['data-type'] = 0;
			}
		}
		
		$hasnext = (ceil((int) $total / $this->perpage) - $page) > 0 ? true : false;
		$this->output(0, '', array('list'=>$temp, 'hasnext'=>$hasnext, 'curpage'=>$page));
	}
}
