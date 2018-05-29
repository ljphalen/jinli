<?php

if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * Enter description here ...
 * @author rainkid
 */
class WallpaperadminController extends Admin_BaseController {

    public $status = array(
        0 => '默认',
        1 => '已提交',
        2 => '未通过',
        3 => '已通过',
        4 => '已上架',
        5 => '已下架',
    );
//    每页显示的条数;
    private static $Num = 12;
//每页上有多少个页码;
    private static $pages = 10;
    private $wallpaperType = array("1" => "微乐", "2" => "梦向");

    private function __inits() {
        $this->webroot = Yaf_Application::app()->getConfig()->adminroot;
        $this->roots = Yaf_Application::app()->getConfig()->staticroot;
        $this->staticroot = Yaf_Application::app()->getConfig()->staticroot;
        $this->downloadroot = Yaf_Application::app()->getConfig()->downloadroot;
        $this->upload_dir = BASE_PATH . '../attachs/theme/attachs/wallpaper/';
    }

    public function indexAction() {
        $tid = intval($this->getInput("tid")) ? intval($this->getInput("tid")) : 0;
        $status = intval($this->getInput("status"))? : false;

        $search = $this->getInput("search") ? $this->getInput("search") : false;
        $is_ajax = $this->getPost("isAjax");
        $mainTag = $this->getInput("targId");
        $page = (intval($this->getInput("page"))) ? (intval($this->getInput("page"))) : 1;
        $targ = Theme_Service_Wallpapertype::getAll();
        $lineTarg = $this->lineTarg($targ);

        $targsed = $this->getInput("ids");
        $sedIds = explode("_", $targsed);
        $this->assign("selectsedIds", $sedIds);

        $sedTarg = $this->mk_sedtypeData(Theme_Service_Wallpapersubtype::getAll());
        $limit = ($page - 1) * self::$Num;
        if ($tid) {
            $wherecount = " wallpaper_type=$tid";
            $wheresql = " wallpaper_type=$tid order by wallpaper_online_time DESC limit $limit," . self::$Num;
        } else {
            $wherecount = "1=1";
            $wheresql = "1=1 order by wallpaper_id DESC limit $limit," . self::$Num;
        }

        if ($status) {
            $pare = array("status" => $status);
            $this->assign("pare", $pare);
            $wherecount = " wallpaper_status=$status";
            $wheresql = " wallpaper_status=$status order by wallpaper_id DESC limit $limit," . self::$Num;

            if ($status == 4) {
                $wheresql = " wallpaper_status=$status order by wallpaper_online_time DESC limit $limit," . self::$Num;
            }
        }

//二级标签筛选;
        $targIds = implode(",", $sedIds);
        if ($targIds) {
//分页参数;
            if ($pare) {
                $pare = $this->arrayKey($pare, "ids", $targsed);
            } else {
                $pare = array('ids' => $targsed);
            }
            $sedIdx = Theme_Service_IdexWallpaperSubType::getWallpaperId($targIds);
            if ($sedIdx) {
                $wallIds = implode(",", $this->mk_wallpaper_id($sedIdx));
                if ($tid) {
                    $wherecount = "wallpaper_type=$tid and wallpaper_id in ($wallIds)";
                    $wheresql = " wallpaper_type=$tid and wallpaper_id in ($wallIds) order by wallpaper_id DESC limit $limit," . self::$Num;
                } else {
                    $wherecount = "wallpaper_id in ($wallIds)";
                    $wheresql = " wallpaper_id in ($wallIds) order by wallpaper_id DESC limit $limit," . self::$Num;
                }
            } else {
                $wherecount = " wallpaper_id <0";
                $wheresql = " wallpaper_id <0";
            }
        }
        if ($search) {
            $wherecount = " wallpaper_name like '%$search%'";
            $wheresql = " wallpaper_name like '%$search%' order by wallpaper_id DESC ";
        }
        $filed = "count(*) as count";
        $count = Theme_Service_WallFileImage::getByWhere($wherecount, $filed)[0];
        $wallpaper = $this->mk_imgPath(Theme_Service_WallFileImage::getByWhere($wheresql));
        if ($wallpaper) {
            $sedIdxWallpaper = $this->mk_sed_to_wallpaperId($wallpaper);
        }
        $this->showPages($count['count'], $page, self::$Num, self::$pages, $tid, $pare);
        $this->assign("userinfo", $this->userInfo);
        $this->assign("sedidxwallpaper", $sedIdxWallpaper);
        $this->assign("sedtype", $sedTarg);
        $this->assign("status", $this->status);
        $this->assign("selectstatus", $status);
        $this->assign("tid", $tid);
        $this->assign("wallpaper", $wallpaper);
        $this->assign("targ", $targ);
        $this->assign("search", $search);
        $this->assign("linetarg", $lineTarg);
        $this->assign("meunOn", "bz_zy");
    }

