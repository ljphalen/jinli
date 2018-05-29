<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 *
 * Enter description here ...
 * @author terry
 *
 */
class User_Service_Msg extends Common_Service_Base{

    //消息种类
    public static $msg_cat = array(
        0 => '小慧',
        1 => '知物',
        2 => '问答',
    );

    //消息内容类别
    public static $msg_list = array(
        1 => '%s待审核',
        2 => '%s审核通过',
        3 => '%s审核未通过%s',
        4 => '%s回答了我',
    );

    //消息描述字数限制为50字符
    private static $msg_len = 50;
    //消息描述超过字符后省略标识
    private static $msg_more = '...';

    /**
     *
     * @param array $orderBy
     * @return array
     */
    public static function getAll($orderBy = array()){
        return self::_getDao()->getAll($orderBy);
    }

    /**
     * 分页获取消息列表
     * @param int $page
     * @param int $limit
     * @param array $params
     * @param array $orderBy
     * @return array]
     */
    public static function getList($page = 1, $limit = 10, $params = array(), $orderBy = array()) {
        $params = self::_cookData($params);
        if ($page < 1) $page = 1;
        $start = ($page - 1) * $limit;
        $ret = self::_getDao()->getList($start, $limit, $params, $orderBy);
        $total = self::_getDao()->count($params);
        return array($total, $ret);
    }

    /**
     * get msg info by where
     * @param array $params
     * @return bool|mixed
     */
    public static function getBy($params) {
        if(!is_array($params)) return false;
        $data = self::_cookData($params);
        return self::_getDao()->getBy($data);
    }

    /**
     * get msgs info by where
     * @param $params
     * @return array|bool
     */
    public static function getsBy($params) {
        if(!is_array($params)) return false;
        $data = self::_cookData($params);
        return self::_getDao()->getsBy($data);
    }

    /**
     * 读取一条信息
     * @param $id
     * @return bool|mixed
     */
    public static function getMsg($id) {
        if (!intval($id)) return false;
        return self::_getDao()->get(intval($id));
    }

    /**
     * add msg
     * @param array
     * @return bool|string
     */
    private static function _addMsg(array $data) {
        if (!is_array($data)) return false;
        $data['create_time'] = Common::getTime();
        $data = self::_cookData($data);
        $ret = self::_getDao()->insert($data);
        if (!$ret) return false;
        return self::_getDao()->getLastInsertId();
    }

    /**
     * 回帖通知消息
     * @param int $msg_type     消息类型, User_Service_Msg::$msg_list
     * @param string $uid       消息接收者
     * @param string $desc      消息描述
     * @param string $by_uid    消息提供者
     * @param int $true_id      消息所属主体
     */
    public static function addQaAusMsg($msg_type = 0, $uid = '', $desc = '', $by_uid = '', $true_id = 0){
        $msg = array(
            'uid'       => $uid,
            'msg_type'   => $msg_type,
            'desc'      => $desc,
            'cate'      => 2,
            'true_id'   => $true_id,
            'by_uid'    => $by_uid,
        );
        self::_addMsg($msg);
    }

    /**
     * 问贴审核通知消息
     * @param int $msg_type     消息类型, User_Service_Msg::$msg_list
     * @param string $uid       消息接收者
     * @param string $desc      消息描述
     * @param string $by_uid    消息提供者
     * @param int $true_id      消息所属主体
     * @param int $reason       原因ID, Gou_Service_QaQus::$reason
     */
    public static function addQaQusVerifyMsg($msg_type = 0, $uid = '', $desc = '', $by_uid = '', $true_id = 0, $reason = 0){
        $msg = array(
            'uid'       => $uid,
            'msg_type'  => $msg_type,
            'desc'      => $desc,
            'cate'      => 2,
            'true_id'   => $true_id,
            'by_uid'    => $by_uid,
            'reason'    => $reason,
        );
        $msg['is_sys'] = 1; //属于系统消息
        self::_addMsg($msg);
    }

    /**
     * 知物评论通知消息
     * @param int $msg_type     消息类型, User_Service_Msg::$msg_list
     * @param string $uid       消息接收者
     * @param string $desc      消息描述
     * @param string $by_uid    消息提供者
     * @param int $true_id      消息所属主体
     */
    public static function addStoryCommentMsg($msg_type = 0, $uid = '', $desc = '', $by_uid = '', $true_id = 0){
        $msg = array(
            'uid'       => $uid,
            'msg_type'  => $msg_type,
            'desc'      => $desc,
            'cate'      => 1,
            'true_id'   => $true_id,
            'url'       => 'story/item?id=' . $true_id,
            'by_uid'    => $by_uid,
        );
        self::_addMsg($msg);
    }

