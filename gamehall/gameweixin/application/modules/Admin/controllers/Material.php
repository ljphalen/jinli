<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
class MaterialController extends Admin_BaseController {

    public $actions = array(
                    'listUrl' => '/Admin/Material/index',
                    'addUrl' => '/Admin/Material/add',
                    'editUrl' => '/Admin/Material/edit',
                    'addPostUrl' => '/Admin/Material/addPost',
                    'editPostUrl' => '/Admin/Material/editPost',
                    'deletePostUrl' => '/Admin/Material/deletePost',
                    'fileUploadUrl' => '/Admin/Material/uploadPost',
                    'giftDialogUrl' => '/Admin/Gift/dialog',
                    'dialogUrl' => '/Admin/Material/dialog'
	);
    
    const PERPAGE = 10;
    const GIFT_PERPAGE = 5;
    public $DATATYPE = array("single", "multi");//single //单条图文消息;multi //多条图文
    public $ITEMTYPE = array("gift", "link");//gift //礼包;link //链接
    
    /**
     *  列表界面
     * */
    public function indexAction() {
        $this->showList($this->actions['listUrl']);
    }
    
    public function dialogAction() {
        $this->showList($this->actions['dialogUrl']);
    }
    
    private function showList($rootUrl) {
        $page = $this->getPageInput();
        $inputVars = $this->getInput(array('keyword',  'start_time', 'end_time', 'type'));
        list($params, $itemParams) = $this->getSearchParam($inputVars);
        list($total, $list, $itemsList) =Admin_Service_Material::getList($page, self::PERPAGE, $params, $itemParams);
        $this->assign('inputVars', $inputVars);
        $this->assign('list', $list);
        $this->assign('itemsList', $itemsList);
        $this->assign('total', $total);
        $this->assign('dataTypes', $this->DATATYPE);
        $url = $rootUrl . '/?' . http_build_query($inputVars) . '&';
        $this->assign('pager', Common::getPages($total, $page, self::PERPAGE, $url));
    }
    
    private function getSearchParam($inputVars) {
        $itemParams = array();
        if ($inputVars['keyword']) {
            $itemParams['title'] = array('LIKE', $inputVars['keyword']);
        }
        
        $params = array();
        if ($inputVars['type']) {
            $params['type'] = $inputVars['type'];
        }
        
        if ($inputVars['start_time'] && $inputVars['end_time']) {
        	$params['create_time'] = array(array('>=', strtotime($inputVars['start_time'])), array('<=', strtotime($inputVars['end_time'])));
        } else if ($inputVars['start_time']) {
            $params['create_time'] = array('>=', strtotime($inputVars['start_time']));
        } else if ($inputVars['end_time']) {
            $params['create_time'] = array('<=', strtotime($inputVars['end_time']));
        }
        return array($params, $itemParams);
    }
    
    /**
     *
     * Enter description here ...
     */
    public function addAction() {
    	$dataType = $this->getInput('dataType');
    	if (!in_array($dataType,$this->DATATYPE)) {
    	    exit("参数错误");
    	}
    	$this->assign('dataType', $dataType);
        $this->assign('dataTypes', $this->DATATYPE);
    }
    
    /**
     *
     * Enter description here ...
     */
    public function addPostAction() {
        $inputVars = $this->getInput(array(
        		'id', 'dataType',  //主表字段：id，type
       		 	'data'//子表字段
        ));
    	list($news, $itemsList) = $this->checkInput($inputVars);
    	$news['create_time'] = time();
    	$result = Admin_Service_Material::add($news, $itemsList);
    	if (!$result) $this->failPostOutput('操作失败');
    	$this->successPostOutput($this->actions['listUrl']);
    }
    
    /**
     *
     * Enter description here ...
     */
    public function editAction() {
    	$id = $this->getInput('id');
    	list($info, $itemsList) = Admin_Service_Material::getById($id);
    	foreach ($itemsList as $key=>$value) {
	    	$value['type'] = $value['type'] == 1 ? $this->ITEMTYPE[0] : $this->ITEMTYPE[1];//'gift':礼包，'link':链接
    	    $itemsList[$key] = $value;
    	}
    	
    	$this->assign('info', $info);
    	$this->assign('itemsList', $itemsList);
        $giftNameList = $this->getGiftName($itemsList);
        $this->assign('giftNameList', $giftNameList);
        
    	$this->assign('dataType', $info["type"] == 1 ?$this->DATATYPE[0] : $this->DATATYPE[1]);
        $this->assign('dataTypes', $this->DATATYPE);
    }
    
