<?php

if (!defined('BASE_PATH')) exit('Access Denied!');

class ClockController extends Wallpaper_BaseController {

    private $webroot;
    private $staticroot;
    private $downloadroot;

    /**
     * 初始化常量
     */
    private function __inits() {
        $this->webroot = Yaf_Application::app()->getConfig()->fontcroot;
        $this->staticroot = Yaf_Application::app()->getConfig()->staticroot;
        $this->downloadroot = Yaf_Application::app()->getConfig()->downloadroot;
    }

    private function mk_sqls($str) {
        $str = str_replace("and", "", $str);
        $str = str_replace("execute", "", $str);
        $str = str_replace("update", "", $str);
        $str = str_replace("count", "", $str);
        $str = str_replace("chr", "", $str);
        $str = str_replace("mid", "", $str);
        $str = str_replace("master", "", $str);
        $str = str_replace("truncate", "", $str);
        $str = str_replace("char", "", $str);
        $str = str_replace("declare", "", $str);
        $str = str_replace("select", "", $str);
        $str = str_replace("create", "", $str);
        $str = str_replace("delete", "", $str);
        $str = str_replace("insert", "", $str);
        $str = str_replace("alert", "", $str);
        $str = str_replace("  '", "", $str);
        $str = str_replace("\"", "", $str);
        $str = str_replace(" ", "", $str);
        $str = str_replace("or", "", $str);
        $str = str_replace(" = ", "", $str);
        $str = str_replace("%20", "", $str);

        return $str;
    }

    /**
     * 获取所有上架时钟的ids,并且访问时间已经发布
     * @return json 返回手机需要的ids数据
     */
    public function idsAction() {
        $this->__inits();
        $cur_time = time();
        $where = "c_status = 4 and c_onlinetime < $cur_time order by c_sort DESC,id DESC";
        $json_ids = Theme_Service_Clockmy::getListByWhere($where, 'id');

        echo json_encode($json_ids);
        exit;
    }

    /**
     * 通过id获取时钟的详情
     * @return json 返回手机需要的数据
     */
    public function clockinfoAction() {
        $this->__inits();
        $var_ids = $this->getInput("ids");
        $ids = $this->mk_sqls($var_ids);

        $json_info = array();

        if ($ids) {

            $tmp_info = Theme_Service_Clockmy::getListByids($ids);
            foreach ($tmp_info as $k => $v) {
                $json_info[$k]["id"] = $v['id'];
                $json_info[$k]["sort"] = $v["c_sort"];
                $json_info[$k]["name"] = $v["c_filename"];
                $json_info[$k]["author"] = $v["c_author"];
                $json_info[$k]["size"] = $v["c_size"];
                $json_info[$k]["imgthumb"] = $this->webroot . '/attachs/theme' . '/clock' . $v["c_imgthumb"];
                $json_info[$k]["imgthumbbig"] = $this->webroot . '/attachs/theme' . '/clock' . $v["c_imgthumbmore"];
                $json_info[$k]["imgdetail"] = $this->webroot . '/attachs/theme' . '/clock' . $v["c_imgdetail"];
                $json_info[$k]["dlurl"] = $this->downloadroot . '/clock' . $v["c_dlurl"];
                $json_info[$k]["rvurl"] = $this->downloadroot . '/clock' . $v["c_rvurl"];
                $json_info[$k]["status"] = $v['c_status'];
                $json_info[$k]["since"] = $v['c_since'];
                $json_info[$k]["onlinetime"] = date("Y-m-d", $v["c_onlinetime"]);
            }
            echo json_encode($json_info);
        }

        exit;
    }

    /**
     * 通过id获取时钟的下载量
     * @return json 返回手机需要的数据
     */
    public function downandlikeAction() {
        $this->__inits();
        $var_id = $this->getInput("id");
        $id = $this->mk_sqls($var_id);

        $json_info = array();
        if ($id) {
            $where = "id = $id";
            $tmp_info = Theme_Service_Clockmy::getListByWhere($where, 'id,c_down,c_like');
            foreach ($tmp_info as $k => $v) {
                $json_info[$k]["id"] = $v['id'];
                $json_info[$k]["down"] = $v["c_down"];
                $json_info[$k]["like"] = $v['c_like'];
            }
            echo json_encode($json_info);
        }

        exit;
    }

    //下载前;
    public function downPreAction() {
        echo json_encode(array("info" => 1));
        exit;
    }

    /**
     * 通过id增加1时钟的下载量
     * @return json 返回手机需要的数据
     */
    public function increasedownAction() {

        $var_id = $this->getInput("id");
        $id = $this->mk_sqls($var_id);
        if ($id) {
            $res = Theme_Service_Clockmy::increase_field($id, 'c_down');
        }
        exit;
    }

    /**
     * 通过id增加1时钟的点赞量
     * @return json 返回手机需要的数据
     */
    public function increaselikeAction() {

        $var_id = $this->getInput("id");
        $id = $this->mk_sqls($var_id);
        if ($id) {
            $res = Theme_Service_Clockmy::increase_field($id, 'c_like');
        }
        exit;
    }

