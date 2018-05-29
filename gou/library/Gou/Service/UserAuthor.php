<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 *
 * Gou_Service_News
 * @author fanzh
 *
*/
class Gou_Service_UserAuthor{


    public static function getsBy($params, $orderBy = array()){
        $params = self::_cookData($params);
        return self::_getDao()->getsBy($params, $orderBy);
    }

    /**
     * @description 知物列表
     * @param int $page current page
     * @param int $limit page size
     * @param array $params conditions
     * @param array $orderBy order by index string
     * @return array
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
     * @function get
     * @description 根据id获取单条记录
     *
     * @param integer $id input id
     * @return array
     */
    public static function get($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}

	/**
	 * @description 添加记录
     *
	 * @param array $data data will created
	 * @return boolean 
	 */
	public static function add($data){
		if(!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->insert($data);
	}

    /**
     * @description 更新记录
     *
     * @param string $data new data
     * @param integer $id record modified
     * @return bool|int
     */
    public static function update($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}

    /**
     * @description 删除记录
     * @param integer $id
     * @return bool|int
     */
    public static function delete($id) {
		return self::_getDao()->delete(intval($id));
	}
	

	/**
	 * 参数过滤
	 * 
	 * @param array $data
	 * @return array
	 */
	private static function _cookData($data) {
		$tmp = array();
        if(isset($data['id'])) $tmp['id'] = $data['id'];
		if(isset($data['uid'])) $tmp['uid'] = $data['uid'];
		if(isset($data['username'])) $tmp['username'] = $data['username'];
		if(isset($data['nickname'])) $tmp['nickname'] = $data['nickname'];
		if(isset($data['avatar'])) $tmp['avatar'] = $data['avatar'];
		return $tmp;
	}

    /**
     * @return array 获取所有作者列表
     */
    public static function getAuthors(){
        $data =self::_getDao()->getAll();
        return Common::resetKey($data,'id');
    }

	/**
	 * 
	 *
	 * @return Gou_Dao_News
	 */
	private static function _getDao() {
		return Common::getDao("Gou_Dao_UserAuthor");
	}
	
}