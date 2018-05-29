<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class GuideController extends Front_BaseController {
	
	 public $actions = array(
        'listUrl' => '/guide/index',
    );
	 public $perpage = 10;
	 public $Gc_index_tool_file = '/cache/gc_index_tool.php';
	
    /**
     *  guide list
     */
    public function indexAction() {
    	$cache_file = Common::getConfig('siteConfig', 'dataPath') . $this->Gc_index_tool_file;
    	$title = "精品导航";
    	if (is_file($cache_file)) {
    		$tool= include $cache_file;
    	}
    	$this->assign('tool', $tool);
    	$this->assign('title', $title);
    	
    	list($total, $guideTypes) = Gc_Service_GuideType::getAllGuideTypeSort();
    	$this->assign('guideTypes', $guideTypes);
    
    	
    	list(, $guideList) = Gc_Service_Guide::getAllGuideSort();
    	$temp = array();
    	foreach ($guideList as $key=>$value) {
    		$temp[$value['pptype']][] = $value;
    	}
    	$this->assign('guideList', $temp);
    	
    	
    	//get ad 
    	list(, $ads) = Gc_Service_Ad::getCanUseAds(1, 5, array('ad_type'=>2));
    	$this->assign('ads', $ads);
    }
}