    /**
     * 获取时钟专题的详情
     * @return json 返回手机需要的数据
     */
    public function subjectinfoAction() {
        $this->__inits();

        $json_info = array();
        $cur_time = time();
        // $where = "cs_status = 2 and cs_pushlish_time < $cur_time order by Id DESC limit 1";
        // $tmp_info = Theme_Service_Clocksubject::getsubject_bywheres($where);
        $tmp_info = Theme_Service_Clocksubject::getsubject_byGroupType(FALSE);
        foreach ($tmp_info as $k => $v) {
            $json_info[$k]["id"] = $v['id'];
            $json_info[$k]["name"] = $v["cs_name"];
            $json_info[$k]["detail"] = $v["cs_detail"];
            $json_info[$k]["status"] = $v['cs_status'];
            $json_info[$k]["type"] = $v['cs_type'];
            $json_info[$k]["screenque"] = $v['cs_screenque'];

            $json_info[$k]["img"] = $this->webroot . '/attachs/theme' . $v["cs_image_face"];
            $json_info[$k]["imgthumb"] = $this->webroot . '/attachs/theme' . $v["cs_image_face"];
            if ($v['cs_type'] == 1) {
                $json_info[$k]["image"] = $this->mk_sqls($v["cs_image"]);
            } elseif ($v['cs_type'] == 9) {
                $json_info[$k]["image"] = $this->webroot . '/attachs/theme' . $v["cs_image"];
            }
            $json_info[$k]["pushlish_time"] = $v["cs_pushlish_time"];
        }
        echo json_encode($json_info);
        exit;
    }

    /**
     * 根据时钟专题ID获取专题下所有的时钟
     * @return json 返回手机需要的数据
     */
    public function getclocksbysubidAction() {
        $this->__inits();
        $json_info = array();
        $clock_info = array();
        $var_sid = $this->getInput("sid");
        $id = $this->mk_sqls($var_sid);
        if ($id) {
            $where = "id = $id";
            $tmp_info = Theme_Service_Clocksubject::getsubject_bywheres($where);

            if ($tmp_info) {
                $json_info["id"] = $tmp_info[0]['id'];
                $json_info["name"] = $tmp_info[0]["cs_name"];
                $json_info["detail"] = $tmp_info[0]["cs_detail"];
                $json_info["status"] = $tmp_info[0]['cs_status'];
                $json_info["type"] = $tmp_info[0]['cs_type'];
                $json_info["screenque"] = $tmp_info[0]['cs_screenque'];

                $json_info["imgthumb"] = $this->webroot . '/attachs/theme' . $tmp_info[0]["cs_image_face"];
                if ($tmp_info[0]['cs_type'] == 1) {
                    $json_info["image"] = $this->mk_sqls($tmp_info[0]["cs_image"]);
                } elseif ($tmp_info[0]['cs_type'] == 9) {


                    $json_info["image"] = $this->webroot . '/attachs/theme' . $tmp_info[0]["cs_image"];
                }
                $json_info["pushlish_time"] = date("Y-m-d", $tmp_info[0]["cs_pushlish_time"]);

                if ($json_info['type'] == 1) {
                    $ids = $json_info['image'];
                    $ids = str_replace("[", "", $ids);
                    $ids = str_replace("]", "", $ids);


                    $tmp_clock_info = Theme_Service_Clockmy::getListByids($this->mk_sqls($ids), true);
                    foreach ($tmp_clock_info as $c_k => $c_v) {
                        $clock_info[$c_k]["id"] = $c_v['id'];
                        $clock_info[$c_k]["sort"] = $c_v["c_sort"];
                        $clock_info[$c_k]["name"] = $c_v["c_filename"];
                        $clock_info[$c_k]["author"] = $c_v["c_author"];
                        $clock_info[$c_k]["size"] = $c_v["c_size"];
                        $clock_info[$c_k]["imgthumb"] = $this->webroot . '/attachs/theme' . '/clock' . $c_v["c_imgthumb"];
                        $clock_info[$c_k]["imgthumbbig"] = $this->webroot . '/attachs/theme' . '/clock' . $c_v["c_imgthumbmore"];
                        $clock_info[$c_k]["imgdetail"] = $this->webroot . '/attachs/theme' . '/clock' . $c_v["c_imgdetail"];
                        $clock_info[$c_k]["dlurl"] = $this->downloadroot . '/clock' . $c_v["c_dlurl"];
                        $clock_info[$c_k]["rvurl"] = $this->downloadroot . '/clock' . $c_v["c_rvurl"];
                        $clock_info[$c_k]["status"] = $c_v['c_status'];
                        $clock_info[$c_k]["since"] = $c_v['c_since'];
                        $clock_info[$c_k]["onlinetime"] = date("Y-m-d", $c_v["c_onlinetime"]);
                    }
                } else {
                    $clock_info = $json_info['image'];
                    $conn = $json_info["detail"];
                    echo json_encode(array('url' => array($clock_info), "conn" => $conn));
                    exit;
                }

                echo json_encode(array('subject' => $json_info, 'clock' => $clock_info,));
            }
        }
        exit;
    }

}