    /**
     * 热门列表人工干预排序
     * @return [type] [description]
     */
    public function hotAction() {
        $tid = intval($this->getInput("tid")) ? intval($this->getInput("tid")) : 0;

        $search = $this->getInput("search") ? $this->getInput("search") : false;
        $is_ajax = $this->getPost("isAjax");
        $mainTag = $this->getInput("targId");
        $page = (intval($this->getInput("page"))) ? (intval($this->getInput("page"))) : 1;
        $targ = Theme_Service_Wallpapertype::getAll();
        $lineTarg = $this->lineTarg($targ);

        $targsed = $this->getInput("ids");
        $sedIds = explode("_", $targsed);
        $this->assign("selectsedIds", $sedIds);

        $sedTarg = $this->mk_sedtypeData(Theme_Service_Wallpapersubtype::getAll());
        $limit = ($page - 1) * self::$Num;
        if ($tid) {
            $wherecount = " wallpaper_type=$tid and wallpaper_status=4";
            $wheresql = " wallpaper_type=$tid and wallpaper_status=4 order by hot_sort DESC limit $limit," . self::$Num;
        } else {
            $wherecount = "1=1 and wallpaper_status=4 ";
            $wheresql = "1=1 and wallpaper_status=4  order by hot_sort DESC limit $limit," . self::$Num;
        }

        //二级标签筛选;
        $targIds = implode(",", $sedIds);
        if ($targIds) {
            //分页参数;
            if ($pare) {
                $pare = $this->arrayKey($pare, "ids", $targsed);
            } else {
                $pare = array('ids' => $targsed);
            }
            $sedIdx = Theme_Service_IdexWallpaperSubType::getWallpaperId($targIds);
            if ($sedIdx) {
                $wallIds = implode(",", $this->mk_wallpaper_id($sedIdx));
                if ($tid) {
                    $wherecount = "wallpaper_type=$tid and wallpaper_id in ($wallIds) and wallpaper_status=4 ";
                    $wheresql = " wallpaper_type=$tid and wallpaper_id in ($wallIds) and wallpaper_status=4 order by hot_sort DESC limit $limit," . self::$Num;
                } else {
                    $wherecount = " wallpaper_id in ($wallIds) and wallpaper_status=4 ";
                    $wheresql = " wallpaper_id in ($wallIds) and wallpaper_status=4  order by hot_sort DESC limit $limit," . self::$Num;
                }
            } else {
                $wherecount = " wallpaper_id <0";
                $wheresql = " wallpaper_id <0";
            }
        }
        if ($search) {
            $wherecount = " wallpaper_name like '%$search%' and wallpaper_status=4 ";
            $wheresql = " wallpaper_name like '%$search%' and wallpaper_status=4 order by hot_sort DESC ";
        }
        $filed = "count(*) as count";
        $count = Theme_Service_WallFileImage::getByWhere($wherecount, $filed)[0];
        $wallpaper = $this->mk_imgPath(Theme_Service_WallFileImage::getByWhere($wheresql));
        if ($wallpaper) {
            $sedIdxWallpaper = $this->mk_sed_to_wallpaperId($wallpaper);
        }
        $this->showPages($count['count'], $page, self::$Num, self::$pages, $tid, $pare);
        $this->assign("userinfo", $this->userInfo);
        $this->assign("sedidxwallpaper", $sedIdxWallpaper);
        $this->assign("sedtype", $sedTarg);
        $this->assign("tid", $tid);
        $this->assign("wallpaper", $wallpaper);
        $this->assign("targ", $targ);
        $this->assign("search", $search);
        $this->assign("linetarg", $lineTarg);
        $this->assign("meunOn", "bz_hot");
    }

