<?php

if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * Enter description here ...
 * @author rainkid  ...........
 */
class ThemenewController extends Admin_BaseController {

    private static $Num = 12;
    private static $pages = 10;
    public $status = array(
        1 => '已提交',
        2 => '未通过',
        3 => '已通过',
        4 => '上架',
        5 => '下架'
    );

    public function indexAction() {
        $page = $this->getInput("page")? : 1;
        $type = $this->getInput('type')? : 0;
        $search = $this->getInput("search")? : false;
        $limit = ($page - 1) * self::$Num;

        if ($type) {
            $res = Theme_Service_IdxFileType::getByTypeId($type);
            $themeids = $this->mk_typeids($res);
            $ids = implode(",", $themeids);
            $where = " id in($ids) and status=4  order by sort DESC,id DESC  limit $limit," . self::$Num;
            $wherecount = " id in($ids) and status=4  order by sort DESC";
            $filedscount = "count(*)as count";
        } else {
            $where = "status=4 order by sort DESC,id DESC  limit $limit," . self::$Num;
            $wherecount = "status=4 order by sort DESC";
            $filedscount = "count(*)as count";
        }

        if ($search) {
            $where = "title like '%$search%' order by id DESC ";
            $wherecount = "title like '%$search%' ";
            $filedscount = "count(*)as count";
        }
        $themecount = Theme_Service_File::getByWhere($wherecount, $filedscount)[0];
        $theme = Theme_Service_File::getByWhere($where, "*");
        foreach ($theme as $values) {
            $file_ids[] = $values["id"];
        }
        //查缩略图;
        $file_imgs = Theme_Service_FileImg::getByFileIds($file_ids);

        //缩略图归类
        if (!$file_imgs) {
            $file_imgs_catg = array();
        } else {
            foreach ($file_imgs as $values) {
                $file_imgs_catg[$values['file_id']][] = $values;
            }
        }
        $targ = Theme_Service_FileType::getAllFileType();

        $targline = $this->mk_targline($targ[1]);

        $this->showPages($themecount['count'], $page, self::$Num, self::$pages);

        $this->assign("search", $search);
        $this->assign("status", $this->status);
        $this->assign("file_imgs_catg", $file_imgs_catg);
        $this->assign("type", $type);
        $this->assign("targ", $targ[1]);
        $this->assign("theme", $theme);
        $this->assign("meunOn", "zt_newArrivals");
    }

    public function spcSortAction() {
        $page = $this->getInput("page")? : 1;
        $type = $this->getInput('type')? : 0;
        $search = $this->getInput("search")? : false;
        $limit = ($page - 1) * self::$Num;
        $targ = Theme_Service_FileType::getAllFileType();
        $targline = $this->mk_targline($targ[1]);

        if ($type) {
            $res = Theme_Service_IdxFileType::getByTypeId($type);
            $themeids = $this->mk_typeids($res);
            $ids = implode(",", $themeids);
            $where = " id in($ids) and status=4  order by spc_sort DESC,id DESC  limit $limit," . self::$Num;
            $wherecount = " id in($ids) and status=4  order by spc_sort DESC";
            $filedscount = "count(*)as count";
        } else {
            $where = "status=4 order by spc_sort DESC,id DESC  limit $limit," . self::$Num;
            $wherecount = "status=4 order by spc_sort DESC";
            $filedscount = "count(*)as count";
        }

        if ($search) {
            $where = "title like '%$search%' order by id DESC";
            $wherecount = "title like '%$search%' ";
            $filedscount = "count(*)as count";
        }
        $themecount = Theme_Service_File::getByWhere($wherecount, $filedscount)[0];
        $this->showPages($themecount['count'], $page, self::$Num, self::$pages);
        $theme = Theme_Service_File::getByWhere($where, "*");
        foreach ($theme as $values) {
            $file_ids[] = $values["id"];
        }
        //查缩略图;
        $file_imgs = Theme_Service_FileImg::getByFileIds($file_ids);

        //缩略图归类
        if (!$file_imgs) {
            $file_imgs_catg = array();
        } else {
            foreach ($file_imgs as $values) {
                $file_imgs_catg[$values['file_id']][] = $values;
            }
        }

        $this->assign("search", $search);
        $this->assign("status", $this->status);
        $this->assign("file_imgs_catg", $file_imgs_catg);
        $this->assign("type", $type);
        $this->assign("targ", $targ[1]);
        $this->assign("theme", $theme);
        $this->assign("meunOn", "zt_spcArrivals");
    }

