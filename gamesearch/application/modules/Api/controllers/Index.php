<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 *
 * API V1
 * @author fanch
 *
 */
class IndexController extends Api_BaseController{
	private $mGameId = null;
	private $mGameName = null;
	private $mResume = null;
	private $mLabel = null;
	
	
	
    public function indexAction() {
		
    }

    public function suggestAction() {
    	$keyword = $this->getInput('keyword');
    	
        $search = new Search_Service_Xs();
        $ret = $search->suggest($keyword);
        $ret = array('list' => $ret);
        $this->output(0, '', $ret );
        
    }

    public function searchAction() {
    	
    	$keyword = $this->getInput('keyword');
    	$page    = $this->getInput('page');
    	$limit   = $this->getInput('limit');
    	
        $start = gettimeofday(true);
        $search = new Search_Service_Xs();//默认搜索结果不高亮，传入true参数，搜索结果高亮部分会用<em></em>标签括起
 
        $limit = $limit? $limit:10;
        $page = $page > 0 ? $page : 1;
        $data = $search->searchGame($keyword, $page, $limit);
        
        $searchCostTime = gettimeofday(true) - $start;
        $ret['searchCostTime'] = $searchCostTime; 
        $this->output(0, '', $data );
      
    }

    /*
     * 在后台上线游戏时直接将游戏插入索引库
     */
    public function addIndexAction() {   
    	
    
    	$this->checkGameInfo();
    	//检查签名
    	if(!$this->checkSign ()){
    		 $this->output(-1, '签名不一致');
    	}

        //添加到搜索索引库
        $search = new Search_Service_Xs();
        $data = array(
            'id' => $this->mGameId,
            'name' => $this->mGameName,
            'resume' => $this->mResume,
            'label' => $this->mLabel,
            'create_time' => time(),
        );
        $ret = $search->addIndex('game', $data);
        $this->output(0, '', $ret );
    }
    
    
	/**
	 * 
	 */
	 private function checkSign() {
		$sign = $this->getInput('sign');			
    	$rsa = new Util_Rsa();
    	$decryptSign = $rsa->decrypt($sign, Common::getConfig("siteConfig", "rsaPubFile"));
    	$crypt = Common::getConfig('searchConfig', 'sign');
    	if($decryptSign == $crypt){
    		return true;
    	}
    	return false;
	}


    /*
     * 在后台更新上线游戏名称、简述或标签时更新索引库
     */
    public function updateIndexAction() {
    	$this->checkGameInfo();
    	
    	//检查签名
    	if(!$this->checkSign ()){
    		$this->output(-1, '签名不一致');
    	}
        //添加到搜索索引库
        $search = new Search_Service_Xs();
        $data = array(
            'id' => $this->mGameId,
            'name' => $this->mGameName,
            'resume' => $this->mResume,
            'label' => $this->mLabel,
            'create_time' => time(),
        );
        $ret = $search->updateIndex('game', $data);
        $this->output(0, '', $ret );
    }
    


    /*
     * 在后台下线或删除游戏时更新索引库
     */
    public function deleteIndexAction() {
    	$gameId = $this->getInput('gameId');
    	if(!$gameId){
    		return false;
    	}
    	
    	//检查签名
    	if(!$this->checkSign ()){
    		$this->output(-1, '签名不一致');
    	}
    	
        //添加到搜索索引库
        $search = new Search_Service_Xs();
        
        //可批量根据游戏id删除，传入多个id即可
        $data = array(
            'id' => $gameId,
        );
        $ret = $search->deleteIndex('game', $data);
        $this->output(0, '', $ret );
    }
    
    private function checkGameInfo(){
    	$gameId = $this->getInput('gameId');
    	$gameName    = $this->getInput('gameName');
    	$resume   = $this->getInput('resume');
    	$label   = $this->getInput('label');
    	 
    	if(!$gameId){
    		$this->output(0,'gameId is empty');
    	}
    	 
    	if(!$gameName){
    		$this->output(0,'gameName is empty');
    	}
    	 
    	$this->mGameId = $gameId;
    	$this->mGameName = $gameName;
    	$this->mResume = $resume;
    	$this->mLabel = $label;
    	  
    }
    
    public function testSuggestAction() {
    	$search = new Search_Service_Xs();
    	$query = $_GET['keyword'];
    	$ret = $search->suggest($query);
    	$ret = array('list' => $ret);
    	header("Content-type:Application/json");
    	exit(json_encode($ret));
    }
    
    public function testSearchAction() {
    	$start = gettimeofday(true);
    	$search = new Search_Service_Xs();//默认搜索结果不高亮，传入true参数，搜索结果高亮部分会用<em></em>标签括起
    	$query = $_GET['keyword'];
    	$pn = intval($_GET['pn']);
    	$pn = $pn > 0 ? $pn : 1;
    	$ret = $search->searchGame($query, $pn);
    	$searchCostTime = gettimeofday(true) - $start;
    	$ret['searchCostTime'] = $searchCostTime;
    	header("Content-type:Application/json");
    	exit(json_encode($ret));
    }
    

    public function cleanIndexAction(){   	
    	//检查签名
    	if(!$this->checkSign ()){
    		$this->output(-1, '签名不一致');
    	}
    	$search = new Search_Service_Xs();
    	$search->cleanIndex();
    	
    }
    
    public function getCustomDictAction(){  
    	$search = new Search_Service_Xs();
    	$resutl = $search->getCustomDict();
    	var_dump($resutl);
    }
}
