<?php
if (!defined('BASE_PATH')) exit('Access Denied!');


class Gionee_Service_Tag {
    /**
     * @return Gionee_Dao_Tag
     */
    static public function getDao() {
        return Common::getDao('Gionee_Dao_Tag');
    }

    public static function incrBy($tags, $num = 1) {
        if (empty($tags[0]) && empty($tags[1])) {
            return false;
        }
        $rcKey = 'TAG_' . date('Ymd');
        Common::getCache()->hIncrBy($rcKey, implode(',', $tags), $num);
    }

    /**
     * category    必选    String    要监控的目标的类型名称
     * action    必选    String    用户跟网页进行交互的动作名称
     * value    可选    Number    跟事件相关的数值
     **/

    public static function sync2DB($nowDate = '') {
        if (empty($nowDate)) {
            $nowDate = date('Ymd', time() - 3600); //目的 在切换日期的时候不会漏掉数据
        }
        $rcKey = 'TAG_' . $nowDate;
        $list  = Common::getCache()->hGetAll($rcKey);
        Common::getCache()->delete($rcKey);
        foreach ($list as $key => $num) {
            $tmpKey = explode(',', $key);
            if (empty($tmpKey[0]) && empty($tmpKey[1])) {
                continue;
            }
            $params = array(
                'date'     => $nowDate,
                'category' => isset($tmpKey[0]) ? $tmpKey[0] : '',
                'action'   => isset($tmpKey[1]) ? $tmpKey[1] : '',
                'value'    => isset($tmpKey[2]) ? $tmpKey[2] : '',
            );

            $row = Gionee_Service_Tag::getDao()->getBy($params);

            if (!empty($row['id'])) {
                $params['num'] = $row['num'] + $num;
                Gionee_Service_Tag::getDao()->update($params, $row['id']);
            } else {
                $params['num'] = $num;
                Gionee_Service_Tag::getDao()->insert($params);
            }
        }
    }
}