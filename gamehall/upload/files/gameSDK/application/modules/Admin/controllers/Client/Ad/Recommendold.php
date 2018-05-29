<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class Client_Ad_RecommendoldController extends Admin_BaseController {
	
	public $actions = array(
		'oldListUrl' => '/Admin/Client_Ad_Recommendold/index',
		'imgLogUrl' => '/Admin/Client_Ad_Recommend/index',
		'bannerUrl' => '/Admin/Client_Ad_Turn/index',
		'imgUrl' => '/Admin/Client_Ad_Picture/index',
		'recImgUrl' => '/Admin/Client_Ad_Recpic/index',
		'recListUrl' => '/Admin/Client_Ad_Subject/index',
	);

	public function indexAction() {
	}
	
}
