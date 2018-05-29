<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author tiansh
 *
 */
class Type_PtypeController extends Admin_BaseController {
	
	public $actions = array(
        'listUrl' => '/Admin/Type_Ptype/index',
        'addUrl' => '/Admin/Type_Ptype/add',
        'addPostUrl' => '/Admin/Type_Ptype/add_post',
        'editUrl' => '/Admin/Type_Ptype/edit',
        'editPostUrl' => '/Admin/Type_Ptype/edit_post',
        'deleteUrl' => '/Admin/Type_Ptype/delete',
        'uploadUrl' => '/Admin/Type_Ptype/upload',
        'uploadPostUrl' => '/Admin/Type_Ptype/upload_post',
        'uploadImgUrl' => '/Admin/Type_Ptype/uploadImg'
	);
	
	public $perpage = 20;
	public $versionName = 'Type_Version';
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		$perpage = $this->perpage;
        $search = array('pid'=>0);
	    list($total, $suppliers) = Type_Service_Ptype::getList($page, $perpage,$search);
        $pid = array_keys(Common::resetKey($suppliers,'id'));
        list(,$typeArr) = Type_Service_Ptype::getsBy(array('pid'=>array('IN',$pid)));
        foreach ($typeArr as $t) {
            $type[$t['pid']][]=$t['name'];
        }
        $this->assign('suppliers', $suppliers);
		$this->assign('type', $type);
		$this->assign('pager', Common::getPages($total, $page, $perpage, $this->actions['listUrl'] . '?'));
	}
	
	/**
	 * 
	 * edit an Ptype
	 */
	public function editAction() {
		$id = $this->getInput('id');
		$info = Type_Service_Ptype::getType(intval($id));
        list(,$typeArr) = Type_Service_Ptype::getsBy(array('pid'=>$id));
		$this->assign('info', $info);
		$this->assign('items', $typeArr);
	}
		
	/**
	 * 
	 * Enter description here ...
	 */
	public function addAction() {
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function add_postAction() {
        $info = $this->getPost(array('sort', 'name', 'icon', 'recommend_status', 'recommend', 'status'));
        $item = $this->getPost('item');
        $item = $this->_cookItem($item);
        $supplier = Type_Service_Ptype::getBy(array('name'=>$info['name']));
        $this->_setItem($item,$supplier['id']);
        if($supplier) $this->output(-1, '操作失败,该分类已存在');
		$info = $this->_cookData($info);
		$result = Type_Service_Ptype::addType($info);
        if (!$result) $this->output(-1, '操作失败');
        $pid = Type_Service_Ptype::lastInsertId();

        $this->_setItem($item,$pid);
        $this->output(0, '操作成功');
	}


    private function _setItem($item,$pid){
        if(empty($item)||empty($pid)) return false;
        foreach ($item as $i) {
            $i['pid']=$pid;
            $flag=1;
            if(empty($i['id'])){
                $supplier = Type_Service_Ptype::getBy(array('name'=>$i['name'],'pid'=>$i['pid']));
                if($supplier) continue ;
                Type_Service_Ptype::addType($i);
            }else{
                $id = $i['id'];
                unset($i['id']);
                Type_Service_Ptype::updateType($i,$id);
            }
        }
        return true;
    }
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function edit_postAction() {
		$info = $this->getPost(array('id', 'sort', 'name', 'icon', 'recommend_status', 'recommend', 'status'));
        $item = $this->getPost('item');
        $item = $this->_cookItem($item);

        $drop = $this->getPost('drop');
        $info = $this->_cookData($info);
        $supplier = Type_Service_Ptype::getBy(array('name'=>$info['name']));
        if($supplier && $supplier['id'] != $info['id']) $this->output(-1, '操作失败,该供分类已存在');
        $ret = Type_Service_Ptype::updateType($info, intval($info['id']));
        if (!$ret) $this->output(-1, '操作失败');
        //删除子项
        if(!empty($drop))Type_Service_Ptype::deletes('id',array_filter($drop));
        $this->_setItem($item,$info['id']);
        $this->output(0, '操作成功.');
	}

	/**
	 * 
	 * Enter description here ...
	 */
	private function _cookData($info) {
		if(!$info['name']) $this->output(-1, '名称不能为空.'); 
		if (!$info['icon']) $this->output(-1, 'icon不能为空.');
		return $info;
	}
    private function _cookItem($info) {
        $tmp = array();
        foreach ($info as $item) {
            if(!$item['name'])  continue;
            $tmp[]=$item;
        }
        if(empty($tmp))$this->output(-1, '操作失败,至少添加一个子分类');
        return $tmp;
	}
		
	/**
	 * 
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id = $this->getInput('id');
		$info = Type_Service_Ptype::getType($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		$result = Type_Service_Ptype::deleteType($id);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public function uploadAction() {
	    $imgId = $this->getInput('imgId');
	    $this->assign('imgId', $imgId);
	    $this->getView()->display('common/upload.phtml');
	    exit;
	}
	
	/**
	 *
	 */
	public function uploadImgAction() {
	    $ret = Common::upload('imgFile', 'gou_ptype');
	    if ($ret['code'] != 0) die(json_encode(array('error' => 1, 'message' => '上传失败！')));
	    exit(json_encode(array('error' => 0, 'url' => '/attachs/' .$ret['data'])));
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public function upload_postAction() {
	    $ret = Common::upload('img', 'gou_ptype');
	    $imgId = $this->getPost('imgId');
	    $this->assign('code' , $ret['data']);
	    $this->assign('msg' , $ret['msg']);
	    $this->assign('data', $ret['data']);
	    $this->assign('imgId', $imgId);
	    $this->getView()->display('common/upload.phtml');
	    exit;
	}

}
