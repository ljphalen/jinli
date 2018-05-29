<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class IndexController extends Front_BaseController {

	public $actions = array(
			'indexUrl' => '/index/index',
		);
	public $perpage = 10;

	public function indexAction() {
		//统计首页点击量
		Common::getCache()->increment('games_index_pv');
		exit(0);
	}
	
	public function adsAction() {
		//ads
		list(,$ads)  = Games_Service_Ad::getCanUseAds(0, 6, array('ad_type'=>1));
		$tmp = array();
		$webroot = Yaf_Application::app()->getConfig()->webroot;
		foreach($ads as $key=>$value) {
			array_push($tmp, array(
				'img' => $webroot. '/attachs' . $value['img'],
				'title' => $value['title'],
				'link' => $value['link'],
				'ptype'=>$value['ptype'],
				'gameid'=>$value['gameid'],
				'id' => $value['id'],
			));
		}
		$this->output(0, '', $tmp);
	}
	
	public function subjectAction() {
		//subjects
		list(, $subjects) = Games_Service_Subject::getCanUseSubjects(0, 3);
		$tmp = array();
		$webroot = Yaf_Application::app()->getConfig()->webroot;
		foreach($subjects as $key=>$value) {
			array_push($tmp, array(
				'icon' => $webroot. '/attachs' . $value['icon'],
				'img' => $webroot. '/attachs' . $value['img'],
				'title' => $value['title'],
				'ptype'=>$value['ptype'],
				'id' => $value['id'],
				'link'=>$value['link'],
				'package'=>$value['package'],
			));
		}
		$this->output(0, '', $tmp);
	}
	
	public function icon_recommendAction() {

		//icons recommend
		list( ,$icon_recommends) = Games_Service_Recommend::getCanUseRecommends(0, 6, array('ptype'=>1));
		$tmp = array();
		$webroot = Yaf_Application::app()->getConfig()->webroot;
		foreach($icon_recommends as $key=>$value) {
			array_push($tmp, array(
			'pptype' =>$value['pptype'],
			'gameid' => $value['gameid'],
			'img' => $webroot. '/attachs' . $value['img'],
			'link' => $value['link'],
			));
		}
		$this->output(0, '', $tmp);
	}
	
	public function list_recommendAction(){
		//list recommend
		list( ,$list_recommends) = Games_Service_Recommend::getCanUseRecommends(0, 8, array('ptype'=>2));
		$tmp = array();
		$webroot = Yaf_Application::app()->getConfig()->webroot;
		foreach($list_recommends as $key=>$value) {
			array_push($tmp, array(
			'pptype' =>$value['pptype'],
			'gameid' => $value['gameid'],
			'title' => $value['title'],
			'resume' => $value['resume'],
			'img' => $webroot. '/attachs' . $value['img'],
			'link' => $value['link'],
			));
		}
		$this->output(0, '', $tmp);
	}
}
