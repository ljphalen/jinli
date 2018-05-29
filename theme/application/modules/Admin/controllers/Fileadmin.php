<?php

if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * Enter description here ...
 * @author rainkid
 *
 */
class FileadminController extends Admin_BaseController {

    private static $Num = 12;
    private static $pages = 10;
    private static $groupid = array(
        "sheji" => 1,
        "ceshi" => 2,
        "yunyun" => 3
    );
    public $status = array(
        0 => '全部',
        1 => '已提交',
        2 => '不通过',
        3 => '已通过',
        4 => '上架',
        5 => '下架'
    );

    public function indexAction() {
        $page = intval($this->getInput('page'))? : 1;
        $type = $this->getInput("tid")? : 0;
        $search = $this->getInput("search")? : false;
        $status_sel = intval($this->getInput("status"))? : 0;
        $targsed = $this->getInput("ids");
        $sedType = (explode("_", $targsed))? : 0;
        $limit = ($page - 1) * self::$Num;

        if ($this->userInfo["groupid"] == 2) {
            $status = 1;
        }
        if ($this->userInfo["groupid"] == 3) {
            $status = 3;
        }
        if ($status_sel) {
            $pare = array('status' => $status_sel);
            $status = $status_sel;
            $status_opt = '=';
        } else {
            $status_opt = ">=";
        }

        $sedTypeStr = implode(",", $sedType);
        if ($sedTypeStr) {
            if ($pare) $pare = $this->arrayKey($pare, "ids", $targsed);
            else $pare = array("ids" => $targsed);
        }
        if ($search) {
            if ($pare) $pare = $this->arrayKey($pare, "search", $search);
            else $pare = array("search" => $search);
        }
        list($count, $res) = Theme_Service_FileAdmin::themeFileShows($type, $search, $sedTypeStr, $status, $status_opt, $limit);
//分页处理
        $this->showPages($count['count'], $page, self::$Num, self::$pages, $type, $pare);
//取id
        foreach ($res as $values) {
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
//取类别
// $file_ids = array(28, 38);
        $alltype = $this->mk_typeids(Theme_Service_FileType::getAllFileType()[1]);
        $idx = Theme_Service_IdxFileType::getByFileIds($file_ids);

//取二级分类
        $sedtype = Theme_Service_FilesedType::getAll();
        $line_sedtype = $this->mk_typeids($sedtype);
        $fidStr = implode(",", $file_ids);
        $idxsed = Theme_Service_IdxFilesedType::getsedTypebyFileid_ToArray($fidStr);


        $this->assign("status_sel", $status_sel);
        $this->assign("search", $search);
        $this->assign("linesed", $line_sedtype);
        $this->assign("sedidx", $idxsed);
        $this->assign("idx", $idx);
        $this->assign("alltype", $alltype);


        $this->assign("selectedsed", $sedType);
        $this->assign("sedtype", $sedtype);
        $this->assign("type", $type);
        $this->assign("themeimage", $file_imgs_catg);
        $this->assign("theme", $res);
        $this->assign("status", $this->status);
        $this->assign("userinfo", $this->userInfo);
        $this->assign("meunOn", "zt_resourceAdmin");
    }

    public function fileeditAction() {
        $themeId = $this->getInput("themeid");


        $where = "id=$themeId";
        $flied = "*";
        $themeinfo = Theme_Service_File::getbyWhere($where, $flied)[0];
        $imgs = Theme_Service_FileImg::getByFileId($themeinfo["id"]);
        $themetype = Theme_Service_IdxFileType::getByFileId($themeinfo["id"]);
        $filetype = Theme_Service_FileType::getAllFileType();

        $lineType = $this->mk_typelist($filetype[1]);

        $themetypeId = $themetype[0]['type_id'];
        $selecttype = array($themetypeId, $lineType[$themetypeId]["name"]);

//二级标签;
        $subtype = Theme_Service_FilesedType::getAll();
        $subtypeline = $this->mk_typeids($subtype);
        $themeSub_type = Theme_Service_IdxFilesedType::getsedTypebyFileid($themeId);


        if ($this->userInfo["groupid"] == 3) {
            $status = array(4 => "上架", 5 => "下架");
        } elseif ($this->userInfo["groupid"] == 2) {
            $status = array(3 => "通过", 2 => "不通过");
        } elseif($this->userInfo["username"] == 'admin'){
            $status = array(3 => "通过", 2 => "不通过", 4 => "上架", 5 => "下架");
        }


        $this->assign("datatimes", date("Y-m-d H:i:s", $themeinfo['create_time']));
        $this->assign("themesub_type", $themeSub_type);
        $this->assign("subtypeline", $subtypeline);
        $this->assign("subtype", $subtype);

        $this->assign("selecttype", $selecttype);
        $this->assign("themetype", $themetype);
        $this->assign("filetype", $filetype[1]);
        $this->assign("themeinfo", $themeinfo);
        $this->assign("imgs", $imgs);
        $this->assign("status", $this->status);

        $this->assign("selstatus", $status);
        $this->assign("meunOn", "zt_resourceAdmin");
    }

    public function editPostAction() {
        $tid = $this->getPost("tid");
        $subids = $this->getPost("subids");
        $status = $this->getPost("check");
        $themeDesc = $this->getPost("themeDesc"); //主题描述;
        $resData = $this->getPost("conns"); //不通过的理由;

        $pubtime = strtotime($this->getPost("pubtime"))? : time();
        $subres = array_unique(explode("_", $subids));

        foreach ($subres as $k => &$v) {
            if (!$v) {
                unset($subres[$k]);
            }
        }

        $name = $this->getPost("wName");
        $ename = $this->getPost("ename");
        $mainTarg = $this->getPost("wtarg");


        $data = array(
            "title" => $name,
            "descript" => $themeDesc,
            "reason" => $resData,
            "Ename" => $ename,
            "status" => $status,
            'create_time' => $pubtime,
            'userName' => $this->userInfo["nick_name"]? : $this->userInfo["username"],
            "userGroupId" => $this->userInfo["groupid"],
            "userId" => $this->userInfo["uid"],
        );


        Theme_Service_LogsMsg::updateData($data, $tid);
        $res = Theme_Service_File::updateFile($data, $tid);
        $targ = Theme_Service_IdxFileType::addTargs($mainTarg, $tid);
        $subtarg = Theme_Service_IdxFilesedType::setsubTarg($subres, $tid);
        echo $res;
        exit;
    }

    public function getThemeAction() {
        $page = intval($this->getInput('pageNum'))? : 1;
        $type = $this->getInput("tid")? : 0;
        $search = $this->getPost("search")? : FALSE;
        $limit = ($page - 1) * self::$Num;

        $status = 4;
        if ($type) {
            $res = Theme_Service_IdxFileType::getByTypeId($type);
            $themeids = $this->mk_catagoryids($res);
            $ids = implode(",", $themeids);
            $where = " id in($ids) and status=$status  order by id DESC  limit $limit," . self::$Num;
            $wherecount = " id in($ids) and status=$status  ";
            $filedscount = "count(*)as count";
        } else {
            $where = "status=$status order by id DESC  limit $limit," . self::$Num;
            $wherecount = "status=$status ";
            $filedscount = "count(*)as count";
        }
        if ($search) {
            $where = " status=$status and title like '%$search%'  order by id DESC  limit $limit," . self::$Num;
            $wherecount = " title like '%$search%'  and status=$status  ";
            $filedscount = "count(*)as count";
        }
        $res = Theme_Service_File::getByWhere($where);
        $count = Theme_Service_File::getByWhere($wherecount, $filedscount)[0];

        foreach ($res as $values) {
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
        //取类别
        // $file_ids = array(28, 38);
        $alltype = $this->mk_typeids(Theme_Service_FileType::getAllFileType()[1]);
        $idx = Theme_Service_IdxFileType::getByFileIds($file_ids);

        $themeids = $this->mk_line_themecatagory($idx);
        $result = $this->mk_theme_ajax($res, $file_imgs_catg, $alltype, $themeids);
        $ajax = array("datas" => $result, "pageCount" => ceil($count['count'] / self::$Num), "recordCount" => $count['count']);
        echo json_encode($ajax);
        exit;
    }

    private function mk_theme_ajax($theme, $imgs, $type = '', $fid = '') {

        if (!is_array($theme)) return null;
        foreach ($theme as $k => $v) {
            $tem[$k]['tagOne'] = $type[$fid[$v['id']]];
            $tem[$k]["id"] = $v["id"];
            $tem[$k]["picName"] = $v["title"];
            $tem[$k]["resolution"] = "V" . $v['package_type'];
            $tem[$k]["status"] = $this->status[$v['status']];

            $tem[$k]["TagTwo"] = "codeing...";
            $tem[$k]["uploadDate"] = date("Y-m-d H:i:s", $v['create_time']);
            $tem[$k]["url"] = $this->imageurl . $imgs[$v['id']][0]['img'];
        }
        return $tem;
    }

    private function mk_typelist($type) {
        foreach ($type as $v) {
            $tem[$v['id']] = $v;
        }

        return $tem;
    }

    private function mk_typeids($types) {
        foreach ($types as $v) {
            $tem[$v["id"]] = $v["name"];
        }
        return $tem;
    }

    private function mk_catagoryids($res) {
        foreach ($res as $v) {
            $tem[] = $v['file_id'];
        }
        return $tem;
    }

    private function mk_line_themecatagory($res) {
        foreach ($res as $v) {
            $tem[$v['file_id']] = $v['type_id'];
        }
        return $tem;
    }

    /**
     *
     * Enter description here ...
     */
    public function deleteAction() {
        $id = $this->getInput('id');
        $info = Theme_Service_File::getFile($id);
        if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
        if ($info['status'] == 4) $this->output(-1, '该文件是已上架状态，无法删除');
        if (!in_array($this->userInfo['groupid'], array(1, 3))) $this->output(-1, '没有权限删除');
//记录日志
        $log_data = array(
            'uid' => $this->userInfo['uid'],
            'username' => $this->userInfo['username'],
            'message' => $this->userInfo['username'] . '删除了文件：' . $info['title'],
            'file_id' => $info['id']
        );
        $result = Theme_Service_File::delete($id, $log_data);

        if (!$result) $this->output(-1, '操作失败');
        Theme_Service_File::updateVersion();
        echo $result;
        exit;
    }

    private function arrayKey(&$array, $key, $val) {
        $array[$key] = $val;
        return $array;
    }

}
