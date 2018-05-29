<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 *
 * Gou_Service_Sensitive
 * @author ryan
 *
*/
class Gou_Service_Sensitive{
	
	private static $rows = array();
	private static $kwd = array();
	private static $dict = array();
	private static $encode   = "UTF-8";
	private static $ret  = "";


    /**
     * 获取关键词列表
     * @param int $page 页数
     * @param int $limit 条数
     * @param array $params 条件
     * @param array $orderBy 排序
     * @param bool $show_count 是否显示统计
     * @return array
     */
    public static function getList($page = 1, $limit = 10, $params = array(), $orderBy = array(),$show_count=false) {
        $params = self::_cookData($params);
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getList($start, $limit, $params, $orderBy);
        $res = static::getDict($ret,$show_count);
		$total = self::_getDao()->count($params);
		return array($total, $res);
	}

    /**
     * 生成字典
     * @param array $keywords 关键词
     * @param bool $show_count 是否显示统计
     * @return array
     */
    public static function getDict(array $keywords,$show_count=false){
        $dict=array();
        foreach($keywords as $keyword){
            $k = $keyword['id'];
            $name = $keyword['name'];
            $hit = sprintf("<a href='/admin/stat/click?type_id=34&item_id=%d' target='_blank'>%d</a>",$k,$keyword['hit']);
            if(empty($keyword)) continue;
            $key = mb_substr(trim($keyword['name']),0,1,static::$encode);

            if($show_count){
                $dict[$key]['list'][$k]=$name."[$hit]";
            }else{
                $dict[$key]['list'][$k]=$name;
            }
            $dict[$key]['max']=max($dict[$key]['max'],mb_strlen($keyword,static::$encode));
        }
        return $dict;
    }

    /**
     * 关键词过滤
     * @param array $content 需要过滤内容
     * @param bool|false $highlight
     * @return array
     */
    public static function fuck($content, $highlight = false)
    {
        $kwd = static::_getDao()->getAll(array('name' => 'ASC'));
        $dict = static::getDict($kwd);

        static::$dict = $dict;
        $encode = "UTF-8";
        $len = mb_strlen($content, static::$encode);
        $ret = '';
        for ($i = 0; $i < $len; ++$i) {
            $key = mb_substr($content, $i, 1, static::$encode);
            if (!array_key_exists($key, $dict)) {
                static::$ret .= mb_substr($content, $i, 1, $encode);
                continue;
            }
            if ($highlight) {
                static::highlight(mb_substr($content, $i, $dict[$key]['max'], static::$encode), $key, $af);
            } else {
                static::deal(mb_substr($content, $i, $dict[$key]['max'], static::$encode), $key, $af);
            }
            $i += $af;
        }
        return array(static::$ret, array_values(array_unique(static::$rows)));
    }

    public static function highlight($res, $key, &$af)
    {
        $af = 0;
        foreach (static::$dict[$key]['list'] as $key => $keyword) {
            if (strpos($res, $keyword) !== false) {
                $len = mb_strlen($keyword, static::$encode);
                static::updateTJ($keyword);
                Gou_Service_ClickStat::increment('34', $key);
                $af = $len - 1;
                static::$ret .= '<span style="color:red">' . $keyword . '</span>';

                array_push(static::$rows, $keyword);
                return;
            }
        }
        static::$ret .= mb_substr($res, 0, 1, static::$encode);
    }
    /**
     * @param string $res 源字符串
     * @param string $key 关键字数组
     * @param $af
     */
    public static function deal($res,$key,&$af){
        $af=0;
        foreach(static::$dict[$key]['list'] as $key => $keyword){
            if(strpos($res,$keyword) !==false){
                $len=mb_strlen($keyword,static::$encode);
                static::updateTJ($keyword);
                Gou_Service_ClickStat::increment('34',$key);
                $af=$len-1;
                static::$ret .=str_repeat("*",$len);
                array_push(static::$rows,$keyword);
                return;
            }
        }
        static::$ret .= mb_substr($res,0,1,static::$encode);
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

    public static function getSum() {
		return self::_getDao()->getSum('hit');
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
		if(self::_getDao()->insert($data))
        {
            return self::_getDao()->getLastInsertId();
        }else{
            return false;
        }
	}


	/**
	 * @description 添加记录
     *
	 * @param array $data data will created
	 * @return boolean
	 */
	public static function mutilInsert($data){
        if(!is_array($data)) return false;
        $data = array_unique($data);
        list(,$ret) = static::getsBy(array('name'=>array('IN',$data)));
        array_walk($ret,function(&$v){$v=$v['name'];});
        $rows = array_diff(array_filter($data),array_unique($ret));
        if(empty($rows)){
            return 0;
        }
        array_walk($rows,function(&$v){$v=array(0,'name'=>$v,'hit'=>0);});

		if(self::_getDao()->mutiInsert($rows))
        {
            return count($rows);
        }else{
            return false;
        }
	}


    /**
     * @description 统计更新
     * @param $id
     * @return bool|int
     */
    public static function updateTJ($name) {
        if (!$name) return false;
        return self::_getDao()->increment('hit', array('name'=>$name));
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
	 * get by
	 */
	public static function getBy($params = array()) {
	    if(!is_array($params)) return false;
	    return self::_getDao()->getBy($params);
	}

    /**
     * @param $params
     * @param $sort
     * @return array|bool
     */
    public static function getsBy($params, $sort=array()) {
	    if (!is_array($params) || !is_array($sort)) return false;
	    $ret = self::_getDao()->getsBy($params, $sort);
	    $total = self::_getDao()->count($params);
	    return array($total, $ret);
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
     * 删多条
     * @param string $field
     * @param array $values
     * @return bool|int
     */
    public static function deletes($field,$values) {
		return self::_getDao()->deletes($field,$values);
	}


	/**
	 * 参数过滤
	 * 
	 * @param array $data
	 * @return array
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['name'])) $tmp['name'] = $data['name'];
		if(isset($data['hit'])) $tmp['hit'] = $data['hit'];
		return $tmp;
	}

    /**
     * @return Gou_Dao_Sensitive
     */
    private static function _getDao() {
		return Common::getDao("Gou_Dao_Sensitive");
	}
	
}