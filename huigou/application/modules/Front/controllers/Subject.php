<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class SubjectController extends Front_BaseController {
	public $actions = array(
			'indexUrl' => '/subject/index',
			'detailinfooUrl' => '/subject/detailinfo',
	);
	public $perpage = 10;

	/**
	 * 
	 */
	public function indexAction() {
    	$page = intval($this->getInput('page'));
    	$title = "主题汇";
		if ($page < 1) $page = 1;
		$perpage = $this->perpage;
		list($total, $subjects) = Gc_Service_Subject::getCanUseSubjects($page, $this->perpage,array());
		$this->assign('subjects', $subjects);
		
		$counts = Gc_Service_TaokeGoods::getCountBySubjectId();
		$temp = array();
		foreach($counts as $key=>$value) {
			$temp[$value['subject_id']] = $value;
		}
		$this->assign('counts', $temp);
		$this->assign('title', $title);
	}
	
	/**
	 * get subject detail
	 */
	public function detailAction() {
		$id = intval($this->getInput('id'));
		$info = Gc_Service_Subject::getSubject($id);
		$this->assign('info', $info);
		$title = $info['title'];
		$page = intval($this->getInput('page'));
		
		if ($page < 1) $page = 1;
		//get goods list
		list($total, $goods) = Gc_Service_TaokeGoods::getNomalGoods($page, $this->perpage, array('subject_id'=>$id));	
		$hasnext = (ceil((int) $total / $this->perpage) - 1) > 0 ? true : false;

		
		$this->assign('hasnext', $hasnext);
		$this->assign('goods', $goods);
		$this->assign('title', $title);
		$this->assign('id', $id);
	}
	
	
	/**
	 * get subject detail
	 */
	public function publWrapAction() {
		$id = intval($this->getInput('id'));
		$info = Gc_Service_Subject::getSubject($id);
		$this->assign('info', $info);
		$title = $info['title'];
		$page = intval($this->getInput('page'));
	
		if ($page < 1) $page = 1;
		//get goods list
		list($total, $goods) = Gc_Service_TaokeGoods::getList($page, $this->perpage, array('subject_id'=>$id));
	
		$temp = array();
		$webroot = Yaf_Application::app()->getConfig()->webroot;
		foreach($goods as $key=>$value) {
			$temp[$key]['title'] = $value['title'];
			$temp[$key]['href'] = $webroot . '/subject/detail/?id='.$value['id'];
			if(strpos($value['img'], 'http://') === false) {
				$temp[$key]['img'] = $webroot .'/attachs'.$value['img'];
			}else{
				$temp[$key]['img'] = $value['img'].'_200x200.'.end(explode(".",  $value['img']));
			};
			$temp[$key]['price'] = $value['price'];
			$temp[$key]['want'] = $value['want'] + $value['default_want'];
		}
	
		$hasnext = (ceil((int) $total / $this->perpage) - ($page)) > 0 ? true : false;
		$this->output(0, '', array('list'=>$temp, 'hasnext'=>$hasnext, 'curpage'=>$page));
		$this->assign('hasnext', $hasnext);
		$this->assign('goods', $goods);
		$this->assign('total', $total);
		$this->assign('title', $title);
		$this->assign('id', $id);
	}
	
	/**
	 * get more subjects 
	 */
	public function moreAction() {
		$page = intval($this->getInput('page'));
		
		$counts = Gc_Service_TaokeGoods::getCountBySubjectId();
		$counts = Common::resetKey($counts, 'subject_id');

		list($total, $subjects) = Gc_Service_Subject::getCanUseSubjects($page, $this->perpage,array());
		
		$webroot = Yaf_Application::app()->getConfig()->webroot;
		foreach($subjects as $key=>$value) {
			$subjects[$key]['title'] = $value['title'];
			$subjects[$key]['img'] =  $webroot . '/attachs' . $value['icon'];
			$subjects[$key]['href'] = $webroot . $this->actions['indexUrl'] .'?id='. $value['id'];
			$subjects[$key]['start_time'] = date('Y-m-d', $value['start_time']);
			$subjects[$key]['end_time'] = date('Y-m-d', $value['end_time']);
			$subjects[$key]['descrip'] = html_entity_decode($value['descrip']);
			$subjects[$key]['count'] = intval($counts[$value['id']]['count']);
		}
		
		$totalpage = ceil((int) $total / $this->perpage);
		$hasnext = ($totalpage - ($page)) > 0 ? true : false;
		$this->output(0, '', array('list'=>$subjects, 'hasnext'=>$hasnext, 'curpage'=>$page, 'total'=>$totalpage));
	}
}
