<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author tiansh
 *
 */
class Type_TypeController extends Admin_BaseController{
	
	public $actions = array(
		'indexUrl' => '/Admin/Type_Type/index',
		'addUrl' => '/Admin/Type_Type/add',
		'addPostUrl' => '/Admin/Type_Type/add_post',
		'editUrl' => '/Admin/Type_Type/edit',
		'editPostUrl' => '/Admin/Type_Type/edit_post',
		'deleteUrl' => '/Admin/Type_Type/delete',
		'uploadUrl' => '/Admin/Type_Type/upload',
		'uploadPostUrl' => '/Admin/Type_Type/upload_post',
		'uploadImgUrl' => '/Admin/Type_Type/uploadImg'
	);
	public $perpage = 15;
	public $versionName = 'Type_Version';

	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		$page < 1 && $page = 1;
		$perpage = $this->perpage;
		
		$param = $this->getInput(array('type_id','ctype_id'));
		$search = array();
		if ($param['type_id']) $search['type_id'] = $param['type_id'];
		if ($param['ctype_id']) $search['ctype_id'] = $param['ctype_id'];

		list($total, $list) = Type_Service_Type::getList($page, $this->perpage, $search);
        list(, $pType) = Type_Service_Ptype::getsBy(array('status'=>1,'pid'=>0),array('sort'=>'DESC','id'=>'DESC'));
        list(, $types) = Type_Service_Ptype::getAllType(array('status'=>1,'pid'=>0));
        $pType = Common::resetKey($pType, 'id');
        list(,$cType) = Type_Service_Ptype::getsBy(array('status'=>1,'pid'=>array('IN',array_keys($pType))),array('pid'=>'DESC','sort'=>'DESC','id'=>'DESC'));


        foreach ($cType as $k=>$v) {
            $child_type_data[$v['pid']][] = array(
              'id' => $v['id'],
              'name' => html_entity_decode($v['name']),
            );
        }

        foreach ($pType as $key=>$value) {
            $parent_type_data[] = array(
              'id' => $value['id'],
              'name' => html_entity_decode($value['name']),
            );
        }

        $this->assign('current_child_type', $child_type_data[$search['type_id']]);
        $this->assign('child_type', json_encode($child_type_data));
        $this->assign('type', $parent_type_data);
        $this->assign('types',Common::resetKey($types,'id'));
		$this->assign('list', $list);
		$this->assign('search', $search);
		$url = $this->actions['indexUrl'] .'/?'. http_build_query($param) . '&';
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
		$this->cookieParams();
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function addAction(){
        list($total, $pType) = Type_Service_Ptype::getList(1, 100, array('status'=>1,'pid'=>0));

        $ptype = Common::resetKey($pType, 'id');
        list(,$cType) = Type_Service_Ptype::getsBy(array('status'=>1,'pid'=>array('IN',array_keys($ptype))));

        foreach ($cType as $k=>$v) {
            $child_type_data[$v['pid']][] = array(
              'id' => $v['id'],
              'name' => html_entity_decode($v['name']),
            );
        }
        foreach ($pType as $key=>$value) {
            $parent_type_data[] = array(
              'id' => $value['id'],
              'name' => html_entity_decode($value['name']),
            );
        }

        $this->assign('current_child_type', $child_type_data[$parent_type_data[0]['id']]);
        $this->assign('child_type', json_encode($child_type_data));
        $this->assign('type', $parent_type_data);
	}

	/**
	 * 
	 * Enter description here ...
	 */
	public function add_postAction(){
		$info = $this->getPost(array('name', 'sort', 'keyword', 'status', 'img', 'is_recommend', 'type_id', 'ctype_id'));
		
		$info = $this->_cookData($info);
		$ret = Type_Service_Type::addType($info);
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功.');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */

	public function editAction(){
		$id = $this->getInput('id');
		$info = Type_Service_Type::getType(intval($id));
        list($total, $pType) = Type_Service_Ptype::getsBy(array('status'=>1,'pid'=>0));

        $ptype = Common::resetKey($pType, 'id');
        list(,$cType) = Type_Service_Ptype::getsBy(array('status'=>1,'pid'=>array('IN',array_keys($ptype))));

        foreach ($cType as $k=>$v) {
            $child_type_data[$v['pid']][] = array(
              'id' => $v['id'],
              'name' => html_entity_decode($v['name']),
            );
        }

        foreach ($pType as $key=>$value) {
            $parent_type_data[] = array(
              'id' => $value['id'],
              'name' => html_entity_decode($value['name']),
            );
        }

		$this->assign('current_child_type', $child_type_data[$info['type_id']]);
		$this->assign('child_type', json_encode($child_type_data));
		$this->assign('type', $parent_type_data);
	    $this->assign('info', $info);
	}

	/**
	 * 
	 * Enter description here ...
	 */

	public function edit_postAction(){
		$info = $this->getPost(array('id', 'name', 'sort', 'keyword', 'status', 'img','is_recommend', 'type_id', 'ctype_id'));
		$info = $this->_cookData($info);
		$ret = Type_Service_Type::updateType($info, $info['id']);
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功.');
	}

	/**
	 * 
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id = $this->getInput('id');
		$info = Type_Service_Type::getType($id);
		if ($info && $info['id'] == 0) $this->output(-1, '信息不存在无法删除');
		$ret = Type_Service_Type::deleteType($id);
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	/**
	 * 
	 * @param unknown_type $pids
	 * @param unknown_type $categorys
	 */
	private function _cookData($info) {
		if (!$info['name']) $this->output(-1, '名称不能为空.');
		if (!$info['img']) $this->output(-1, '图片不能为空.');
		/*if (!$info['link'])  $this->output(-1, '链接不能为空.');
		if(strpos($data['link'], 'http://') === false || !strpos($data['link'], 'https://') === false) {
			$this->output(-1, '链接地址不规范.');
		} */
		return $info;
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
		$ret = Common::upload('imgFile', 'gou_type');
		if ($ret['code'] != 0) die(json_encode(array('error' => 1, 'message' => '上传失败！')));
		exit(json_encode(array('error' => 0, 'url' => '/attachs/' .$ret['data'])));
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public function upload_postAction() {
		$ret = Common::upload('img', 'gou_type');
		$imgId = $this->getPost('imgId');
		$this->assign('code' , $ret['data']);
		$this->assign('msg' , $ret['msg']);
		$this->assign('data', $ret['data']);
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
	}
}
