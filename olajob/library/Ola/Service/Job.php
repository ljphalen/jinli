<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * Ola_Service_User
 * created by sam
 */

class Ola_Service_Job {
    /**
     * @param int $page
     * @param int $limit
     * @param array $params
     * @return array
     */
    public static function getList($page = 1, $limit = 10, $params = array(), $orderBy = array() ) {
        $params = self::_cookData($params);
        if ($page < 1) $page = 1;
        $start = ($page - 1) * $limit;
        $ret = self::_getDao()->getList($start, $limit, $params, $orderBy);
        $total = self::_getDao()->count($params);
        return array($total, $ret);
    }

    /**
     * @param $id
     * @return bool|mixed
     */
    public static function get($id) {
        if (!intval($id)) return false;
        return self::_getDao()->get(intval($id));
    }

    /**
     * @param $data
     * @param $id
     * @return bool|int
     */
    public static function update($data, $id) {
        if (!is_array($data)) return false;
        $data = self::_cookData($data);
        return self::_getDao()->update($data, intval($id));
    }

    /**
     * @param $id
     * @return bool|int
     */
    public static function delete($id) {
        return self::_getDao()->delete(intval($id));
    }


    /**
     * @param $data
     * @return bool|int
     */
    public static function add($data) {
        if (!is_array($data)) return false;
        $data = self::_cookData($data);
        $data["create_time"] = Common::getTime();
        //$data['status'] = 1;
        return self::_getDao()->insert($data);
    }

    public static function icrement($field, $id) {
        if (!in_array($field, array("favorite_count", "signup_count", "report_count"))) return false;
        return self::_getDao()->increment($field,array("id"=>$id));
    }

    /**
     * @param $params
     * @param array $orderBy
     * @return bool|mixed
     */
    public static function getBy($params, $orderBy = array()) {
        if (!is_array($params)) return false;
        $params = self::_cookData($params);
        return self::_getDao()->getBy($params, $orderBy);
    }
    
    /**
     * @param array $params
     * @return array
     */
    public static function getsBy($params, $sort=array()) {
        if (!is_array($params) || !is_array($sort)) return false;
        $ret = self::_getDao()->getsBy($params, $sort);
        $total = self::_getDao()->count($params);
        return array($total, $ret);
    }

    /**
     *
     * Enter description here ...
     * @param array $data
     */
    private static function _cookData($data) {
        $tmp = array();
        if(isset($data['id'])) $tmp['id'] = intval($data['id']);
        if(isset($data['category_id'])) $tmp['category_id'] = $data['category_id'];
        if(isset($data['user_id'])) $tmp['user_id'] = $data['user_id'];
        if(isset($data['area_id'])) $tmp['area_id'] = $data['area_id'];
        if(isset($data['title'])) $tmp['title'] = $data['title'];
        if(isset($data['company_name'])) $tmp['company_name'] = $data['company_name'];
        if(isset($data['star'])) $tmp['star'] = $data['star'];
        if(isset($data['money'])) $tmp['money'] = $data['money'];
        if(isset($data['job_type'])) $tmp['job_type'] = $data['job_type'];
        if(isset($data['money_type'])) $tmp['money_type'] = $data['money_type'];
        if(isset($data['check_type'])) $tmp['check_type'] = $data['check_type'];
        if(isset($data['sex'])) $tmp['sex'] = $data['sex'];
        if(isset($data['address'])) $tmp['address'] = $data['address'];
        if(isset($data['image'])) $tmp['image'] = $data['image'];
        if(isset($data['description'])) $tmp['description'] = $data['description'];
        if(isset($data['author'])) $tmp['author'] = $data['author'];
        if(isset($data['status'])) $tmp['status'] = $data['status'];
        if(isset($data['phone'])) $tmp['phone'] = $data['phone'];
        if(isset($data['signup_count'])) $tmp['signup_count'] = $data['signup_count'];
        if(isset($data['report_count'])) $tmp['report_count'] = $data['report_count'];
        if(isset($data['hits'])) $tmp['hits'] = $data['hits'];
        if(isset($data['sort'])) $tmp['sort'] = $data['sort'];
        return $tmp;
    }

    public static function format($data) {
        $tmp = array();
        foreach($data as $key=>$value) {
            if (isset($value["name"])) {
                $tmp[] = array("id"=>$value["id"], "name"=>$value["name"]);
            } else if (isset($value["title"])) {
                $tmp[] = array("id"=>$value["id"], "name"=>$value["title"]);
            }else {
                $tmp[] = array("id"=>$key, "name"=>$value);
            }
        }
        return $tmp;
    }
    
    /**
     *
     * @param number $sex
     * @return Ambigous <multitype:string , string>
     */
    public static function sex($key=0) {
        $sex = array(
            1 => '限男生',
            2 => '限女生',
            3 => '男女不限',
        );
        return $key ? $sex[intval($key)] : $sex;
    }
    
    /**
     *
     * @param number $sex
     * @return Ambigous <multitype:string , string>
     */
    public static function jobType($key=0) {
        $type = array(
            1 => '个人发布',
            2 => '企业直发',
            3 => '职介代发',
        );
        return $key ? $type[intval($key)] : $type;
    }
    
    /**
     *
     * @param number $sex
     * @return Ambigous <multitype:string , string>
     */
    public static function moneyType($key=0) {
        $type = array(
            1 => '时',
            2 => '日',
            3 => '周',
            4 => '月',
        );
        return $key ? $type[intval($key)] : $type;
    }
    
    /**
     *
     * @param number $sex
     * @return Ambigous <multitype:string , string>
     */
    public static function checkType($key=0) {
        $type = array(
            1 => '日结',
            2 => '周结',
            3 => '月结',
            4 => '完工结',
        );
        return $key ? $type[intval($key)] : $type;
    }

    /**
     *
     * @return Ola_Dao_Job
     */
    private static function _getDao() {
        return Common::getDao("Ola_Dao_Job");
    }

    /**
     * @param $val
     */
    public static function detail_info(&$val) {
        $area = Ola_Service_Area::get($val["area_id"]);
        $category = Ola_Service_Category::get($val["category_id"]);
        $user = Ola_Service_User::get($val["user_id"]);
        $day = date("Ymd") - date("Ymd", $val["create_time"]);

        $val = array(
            "id"=>$val["id"],
            "sex" => Ola_Service_Job::sex($val["sex"]),
            "money_type"=> Ola_Service_Job::moneyType($val["money_type"]),
            "check_type"=> Ola_Service_Job::checkType($val["check_type"]),
            "job_type"=> Ola_Service_Job::jobType($val["job_type"]),
            "link"=> Common::getWebRoot()."/front/index/info?id=".$val["id"],
            "area"=> $area["name"],
            "money"=> intval($val["money"]),
            "status"=> intval($val["status"]),
            "address"=>$val["address"],
            "description"=>$val["description"],
            "author"=>$val["author"],
            "company_name"=>$val["company_name"],
            "phone"=>$val["phone"],
            "user_id"=> $user["id"],
            "signup_count"=>$val["signup_count"],
            "favorite_count"=>$val["favorite_count"],
            "category"=> $category["title"],
            "category_img"=> Common::getAttachPath() . $category["img"],
            "dayfmt"=> $day == 0 ? "今天" : $day."天前",
            "create_time"=> date("Y-m-d", $val["create_time"]),
            "title"=> $val["title"],
        );
    }
}