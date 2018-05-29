<?php

if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * 动态壁纸专题
 * @author tony
 *
 */
class LivewallpapersubjectController extends Admin_BaseController {

    private $status = array(
        0 => "默认",
        1 => '未上线',
        2 => '已上线',
    );
    private $subject_types_v3 = array(
        1 => '屏序1', 2 => '屏序2', 3 => '屏序3', 4 => '屏序4', 5 => '屏序5',
        6 => '屏序6', 7 => '屏序7', 8 => '屏序8', 9 => '屏序9',
        15 => '屏序15', 16 => '屏序16',
    );
    //每页显示的条数;
    private static $Num = 12;
    //每页上有多少个页码;
    private static $pages = 10;

    private function __inits() {
        $this->webroot = Yaf_Application::app()->getConfig()->adminroot;
    }

    /**
     * 动态壁纸专题列表
     * @return null
     */
    public function indexAction() {
        $page = $this->getInput("page") ? $this->getInput("page") : 1;

        $status = $this->getInput("status") ? intval($this->getInput("status")) : 0;

        $where = $status ? "status=$status" : 1;
        $where .=" order by id DESC";
        $pare = array("status" => $status);
        $subject = Theme_Service_Livewallpapersubject::getAll($where, self::$Num, $page);

        $subjectinfo = $this->mk_subject_img($subject[1]);

        parent::showPages($subject[0], $page, self::$Num, 10, 0, $pare);
        $this->assign("status", $this->status);
        $this->assign("selstatus", $status);
        $this->assign("subjectinfo", $subjectinfo);
        $this->assign("meunOn", "lbz_bzlwpsubject_bzlwpsubjectlist");
    }

    private function mk_subject_img(array $subject) {
        foreach ($subject as &$v) {
            $v["url"] = $this->imageurl . $v["cover"];
        }
        return $subject;
    }

    /**
     * 添加动态壁纸专题
     */
    public function addAction() {
        $typeid = intval($this->getInput("tid"))? : 0;

        $next = $this->getInput("next")? : 0;


        $this->assign("next", $next);
        $this->assign("meunOn", "lbz_bzlwpsubject_bzlwpsubjectadd");
    }

    /**
     * 添加专题第二步
     */
    public function addtwoAction() {
        $all_screen_key = array(15, 16);
        foreach ($this->subject_types_v3 as $key => $val) {
            if (in_array($key, $all_screen_key)) {
                $all_screen_sort[$key] = $val;
            }
        }
        $this->assign("all_screen_sort", $all_screen_sort);
        $this->assign("meunOn", "lbz_bzlwpsubject_bzlwpsubjectadd");
    }

    /**
     * 获取动态壁纸
     */
    public function getlivewallpaperAction() {
        $page = $this->getPost("pageNum") ? $this->getPost("pageNum") : 1;
        $start = ($page - 1 ) * self::$Num;

        $where = "wallpaperlive_status = 4 order by wallpaperlive_id DESC";
        $paper = Theme_Service_Wallpaperlive::getAll($where, self::$Num, $page);

        $count = $paper[0];
        $wallpaperdata = $this->mk_data_livewallpaper($paper[1]);
        $pageall = ceil($count / self::$Num);
        $arr = array(
            "datas" => $wallpaperdata,
            "pageCount" => $pageall,
            "recordCount" => (int) $count
        );
        echo json_encode($arr);
        exit;
    }

    private function mk_data_livewallpaper($livewallpaper) {
        foreach ($livewallpaper as &$v) {
            if ($v['wallpaperlive_url_image']) {
                $v['imgs'] = explode(",", $v['wallpaperlive_url_image']);
                foreach ($v['imgs'] as $k => &$s) {
                    $s = $this->imageurl . "/livepaper/" . $s;
                }
                $v['url'] = $v['imgs'][0];
            }
            $v['wallpaperlive_onlinetime'] = date('Y-m-d H:i:s', $v['wallpaperlive_onlinetime']);
            $v['wallpaperlive_uploadtime'] = date('Y-m-d H:i:s', $v['wallpaperlive_uploadtime']);
        }

        return $livewallpaper;
    }

