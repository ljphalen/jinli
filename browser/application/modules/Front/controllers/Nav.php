<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class NavController extends Front_BaseController{
	
	public $actions = array(
		'listUrl' => 'nav/index',
	);

	public $types;
	public $perpage = 100;

	/**
	 * 
	 * Enter description here ...
	 */
	public function init() {
		parent::init();
		$this->types = Common::getConfig('productConfig', 'navType');
	}

	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		$perpage = $this->perpage;

		list($total, $f_navs) = Browser_Service_Nav::getList($page, $perpage, array('type'=>0));
		list($total, $j_navs) = Browser_Service_Nav::getList($page, $perpage, array('type'=>1));

		list(,$f_navtypes) = Browser_Service_NavType::getList($page, $perpage, array('type'=>0));
		list(,$j_navtypes) = Browser_Service_NavType::getList($page, $perpage, array('type'=>1));

		$this->assign('types', $this->types);
		$this->assign('f_navtypes', $f_navtypes);
		$this->assign('j_navtypes', $j_navtypes);
		$this->assign('f_navs', $f_navs);
		$this->assign('j_navs', $j_navs);
		$this->assign('pageTitle','网址导航');
	}	

}
