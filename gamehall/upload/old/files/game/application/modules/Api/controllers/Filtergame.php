<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class FiltergameController extends Api_BaseController {
	
    /**
     * filter games
     */
	public function filterListAction(){
		$parmes = $this->filterInputed();
		$parmes = $this->checkfilterParam($parmes);
		$this->filterGames($parmes);
	}
	
    private function filterGames($parmes) {
    	$games = $filterList = $outGames = array();
        $pgroup = Resource_Service_Pgroup::getPgroupBymodel($parmes['device']);  //根据机型找出分组	
        
    	if($pgroup){
    		$pgroupId = intval($pgroup['id']);
    	} else {
    		$pgroupId = intval(Resource_Service_Pgroup::DEFAULT_PGROUP);
    	}
    	
    	if(!$pgroupId) $this->localOutput('','',array());
    	
    	$games = Resource_Service_FilterGame::getFilterGames($pgroupId);
    	$filterList['list'] = $this->assembleFilterGames($games);
    	$this->localOutput('','',$filterList);	
    }
    
    private function filterInputed() {
    	$parmes = array(
    			'sp' => strtolower($this->getInput('sp')),
    	);
    	return $parmes;
    }
    
    private function checkfilterParam($parmes) {
    	if (!$parmes['sp']) {
    		$this->localOutput(-1, 'illegal request');
    	}
    	
    	$spArr = Common::parseSp($parmes['sp']);
    	$device = $spArr['device'];
    	$parmes['device'] = $device;
    	return $parmes;
    }
    
    private function assembleFilterGames($games){
    	$outGames = array();
    	foreach($games as $key=>$value){
    		$outGames[$key]['package'] = $value;
    	}
    	return $outGames;
    }
}