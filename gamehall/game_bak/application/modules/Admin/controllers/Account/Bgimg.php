<?php
if (! defined('BASE_PATH')) exit('Access Denied!');

class Account_BgimgController extends Admin_BaseController {
    
    //@formatter:off
    public $actions = array(
                    'listUrl' => '/Admin/Account_Bgimg/index',
                    'addUrl' => '/Admin/Account_Bgimg/add',
                    'addPostUrl' => '/Admin/Account_Bgimg/add_post',
                    'editUrl' => '/Admin/Account_Bgimg/edit',
                    'editPostUrl' => '/Admin/Account_Bgimg/edit_post',
                    'deleteUrl' => '/Admin/Account_Bgimg/delete',
                    'uploadUrl' => '/Admin/Account_Bgimg/upload',
                    'uploadPostUrl' => '/Admin/Account_Bgimg/upload_post',
                    'batchUpdateUrl'=>'/Admin/Account_Bgimg/batchUpdate'
    );
    
    public $ad_ptypes = array ( 
                    1 => '普通', 
                    2 => '节日' 
    );
    
    //@formatter:on
    const PERPAGE = 20;
    const AD_TYPE = Client_Service_Ad::AD_TYPE_BGIMG;
    public $versionName = 'Bgimg_Version';
    
    /**
     */
    public function indexAction() {
        $page = intval($this->getInput('page'));
        $page = $page < 1 ? 1 : $page;
        $inputVars = $this->getInput(array('title', 'status', 'start_time', 'end_time'));
        
        $params = $this->buildParamsForIndex($inputVars);
        list ( $total , $data ) = Client_Service_Ad::getCanUseAds($page, self::PERPAGE, $params);
        
        $this->assignForIndex($inputVars, $data, $total, $page);
    }
    
    private function buildParamsForIndex(&$inputVars) {
        $params = array();
        if ($inputVars['status']) {
            $params['status'] = $inputVars['status'] - 1;
        }
        if ($inputVars['title']) {
            $params['title'] = array('LIKE', $inputVars['title']);
        }
        //求交集
        if ($inputVars['start_time']) {
            $params['end_time'] = array('>=', strtotime($inputVars['start_time']));
        }
        if ($inputVars['end_time']) {
            $params['start_time'] = array('<=', strtotime($inputVars['end_time']));
        }
        $params['ad_type'] = self::AD_TYPE;
        return $params;
    }
    
    private function assignForIndex(&$inputVars, &$data, $total, $page) {
        $this->assign('inputVars', $inputVars);
        $this->assign('data', $data);
        $this->assign('ad_type', self::AD_TYPE);
        $this->assign('ad_ptypes', $this->ad_ptypes);
        $this->assign("total", $total);
        
        $url = $this->actions['listUrl'] . '/?' . http_build_query($inputVars) . '&';
        $this->assign('pager', Common::getPages($total, $page, self::PERPAGE, $url));
    }
    
    /**
     * Enter description here . ..
     */
    public function editAction() {
        $id = $this->getInput('id');
        $info = Client_Service_Ad::getAd(intval($id));
        
        $this->assign('ad_type', self::AD_TYPE);
        $this->assign('ad_ptypes', $this->ad_ptypes);
        $this->assign('info', $info);
    }
    
    /**
     * Enter description here . ..
     */
    public function addAction() {
        $this->assign('ad_ptypes', $this->ad_ptypes);
    }
    
    public static function parseImgUrl($adPtype, $imgUrl) {
        if (1 == $adPtype) {
            return explode("@@", $imgUrl);
        } else if (2 == $adPtype) {
            return array($imgUrl);
        }
    }
    
    /**
     * Enter description here . ..
     */
    public function add_postAction() {
        $info = $this->getPost(array('title', 'ad_ptype', 'img_day', 'img_night', 'start_time', 'end_time', 'status'));
        $info['ad_type'] = self::AD_TYPE;
        $this->cookData($info);
        $this->mergeImgParam($info);
        $result = Client_Service_Ad::addAd($info);
        if (! $result) $this->output(- 1, '操作失败');
        $this->output(0, '操作成功');
    }
    
    private function mergeImgParam(&$info) {
        $ptype = $info['ad_ptype'];
        $img_day = $info['img_day'];
        $img_night = $info['img_night'];
        if (1 == $ptype) {
            $info['img'] = $img_day . '@@' . $img_night;
        } else {
            $info['img'] = $img_day;
        }
        unset($info['img_day']);
        unset($info['img_night']);
    }
    
    public function edit_postAction() {
        $info = $this->getPost(array('id', 'title', 'ad_ptype', 'img_day', 'img_night', 'start_time', 'end_time', 'status'));
        $info['ad_type'] = self::AD_TYPE;
        $this->cookData($info);
        $this->mergeImgParam($info);
        $ret = Client_Service_Ad::updateAd($info, intval($info['id']));
        if (! $ret) $this->output(- 1, '操作失败');
        $this->output(0, '操作成功.');
    }
    
    private function cookData(&$info) {
        if (! $info['title']) {
            $this->output(- 1, '标题不能为空.');
        }
        if (! $info['img_day']) {
            $this->output(- 1, '图片不能为空.');
        }
        if ($info['ad_ptype'] == 1 && ! $info['img_night']) {
            $this->output(- 1, '图片不能为空.');
        }
        if (! $info['start_time']) {
            $this->output(- 1, '开始时间不能为空.');
        }
        if (! $info['end_time']) {
            $this->output(- 1, '结束时间不能为空.');
        }
        
        $info['start_time'] = strtotime($info['start_time']);
        $info['end_time'] = strtotime($info['end_time']);
        if ($info['end_time'] <= $info['start_time']) {
            $this->output(- 1, '开始时间不能大于或等于结束时间.');
        }
        if ($this->checkDateOverlap($info)) {
        	$this->output(- 1, '同类型背景图,时间不能重叠');
        }
        return $info;
    }
    
    private function checkDateOverlap(&$info) {
        $params = array();
        $params['ad_type'] = self::AD_TYPE;
        $params['ad_ptype'] = $info['ad_ptype'];
        if ($info['start_time']) {
            $params['end_time'] = array('>', $info['start_time']);
        }
        if ($info['end_time']) {
            $params['start_time'] = array('<', $info['end_time']);
        }
        if ($info['id']) {
        	$params['id'] = array('<>', $info['id']);
        }
        $total = Client_Service_Ad::getAdCount($params);
        return $total;
    }
    
    public function deleteAction() {
        $id = $this->getInput('id');
        $info = Client_Service_Ad::getAd($id);
        if ($info && $info['id'] == 0) {
            $this->output(- 1, '无法删除');
        }
        
        Util_File::del(Common::getConfig('siteConfig', 'attachPath') . $info['img']);
        $result = Client_Service_Ad::deleteAd($id);
        if (! $result) $this->output(- 1, '操作失败');
        $this->output(0, '操作成功');
    }
    
    /**
     * Enter description here . ..
     */
    public function uploadAction() {
        $imgId = $this->getInput('imgId');
        $this->assign('imgId', $imgId);
        $this->getView()
            ->display('common/upload.phtml');
    }
    
    /**
     * Enter description here . ..
     */
    public function upload_postAction() {
        $ret = Common::upload('img', 'bgimg');
        $imgId = $this->getPost('imgId');
        $this->assign('code', $ret['data']);
        $this->assign('msg', $ret['msg']);
        $this->assign('data', $ret['data']);
        $this->assign('imgId', $imgId);
        $this->getView()
            ->display('common/upload.phtml');
        exit();
    }
}