    /**
     * 添加专题
     * @return null
     */
    public function createAction() {
        $img = $this->getPost("loadurl");
        $description = $this->getPost("txt_editor");
        $title = $this->getPost("sname") ? : "";
        $pre_publish = $this->getPost("c_time") ? : time();

        $screen_sort = $this->getPost("screenid");

        //专题类别
        $subject_type = $this->getPost("subjecttype");

        if ($subject_type == 9) {
            $imgs = $this->getPost("url_adv");
        } else {
            $imgs = json_encode(explode("_", $this->getPost("imgids")));
        }
        $data = array(
            "title" => $title,
            "description" => $description,
            "cover" => $img,
            'category' => $subject_type,
            'screen_sort' => $screen_sort,
            'images' => $imgs,
            'created_time' => time(),
            'online_time' => strtotime($pre_publish),
        );

        $res = Theme_Service_Livewallpapersubject::add($data);
        echo $res;
        exit;
    }

    /**
     * 修改专题状态
     * @return null
     */
    public function cstatusAction() {
        $status = $this->getPost("status");
        $sid = $this->getPost("sid");

        if (!$status) return 0;
        $data['status'] = $status;
        if ($status == 2) {
            $data['online_time'] = time();
        }
        $res = Theme_Service_Livewallpapersubject::update($data, $sid);
        echo $res;
        exit;
    }

    /**
     * 上传封面
     * @return null
     */
    public function uploadimgAction() {
        $ret = Common::upload('files', 'liveWallPaperSubjectImage');
        if ($ret['code'] != 0) die(json_encode(array('error' => 1, 'message' => '上传失败！')));
        exit(json_encode(array('error' => 0, 'url' => $ret['data'])));
    }

    /**
     * 删除专题
     * @return null
     */
    public function deleteAction() {
        $sub_id = $this->getPost("sid");
        $res = Theme_Service_Livewallpapersubject::delete($sub_id);
        echo $res;
        exit;
    }

    public function updateAction() {
        $sid = $this->getInput("sid");
        $res = Theme_Service_Livewallpapersubject::get($sid);
        $ids = $res['images'];
        if ($res["category"] == 9) {
            $imginfo = $res['images'];
        } elseif ($res["category"] == 0) {
            $ids = json_decode($ids);
            foreach ($ids as $key => $id) {
                if (!$id) {
                    unset($ids[$key]);
                }
            }
            if (!empty($ids)) {
                $wallpaper = Theme_Service_Wallpaperlive::getByIdsToSort($ids);
                $imginfo = $this->mk_data_livewallpaper($wallpaper);
            }
        }

        $all_screen_key = array(15, 16);
        foreach ($this->subject_types_v3 as $key => $val) {
            if (in_array($key, $all_screen_key)) {
                $all_screen_sort[$key] = $val;
            }
        }



        $datatimes = $res['online_time'] ? : time();
        $this->assign("all_screen_sort", $all_screen_sort);
        $this->assign("imginfo", $imginfo);
        $this->assign("subject", $res);
        $this->assign('datatimes', $datatimes);
        $this->assign("meunOn", "lbz_bzlwpsubject_bzlwpsubjectlist");
    }

    /**
     * 更新专题
     * @return null
     */
    public function doupdateAction() {
        $sid = $this->getPost("sid");

        $img = $this->getPost("loadurl");
        $description = $this->getPost("txt_editor");
        $title = $this->getPost("sname") ? : "";
        $p_time = $this->getPost("p_time") ? : time();
        $screen_sort = $this->getPost("screenid");
        //专题类别
        $subject_type = $this->getPost("subjecttype");
        if ($subject_type == 9) {
            $imgs = $this->getPost("url_adv");
        } else {
            $imgs = json_encode(explode("_", $this->getPost("imgids")));
        }
        $data = array(
            "title" => $title,
            "description" => $description,
            "cover" => $img,
            'category' => $subject_type,
            'screen_sort' => $screen_sort,
            'images' => $imgs,
            'online_time' => strtotime($p_time),
            'last_update_time' => time(),
            'status' => 1,
        );
        $res = Theme_Service_Livewallpapersubject::update($data, $sid);
        echo $res;
        exit;
    }

}