    /**
     * 更新热门排序
     * @return null
     */
    public function updateHotAction() {
        $id = $this->getPost("id");

        $sort = $this->getPost("sort");
        $data = array('hot_sort' => $sort);
        $res = Theme_Service_WallFileImage::update($data, $id);
        echo $res;
        exit;
    }

    public function indexeditAction() {

        $wid = $this->getInput("wid");
        $page = $this->getInput("page")? : 1;
        if ($this->userInfo['groupid'] == 2) {
            $status = array(2 => "未通过", 3 => "已通过");
        }
        if ($this->userInfo['groupid'] == 3) {
            $status = array(4 => "上架", 5 => "下架");
        }
        $targ = Theme_Service_Wallpapertype::getAll();
        $subtargs = Theme_Service_Wallpapersubtype::getAll();
        $linesubtargs = $this->linesubTarg($subtargs);

        $wallpaper_subtargs = Theme_Service_IdexWallpaperSubType::getPaperTargs($wid);
        $wallpaper_sub_targName = $this->mk_subtargs_wallpaper($linesubtargs, $wallpaper_subtargs);
        $lineTarg = $this->lineTarg($targ);

        $where = "wallpaper_id=$wid";
        $wallpaperinfo = $this->mk_imgPath(Theme_Service_WallFileImage::getByWhere($where));

        if ($wallpaperinfo[0]['wallpaper_online_time']) {
            $this->assign('datatimes', $wallpaperinfo[0]['wallpaper_online_time']);
        } else {
            $this->assign('datatimes', date("Y-m-d H:i:s", time()));
        }



        $this->assign("page", $page);
        $this->assign("wallpaper_sub_targName", $wallpaper_sub_targName);
        $this->assign("wallpaperinfo", $wallpaperinfo[0]);
        $this->assign("status", $this->status);
        $this->assign("selstatus", $status);
        $this->assign("subtargs", $subtargs);
        $this->assign("targs", $targ);
        $this->assign("linetarg", $lineTarg);
        $this->assign("meunOn", "bz_zy");
    }

    private function mk_sedtypeData(array $data) {
        foreach ($data as $v) {
            $tem[$v['w_subtype_id']] = $v;
        }
        return $tem;
    }

    private function mk_sed_to_wallpaperId(array $data = array()) {
        if (!is_array($data)) return 0;
        foreach ($data as $v) {
            $tem[] = $v['wallpaper_id'];
        }
        $ids = implode(",", $tem);
        $res = Theme_Service_IdexWallpaperSubType::getPaperTargsWhereIn($ids);

        if (!$res) return null;
        foreach ($res as $r) {
            $restem[$r['wallpaper_id']][] = $r['wallpaper_type_subid'];
        }
        return $restem;
    }

    public function updateStatusWallpaperAction() {
        $id = $this->getPost("id");
        $status = $this->getPost("status");
        if ($status == 4) {
            $where = "wallpaper_status=$status,wallpaper_online_time=" . time();
        } else {
            $where = "wallpaper_status=$status";
        }
        $res = Theme_Service_WallFileImage::updatewheres($where, $id);
        echo $res;
        exit;
    }

