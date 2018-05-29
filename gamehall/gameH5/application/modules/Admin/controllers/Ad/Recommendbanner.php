<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 推荐banner图
 * Client_Ad_RecommendbannerController
 * @author wupeng
 */
class Ad_RecommendbannerController extends Admin_BaseController {
	
	public $actions = array(
		'listEditUrl' => '/Admin/Ad_Recommendlist/edit',
		'addUrl' => '/Admin/Ad_Recommendbanner/add',
		'editUrl' => '/Admin/Ad_Recommendbanner/edit',
		'addPostUrl' => '/Admin/Ad_Recommendbanner/addPost',
		'editPostUrl' => '/Admin/Ad_Recommendbanner/editPost',
		'deleteUrl' => '/Admin/Ad_Recommendbanner/delete',
        
	    'uploadUrl' => '/Admin/Ad_Recommendbanner/upload',
	    'uploadPostUrl' => '/Admin/Ad_Recommendbanner/upload_post',
	);
	
	/**
	 * 添加
	 */
	public function addAction() {
	    $day_id = $this->getInput('day_id');
	    $this->assign('day_id', $day_id);
	    $this->assign('linkType', Game_Service_Util_Link::$linkType);
	}
	
	/**
	 * 提交添加内容
	 */
	public function addPostAction() {
		$requestData = $this->getInput(array('title', 'link_type', 'link', 'img','day_id'));
		if(! ($requestData['day_id'])) $this->output(-1, '生效日期不能为空.');
		$postData = $this->checkRequestData($requestData);
		$searchParams['day_id'] = $requestData['day_id'];
		$list = Game_Service_H5RecommendBanner::getRecommendBannersBy($searchParams);
		if(count($list) >=4) {
		    $this->output(-1, 'Banner图不能多于4个.');
		}
		$postData['status'] = 1;
		$postData['sort'] = 0;
		$postData['create_time'] = Common::getTime();
		$result = Game_Service_H5RecommendBanner::addRecommendBanner($postData);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	/**
	 * 编辑
	 */
	public function editAction() {
		$keys = $this->getInput(array('id'));
		$info = Game_Service_H5RecommendBanner::getRecommendBanner($keys['id']);
		$this->assign('info', $info);
		$this->assign('linkType', Game_Service_Util_Link::$linkType);
	}
	
	/**
	 * 提交编辑内容
	 */
	public function editPostAction() {
		$requestData = $this->getInput(array('id', 'title', 'link_type', 'link', 'img'));
		
		$postData = $this->checkRequestData($requestData);
		$editInfo = Game_Service_H5RecommendBanner::getRecommendBanner($requestData['id']);
		if (!$editInfo) $this->output(-1, '编辑的内容不存在');	

		$updateParams = $this->getUpdateParams($postData, $editInfo);
		if (count($updateParams) >0) {
			$ret = Game_Service_H5RecommendBanner::updateRecommendBanner($updateParams, $requestData['id']);
			if (!$ret) $this->output(-1, '操作失败');
		}
		$this->output(0, '操作成功.');
	}
	
	/**
	 * 删除
	 */
	public function deleteAction() {
		$keys = $this->getInput(array('id'));
		$info = Game_Service_H5RecommendBanner::getRecommendBanner($keys['id']);
		if (!$info) $this->output(-1, '无法删除');
		$result = Game_Service_H5RecommendBanner::deleteRecommendBanner($keys['id']);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	/**取需要更新的数据*/
	private function getUpdateParams($postData, $dbData) {
	    $updateParams = array();
	    foreach ($postData as $key => $value) {
	        if ($value == $dbData[$key])
	            continue;
	        $updateParams[$key] = $value;
	    }
	    return $updateParams;
	}
	
	/**
	 * 检查数据有效性
	 */
	private function checkRequestData($requestData) {
		if(! ($requestData['title'])) $this->output(-1, '标题不能为空.');
		if(! isset($requestData['link_type'])) $this->output(-1, '链接类型不能为空.');
		if(! isset($requestData['link'])) $this->output(-1, '链接参数不能为空.');
		if(! Game_Service_Util_Link::checkLinkValue($requestData['link_type'], $requestData['link'])) {
		    $this->output(-1, '链接参数错误.');
		}
		if(! ($requestData['img'])) $this->output(-1, '请上传图片文件.');
		return $requestData;
	}

	public function uploadAction() {
	    $imgId = $this->getInput('imgId');
	    $this->assign('imgId', $imgId);
	    $this->getView()->display('common/upload.phtml');
	    exit;
	}
	
	public function upload_postAction() {
	    $ret = Common::upload('img', 'ad');
	    $imgId = $this->getPost('imgId');
	    $this->assign('code' , $ret['data']);
	    $this->assign('msg' , $ret['msg']);
	    $this->assign('data', $ret['data']);
	    $this->assign('imgId', $imgId);
	    $this->getView()->display('common/upload.phtml');
	    exit;
	}
	
}