    /**
     * 热门推荐
     * @return null
     */
    public function hotAction() {
        $page = $this->getInput("page")? : 1;
        $type = $this->getInput('type')? : 0;
        $search = $this->getInput("search")? : false;
        $limit = ($page - 1) * self::$Num;
        $targ = Theme_Service_FileType::getAllFileType();
        $targline = $this->mk_targline($targ[1]);

        if ($type) {
            $res = Theme_Service_IdxFileType::getByTypeId($type);
            $themeids = $this->mk_typeids($res);
            $ids = implode(",", $themeids);
            $where = " id in($ids) and status=4  order by hot_sort DESC,id DESC  limit $limit," . self::$Num;
            $wherecount = " id in($ids) and status=4  order by hot_sort DESC";
            $filedscount = "count(*)as count";
        } else {
            $where = "status=4 order by hot_sort DESC,id DESC  limit $limit," . self::$Num;
            $wherecount = "status=4 order by hot_sort DESC";
            $filedscount = "count(*)as count";
        }

        if ($search) {
            $where = "title like '%$search%' order by id DESC";
            $wherecount = "title like '%$search%' ";
            $filedscount = "count(*)as count";
        }
        $themecount = Theme_Service_File::getByWhere($wherecount, $filedscount)[0];
        $this->showPages($themecount['count'], $page, self::$Num, self::$pages);
        $theme = Theme_Service_File::getByWhere($where, "*");
        foreach ($theme as $values) {
            $file_ids[] = $values["id"];
        }
        //查缩略图;
        $file_imgs = Theme_Service_FileImg::getByFileIds($file_ids);

        //缩略图归类
        if (!$file_imgs) {
            $file_imgs_catg = array();
        } else {
            foreach ($file_imgs as $values) {
                $file_imgs_catg[$values['file_id']][] = $values;
            }
        }

        $this->assign("search", $search);
        $this->assign("status", $this->status);
        $this->assign("file_imgs_catg", $file_imgs_catg);
        $this->assign("type", $type);
        $this->assign("targ", $targ[1]);
        $this->assign("theme", $theme);
        $this->assign("meunOn", "zt_themehot");
    }

    /**
     * 更新热门排序
     * @return null
     */
    public function updateHotAction() {
        $id = $this->getPost("id");

        $sort = $this->getPost("sort");
        $data = array('hot_sort' => $sort);
        $res = Theme_Service_File::updateFile($data, $id);
        echo $res;
        exit;
    }

    public function editersortAction() {
        $page = $this->getInput("page")? : 1;
        $type = $this->getInput('type')? : 0;
        $search = $this->getInput("search")? : false;
        $limit = ($page - 1) * self::$Num;
        $targ = Theme_Service_FileType::getAllFileType();
        $targline = $this->mk_targline($targ[1]);

        $notids_sql = "status=4 order by sort DESC  limit 0, 27";
        $notIds_res = Theme_Service_File::getByWhere($notids_sql, "*");
        foreach ($notIds_res as $v) {
            $tstr .=$v["id"] . ",";
        }

        $notIds = substr($tstr, 0, strlen($tstr) - 1);

        if ($type) {
            $res = Theme_Service_IdxFileType::getByTypeId($type);
            $themeids = $this->mk_typeids($res);
            $ids = implode(",", $themeids);
            $where = " id in($ids) and id not in($notIds)"
                    . "and status=4 and package_type>1  order by editer_sort DESC,id DESC  limit $limit," . self::$Num;
            $wherecount = " id in($ids) and id not in($notIds) and status=4 and package_type>1  order by editer_sort DESC";
            $filedscount = "count(*)as count";
        } else {


            $where = "id not in($notIds)and status=4 and package_type>1  order by editer_sort DESC,id DESC  limit $limit," . self::$Num;
            $wherecount = "id not in($notIds)and status=4 and package_type>1 order by editer_sort DESC";
            $filedscount = "count(*)as count";
        }
        if ($search) {
            $where = "title like '%$search%' order by id DESC";
            $wherecount = "title like '%$search%' ";
            $filedscount = "count(*)as count";
        }


        $themecount = Theme_Service_File::getByWhere($wherecount, $filedscount)[0];
        $this->showPages($themecount['count'], $page, self::$Num, self::$pages);
        $theme = Theme_Service_File::getByWhere($where, "*");

        foreach ($theme as $values) {
            $file_ids[] = $values["id"];
        }
        //查缩略图;
        $file_imgs = Theme_Service_FileImg::getByFileIds($file_ids);

        //缩略图归类
        if (!$file_imgs) {
            $file_imgs_catg = array();
        } else {
            foreach ($file_imgs as $values) {
                $file_imgs_catg[$values['file_id']][] = $values;
            }
        }

        $this->assign("search", $search);
        $this->assign("status", $this->status);
        $this->assign("file_imgs_catg", $file_imgs_catg);
        $this->assign("type", $type);
        $this->assign("targ", $targ[1]);
        $this->assign("theme", $theme);
        $this->assign("meunOn", "zt_editerArrivals");
    }

    private function mk_typeids($res) {
        foreach ($res as $v) {
            $tem[] = $v['file_id'];
        }
        return $tem;
    }

    private function mk_targline($targ) {
        foreach ($targ as $k => $v) {
            $tem[$v['id']] = $v["name"];
        }
        return $tem;
    }

    public function upsortAction() {
        $id = $this->getPost("id");

        $sort = $this->getPost("sort");
        $data = "sort=$sort";
        $where = "id=$id";
        $res = Theme_Service_File::updateWhere($data, $where);
        echo $res;
        exit;
    }

    public function upsortSpcAction() {
        $id = $this->getPost("id");

        $sort = $this->getPost("sort");
        $data = "spc_sort=$sort";
        $where = "id=$id";
        $res = Theme_Service_File::updateWhere($data, $where);
        echo $res;
        exit;
    }

    public function upsortEditerAction() {
        $id = $this->getPost("id");
        $sort = $this->getPost("sort");
        $data = "editer_sort=$sort";
        $where = "id=$id";
        $res = Theme_Service_File::updateWhere($data, $where);
        echo $res;
        exit;
    }

}
