<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
class GiftController extends Admin_BaseController {

    public $actions = array(
                    'listUrl' => '/Admin/Gift/index',
                    'addUrl' => '/Admin/Gift/add',
                    'editUrl' => '/Admin/Gift/edit',
                    'deletePortUrl' => '/Admin/Gift/deletePort',
                    'addEditPortUrl' => '/Admin/Gift/addEditPort',
                    'fileUploadUrl' => '/Admin/Gift/fileUpload',
                    'dialogUrl' => '/Admin/Gift/dialog'
	);
    
    const PERPAGE = 20;
    
    public function indexAction() {
        $this->showList($this->actions['listUrl']);
    }
    
    /**
     * 图文选择礼包弹出窗
     */
    public function dialogAction() {
        $this->showList($this->actions['dialogUrl']);
    }
    
    public function getParams($inputVars) {
        $params = array();
        if ($inputVars['keyword']) {
            $params['title'] = array('LIKE', $inputVars['keyword']);
        }
        if ($inputVars['status'] && $inputVars['status'] != 0) {
            $params['status'] = $inputVars['status'] == 1 ? 1 : 0;
        }
        return $params;
    }
    
    private function showList($rootUrl) {
        $page = $this->getPageInput();
        $inputVars = $this->getInput(array('status', 'keyword'));
        $params = $this->getParams($inputVars);
        list($total, $list) = Admin_Service_Gift::getList($page, self::PERPAGE, $params);
        foreach ($list as &$gift) {
            $gift['send_count'] = $this->getGiftSendCount($gift['id']);
        }
        $this->assign('inputVars', $inputVars);
        $this->assign('list', $list);
        $this->assign('total', $total);
        $url = $rootUrl . '/?' . http_build_query($inputVars) . '&';
        $this->assign('pager', Common::getPages($total, $page, self::PERPAGE, $url));
    }
    
    public function addAction() {
    }
    
    public function editAction() {
        $id = $this->getInput('id');
        $gift = Admin_Service_Gift::getById($id);
        $giftId = $gift['id'];
        if ($gift['code_type'] == Admin_Service_Gift::CODE_TYPE_SINGLE) {
            $gift['single_code'] = $this->getSingleCode($giftId);
        }
        $sendCount = $this->getGiftSendCount($giftId);
        $this->assign('sendCount', $sendCount);
        $this->assign('giftData', $gift);
    }

    private function getSingleCode($giftId) {
        $params = array(
                        'gift_bag_id' => $giftId
        );
        $giftCode = Admin_Service_GiftCode::getBy($params);
        if ($giftCode) {
            return $giftCode['code'];
        }
        return '';
    }
    
    private function getGiftSendCount($giftId) {
        $params = array(
                        'gift_bag_id' => $giftId,
                        'status' => Admin_Service_GiftGrabLog::STATUS_SENDED
        );
        return Admin_Service_GiftGrabLog::count($params);
    }
    
    public function deletePortAction() {
        $id = $this->getInput('giftId');
        $result = Admin_Service_Gift::delete($id);
        $status = $result ? '1' : '0';
        $this->ajaxJsonOutput(array(Util_JsonKey::STATUS => $status));
    }
    
    public function fileUploadAction() {
        $result = Admin_Service_GiftCode::uploadCodeFile();
        if ($result) {
        	$this->ajaxJsonOutput(array(
        	                Util_JsonKey::STATUS => '1',
        	                Util_JsonKey::SIZE => $_FILES["file"]["size"],
        	                Util_JsonKey::TYPE => $_FILES["file"]["type"],
        	                Util_JsonKey::NAME => $_FILES["file"]["name"],
        	                Util_JsonKey::URL => $result
        	));
        } else {
            $this->ajaxJsonOutput(array(
                            Util_JsonKey::STATUS => '0'
            ));
        }
    }
    
    /**
     * 新增或编辑礼包
     * @author yinjiayan
     */
    public function addEditPortAction() {
        $inputVars = $this->getInput(array(
                        'id',
                        'type',
                        'codeType',
                        'giftName',
                        'giftInfo',
                        'giftCode',
                        'codeFileName',
                        'exchangeStartDate',
                        'exchangeEndDate',
                        'giftRate',
                        'giftStatus',
                        'eventStartDate',
                        'eventEndDate',
        ));
        $result = false;
        $result = $this->checkInput($inputVars);
        if ($result) {
            $params = array(
                            'title' => $inputVars['giftName'],
                            'content' => $inputVars['giftInfo'],
                            'code_type' => $inputVars['codeType'],
                            'activity_start_time' => $inputVars['eventStartDate'],
                            'activity_end_time' => $inputVars['eventEndDate'],
                            'exchange_start_time' => $inputVars['exchangeStartDate'],
                            'exchange_end_time' => $inputVars['exchangeEndDate'],
                            'probability' => $inputVars['giftRate'],
                            'status' => $inputVars['giftStatus'],
                            'code_file_name' => $inputVars['codeFileName'],
            );
            
            if ($inputVars['type'] == 'add') {
            	$result = Admin_Service_Gift::add($params, $inputVars['giftCode']);
            } else if ($inputVars['id']) {
                $result = Admin_Service_Gift::edit($inputVars['id'], $params, $inputVars['giftCode']);
            }
        }
        
        $status = $result ? '1' : '0';
        $this->ajaxJsonOutput(array(
                        Util_JsonKey::STATUS => $status,
                        Util_JsonKey::REDIRECT_URL => $this->actions['listUrl']
        ));
    }
    
    /**
     * 
     * @author yinjiayan
     * @param unknown $inputVars
     * @return boolean
     */
    private function checkInput($inputVars) {
        if (!$inputVars['type'] || !$inputVars['giftName'] || !$inputVars['codeType']
        || !$inputVars['giftInfo'] || !$inputVars['giftCode'] || !$inputVars['exchangeStartDate']
        || !$inputVars['exchangeEndDate'] || !$inputVars['giftRate']) {
        	return false;
        }
        
        if ($inputVars['type'] == 'edit' && !$inputVars['id']) {
        	return false;
        }
        
        if ($inputVars['exchangeStartDate'] > $inputVars['exchangeEndDate']) {
        	$this->failPostOutput('保存失败，兑换结束时间不能小于开始时间');
        }
        
        if ($inputVars['exchangeEndDate'] < time()) {
    	    $this->failPostOutput('保存失败，兑换结束时间不能小于今天');
        }
        
        if ($inputVars['codeType'] == Admin_Service_Gift::CODE_TYPE_MULTI && !$inputVars['codeFileName']) {
        	return false;
        }
        
        if ($inputVars['giftStatus'] == 1) {
            if (!$inputVars['eventStartDate'] || !$inputVars['eventEndDate']) {
                return false;
            }
        	if ($inputVars['eventStartDate'] > $inputVars['eventEndDate']) {
        		$this->failPostOutput('保存失败，活动结束时间不能小于开始时间');
        	}
        	
        	if ($inputVars['eventEndDate'] < time()) {
        	    $this->failPostOutput('保存失败，活动不在有效期');
        	}
        	
        	if ($inputVars['exchangeEndDate'] - $inputVars['eventEndDate'] < Util_TimeConvert::SECOND_OF_DAY) {
        	    $this->failPostOutput('保存失败，活动结束时间最少要比兑换结束时间小一天');
        	}
        }
        return true;
    }
}
?>