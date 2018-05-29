<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
class KeywordController extends Admin_BaseController {

    public $actions = array(
                    'autoReplyUrl' => '/Admin/Keyword/index#J_hash_reply',
                    'listUrl' => '/Admin/Keyword/index',
                    'addUrl' => '/Admin/Keyword/add',
                    'editUrl' => '/Admin/Keyword/edit',
                    'addPostUrl' => '/Admin/Keyword/addPost',
                    'editPostUrl' => '/Admin/Keyword/editPost',
                    'deletePostUrl' => '/Admin/Keyword/deletePost',
                    'materialDialogUrl' => '/Admin/Material/dialog',
                    'saveAutoMsgUrl' => '/Admin/Keyword/saveAutoMsg'
	);
    
    public $optType = array(
                    1=>'一条图文',
                    2=>'系统回复',
                    3=>'文字回复'
    );
    
    //appMsg：图文  sysReply：系统回复，text:文本消息
    private $typeAdapter = array(
                    'appMsg' => 1,
                    'sysReply' => 2,
                    'text' => 3
    );
    
    const PERPAGE = 20;
    
    public function indexAction() {
        $page = $this->getPageInput();
        list($total, $list) = Admin_Service_Keyword::getList($page, self::PERPAGE);
        $autoMsg = Admin_Service_Automsg::getby(array('type' => 1));
        if ($autoMsg && $autoMsg['opt_type'] == 1) {
            $materialId = $autoMsg['material_id'];
        	$materialTitle = Admin_Service_Material::getTitle($autoMsg['material_id']);
        	$this->assign('materialTitle', $materialTitle);
        	$this->assign('materialId', $materialId);
        }
        $this->assign('autoMsg', $autoMsg);
        $this->assign('total', $total);
        $this->assign('list', $list);
        $this->assign('optType', $this->optType);
        $url = $this->actions['listUrl'] . '/?' . http_build_query(array()) . '&';
        $this->assign('pager', Common::getPages($total, $page, self::PERPAGE, $url));
    }
    
    public function deletePostAction() {
        $id = $this->getInput('keywordId');
        $result = Admin_Service_Keyword::delete($id);
        if ($result) {
            $this->successPostOutput($this->actions['listUrl']);
        } else {
            $this->failPostOutput();
        }
    }
    
    public function addAction() {
    }
    
    public function editAction() {
        $id = $this->getInput('id');
        $data = Admin_Service_Keyword::get($id);
        if ($data['opt_type'] == Admin_Service_Keyword::OPT_TYPE_NEWS) {
            $materialId = $data['material_id'];
        	$materialTitle = Admin_Service_Material::getTitle($materialId);
        	$this->assign('materialTitle', $materialTitle);
        	$this->assign('materialId', $materialId);
        }
        $this->assign('data', $data);
    }
    
    public function addPostAction() {
        $inputVar = $this->getInput(array('keyword', 'type', 'eventType', 'replyType', 'imgTextId', 'replyContent'));
        $inputVar['keyword'] = $this->trimKeyword($inputVar['keyword']);
        if(!$this->checkInput($inputVar)) {
            $this->failPostOutput('');
        }
        
        if(!$this->checkKeywordExist(0, $inputVar['keyword'])) {
            $this->failPostOutput("保存失败，关键字已存在");
        }
        
        $inputVar['replyContent'] =$_POST['replyContent'];
        $data = $this->buildParams($inputVar);
        $result = Admin_Service_Keyword::add($data);
        if ($result) {
            $this->successPostOutput($this->actions['listUrl']);
        } else {
            $this->failPostOutput('');
        }
    }
    
