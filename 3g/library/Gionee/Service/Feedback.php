<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * @author tiger
 *
 */
class Gionee_Service_Feedback {

    /**
     * 获取专题反馈的选项分布
     *
     * @param array $params
     */
    public static function getFeedbackStat($topicId, $statopts) {
        $list    = $data = array();
        $topicId = intval($topicId);
        list($sum, $res) = self::_getDao()->getTopicFeedbackStat($topicId, $statopts);
        foreach ($res as $k => $v) {
            $data[$v['option_num']] = $v['num'];
        }
        return array($sum, $data);
    }

    /**
     *
     * @param array $params
     * @param int   $page
     * @param int   $limit
     */
    public static function getList($page = 1, $limit = 10, $params = array(), $sort = array()) {
        $params = self::_cookData($params);
        if ($page < 1) $page = 1;
        $start = ($page - 1) * $limit;
        $ret   = self::_getDao()->getList($start, $limit, $params, $sort);
        $total = self::_getDao()->count($params);
        return array($total, $ret);
    }

    /**
     *
     * @param int $id
     *
     * @return boolean
     */
    public static function get($id) {
        if (!intval($id)) return false;
        return self::_getDao()->get(intval($id));
    }

    public static function getBy($params = array(), $orderBy = array()) {
        if (!is_array($params)) return false;
        return self::_getDao()->getBy($params, $orderBy);
    }


    public static function getsBy($params = array(), $orderBy = array()) {
        if (!is_array($params) || !is_array($orderBy)) return false;
        return array(self::_getDao()->count($params), self::_getDao()->getsBy($params, $orderBy));
    }

    /**
     *
     * @param array $data
     * @param int   $id
     */
    public static function updateApp($data, $id) {
        if (!is_array($data)) return false;
        $data = self::_cookData($data);
        return self::_getDao()->update($data, intval($id));
    }

    /**
     *
     * @param int $id
     */
    public static function deleteApp($id) {
        return self::_getDao()->delete(intval($id));
    }

    /**
     *
     * @param array $data
     */
    public static function add($data) {
        if (!is_array($data)) return false;
        $data = self::_cookData($data);
        return self::_getDao()->insert($data);
    }


    /**
     *
     */
    public static function getFeedBackDatas($elems, $params = array(), $groupBy = array(), $orderBy = array(), $limitBy = array()) {
        if (!is_array($params) || !is_array($elems)) return false;
        return array(
            self::_getDao()->count($params, $groupBy),
            self::_getDao()->getDataByParams($elems, $params, $groupBy, $orderBy, $limitBy)
        );
    }

    /**
     *
     * @param int $id
     */
    public static function updateTJ($id) {
        if (!$id) return false;
        return self::_getDao()->increment('hits', array('id' => intval($id)));
    }

    /**
     *
     * @param array $data
     */
    private static function _cookData($data) {
        $tmp = array();
        if (isset($data['user_flag'])) $tmp['user_flag'] = $data['user_flag'];
        if (isset($data['topic_id'])) $tmp['topic_id'] = $data['topic_id'];
        if (isset($data['option_num'])) $tmp['option_num'] = $data['option_num'];
        if (isset($data['answer'])) $tmp['answer'] = $data['answer'];
        if (isset($data['contact'])) $tmp['contact'] = $data['contact'];
        if (isset($data['create_time'])) $tmp['create_time'] = $data['create_time'];
        if (isset($data['ip'])) $tmp['ip'] = $data['ip'];
        if (isset($data['num'])) $tmp['num'] = $data['num'];
        return $tmp;
    }

    /**
     *
     * @return Gionee_Dao_Feedback
     */
    private static function _getDao() {
        return Common::getDao("Gionee_Dao_Feedback");
    }

    /**
     * @return Gionee_Dao_Feedbackkey
     */
    public static function getKeyDao() {
        return Common::getDao("Gionee_Dao_Feedbackkey");
    }

    static public function getKeyStr($type,$sync = false) {
        $rckey  = 'FEEDBACK_KEY_ALL_TAG:'.$type;
        $keyStr = Common::getCache()->get($rckey);
        if (empty($keyStr) || $sync) {
            $key  = array();
            $list = Gionee_Service_Feedback::getKeyDao()->getsBy(array('type' => $type,'status' => 1));
            foreach ($list as $val) {
				$key_tag_arr = explode('#', $val['key_tag']);
				foreach($key_tag_arr as $kag_val){
                      $key[] = $kag_val.'_'.$val['id'];
				} 
            }
            $keyStr = implode('|', $key);
           Common::getCache()->set($rckey, $keyStr, Common::T_ONE_DAY);
        }

        return $keyStr;
    }


    static public function getKeyInfo($name, $sync = false) {
        $rcKey = 'FEEDBACK_KEY:' . $name;
        $info  = Common::getCache()->get($rcKey);
        if ($info === false || $sync) {
            $info = Gionee_Service_Feedback::getKeyDao()->getBy(array('name' => $name));
            Common::getCache()->set($rcKey, $info, Common::T_ONE_DAY);
        }
        return $info;
    }

    static public function filter($msg,$type) {
        $keyStr = trim(self::getKeyStr($type));
		$keyStr_arr =explode('|', $keyStr);
		$key_id=array();
		foreach($keyStr_arr as $val){
		   $val_arr =explode('_', $val);
		   if(stristr($msg,$val_arr[0]))$key_id[]=$val_arr[1];
		}
		array_unique($key_id);
		$key_id=array_slice($key_id,0,10);
		$ret='';
		if(count($key_id)>0){ //有配对的
         //  $rcKey = 'FEEDBACK_KEY_TAG:' . $msg.$type;
         //  $ret = Common::getCache()->get($rcKey);
        //   if ($ret === false) {
		     $ret='';
              $list = Gionee_Service_Feedback::getKeyDao()->getsBy(array('id'=> array('IN',$key_id)));
			  foreach($list as $val){
			    $ret.=$val['content'].'<br />';
			  }
			  $ret=rtrim($ret,'<br />');
		 //     Common::getCache()->set($rcKey, $ret, Common::T_ONE_DAY);
          // }
		}
        return $ret;
    }


   static public function isValidData($s){
	  if(preg_match("/([\x{4e00}-\x{9fff}]|.+)\\1{3,}/u",$s)){
		  return false;//同字重复4次以上
	  }elseif(preg_match("/^[0-9a-zA-Z!@#\$%\^&\*\(\)\{\}\[\]\'\"\;\:\.\,\?\/\+]*$/",$s)){
		  return false;//全数字，全英文或全数字英文混合的
	  }elseif(strlen($s)<2){
		  return false;//输入字符长度过短
	  }
	  return true;
  }



}