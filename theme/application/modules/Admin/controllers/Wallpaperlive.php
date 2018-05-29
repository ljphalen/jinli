<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 动态壁纸
 *
 */
class WallpaperliveController extends Admin_BaseController {
    public $status = array(
        1 => '已提交',
        2 => '未通过',
        4 => '上架',
        5 => '下架'
    );

    private static $Num = 12;
    private static $pages = 10;

    private function __inits() {
        $this->webroot = Yaf_Application::app()->getConfig()->adminroot;
        $this->roots = Yaf_Application::app()->getConfig()->staticroot;
        $this->downloadroot = Yaf_Application::app()->getConfig()->downloadroot;
        $this->upload_dir = BASE_PATH . '../attachs/theme/attachs/wallpaper/';
        $this->perpage = 10;
    }

    public function hotAction(){
        $livewallpaperType = array(1 => "微乐", 2 => "梦像");
        $page = intval($this->getInput("page")) ? intval($this->getInput("page")) : 1;
        $type = intval($this->getInput("tid")) ? intval($this->getInput("tid")) : 0;
        $keyword = $this->getInput("keyword");

        $limit = ($page - 1) * self::$Num;
        if ($type) {
            $where = "wallpaperlive_type = $type and wallpaperlive_status = 4 order by hot_sort DESC, wallpaperlive_onlinetime DESC limit $limit, " . self::$Num;
            $where_count = "wallpaperlive_type = $type  and wallpaperlive_status = 4";
        } elseif(!empty($keyword)){
            $where = "wallpaperlive_name like '%$keyword%'  and wallpaperlive_status = 4 order by hot_sort DESC, wallpaperlive_onlinetime DESC";
            $where_count = "wallpaperlive_name like '%$keyword%'  and wallpaperlive_status = 4";
        } else {
            $where = "1 = 1  and wallpaperlive_status = 4 order by hot_sort DESC, wallpaperlive_onlinetime DESC limit $limit, " . self::$Num;
            $where_count = "1 = 1  and wallpaperlive_status = 4";
        }

        $file_count = "count(*) as count";
        $livewallpaper = Theme_Service_Wallpaperlive::getListByWhere($where);
        $count = Theme_Service_Wallpaperlive::getListByWhere($where_count, $file_count);

        $this->showPages($count[0]["count"], $page, self::$Num, self::$pages, $type);
        $reslive = $this->mk_data_livewallpaer($livewallpaper);

        $this->assign("tid", $type);
        $this->assign("livewallpaperType", $livewallpaperType);
        $this->assign("userinfo", $this->userInfo);
        $this->assign("wallpaperlive", $reslive);
        $this->assign("meunOn", "lbz_hot");
    }


    /**
     * 更新热门排序
     * @return null
     */
    public function updateHotAction() {
        $id = $this->getPost("id");

        $sort = $this->getPost("sort");
        $data = array('hot_sort'=>$sort);
        $res = Theme_Service_Wallpaperlive::update($data, $id);
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

    public function indexAction() {
        $this->__inits();
        $page = intval($this->getInput('page')) ? intval($this->getInput('page')) : 1;
        $param = $this->getInput(array('status', 'perpage'));

        $perpage = $this->getInput('perpage') ? : $this->perpage;
        $status = $this->getInput("status")? : 0;
        $limit = ($page - 1) * $perpage;
        $url = $this->webroot . "/Admin/Wallpaperlive/index" . '/?' . http_build_query($param) . '&';
        if ($status) {
            $where = "wallpaperlive_status=$status limit $limit,$this->perpage ";
            $count = "count(*) as count";
            $where_count = "wallpaperlive_status=$status";
        } else {
            $where_count = "1=1";
            $count = "count(*) as count";
            $where = "1=1 limit $limit ,$this->perpage";
        }

        //数量分页用
        $total = Theme_Service_Wallpaperlive::getListByWhere($where_count, $count)[0]["count"];
        //动态壁纸信息;
        $wallpaper = Theme_Service_Wallpaperlive::getListByWhere($where, "*");
        $wallpaper = $this->mk_image_url($wallpaper);

        //--------------------------------------------------------------------//
        $this->assign('pager', Common::getPages($total, $page, $perpage, $url));
        $this->assign('perpage', $perpage);

        $this->assign("wallpaperlivelist", $this->webroot . "/Admin/wallpaperlive/index");
        $this->assign("actionwallpaper", $this->webroot . "/Admin/wallpaperlive/uploads");
        $this->assign("files", $wallpaper);
        $this->assign("listUrl", $this->webroot . "/Admin/Wallpaperadlist/index");
        $this->assign("status", $this->status);
    }

    public function uploadsAction() {
        list(, $file_type) = Theme_Service_FileType::getAllFileType();
        $this->assign("wallpaperlivelist", $this->webroot . "/Admin/wallpaperlive/index");
        $this->assign("listUrl", $this->webroot . "/Admin/Wallpaperadlist/index");
        $this->assign("addPostUrl", "/Admin/Wallpaperlive/upload_post");
        $this->assign("uploadUrl", "/Admin/File/upload");
        $this->assign("uploadAdvUrl", "/Admin/Subject/uploadAdv");
        $this->assign('file_type', Common::resetKey($file_type, 'id'));
    }

    public function update_statusAction() {
        $id = $this->getPost("wallpaperliveid");
        $status = $this->getPost("status");
        Theme_Service_Wallpaperlive::update_status($status, $id);
        exit;
    }

    public function delWallpaperAction() {
        $id = $this->getPost("wallpaperliveid");
        Theme_Service_Wallpaperlive::delWallpaper($id);
        exit;
    }

    public function upload_postAction() {
        $wallpaperPath = $this->getPost("wallpaperlive");
        $wallpapername = $this->getPost("wallpaperlive_name");
        $image_01 = $this->getPost("img_adv01");
        $image_02 = $this->getPost("img_adv02");
        $image_03 = $this->getPost("img_adv03");

        $conn = $this->getPost("descript");
        $size = sprintf("%.2f", $this->getPost("wallpaperlive_size") / 1000 / 1000);
        $auth = $this->getPost("auth");

        $image_url = "";

        if ($image_01) $image_url = $image_01;
        if ($image_02) $image_url .="," . $image_02;
        if ($image_03) $image_url .= "," . $image_03;
        $filedname = array(
            "wallpaperlive_name",
            "wallpaperlive_path",
            "wallpaperlive_auth",
            "wallpaperlive_url_image",
            "wallpaperlive_uploadtime",
            "wallpaperlive_size",
            "wallpaperlive_conn"
        );

        $val = array(
            "'$wallpapername'",
            "'$wallpaperPath'",
            "'$auth'",
            "'$image_url'",
            time(),
            "$size",
            "'$conn'"
        );
        $res = Theme_Service_Wallpaperlive::insert_into($filedname, $val);
        if (!$res) $this->output(-1, '操作失败');
        $this->output(0, '操作成功');
        exit;
    }

    private function mk_image_url($data) {
        if (!$data) return null;
        $this->__inits();
        foreach ($data as &$v) {
            if ($v["wallpaperlive_url_image"]) {
                $tem = explode(",", $v["wallpaperlive_url_image"]);
                if (count($tem) > 1) {
                    foreach ($tem as $val) {
                        $v["url"][] = $this->webroot . "/attachs" . $val;
                    }
                    //$v["url"] = $t;
                } else {
                    $v["url"][] = $this->webroot . '/attachs' . $v["wallpaperlive_url_image"];
                }
            }
        }
        return $data;
    }
}
