<?php

class Theme_Service_LogsMsg extends Common_Service_Base {

    public static $name = "Theme_Dao_LogsMsg";
    public static $LogStart = "Theme_Dao_LogsMsgStart";

    private static function _getDao() {
        return Common::getDao(self:: $name);
    }

    private static function _getDatLogsStart() {
        return Common::getDao(self:: $LogStart);
    }

    /**
     * 设计角色消息处理;
     * @param type $uid
     */
    private static function get_sheji_logs($uid, $groupId, $page = 1) {
        $offNum = 2;
        $pagenum = ($page - 1) * $offNum;
        $array = array(1 => "last_sheji_time", "2" => "last_check_time", "3" => "last_yuny_time");

        $whereId = array("user_id" => $uid);
        $userLastTime = self::_getDatLogsStart()->getby($whereId);
        $where_count = "count(*) as count";


        if ($userLastTime) {
            $last_time = $userLastTime[$array[$groupId]];

            if ($groupId == 1) {
                $where = "authId=$uid and status<>1  order by check_time DESC limit $pagenum,$offNum ";

                $whereNew = "authId=$uid and status<>1 and check_time >$last_time order by check_time DESC limit $pagenum,$offNum ";
            }if ($groupId == 2) {
                $where = " status=1  order by upload_time DESC limit $pagenum,$offNum";

                $whereNew = " status=1 and upload_time >$last_time order by check_time DESC limit $pagenum,$offNum ";
            }if ($groupId == 3) {
                $where = " status>=2 and status<4 order by check_time DESC limit $pagenum,$offNum ";

                $whereNew = " status>=2 and status<4 and check_time >$last_time order by check_time DESC limit $pagenum,$offNum ";
            }
            $msgInfo = self::_getDao()->getDataDao($where, "*");
            $mgsInfo_count = self::_getDao()->getDataDao($where, $where_count);
            $msgInfo['count'] = $mgsInfo_count[0]['count'];
            $msgInfo["new"] = self::_getDao()->getDataDao($whereNew, $where_count)[0]['count'];


            if (!$msgInfo) return 0;
            $last_time = (int) $msgInfo[0]["check_time"]? : $msgInfo[0]['upload_time'];

            $data = array($array[$groupId] => $last_time);
            $StrData = parent::mk_sqlUpdateByArray($data);
            $wheres = "user_id=" . $uid;
            self::_getDatLogsStart()->updataDao($StrData, $wheres);

            return $msgInfo;
        } else {
            if ($groupId == 1) {
                $where = "authId=$uid and status<>1 order by check_time DESC limit $pagenum,$offNum  ";
            }if ($groupId == 2) {
                $where = " status=1 order by upload_time DESC limit $pagenum,$offNum  ";
            }if ($groupId == 3) {
                $where = "status>=3  and status<4 order by check_time DESC  limit $pagenum,$offNum ";
            }

            $msgInfo = self::_getDao()->getDataDao($where, "*");
            $mgsInfo_count = self::_getDao()->getDataDao($where, $where_count);
            $msgInfo['count'] = $mgsInfo_count[0]['count'];
            $last_time = (int) $msgInfo[0]["check_time"]? : $msgInfo[0]['upload_time'];


            $data = array(
                "user_id" => $uid,
                $array[$groupId] => $last_time,
            );
            $res = parent::mk_sqlInsterByArray($data);
            self::_getDatLogsStart()->setDataDao($res["fileds"], $res["values"]);

            return $msgInfo;
        }
    }

    public static function setData($data) {
        $res = parent::mk_sqlInsterByArray($data);
        return self::_getDao()->setDataDao($res["fileds"], $res["values"]);
    }

    /*     * ************************
     * 获取用户的消息;
     * @param type $uid
     * @param type $groupid
     */

    public static function getMsglog($uid, $groupid, $page) {
        if (!$groupid) return 0;
        return self::get_sheji_logs($uid, $groupid, $page);
    }

    public static function updateData($data, $where) {

        $res = Theme_Service_File::getBy(array("id" => $where));

        $arr = array(
            "groupid" => $data['userGroupId'],
            "themeId" => $where,
            "auth" => $res['designer'],
            "name" => $res['title'],
            "upload_time" => $res["create_time"],
            "status" => $data["status"],
            "check_time" => time(),
            "check_name" => $data["userName"],
            "authId" => $res["user_id"]
        );
        self::setData($arr);
    }

    public static function updateRead($id) {

        $array = array("Is_read" => 1);
        $data = parent::mk_sqlUpdateByArray($array);

        $where = "id=" . $id;
        return self::_getDao()->updataDao($data, $where);
    }

    public static function deleteMsg($ids) {
        if (!$ids) return 0;
        return self::_getDao()->deletes("id", $ids);
    }

    public static function updateIsdel($ids) {

    }

}