    public function editPostAction() {
        $wName = $this->getPost("wName");
        $wid = $this->getPost("wid");
        $wtarg = $this->getPost("wtarg");
        $subids = $this->getPost("subids");

        $pubtime = strtotime($this->getPost("pubtime"));
        $status = $this->getpost("check");
        $check_conn = $this->getPost("conns");
        $subres = array_unique(explode("_", $subids));

        //发布时间超过当前1000秒算预发布;
        /* if ($pubtime > time() + 1000) {
          $pre_time = true;
          } else {
          $pre_time = FALSE;
          } */

        $wheres = "wallpaper_name = '$wName', wallpaper_type = $wtarg, wallpaper_status = $status";
        if ($status == 2 && $check_conn) $wheres .=", wallpaper_note = '$check_conn'";
        // if ($pre_time) {
        //     $wheres .= ",wallpaper_online_time = " . $pubtime;
        //   }
        if ($status == 4) $wheres .=", wallpaper_online_time = " . $pubtime;


        $res = Theme_Service_WallFileImage::updatewheres($wheres, $wid);
        //写入二级标签;
        if ($subres[0]) {
            Theme_Service_IdexWallpaperSubType::delPaperTargs($wid);
            foreach ($subres as $v) {
                $res = Theme_Service_IdexWallpaperSubType::addPaperTargs($wid, $v);
            }
        }
        echo $res;
        exit;
    }

//6.0.3
    public function delwallpaperAction() {
        $id = $this->getPost("wall_id");
        $res = Theme_Service_WallFileImage::del_wallpaper($id);
        echo $res;
        exit;
    }

    //6.0.3
    //删除套图;
    public function delSetsAction() {
        $setid = intval($this->getPost("setid")) ? intval($this->getPost("setid")) : 0;
        $res = Theme_Service_Wallsets::delSets($setid);
        echo $res;
        exit;
    }

    public function livewallpaperAction() {

        $livewallpaperType = array(1 => "微乐", 2 => "梦像");

        $page = intval($this->getInput("page")) ? intval($this->getInput("page")) : 1;

        $type = intval($this->getInput("tid")) ? intval($this->getInput("tid")) : 0;

        $limit = ($page - 1) * self::$Num;
        if ($type) {
            $where = "wallpaperlive_type = $type order by wallpaperlive_id DESC limit $limit, " . self::$Num;

            $where_count = "wallpaperlive_type = $type";
        } else {
            $where = "1 = 1 order by wallpaperlive_id DESC limit $limit, " . self::$Num;
            $where_count = "1 = 1";
        }

        $file_count = "count(*) as count";
        $livewallpaper = Theme_Service_Wallpaperlive::getListByWhere($where);
        $count = Theme_Service_Wallpaperlive::getListByWhere($where_count, $file_count);

        $this->showPages($count[0]["count"], $page, self::$Num, self::$pages, $type);
        $reslive = $this->mk_data_livewallpaer($livewallpaper);

        $this->assign("tid", $type);
        $this->assign("livewallpaperType", $livewallpaperType);
        $this->assign("userinfo", $this->userInfo);
        $this->assign("status", $this->status);
        $this->assign("wallpaperlive", $reslive);
        $this->assign("meunOn", "lbz_live");
    }

    public function upstatusLiveAction() {
        $liveid = $this->getPost("liveid");
        $status = $this->getPost("status");
        $res = Theme_Service_Wallpaperlive::update_status($status, $liveid);
        echo $res;
        exit;
    }

    public function livewallpapereditAction() {
        $liveid = intval($this->getInput("liveid"));
        $where = "wallpaperlive_id = $liveid";
        $res = Theme_Service_Wallpaperlive::getListByWhere($where);
        $wallpaperlive = $this->mk_livewallpaperdata($res);


        $this->assign("wallpaperType", $this->wallpaperType);
        $this->assign("wallpaperlive", $wallpaperlive[0]);
        $this->assign("meunOn", "lbz_live");
    }

