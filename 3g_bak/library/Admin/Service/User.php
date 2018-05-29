<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * @author rainkid
 *
 */
class Admin_Service_User {
    private static $hash        = 'xysoza'; // hash值
    private static $sessionTime = 3600;
    private static $sessionName = 'AdminUser';

    public static function getAllUser() {
        return self::_getDao()->getAllUser();
    }

    /**
     * @param array $params
     * @param int   $page
     * @param int   $limit
     */
    public static function getList($page = 1, $limit = 10, $params = array()) {
        $params = self::_cookData($params);
        $start  = (max(intval($page), 1) - 1) * $limit;
        $ret    = self::_getDao()->getList($start, $limit, $params);
        $total  = self::_getDao()->count();
        return array($total, $ret);
    }

    /**
     * @param int $uid
     */
    public static function getUser($uid) {
        if (!intval($uid)) return false;
        return self::_getDao()->get(intval($uid));
    }

    public static function getUserByName($username) {
        if (!$username) return false;
        return self::_getDao()->getByUserName($username);
    }

    /**
     * @param string $email
     */
    public static function getUserByEmail($email) {
        if (!$email) return false;
        return self::_getDao()->getByEmail($email);
    }

    /**
     * @param array  $data
     * @param string $uid
     */
    public static function updateUser($data, $uid) {
        if (!is_array($data)) return false;
        $data = self::_cookData($data);
        return self::_getDao()->update($data, intval($uid));
    }

    /**
     * @param int $uid
     */
    public static function deleteUser($uid) {
        return self::_getDao()->delete(intval($uid));
    }

    /**
     * @param array $data
     */
    public static function addUser($data) {
        if (!is_array($data)) return false;
        $data ['registertime'] = Common::getTime();
        $data                  = self::_cookData($data);
        return self::_getDao()->insert($data);
    }

    /**
     * @param string $password
     */
    static private function _cookPasswd($password) {
        $hash   = Common::randStr(6);
        $passwd = self::_password($password, $hash);
        return array($hash, $passwd);
    }

    /**
     * @param string $username
     * @param string $passwd
     */
    public static function login($username, $password) {
        $result = self::checkUser($username, $password);
        if (!$result || Common::isError($result)) return $result;
        self::_cookieUser($result);
        return true;
    }

    /**
     * Enter description here ...
     */
    public static function logout() {
        $session = Common::getSession();
        $session->del(self::$sessionName);
        return true;
    }

    /**
     * Enter description here ...
     */
    public static function isLogin() {
        $session = Common::getSession();

        if (!$session->has(self::$sessionName)) return false;
        $sessionInfo = $session->get(self::$sessionName);

        $sessionInfo = self::_cookieEncrypt($sessionInfo, 'DECODE');
        if (!$sessionInfo || !$sessionInfo [1] || !$sessionInfo [3]) return false;
        if (!$userInfo = self::_getDao()->getByUsername($sessionInfo [1])) return false;
        if ($sessionInfo [2] != $userInfo ['uid'] || $sessionInfo [3] != $userInfo ['password']) {
            return false;
        }
        self::_cookieUser($userInfo);
        return $userInfo;
    }

    /**
     * cookie字符串加密解密方式
     *
     * @param string $str
     *            加密方式
     * @param string $encode
     *            ENCODE-加密|DECODE-解密
     *
     * @return array
     */
    static private function _cookieEncrypt($str, $encode = 'ENCODE') {
        if ($encode == 'ENCODE') return Common::encrypt($str);
        $result = Common::encrypt($str, 'DECODE');
        return explode('\t', $result);
    }

    /**
     * cookie添加
     *
     * @param string $userInfo
     *            用户信息
     *
     * @return array
     */
    static private function _cookieUser($userInfo) {
        $str = Common::getTime() . '\t';
        $str .= $userInfo ['username'] . '\t';
        $str .= $userInfo ['uid'] . '\t';
        $str .= $userInfo ['password'] . '\t';

        $sessionStr = self::_cookieEncrypt($str);
        $session    = Common::getSession();
        $session->set(self::$sessionName, $sessionStr);
    }

    /**
     *
     *
     * Enter description here ...
     *
     * @param unknown_type $username
     * @param unknown_type $password
     */
    public static function checkUser($username, $password) {
        if (!$username || !$password) return false;
        $userInfo = self::getUserByName($username);
        if (!$userInfo) return Common::formatMsg(-1, '用户不存在.');
        $password = self::_password($password, $userInfo ['hash']);
        if ($password != $userInfo ['password']) return Common::formatMsg(-1, '当前密码不正确.');
        return $userInfo;
    }

    /**
     * @param string $password
     * @param string $hash
     */
    private static function _password($password, $hash) {
        return md5(md5($password) . $hash);
    }

    /**
     *
     *
     * Enter description here ...
     *
     * @param unknown_type $data
     */
    private static function _cookData($data) {
        $tmp = array();
        if (isset ($data ['username'])) $tmp ['username'] = $data ['username'];
        if (isset ($data ['password'])) {
            list ($tmp ['hash'], $tmp ['password']) = self::_cookPasswd($data ['password']);
        }
        if (isset ($data ['uid'])) $tmp ['uid'] = $data ['uid'];
        if (isset ($data ['groupid'])) $tmp ['groupid'] = $data ['groupid'];
        if (isset ($data ['email'])) $tmp ['email'] = $data ['email'];
        if (isset ($data ['registertime'])) $tmp ['registertime'] = $data ['registertime'];
        return $tmp;
    }

    /**
     *
     * @return Admin_Dao_User
     */
    private static function _getDao() {
        return Common::getDao("Admin_Dao_User");
    }
}
