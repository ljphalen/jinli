<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 售后服务
 */
class ServicesController extends Front_BaseController {

	public $actions = array(
		'indexUrl'          => '/services/index',
		'questionsUrl'      => '/services/questions',
		'questionDetailUrl' => '/services/question_detail',
		'questions_moreUrl' => '/services/questions_more',
		'ploicyUrl'         => '/services/policy',
		'serviceUrl'        => '/services/service',
		'saleUrl'           => '/services/sale',
		'contactUrl'        => '/services/contact',
	);

	public $service_type = array(
		'2' => '特约服务站',
		'1' => '客服中心'
	);

	/**
	 * 首页
	 */
	public function indexAction() {
		$this->assign('pageTitle', '售后服务');
		$this->assign('nav', 'services');
	}

	/**
	 * 常见问题
	 */
	public function questionsAction() {
		$perpage = 10;
		$page    = 1;
		list($total, $questions) = Gionee_Service_Questions::getList($page, $perpage, array('status' => 1));

		for ($i = 0; $i < $perpage; $i++) {
			$questions[$i]['num'] = $i + 1;
		}

		$this->assign('questions', $questions);
		$this->assign('pageTitle', '常见问题 - 售后服务');


		$hasnext = (ceil((int)$total / $perpage) - ($page)) > 0 ? 'true' : 'false';
		$this->assign('hasnext', $hasnext);
	}

	/**
	 * 常见问题
	 */
	public function questions_moreAction() {
		$page    = intval($this->getInput('page'));
		$perpage = 10;
		if ($page < 2) $page = 2;

		list($total, $questions) = Gionee_Service_Questions::getList($page, $perpage, array('status' => 1));

		for ($i = 0; $i < $perpage; $i++) {
			$num = $perpage + ($page - 2) * $perpage + $i + 1;
			if ($num > $total) break;
			$questions[$i]['num'] = $num;
		}

		$hasnext = (ceil((int)$total / $perpage) - ($page)) > 0 ? true : false;
		$this->output(0, '', array('list' => $questions, 'hasnext' => $hasnext, 'curpage' => $page));
	}

	/**
	 * 常见问题
	 */
	public function question_detailAction() {
		$qid = intval($this->getInput('qid'));

		$question = Gionee_Service_Questions::getQuestions($qid);

		$webroot = Common::getCurHost();
		if (!$qid || !$question || $question['id'] != $qid) Common::redirect($webroot . $this->actions['questionsUrl']);
		$this->assign('question', $question);
		$this->assign('pageTitle', '常见问题 - 售后服务');
		$this->getView()->display('services/question_detail.phtml');
		exit;
	}

	/**
	 * 售后政策
	 */
	public function policyAction() {
		$this->assign('pageTitle', '售后政策 - 售后服务');
	}

	/**
	 * 服务网点
	 */
	public function serviceAction() {

		$param   = $this->getInput(array('province', 'city', 'service_type'));
		$search  = array();
		$address = '';
		if ($param['province']) $search['province'] = $param['province'];
		if ($param['city']) $search['city'] = $param['city'];
		if ($param['service_type']) $search['service_type'] = $param['service_type'];
		if ($search) {
			$search['address_type'] = 2;
			list($total, $address) = Gionee_Service_Address::getAllAddress($search);
		}
		//省市
		$province = Gionee_Service_Area::getProvinceList();
		$city     = Gionee_Service_Area::getAllCity();

		//print_r(Common::resetKey($city, 'id'));
		$this->assign('province', Common::resetKey($province, 'id'));
		$this->assign('city', $city);
		$this->assign('address', $address);
		$this->assign('search', $search);
		$this->assign('service_type', $this->service_type);
		$this->assign('pageTitle', '服务网点 - 售后服务');
	}

	/**
	 * 销售网点
	 */
	public function saleAction() {
		$param   = $this->getInput(array('province', 'city'));
		$search  = array();
		$address = '';
		if ($param['province']) $search['province'] = $param['province'];
		if ($param['city']) $search['city'] = $param['city'];
		if ($search) {
			$search['address_type'] = 1;
			list($total, $address) = Gionee_Service_Address::getAllAddress($search);
		}
		//省市
		$province = Gionee_Service_Area::getProvinceList();
		$city     = Gionee_Service_Area::getAllCity();

		$this->assign('province', Common::resetKey($province, 'id'));
		$this->assign('city', $city);
		$this->assign('address', $address);
		$this->assign('search', $search);
		$this->assign('pageTitle', '销售网点 - 售后服务');
	}

	/**
	 * 联系我们
	 */
	public function contactAction() {
		$this->assign('pageTitle', '联系我们 - 售后服务');
	}
}