    private function mk_livewallpaperdata($livewallpaper) {
        foreach ($livewallpaper as &$v) {
            $v["imgs"] = explode(",", $v['wallpaperlive_url_image']);
        }
        return $livewallpaper;
    }

    public function updateliveAction() {
        $liveid = $this->getPost("liveid");
        $name = $this->getPost("livename");
        $txt_editor = $this->getPost("txt_editor");

        $arr = array(
            "wallpaperlive_name" => $name,
            "wallpaperlive_conn" => $txt_editor,
        );
        $res = Theme_Service_Wallpaperlive::update_info($arr, $liveid);
        echo $res;
        exit;
    }

    public function updateliveTypeAction() {
        $liveid = $this->getPost("liveid");
        $type = $this->getPost("liveType");

        $arr = array(
            "wallpaperlive_type" => $type,
        );


        $res = Theme_Service_Wallpaperlive::update_info($arr, $liveid);
        echo $res;
        exit;
    }

    private function mk_data_livewallpaer($livewallpaper) {
        foreach ($livewallpaper as &$v) {
            if ($v['wallpaperlive_url_image']) {
                $v['imgs'] = explode(",", $v['wallpaperlive_url_image']);
                foreach ($v['imgs'] as $k => &$s) {
                    $s = $this->imageurl . "/livepaper/" . $s;
                }
            }
        }
        return $livewallpaper;
    }

    private function mk_subtargs_wallpaper($subtargs, $wallpaperinfotargs) {
        foreach ($wallpaperinfotargs as $v) {
            $res[] = $subtargs[$v['wallpaper_type_subid']];
        }
        return $res;
    }

    public function updatestatusAction() {
        $set_id = $this->getPost("set_id");
        $status = $this->getPost("status");
        $res = Theme_Service_Wallsets::update_setStatus($set_id, $status);
        echo $res;
        exit;
    }

    public function typelistAction() {
        $where = "1 = 1";
        $order = "order by w_type_sort DESC";
        $targs = Theme_Service_Wallpapertype::getAll($where, $order);
        $subtargs = Theme_Service_Wallpapersubtype::getAll();
        $this->assign("subtargs", $subtargs);
        $this->assign("targs", $targs);
        $this->assign("meunOn", "bz_tagAdmin");
    }

    public function adminsetAction() {
        $targ = Theme_Service_Wallpapertype::getAll();
        $page = intval($this->getInput("page")) ? intval($this->getInput("page")) : 1;
        $lineTarg = $this->lineTarg($targ);

        $search = $this->getInput("search") ? $this->getInput("search") : false;

        $sorts = intval($this->getInput("sorts")) ? intval($this->getInput("sorts")) : 1;
        $start = ($page - 1) * self::$Num;
        $this->assign("lineTarg", $lineTarg);
        $tid = $this->getInput("tid")? : false;

        if ($sorts == 1) {
            $str_sort = "set_publish_time DESC";
        } else {
            $str_sort = "set_id DESC";
        }
        if ($tid) {
            $where = "set_targ = $tid order by $str_sort limit $start, " . self::$Num;
            $count_where = "set_targ = $tid ";
        } else {
            $where = "1 = 1 order by $str_sort limit $start, " . self::$Num;
            $count_where = "1 = 1";
        }

        $count_flied = "count(*) as count";

        if ($search) {
            $count_where = " set_name like '%$search%'";
            $where = " set_name like '%$search%' order by set_id DESC ";
        }
        $wallset = Theme_Service_Wallsets::getAllBywhere($where);



        $t_wallset = $this->mk_preSet($wallset);

        $wallset = $this->mk_wallsetinfo($t_wallset);
        $count = Theme_Service_Wallsets::getAllBywhere($count_where, $count_flied)[0];
        $pare = array("sorts" => $sorts);
        $this->showPages($count['count'], $page, self::$Num, 10, $tid, $pare);

        $this->assign("search", $search);
        $this->assign("status", $this->status);

        $this->assign("wallset", $wallset);
        $this->assign("tid", $tid);
        $this->assign("targ", $targ);
        $this->assign("sort", $sorts);
        $this->assign("meunOn", "bz_taotu_taotuList");
    }

