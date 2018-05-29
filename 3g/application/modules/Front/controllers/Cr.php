<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * 统计点击量
 * @author tiansh
 *
 */
class CrController extends Front_BaseController {

	public $actions = array(
		'listUrl' => 'Cr/index',
	);

	public function indexAction() {
		$url = trim($this->getInput('url'));
		Common::redirect($url);
		exit;
	}
}
