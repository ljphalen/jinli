<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class ElifeController extends Front_BaseController {

	public $actions = array(
		'indexUrl' => '/index/index',
	);

	public function indexAction() {
		$id    = $this->getInput('id');
		$elife = Gionee_Service_ElifeServer::getsBy();

		if ($id == null) {
			$id = $elife[0]['id'];
		}
		list($total, $images) = Gionee_Service_ElifeServerImages::getsBy($id);
		$one = Gionee_Service_ElifeServer::get($id);

		$this->assign('one', $one);
		$this->assign('elife', $elife);
		$this->assign('images', $images);
		$this->assign('total', $total);
	}

	public function navAction() {
		$this->forward("Front", "Nav", "Index");
		return false;
	}

	public function newsAction() {
		$this->forward("News", "Index");
		return false;
	}
}