    private function mk_preSet($sets) {
        if (!$sets) return 0;
        foreach ($sets as &$v) {
            if ($v['set_publish_time'] > time()) {
                $tem = $this->get_set_images($v);
                $tem = $this->array_sort($tem, "wallpaper_online_time", 'desc');
                if ($v['set_publish_time'] < $tem[0]['wallpaper_online_time']) {
                    $v['pre_opt'] = 1;
                    Theme_Service_Wallsets::update_setStatus($v['set_id'], 5);
                }
                $v["is_pre"] = 1;
            }
        }
        return $sets;
    }

    private function mk_wallsetinfo($wallset) {
        if (!$wallset) return 0;
        foreach ($wallset as &$v) {
            $v["nums"] = count(json_decode($v['set_images']));
        }

        return $wallset;
    }

    private function mk_wallpaper_id(array $data) {
        foreach ($data as $v) {
            $tem[] = $v["wallpaper_id"];
        }

        return $tem;
    }

    public function seteditAction() {
        $set_id = $this->getinput("setid");
        $targ = Theme_Service_Wallpapertype::getAll();
        $lineTarg = $this->lineTarg($targ);

        $where = "set_id = $set_id";
        $setinfo = Theme_Service_Wallsets::getAllBywhere($where);


        $res_set = $this->get_set_images($setinfo[0]);
        if ($setinfo[0]['set_publish_time']) {
            $this->assign('datatimes', date("Y-m-d H:i:s", $setinfo[0]['set_publish_time']));
        } else {
            $this->assign('datatimes', date("Y-m-d H:i:s", time()));
        }



        $this->assign("setimage", $res_set);
        $this->assign("setinfo", $setinfo[0]);
        $this->assign("lineTarg", $lineTarg);
        $this->assign("targs", $targ);
        $this->assign("meunOn", "bz_taotu_taotuList");
    }

    public function update_editAction() {
        $setid = $this->getPost("setid");
        $name = $this->getPost("name");
        $ids = $this->getPost("imids");
        $color = $this->getPost("color");
        $targid = $this->getPost("targid");
        $packgetime = strtotime($this->getPost("packagetime")); //2014-11-11->1234546567;

        $linetime = strtotime($this->getPost("lineTime")); //2014-11-11->1234546567;
        $wallids = json_encode(explode("_", $ids));
        if ($linetime) {
            $where = "set_status = 4, set_name = '$name', set_images = '$wallids'"
                    . ", set_targ = $targid, set_publish_time = $linetime, set_image_color = '$color'";
        } else {
            $where = "set_name = '$name', set_image_color = '$color', set_images = '$wallids', set_targ = $targid, set_create_time = $packgetime";
        }
        $res = Theme_Service_Wallsets::update_fileds_bywhere($setid, $where);
        echo $res;
        exit;
    }

    private function get_set_images($setinfo) {
        $imgs = $setinfo['set_images'];
        $imgs = str_replace("[", " ", $imgs);
        $imgs = str_replace("]", " ", $imgs);
        $res = Theme_Service_WallFileImage::get_in_imagesAdmin($imgs, true);

        if (!$res) return null;
        foreach ($res as &$v) {
            $str_tmp = strrpos($v['wallpaper_path'], "/");
            $tmp_url = substr($v['wallpaper_path'], 0, $str_tmp + 1);
            $tmp_name = substr($v["wallpaper_path"], $str_tmp + 1);

            $v['wallpaper_path'] = $this->wallpaperPath . "/attachs/wallpaper" . $v['wallpaper_path'];
            $v["url"] = $this->wallpaperPath . '/attachs/wallpaper' . $tmp_url . 'xhdpi' . '/' . $tmp_name;
        }
        return $res;
    }

