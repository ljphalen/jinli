<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghua
 *
 */
class RankController extends Client_BaseController{
	
	public $actions = array(
		'listUrl' => '/client/rank/index',
		'newUrl' => '/client/rank/new',
	);

	public $perpage = 10;
	/**
	 * 
	 * index page view
	 */
	public function indexAction() {
	}
	
	public function newAction() {
		
	}
}