    /**
     *
     * Enter description here ...
     */
    public function editPostAction() {
        $inputVars = $this->getInput(array(
        		'id', 'dataType',  //主表字段：id，type
       		 	'data'//子表字段
        ));
    	list($news, $itemsList) = $this->checkInput($inputVars);
    	$id = intval($news['id']);
    	list($edit_info, $oldItemsList) = Admin_Service_Material::getById($id);
    	if (!$edit_info ) {
    		$this->failPostOutput('修改的素材不存在');
    	}
    	$result  = Admin_Service_Material::edit($id, $news, $itemsList);
    	if (!$result) $this->failPostOutput('操作失败');
    	$this->successPostOutput($this->actions['listUrl']);
    }
    
    /**
     *
     * Enter description here ...
     */
    public function deletePostAction() {
    	$id = $this->getInput('id');
    	list($info, $itemsList) = Admin_Service_Material::getById($id);
    	if ($info && $info['id'] == 0) $this->failPostOutput('无法删除');
    	$result = Admin_Service_Material::delete($id);
    	if (!$result) $this->failPostOutput('操作失败');
    	$attachPath = Common::getConfig('siteConfig', 'attachPath');
    	$attachroot = Yaf_Application::app()->getConfig()->attachroot;
    	foreach ($itemsList as $items) {
    		Util_File::del($attachPath.str_replace($attachroot, "", $items['image']));
    	}
    	$this->successPostOutput($this->actions['listUrl']);
    }
    
    /**
     * 
     * 上传图片
     * */
    public function uploadPostAction() {
		$output = Common::upload('appMsgPic', 'material', 10240, array('png', 'jpg', 'jpeg')
// 		     , array(900, 500)
		    );
		exit(json_encode($output));
	}
    
	/**
	 * 获取礼包名称
	 * @param unknown $itemsList
	 * @return multitype:mixed
	 */
	private function getGiftName($itemsList) {
        $giftNameList = array();
	    foreach ($itemsList as $items) {
	        if($items['gift_bag_id']) {
	            if(isset($giftNameList[$items['gift_bag_id']])) continue;
	            $gift =  Admin_Service_Gift::getById($items['gift_bag_id']);
	            if($gift) {
	                $giftNameList[$items['gift_bag_id']] = $gift['title'];
	            }
	        }
	    }
	    return $giftNameList;
	}
	
    /**
     *
     * 验证提交的数据
     */
    private function checkInput($inputVars) {
    	$news = array(); 
    	$itemList = array();
    	
    	if(isset($inputVars['id'])) {
    		$news['id'] = $inputVars['id'];
    	}
    	if(!isset($inputVars['dataType'])) {
    	    $this->failPostOutput("参数错误：图文类型");
    	}
    	$news['type'] = $inputVars['dataType']  == $this->DATATYPE[0] ? 1 : 2;
    	//子表
    	foreach($inputVars['data'] as $key=>$value){
    	    $items = array();
	    	if(isset($value['id'])) {
	    		$items['id'] = $value['id'];
	    	}
	    	if(isset($value['linkType'])) {
	    		$items['type'] = $value['linkType'] == $this->ITEMTYPE[0] ? 1 : 2;//'gift':礼包，'link':链接
	    	}
	    	if(isset($value['title'])) {
	    		$items['title'] = $value['title'];
	    	}
	    	if(isset($value['author'])) {
	    		$items['author'] = $value['author'];
	    	}
	    	if(isset($value['imgSrc'])) {
	    		$items['image'] = $value['imgSrc'];
	    	}
	    	if(isset($value['desc'])) {
	    		$items['digest'] = $value['desc'];
	    	}else{
	    	    $items['digest'] = "";
	    	}
	    	if(isset($value['link'])) {
	    		$items['content_url'] = $value['link'];
	    	}
	    	if(isset($value['bagId'])) {
	    		$items['gift_bag_id'] = $value['bagId'];
	    	}
	    	if(isset($news['id'])) {
	    		$items['news_id'] = $news['id'];
	    	}
    		$items['order_index'] = $key + 1;
    		$itemList[$key] = $items;
    	}
    	foreach ($itemList as $key => $value) {
    		if(!$value['title'] || !trim($value['title']))  $this->failPostOutput("标题名称不能空");
    		if(mb_strlen($value['title'], 'UTF-8')>64) $this->failPostOutput('标题名称最长不能超过64个字');
    		if($value['author'] && mb_strlen($value['author'], 'UTF-8')>8) $this->failPostOutput('作者名称最长不超过8个字！');
    		if($value['type'] == 2) {
        		if(!$value['content_url']) $this->failPostOutput('链接地址不能为空.');
        		if(strpos($value['content_url'], 'http://') === false || !strpos($value['content_url'], 'https://') === false) {
        			$this->failPostOutput('链接地址不规范.');
        		}
    		}
    		if(!$value['gift_bag_id']) {
    			$gift = Admin_Service_Gift::getById($value['gift_bag_id']);
    			if($gift['url'] != $value['content_url']) {
    				$value['gift_bag_id'] = 0;
    			}
    		}
    	}
    	return array($news, $itemList);
    }
}
?>