    private function linesubTarg(array $targ) {
        foreach ($targ as $v) {
            $tem[$v['w_subtype_id']] = $v;
        }
        return $tem;
    }

    private function lineTarg(array $targ) {
        foreach ($targ as $v) {
            $tem[$v['w_type_id']] = $v;
        }
        return $tem;
    }

    public function addsetAction() {
        $targ = Theme_Service_Wallpapertype::getAll();
        $tid = $this->getInput("tid")? : false;
        $this->assign("tid", $tid);
        $this->assign("targ", $targ);
        $this->assign("meunOn", "bz_taotu_taotuPack");
    }

    public function adminsettwoAction() {
        $targ = Theme_Service_Wallpapertype::getAll();

        $this->assign("targs", $targ);
        $this->assign("meunOn", "bz_taotu_taotuPack");
    }

//一级标签保存;
    public function savetargAction() {
        $typeName = $this->getPost("name") ? $this->getPost("name") : false;


        $tid = $this->getPost("tid") ? $this->getPost("tid") : false;
        $imageurl = $this->getPost("urlImage") ? $this->getPost("urlImage") : false;
        $sort = $this->getPost("sort") ? $this->getPost("sort") : 0;
        $setdata = "";
        if ($tid) {
            if ($typeName) $setdata .="w_type_name = '$typeName'";
            if ($imageurl) $setdata .=", w_type_image = '$imageurl'";
            if ($sort) $setdata .=", w_type_sort = $sort ";
            if (empty($setdata)) return null;
            Theme_Service_Wallpapertype::update($setdata, $tid);
        } else {
            $filedname = 'w_type_name,w_type_image,w_type_time';
            $valname = "'$typeName', '$imageurl', " . time();


            $res = Theme_Service_Wallpapertype::insertTarg($filedname, $valname);


            if ($res) echo json_encode(array("opt" => "insert"));
            exit;
        }
    }

    //二级级标签处理; 6.0.3
    public function optsubstargAction() {
        $typeName = ($this->getPost("name") != 'undefined') ? $this->getPost("name") : false;
        $tid = ($this->getPost("id") != 'undefined') ? $this->getPost("id") : false;
        if ($tid) {
            $setData = "w_subtype_name = '$typeName', w_subtype_time = " . time();
            $where = "w_subtype_id = $tid";
            Theme_Service_Wallpapersubtype::updatesubtarg($setData, $where);
        } else {
            $res = Theme_Service_Wallpapersubtype::addTarg($typeName);
            if ($res) echo json_encode(array("opt" => "insert"));
        }
        exit;
    }

//删除二级标签;
    public function deltargsubAction() {
        $tid = ($this->getPost("tid") != 'undefined') ? $this->getPost("tid") : false;
        $res = Theme_Service_Wallpapersubtype::delsubtarg($tid);
        echo $res;
        exit;
    }

    public function delTargAction() {
        $targ_id = $this->getPost("tid");
        $res = Theme_Service_Wallpapertype::delWallpaperType($targ_id);
        echo json_encode($res);
        exit;
    }