    /**
     * 更新信息
     * @param array $data
     * @param int $id
     * @return bool|int
     */
    public static function updateMsg($data, $id) {
        if (!is_array($data)) return false;
        $data = self::_cookData($data);
        return self::_getDao()->update($data, intval($id));
    }

    /**
     * 批量更新
     * @param $field
     * @param $values
     * @param $data
     * @return bool|int
     */
    public static function updatesMsg($data, $params) {
        if (!is_array($data) && !is_array($values)) return false;
        $data = self::_cookData($data);
        return self::_getDao()->updateBy($data, $params);
    }

    /**
     * 批量更新消息已读
     * @param $ids
     * @return bool
     */
    public static function updatesReadMsg($uid){
        if (empty($uid)) return false;
        self::updatesMsg(array('is_read'=>1), array('uid'=>$uid));
    }

    /**
     * del msg
     * @param $id
     * @return bool|int
     */
    public static function deleteMsg($id) {
        return self::_getDao()->delete(intval($id));
    }

    /**
     * 获取记录数
     * @param $params
     * @return bool|string
     */
    public static function getCount($params){
        if(!is_array($params)) return false;
        return self::_getDao()->count($params);
    }

    /**
     * 是否有消息
     * @param $uid
     * @return bool|string
     */
    public static function hasMsg($uid){
        if(!$uid) return false;
        return (bool)self::getCount(array('uid' => $uid, 'cate' => array('!=', 2), 'is_read' => 0)); //去掉问答类型
    }

    /**
     * 消息格式化
     * @param int $msg_type
     * @param int $reason
     * @param string $nickname
     * @return string
     */
    public static function msgFmt($msg_type, $reason, $nickname){
        $msg_list = self::$msg_list;
        $qa_qua_reson_list = Gou_Service_QaQus::$reason;
        $msg_con = '';
        $reason_con = '';
        if($reason) $reason_con = sprintf(', 原因:%s', $qa_qua_reson_list[$reason]);
        switch($msg_type){
            case 1:
                break;
            case 2:
                $msg_con = sprintf($msg_list[2], '您发表的帖子：');
                break;
            case 3:
                $msg_con = sprintf($msg_list[3], '您发表的帖子：', $reason_con);
                break;
            case 4:
                $msg_con = sprintf($msg_list[4], $nickname);
                break;
            default:
                $msg_con = '';
        }
        return $msg_con;
    }

    /**
     * @param $data
     * @return array
     */
    private static function _cookData($data) {
        $tmp = array();
        if(isset($data['uid'])) $tmp['uid'] = $data['uid'];
        if(isset($data['msg_type'])) $tmp['msg_type'] = intval($data['msg_type']);
        if(isset($data['desc'])){
            $tmp['desc'] = $data['desc'];
            if(Util_String::strlen($tmp['desc']) > self::$msg_len){
                $tmp['desc'] = Util_String::substr($tmp['desc'], 0, self::$msg_len);
                $tmp['desc'] .= self::$msg_more;
            }
        }
        if(isset($data['cate'])) $tmp['cate'] = intval($data['cate']);
        if(isset($data['true_id'])) $tmp['true_id'] = intval($data['true_id']);
        if(isset($data['url'])) $tmp['url'] = $data['url'];
        if(isset($data['create_time'])) $tmp['create_time'] = $data['create_time'];
        if(isset($data['is_sys'])) $tmp['is_sys'] = intval($data['is_sys']);
        if(isset($data['is_read'])) $tmp['is_read'] = intval($data['is_read']);
        if(isset($data['by_uid'])) $tmp['by_uid'] = $data['by_uid'];
        if(isset($data['reason'])) $tmp['reason'] = intval($data['reason']);
        return $tmp;
    }

    /**
     *
     * @return User_Dao_Uid
     */
    private static function _getDao() {
        return Common::getDao("User_Dao_Msg");
    }

    /**
     * Baidu push msg
     * @param string $uid
     * @param string $title
     * @param string $content
     * @param int $type
     * @param string $cus
     * @return bool
     */
    public static function pushMsg($uid = '', $title = '', $content = '', $type = 3, $cus = ''){
        if(empty($uid)) return false;
        $user = User_Service_Uid::getByUid($uid);
        if(empty($user) || empty($user['baidu_uid']) || $user['type'] == 1) return false;

        $custom_content = '';
        switch($type){
            case 1:
                break;
            case 2:
                $custom_content = array('url' => $cus);
                break;
            case 3:
                $custom_content = array('action' => $cus);
                break;
        }
        $title = html_entity_decode($title);
        $content = html_entity_decode($content);
        @Api_Baidu_Push::pushMessage($user['baidu_uid'], $title, $content, $type, $custom_content, true);
    }
}
