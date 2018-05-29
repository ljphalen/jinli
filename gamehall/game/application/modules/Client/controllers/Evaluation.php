<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author fanch
 *
 */
class EvaluationController extends Client_BaseController{
	
	public $actions = array(
		'detailUrl' => '/Evaluation/detail/',
	);

	
	public function detailAction(){
		Common::addSEO($this,'评测详情');
		$id = intval($this->getInput('id'));
		$intersrc = $this->getInput('intersrc');
		if(!$intersrc) 	$intersrc = 'PC'.$id;
		$configs = Game_Service_Config::getAllConfig();
		$info = Client_Service_News::getNews($id);
		
		$this->assign('configs', $configs);
		$this->assign('source', $this->getSource());
		$this->assign('intersrc', $intersrc);
		$this->assign('info', $info);
	}
}
