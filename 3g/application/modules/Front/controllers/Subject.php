<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 专题
 */
class SubjectController extends Front_BaseController {

	public $actions = array(
		'indexUrl' => '/subject/index',
	);

	public function indexAction() {
		$id    = intval($this->getInput('id'));
		$token = $this->getInput('token');

		$hideTitle = $this->getInput('hide_title');
		$backUrl   = $this->getInput('back_url');

		$subject = Gionee_Service_Subject::getSubject($id);
		$webroot = Common::getCurHost();
		if (!$id || !$subject || $subject['id'] != $id || ($subject['status'] != 1 && !$token))
			Common::redirect($webroot);

		if (empty($backUrl)) {
			$backUrl = isset($_SERVER['HTTP_REFERER']) ? Common::getHttpReferer() : $webroot . '/nav';
		}

		$this->assign('hideTitle', $hideTitle);
		$this->assign('backUrl', $backUrl);


		$this->assign('subject', $subject);
		$this->assign('pageTitle', $subject['title']);
	}
}
