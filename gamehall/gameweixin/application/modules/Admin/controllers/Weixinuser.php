<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
class WeixinuserController extends Admin_BaseController {

    public $actions = array(
                    'listUrl' => '/Admin/Weixinuser/index',
	);
    
    const PERPAGE = 10;
    
    /**
     *  列表界面
     * */
    public function indexAction() {
        $page = $this->getPageInput();
        $inputVars = $this->getInput(array('keyword', 'group', 'uuid'));
        
        list($total, $list) = $this->getUserList($page, $inputVars);         
         $list =  $this->handlerUserInfo($list);
         $bindInfoList = $this->getBinderUserInfoList($list);
        $allNums =  Admin_Service_Weixinuser::getTotal(array());
        $bindNums =  Admin_Service_Weixinuser::getBindedTotal();
         
        $this->assign('list', $list);
        $this->assign('gameInfoList', $bindInfoList);
        $this->assign('total', $total);
        $this->assign('allNums', $allNums);
        $this->assign('bindNums', $bindNums);
        $this->assign('inputVars', $inputVars);
        $url = $this->actions['listUrl'] . '/?' . http_build_query($inputVars) . '&';
        $this->assign('pager', Common::getPages($total, $page, self::PERPAGE, $url));
    }
    
    
    private function getUserList($page, $inputVars) {
        $params = array();
        if ($inputVars['keyword']) {
            $params['nickname'] = array('LIKE', $inputVars['keyword']);
        }
        if ($inputVars['group']) {
            $params['is_binded'] = 1;
        }
        if ($inputVars['uuid']) {
            $params['is_binded'] = 1;
        	$params['binded_uuid'] = $inputVars['uuid'];
        }
       return Admin_Service_Weixinuser::getList($page, self::PERPAGE, $params);
    }
    
    
    /**
     * 设置分组信息
     * @param unknown $list
     * @return mixed
     */
    private function handlerUserInfo($list) {
        $size = count($list);
        for ($i=0;$i<$size;$i++) {
            $list[$i]['groupId'] = WeiXin_Service_User::getGroupNameByGroupId($list[$i]['groupId']);
            $srcHeadImgUrl = $list[$i]['headimgurl'];
            if (strrchr($srcHeadImgUrl, '.jpg') == '.jpg') {
                $list[$i]['headimgurl'] = Common::getAttachPath().$srcHeadImgUrl;
            } else {
                $list[$i]['headimgurl'] = '';
            }
        }
        return $list;
    }
    
    /***
     * 获取绑定用户信息
     * @param unknown $list
     * @return Ambigous <Ambigous, multitype:multitype:number string unknown  >
     */
    private function getBinderUserInfoList($list) {
        $bindInfoList = array();
        foreach ($list as $user) {
            if(!$user['is_binded']) continue;
            $userId = $user['id'];
            $uuid = $user['binded_uuid'];
            $giftCodeCount = Admin_Service_GiftGrabLog::userCodeCount($uuid);
            $bindInfoList[$userId]['bagNums'] = $giftCodeCount;
            
            $bindInfo = Admin_Service_Weixinuser::getBindInfo($uuid);
            if($bindInfo) {
                $bindInfoList[$userId]['tick'] = $bindInfo['ATick'];
                $bindInfoList[$userId]['coin'] = $bindInfo['ACoin'];
                $bindInfoList[$userId]['uname'] = $bindInfo['uname'];
                $bindInfoList[$userId]['nickName'] = $bindInfo['nickName'];
            }
        }
        return $bindInfoList;
    } 
    
}
?>