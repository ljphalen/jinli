<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
class MenuController extends Admin_BaseController {

    public $actions = array(
                    'listUrl' => '/Admin/Menu/index',
                    'editPostUrl' => '/Admin/Menu/editPost'
	);
    
    public function indexAction() {
        $keywordList = Admin_Service_Keyword::getAll();
        $menuInfo = $this->getMenuList();
        $this->assign('keyIdList', $menuInfo['keyList']);
        $this->assign('newMenusList', $menuInfo['list']);
        $this->assign('keywordList', $keywordList);
    }
    
    public function editPostAction() {
    	$status = $this->submitWeixinMenu();
    	if($status['errcode'] === 0) {
    		$this->successPostOutput($this->actions['listUrl']);
    	} else {
    		$this->failPostOutput($status->errmsg);
    	}
    }
    
    private function submitWeixinMenu() {
    	$menuInput = $this->getInput('menu');
    	if(!$menuInput) {
    	    return WeiXin_Service_Menu::delMenu();
    	}
    	
    	$i=$mainNum=0;
    	foreach($menuInput as $key => $menu) {
    		$i++;
    		$subMenu = count($menu['subMenu']);
    		$menu['mainOrder'] = intval($menu['mainOrder']);
    		if($subMenu > 5 || $mainNum >= 3) {
    			$this->failPostOutput('');
    		} elseif($subMenu >= 1) {
    			$newList[$menu['mainOrder']] = $this->makeMainMenu($menu, $menu['mainOrder']);
    			$newList[$menu['mainOrder']]['smallList'] = $this->makeSubMenu($menu['subMenu'], $menu['mainOrder']);
    		} else {
    			$mainNum++;
    			$newList[$menu['mainOrder']] = $this->makeMainMenu($menu, $menu['mainOrder']);
    		}
    	}
    	return WeiXin_Service_Menu::createMenu($newList);
    }
    
    private function getMenuList() {
    	$lists =  WeiXin_Service_Menu::getAllMenu();
    	$i=$mainNum=0;
    	foreach($lists['menu']['button'] as $key => $menu) {
    		$i++;
    		$newMenusList['main'][$i] = $this->formatMenu($i, $menu, $key+1);
    		if($menu['key']) {
    			$keyIDList[] = $menu['key'];
    		}
    		foreach($menu['sub_button'] as $k => $subMenu) {
    			$mainNum++;
    			$newMenusList['sub'][$i][] = $this->formatMenu($mainNum.'0', $subMenu, $k+1);
    			if($subMenu['key']) {
    				$keyIDList[] = $subMenu['key'];
    			}
    		}
    	}
    	$keyIDList = $keyIDList ? Admin_Service_Keyword::getKeyworkByIDList($keyIDList) : array();
    	return array('list' => $newMenusList, 'keyList' => $keyIDList);
    }
    
    private function formatMenu($id, $menu, $key) {
    	return array(
    			'id' => $id,
    			'name' => $menu['name'],
    			'super_menu_id' => 0,
    			'opt_type' => $menu['type'],
    			'menuset' => $menu['key'] ? $menu['key'] : $menu['url'],
    			'sequence' => $key
    	);
    }
    
    private function makeMainMenu($menu, $mainid) {
    	return array('id' => $mainid,
    			'name' => $menu['mainMenuName'],
    			 'super_menu_id' => 0,
    			 'opt_type' => $this->optChange($menu['mainMenuDataType']),
    			'menuset' => $menu['mainMenuKeywordId'] ? $menu['mainMenuKeywordId'] : $menu['mainMenuKeywordLink'], 
    			'sequence' => $menu['mainOrder']);
    }
    
    private function makeSubMenu($menu, $mainid) {
    	$i=0;
    	 foreach($menu as $key => $subMenu) {
    	 	$i++;
    	 	$subList[intval($subMenu['subOrder'])] = array('id' => $mainid.'0'.$i, 
    	 			'name' => $subMenu['subMenuName'], 
    	 			'super_menu_id' => $mainid, 
    	 			'opt_type' => $this->optChange($subMenu['subMenuDataType']),
    	 			'menuset' => $subMenu['subMenuKeywordId'] ? $subMenu['subMenuKeywordId'] : $subMenu['subMenuKeywordLink'], 
    	 			'sequence' => $subMenu['subOrder']);
    	 }
    	 ksort($subList);
    	 return $subList;
    }
    
    private function optChange($opt) {
    	switch ($opt) {
    		case 'link':
    			return 'view';
    		case 'keyword':
    			return 'click';
    		default:
    			return '';
    	}
    }
    
}
?>