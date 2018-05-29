<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * 畅言接口
 * @author Changyan
 *
 */
class ChangyanController extends Api_BaseController {
    public function getUserInfoAction() {
        $userInfo        = Gionee_Service_User::getCurUserInfo();
        $staticResPath   = Yaf_Application::app()->getConfig()->staticroot . '/apps/3g/';
        $ret['is_login'] = empty($userInfo['id']) ? 0 : 1;
        $imgUrl          = $staticResPath . 'call/img/user_amigo_icon.jpg';
        $profileUrl      = '';
        $isvUserId       = $userInfo['id'];
        $nickname        = $userInfo['nickname'];
        $sign            = $this->sign('650ef02a49356bd5059c6d2e87e8ff4a', $imgUrl, $nickname, $profileUrl, $isvUserId);
        $ret['user']     = array(
            "img_url"     => $imgUrl,
            "nickname"    => !empty($userInfo['nickname']) ? $userInfo['nickname'] : substr_replace($userInfo['mobile'], '**', 5, 2),
            "profile_url" => $profileUrl,
            "user_id"     => $isvUserId,
            "sign"        => $sign
        );
        $callback        = $this->getInput('callback');
        header("Content-type:text/javascript;charset=utf-8");
        exit($callback . '(' . Common::jsonEncode($ret) . ')');
    }

    private function sign($key, $imgUrl, $nickname, $profileUrl, $isvUserId) {
        $toSign    = "img_url=" . $imgUrl . "&nickname=" . $nickname . "&profile_url=" . $profileUrl . "&user_id=" . $isvUserId;
        $signature = base64_encode(hash_hmac("sha1", $toSign, $key, true));
        return $signature;
    }

    public function loginAction() {
        $from  = $this->getInput('from');
        $login = Common_Service_User::checkLogin($from);        //检测登陆状态
        if (!$login['key']) {
            Common::redirect($login['keyMain']);
            exit;
        }
        Common::redirect($from);
        exit;
    }

    public function logoutAction() {
        Gionee_Service_User::logout();
        exit;
    }

    public function commentAction() {
        //error_log(date('Y-m-d H:i:s') . " " . $_POST['data'] . "\n", 3, '/tmp/3g_changyan_comment');
        $data = json_decode($_POST['data'], true);
        if (empty($data['comments'])) {
            exit('access deny!');
        }

        if (stristr($data['sourceid'], 'nav_fun_')) {
            $id   = intval(str_ireplace('nav_fun_', '', $data['sourceid']));
            $info = Nav_Service_NewsDB::getRecordDao()->get($id);
            if (!empty($info['id'])) {
                $total = $info['c_num'] + 1;
                Nav_Service_NewsDB::getRecordDao()->update(array('c_num' => $total), $id);
                $rcKey = 'NAV_FUN_OP:' . intval($info['id']);
                Common::getCache()->hSet($rcKey, 'c_num', $total);
            }
        }

        $addData = array(
            'content'    => trim($data['comments'][0]['content']),
            'ctime'      => substr($data['comments'][0]['ctime'], 0, 10),
            'ip'         => $data['comments'][0]['ip'],
            'cmtid'      => $data['comments'][0]['cmtid'],
            'userid'     => $data['comments'][0]['user']['userid'],
            'sourceid'   => $data['sourceid'],
            'url'        => $data['url'],
            'created_at' => Common::getTime(),
        );

       // error_log(date('Y-m-d H:i:s') . " " . Common::jsonEncode($addData) . "\n", 3, '/tmp/3g_changyan_comment');
        User_Service_Changyan::getDao()->insert($addData);

        exit;
    }
}