    public function editPostAction() {
        $id = $this->getInput('id');
        $inputVar = $this->getInput(array('keyword', 'type', 'eventType', 'replyType', 'imgTextId', 'replyContent'));
        $inputVar['keyword'] = $this->trimKeyword($inputVar['keyword']);
        if(!$id || !$this->checkInput($inputVar)) {
            $this->failPostOutput();
        }
        
        if(!$this->checkKeywordExist($id, $inputVar['keyword'])) {
            $this->failPostOutput("保存失败，关键字已存在");
        }
        
        $inputVar['replyContent'] =$_POST['replyContent'];
        $data = $this->buildParams($inputVar);
        $result = Admin_Service_Keyword::update($data, $id);
        if ($result) {
            $this->successPostOutput($this->actions['listUrl']);
        } else {
            $this->failPostOutput();
        }
    }
    
    private function checkInput($inputVar) {
        if (!$inputVar['keyword'] || !$inputVar['type'] || !$inputVar['eventType']) {
            return false;
        }
    
        if ($inputVar['eventType'] == 'appMsg') {
            if (!$inputVar['imgTextId']) {
                return false;
            }
        } else if($inputVar['eventType'] == 'sysReply') {
            if (!$inputVar['replyType']) {
                return false;
            }
        } else if ($inputVar['eventType'] == 'text') {
            if (!$inputVar['replyContent']) {
                return false;
            }
        } else {
            return false;
        }
    
        return true;
    }
    
    private function trimKeyword($keyWordStr) {
        $keyWordStr = str_replace('；', ';', $keyWordStr);
        $keyWordInputArr = explode(';', $keyWordStr);
        $keyWordArr = array();
        foreach ($keyWordInputArr as $keyWord) {
            $keyWord = trim($keyWord);
            if ($keyWord) {
            	$keyWordArr[] = $keyWord;
            }
        }
        return implode(';', $keyWordArr);
    }
    
    private function checkKeywordExist($id, $keyWordInput) {
        $keyWords = Admin_Service_Keyword::getAll();
        foreach ($keyWords as $item) {
            if ($item['id'] == $id) {
            	continue;
            }
            $keyWordArray = explode(';', $item['keyword']);
            $keyWordInputArr = explode(';', $keyWordInput);
            if (array_intersect($keyWordArray, $keyWordInputArr)) {
            	return false;
            }
        }
        return true;
    }

    private function buildParams($inputVar) {
        $params = array();
        $params['keyword'] = $inputVar['keyword'];
        $params['match_type'] = $inputVar['type'] == 'full' ? 1 : 2;
        $params['opt_type'] = $this->typeAdapter[$inputVar['eventType']];
        
        if ($params['opt_type'] == 1) {
        	$params['material_id'] = $inputVar['imgTextId'];
        	$params['sys_event'] = 0;
        	$params['text_content'] = '';
        } else if ($params['opt_type'] == 2) {
            $params['material_id'] = 0;
            $params['sys_event'] = $inputVar['replyType'] == 'acoin' ? 1 : 2;
            $params['text_content'] = '';
        } else if ($params['opt_type'] == 3) {
            $params['material_id'] = 0;
            $params['sys_event'] = 0;
            $params['text_content'] = $inputVar['replyContent'];
        }
        return $params;
    }
    
    public function saveAutoMsgAction() {
        $inputVar = $this->getInput(array('replyType', 'replyContent', 'imgTextId'));
        if (!$inputVar['replyType']) {
        	$this->failPostOutput('参数错误');
        }
        $inputVar['replyContent'] =$_POST['replyContent'];
        $data = array(
                        'type' => 1,
                        'opt_type' => $inputVar['replyType'] == text ? 3 : 1,
                        'material_id' => $inputVar['imgTextId'],
                        'text_content' => $inputVar['replyContent']
        );
        $autoMsg = Admin_Service_Automsg::getby(array('type' => 1));
        $result = false;
        if ($autoMsg) {
            $result = Admin_Service_Automsg::update($data, $autoMsg['id']);
        } else {
            $result = Admin_Service_Automsg::install($data);
        }
        if ($result) {
            $this->successPostOutput($this->actions['autoReplyUrl']);
        } else {
            $this->failPostOutput();
        }
    }
}
?>