    public function getWallpaperAction() {
        $tid = $this->getPost("tid") ? $this->getPost("tid") : false;
        // $subTid = $this->getPost("subtid");
        $targ = Theme_Service_Wallpapertype::getAll();
        $lineTarg = $this->lineTarg($targ);
        $page = $this->getPost("pageNum") ? $this->getPost("pageNum") : 1;
        $start = ($page - 1 ) * self::$Num;
        if ($tid) {
            $where = "wallpaper_status = 4 and wallpaper_type = $tid order by wallpaper_online_time DESC limit $start, " . self::$Num;
            $where_count = "wallpaper_status = 4 and wallpaper_type = $tid";
            $count_filed = "count(*)as count";
        } else {
            $where = "wallpaper_status = 4 order by wallpaper_online_time DESC limit $start, " . self::$Num;
            $where_count = "wallpaper_status = 4";
            $count_filed = "count(*)as count";
        }
        // if ($tid) $where .= "and wallpaper_type = $tid";
        $wallpaper = Theme_Service_WallFileImage::getByWhere($where);
        $counts = Theme_Service_WallFileImage::getByWhere($where_count, $count_filed)[0];

        $wallpaperdata = $this->mk_image_data($wallpaper, $lineTarg);
        $pageall = ceil($counts['count'] / self::$Num);
        $arr = array("datas" => $wallpaperdata,
            "pageCount" => $pageall,
            "recordCount" => (int) $counts['count']);
        echo json_encode($arr);
        exit;
    }

//套图打包;
    public function addsetsAction() {
        $sort = $this->getPost("sort") ? $this->getPost("sort") : 0;
        $color = $this->getPost("setcolor");
        $tpyeid = $this->getPost("typeid") ? $this->getPost("typeid") : 0;
        $title = $this->getPost("setname") ? $this->getPost("setname") : "";
        $wallids = $this->getPost("setids");
        $wallids = json_encode(explode("_", $wallids));

        $pre_publish = $this->getPost("pre_publish") ? $this->getPost("pre_publish") : time();

        $data = array("set_name" => parent::mk_sqls($title),
            // "set_conn" => parent::mk_sqls($descrip),
// "set_publish_time" => strtotime(parent::mk_sqls($pre_publish)),
            "set_targ" => $tpyeid,
            "set_sort" => parent::mk_sqls($sort),
            "set_images" => $wallids,
            'set_create_time' => time(),
            'set_image_color' => $color,
        );

        $res = Theme_Service_Wallsets::setdata($data);
        echo $res;
        exit;
    }

    public function del_livewallpaperAction() {
        $liveId = $this->getPost("id");
        $info = Theme_Service_Wallpaperlive::getListByWhere("wallpaperlive_id = $liveId");

        $res = Theme_Service_Wallpaperlive::delWallpaper($liveId);

        echo $res;
        exit;
    }

    public function addtargImgAction() {
        $ret = Common::upload('files', 'typeImage');
        if ($ret['code'] != 0) die(json_encode(array('error' => 1, 'message' => '上传失败！')));
        exit(json_encode(array('error' => 0, 'url' => $ret['data'])));
    }

    private function arrayKey(&$array, $key, $val) {
        $array[$key] = $val;
        return $array;
    }

    private function mk_image_data(array $wallpaper, $lineTarg) {
        $resulution = "qhdpi";
        foreach ($wallpaper as $k => $v) {
            $str_tmp = strrpos($v['wallpaper_path'], "/");
            $tmp_url = substr($v['wallpaper_path'], 0, $str_tmp + 1);
            $tmp_name = substr($v["wallpaper_path"], $str_tmp + 1);

            $res[$k]["url"] = $this->wallpaperPath . '/attachs/wallpaper' . $tmp_url . $resulution . '/' . $tmp_name;

            //$res[$k]["url"] = $this->imageurl . "/wallpaper/" . $v['wallpaper_path'];
            $res[$k]["picName"] = $v["wallpaper_name"];
            $res[$k]["id"] = $v["wallpaper_id"];
            $res[$k]["resolution"] = $v['wallpaper_width'] . "X" . $v["wallpaper_height"];
            //$res[$k]["status"] = $this->status[$v['wallpaper_status']];


            if ($v['wallpaper_online_time'] > time()) {
                $res[$k]["status"] = "预发布";
            } else {
                $res[$k]["status"] = $this->status[$v['wallpaper_status']];
            }
            $res[$k]["tagOne"] = $lineTarg[$v['wallpaper_type']]['w_type_name'];
            $res[$k]["tagTwo"] = 20;
            $res[$k]["times"] = 4;
            $res[$k]["uploadDate"] = date("Y-m-d H:i:s", $v["wallpaper_online_time"]);
        }
        return $res;
    }

}
