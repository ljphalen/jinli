<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class ServicesController extends Front_BaseController {

	public $actions = array(
			'aboutUrl' => '/index/about',
			'indexUrl' => '/index/index',
			'productUrl' => '/index/product',
			'servicesUrl' => '/services/index',
			'servicesFaqUrl' => '/services/faq',
			'servicesBranchUrl' => '/services/branch',
			'servicesSaleUrl' => '/services/sale',
		);

	public function indexAction() {
		$this->assign('pageTitle','联系方式 - 客户服务');
	}

	public function faqAction() {
		$this->assign('pageTitle','常见问题 - 客户服务');
	}

	public function branchAction() {
		$this->assign('pageTitle','销售网点 - 客户服务');
	}

	public function saleAction() {
		$this->assign('pageTitle','服务网点 - 客户服务');
	}
}
