<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class User_Service_UserVisit {

    /**
     * @return User_Dao_UserVisit
     */
    static public function getDao() {
        return Common::getDao('User_Dao_UserVisit');
    }


    static public function initInfo($uid, $sync = false) {
        $ua = Util_Http::ua();
        if (empty($ua['uuid']) || empty($uid)) {
            return false;
        }

        if (empty($uid)) {
            return false;
        }
        $rcKey = 'USER_VISIT:' . $uid;
        $info  = Common::getCache()->get($rcKey);
        if (empty($info['uid']) || $sync) {
            $info = User_Service_UserVisit::getDao()->getBy(array('uid' => $uid));
            if (empty($info['uid'])) {
                $info = array(
                    'uuid'       => $ua['uuid'],
                    'imei'       => $ua['uuid_real'],
                    'model'      => $ua['model'],
                    'app_ver'    => $ua['app_ver'],
                    'sys_ver'    => $ua['sys_ver'],
                    'uid'        => $uid,
                    'ip_addr'    => $ua['ip'],
                    'created_at' => Common::getTime(),
                );
                $ret  = User_Service_UserVisit::getDao()->insert($info);
                if ($ret) {
                    $info['id'] = User_Service_UserVisit::getDao()->getLastInsertId();
                }
            }

            if (!empty($info['uid'])) {
                Common::getCache()->set($rcKey, $info, Common::T_ONE_DAY);
            }
        }
        return $info;
    }

}