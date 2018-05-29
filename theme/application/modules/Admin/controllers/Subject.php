<?php

if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * Enter description here ...
 * @author rainkid
 *
 */
class SubjectController extends Admin_BaseController {

    public $perpage = 20;
    public $appCacheName = 'APPC_Front_Index_index';
    public $subject_types = array(
        1 => '普通',
        3 => '置顶1',
        4 => '置顶2',
            // 9 => '广告'
    );
    public $status = array(
        "0" => "默认",
        "1" => "未上线",
        "2" => "已上线",
    );
    public $subject_types_v3 = array(
        31 => '屏序1', 32 => '屏序2', 33 => '屏序3', 34 => '屏序4', 35 => '屏序5',
        36 => '屏序6', 37 => '屏序7', 38 => '屏序8', 39 => '屏序9',
    );
    public $Page_subject = array("page_1" => 1, "page_2" => 2, "page_3" => 3,
        "page_4" => 4, "page_5" => 5, "page_6" => 6);
    private static $Num = 12;

    /**
     *
     * Enter description here ...
     */
    public function indexAction() {
        $page = intval($this->getInput('page'));
        $status = intval($this->getInput("status"))? : 0;
        $perpage = $this->perpage;
        $this->assign("meunOn", "zt_special_specialList");
        if ($status) {
            $where = array("status" => $status);
            $res = Theme_Service_Subject::getList($page, self::$Num, $where, array("id" => "DESC"));
        } else {
            $res = Theme_Service_Subject::getList($page, self::$Num, '', array("id" => "DESC"));
        }
        $subject = $this->mk_subjectinfo($res[1]);
        $this->assign("status", $this->status);
        $this->showPages($res[0], $page, self::$Num, 10, 0, $where);
        $this->assign("subjectinfo", $subject);

        $this->assign("statused", $status);
    }

    /* type_id  : 1--10 v1主题;
     * 11-20 v2历史;
     * 30-40 v2主题 31为屏序1；
     */

//6.0.3
    private function mk_subjectinfo($subject) {
        if (!$subject) return null;

        foreach ($subject as &$v) {
            if ($v['type_id'] < 10) {
                $v["type_name"] = "v1专题";
                if ($v['type_id'] == 3) {
                    $v['screen'] = "置顶1";
                    $v["stype"] = 3;
                } else if ($v['type_id'] == 4) {
                    $v['screen'] = "置顶2";
                    $v["stype"] = 4;
                } else {
                    $v['screen'] = "普通专题";
                    $v["stype"] = 0;
                }
            } else if ($v['type_id'] < 30 && $v['type_id'] > 20) {
                $v["type_name"] = "v2历史专题";
                $v["screen"] = ($v['type_id'] % 20);
                $v["stype"] = $v['type_id'];
            } else {
                $v["type_name"] = "v2专题";
                $v["screen"] = $v['type_id'] % 30;
                $v["stype"] = $v['type_id'];
            }

            if ($v["catagory_id"] == 1) {
                $v["type_catagory"] = "主题专题";
            }
            if ($v["catagory_id"] == 9) {
                $v["type_catagory"] = "广告专题";
                $v['img'] = explode(",", $v['img'])[1];
            }
        }
        return $subject;
    }

//6.0.3
    public function uploadimgthemeAction() {
        $ret = Common::upload('files', 'subjectImage');
        if ($ret['code'] != 0) die(json_encode(array('error' => 1, 'message' => '上传失败！')));
        exit(json_encode(array('error' => 0, 'url' => $ret['data'])));
    }

//6.0.3
    public function updatestatusAction() {
        $sid = $this->getPost("sid");
        $statusid = $this->getPost("status");
        $screemid = $this->getPost("screemid");
        if (!$screemid) $types = 0;
        if ($screemid > 20 && $screemid < 30) {
            $types = ($screemid % 20) + 30;
        }

        if ($screemid > 30) {
            $types = $screemid % 30;
        }
        if ($screemid == 1) {
            $types = 3;
        }
        if ($screemid == 2) {
            $types = 4;
        }
        if ($screemid == 4) {
            $types = 34;
        }
        if ($screemid == 5) {
            $types = 35;
        }
        if ($screemid == 8) {
            $types = 38;
        }
        $res = Theme_Service_Subject::update_status($sid, $statusid, $types);
        echo $res;
        exit;
    }

    public function updatestatusPageListAction() {
        $sid = $this->getPost("sid");
        $statusid = $this->getPost("status");
        $screemid = $this->getPost("screemid");
        $res = Theme_Service_Subject::update_statusPage($sid, $statusid, $screemid);
        echo $res;
        exit;
    }

//页面专题列表;
    public function pagelistAction() {
        $page = intval($this->getInput('page'));
        $status = intval($this->getInput("status"))? : 0;

        $search = $this->getInput("search")? : 0;

        if ($status) {
            $where = array("status" => $status);
        } else {
            $where = "";
        }
        if ($search) {
            $where = array("title" => array("LIKE", $search));
            $this->assign("search", $search);
        }

        $res = Theme_Service_Subject::getListPage($page, self::$Num, $where, array("id" => "DESC"));
        //$subject = $this->mk_subjectinfo($res[1]);
        $subject = $this->mk_pageSubject($res[1]);
        $this->assign("page_subject", $this->Page_subject);
        $this->assign("status", $this->status);
        $this->showPages($res[0], $page, self::$Num, 10, 0, $where);
        $this->assign("subjectinfo", $subject);
        $this->assign("statused", $status);
        $this->assign("meunOn", "zt_special_pagespecialList");
    }

    private function mk_pageSubject($arr) {
        if (!$arr) return 0;
        foreach ($arr as &$v) {
            $v['screen'] = '页面专题' . $v['type_id'];
        }

        return $arr;
    }

//6.0.3
    /* public function updatestatusAction() {
      $sid = $this->getPost("sid");
      $status = $this->getPost("status");
      $screemid = $this->getPost("screemid");

      if ($screemid > 20 && $screemid < 30) {
      $screemid+=10;
      }
      $res = Theme_Service_Subject::update_status($sid, $status, $screemid);
      echo $res;
      exit;
      } */

    /**
     *
     * Enter description here ...
     */
    public function editAction() {

        $id = $this->getInput('id');
        $version = $this->getInput("vl");
        $info = Theme_Service_Subject::getSubject(intval($id));

        if ($info['catagory_id'] == 9) {
            $tem = substr($info['img'], 0, strpos($info['img'], ","));
            $adv_images = explode(",", $info['img']);
            $info['img'] = $adv_images[0];
            unset($adv_images[0]);
            $info['adv_img'] = $adv_images;
        }


//已选中的
        $subject_files = Theme_Service_SubjectFile ::getBySubjectId($id, array('id' => 'ASC'));
        $subject_files = Common ::resetKey($subject_files, 'id');

//$subject_files = array_reverse($subject_files , true);

        $ids = array();
        foreach ($subject_files as $key => $value) {
            $ids[] = $value['file_id'];
        }
        if ($ids) $exsit_files = Theme_Service_File::getByIds($ids);
        $exsit_files = Common::resetKey($exsit_files, 'id');
        $exsit = array();
        foreach ($ids as $key => $value) {
            $exsit[$key]['id'] = $exsit_files[$value]['id'];
            $exsit[$key]['title'] = $exsit_files [$value]['id'] . '.' . $exsit_files[$value]['title'];
        }

//未选中的
        $files = Theme_Service_File::getAllFile($ids);
        $arr_files = array();
        foreach ($files as $key => $value) {
            $arr_files[$key]['id'] = $value['id'];
            $arr_files[$key] ['title'] = $value['id'] . '.' . $value['title'];
        } $this->assign('sid', $id);
        $this->assign('file_ids', implode(',', $ids));
        if ($version == 3) {
            $this->assign('types', $this->subject_types_v3);
        } else {
            $this->assign('types', $this->subject_types);
        } $this->assign('info', $info);
        $this->assign('files', json_encode($arr_files));
        $this->assign('exsit', json_encode($exsit));
    }

    public function listAllAction() {

        $page = intval($this->getInput('page'));
        $perpage = $this->perpage;
        $total = 0;
        $subjects = array();
//先显示置顶专题
        $where = array("type_id" => array(">", 10));
        $res = Theme_Service_Subject ::getList(1, 10, $where);

// $subjects = $this->arrays_sort($subjects, 'sort', 'desc');
        $subjects = $res[1];
        foreach ($subjects as $keys => &$values) {
            if ($values['catagory_id'] == 9) {
                $tem = substr($values['img'], 0, strpos($values['img'], ","));
                $subjects[$keys]['img'] = $tem;
            }
        }


        $this->assign('subjects', $subjects);
        $this->assign('types', $this->subject_types_v3);
        $this->assign('pager', Common ::getPages($total, $page, $perpage, $this->actions['listUrl'] . '/?'));
        $this->assign('menu', $this->menu
        );
    }

//v.6.0.3
    public function addtwoAction() {
        $this->assign("meunOn", "zt_special_specialAdd");
    }

//v6.0.3
    private function mk_typeids($types) {
        foreach ($types as $v) {
            $tem[$v["id"]] = $v["name"];
        }

        return $tem;
    }

// 6.0.3
    public function addSubjectAction() {
        $info = $this->getPost(
                array('sname', 'subject_img', 'screeid', 'ctime',
                    'type', 'descrip', "adv_img", 'ids')
        );
        $keys = $info['screeid'];
        if ($this->Page_subject[$keys]) {

            $info['screeid'] = $this->Page_subject[$keys];
            $res = Theme_Service_Subject::addSbujectPage($info);
        } else {
            $res = Theme_Service_Subject::addSubjectTop($info);
        }
        echo $res;
        exit;

        /* if ($info['type'] == 1) {
          $url = $info["subject_img"];
          }
          if ($info['type'] == 9) {
          $url = $info['subject_img'] . "," . $info['adv_img'];
          }
          $subjectinfo = array(
          "title" => $info['sname'],
          "type_id" => $info["screeid"] + 20,
          "descrip" => $info['descrip'],
          "img" => $url,
          "catagory_id" => $info["type"],
          "last_update_time" => strtotime($info['ctime']),
          "create_time" => time(),
          );
          $subject_id = Theme_Service_Subject::addSubject($subjectinfo);
          if (!$subject_id) $this->output(-1, '操作失败');

          if ($info['type'] == 1) {
          $themeids = explode("_", $info["ids"]);
          foreach ($themeids as $key => $value) {
          $file_subject[$key]['id'] = '';
          $file_subject[$key]['file_id'] = $value;
          $file_subject[$key]['subject_id'] = $subject_id;
          }
          $result = Theme_Service_SubjectFile::batchAdd($file_subject);
          }

          if (!$result) $this->output(-1, '操作失败');
         * */
    }

//6.0.3 专题编辑;
    public function editSubjectAction() {

        $sid = $this->getInput("sid");
        $pagelist = $this->getInput("type")? : 0;
        if ($pagelist === "pagelist") {
            $res = Theme_Service_Subject::getSubjectTop($sid);
            $this->assign("meunOn", "zt_special_pagespecialList");
        } else {
            $res = Theme_Service_Subject::getSubject($sid);
            $this->assign("meunOn", "zt_special_specialList");
        }
        $this->assign("pagelist", $pagelist);

        if ($res["catagory_id"] == 1) {
            if ($pagelist === "pagelist") {
                $subject_files = Theme_Service_SubjectFile:: getBySubjectIdPagelist($sid, array("id" => "ASC"));
            } else {
                $subject_files = Theme_Service_SubjectFile ::getBySubjectId($sid, array("id" => "ASC"));
            }
            foreach ($subject_files as $key => $value) {
                $ids[] = $value['file_id'];
            }

            $ids_theme = implode(",", $ids);
            if ($ids) $themefiles = Theme_Service_File::getByIds_order($ids_theme);
            $imgs = Theme_Service_FileImg::getByFileIds($ids);
// if (!$imgs) return "imgs is null";
            foreach ($imgs as $v) {
                $arr[$v['file_id']][] = $v["img"];
            }
            foreach ($arr as $k => $i) {
                $imgurl[$k] = $i[0];
            } foreach ($themefiles as &$val) {
                $val["url"] = $imgurl[$val["id"]];
            }


            $this->assign("themeinfo", $themefiles);
        }

        if ($res["catagory_id"] == 9) {
            $res["adv_img"] = explode(",", $res['img'])[1];
            $res["img"] = explode(",", $res['img'])[0];
        }



        $this->assign("catagoryid", $res["catagory_id"]);
        $this->assign("subjectinfo", $res);
    }

    public function editPostSubjectAction() {
        $info = $this->getPost(
                array('sid', 'sname', 'subject_img', 'screeid', 'ctime',
                    'type', 'descrip', "adv_img", 'ids', "subjectCatagory")
        );
        $res = $this->editPostSubjectcatagory($info);

        echo $res;

        exit;
    }

    private function editPostSubjectcatagory($info) {

        if ($info['type'] == 1) {
            $where = array("id" => $info["sid"]);
            $data = array(
                "img" => $info['subject_img'],
                "title" => $info['sname'],
                "last_update_time" => strtotime($info['ctime']),
                "descrip" => $info['descrip'],
                "catagory_id" => $info['type'],
            );
            if ($info ['subjectCatagory'] === "pagelist") {
                $res = Theme_Service_Subject :: updateBySubjectPage($data, $where);
            } else {

                $res = Theme_Service_Subject ::updateBySubject($data, $where);
            }
            if ($info["ids"]) {
                $arr = explode("_", $info['ids']);

                if ($info ['subjectCatagory'] === "pagelist") {
                    Theme_Service_SubjectFile::deleteBySubjectIdPage($info["sid"]);
                } else {
                    Theme_Service_SubjectFile::deleteBySubjectId($info["sid"]);
                }

                foreach ($arr as $v) {
                    $filed = "file_id,subject_id";
                    $val = $v . "," . $info['sid'];
                    if ($info ['subjectCatagory'] === "pagelist") {
                        Theme_Service_SubjectFile:: insertintobysqlPage($filed, $val);
                    } else {
                        Theme_Service_SubjectFile ::insertintobysql($filed, $val);
                    }
                }
            }
        }

        if ($info['type'] == 9) {
            $where = array("id" => $info["sid"]);
            $data = array(
                "img" => $info ['subject_img'] . ',' . $info['adv_img'],
                "title" => $info['sname'],
                "last_update_time" => strtotime($info['ctime']),
                "descrip" => $info['descrip'],
                "catagory_id" => $info['type'],
            );
            if ($info ['subjectCatagory'] === "pagelist") {
                $res = Theme_Service_Subject :: updateBySubjectPage($data, $where);
            } else {
                $res = Theme_Service_Subject ::updateBySubject($data, $where);
            }
        }

        return $res;
        exit;
    }

    /**
     *
     * Enter description here ...
     */
    public function addAction() {
        $filetype = Theme_Service_FileType::getAllFileType();

        $tid = $this->getInput("tid")? : 0;
        $lineType = $this->mk_typeids($filetype[1]);

        $next = $this->getInput("next")? : 0;
        $this->assign("next", $next);
        $this->assign("tid", $tid);
        $this->assign("maintype", $lineType);
        $this->assign("meunOn", "zt_special_specialAdd");
    }

    /**
     * 广告专题
     * Enter description here ...
     */
    public function addAdvaction() {
        $files = Theme_Service_File::getAllFile();
        $arr_files = array();
        foreach ($files as $key => $value) {
            $arr_files[$key]['id'] = $value['id'];
            $arr_files [$key] ['title'] = $value['id'] . '.' . $value['title'];
        }


        $this->assign('files', json_encode($arr_files));
        $this->assign('types', $this->subject_types);
        $this->assign('exsit', '');
        $this->assign('menu', $this->menu);
    }

    /**
     *
     * Enter description here ...
     */
    public function add_postAction() {


        $info = $this->getPost(
                array('title', 'img', 'img_adv01', 'img_adv02', 'img_adv03', 'value',
                    'type_id', 'descrip', 'sort', 'is_publish', 'publish_conn',
                    'pre_publish', 'sub_type_id')
        );
        $info = $this->_cookData($info);


// print_r($info);
//  exit;
        if ($info['pre_publish']) {
            $pre_time = strtotime($info['pre_publish']);
            if ($pre_time < time()) {

                $info['publish_conn'] = '';
                $info['pre_publish'] = $pre_time;
            } else {
                $info['pre_publish'] = $pre_time;
            }
        } else {
            $info['pre_publish'] = time();
        }

        if ($info['is_publish']) {
            $info['is_publish'] = 1;
        } else {
            $info['is_publish'] = 0;
        }

//专题是广告专题时;
        if ($info['sub_type_id'] == 9) {
            $info["catagory_id"] = $info['sub_type_id'];
            $img = $info['img'];
            if ($info['img_adv01']) $img .= "," . $info['img_adv01'];
            if ($info['img_adv02']) $img .= "," . $info['img_adv02'];
            if ($info['img_adv03']) $img .= "," . $info['img_adv03'];
            $info['img'] = $img;
        }



//添加专题
        if ($info["type_id"] < 30) {
            $subject_id = Theme_Service_Subject::addSubject($info);
            if (!$subject_id) $this->output(-1, '操作失败');
            $file_subject = array();
            $info ['value'] = explode(',', html_entity_decode($info['value']));
            foreach ($info['value'] as $key => $value) {
                $file_subject[$key]['id'] = '';
                $file_subject[$key]['file_id'] = $value;
                $file_subject[$key]['subject_id'] = $subject_id;
            }


            $result = Theme_Service_SubjectFile::batchAdd($file_subject);
            if (!$result) $this->output(-1, '操作失败');
            echo $result;
        } else {
            $type = $info ['type_id'];
            $vals = $type - 10;
            $data = array("type_id" => $vals);
            $where = array("type_id" => $type);
            Theme_Service_Subject ::updateBySubject($data, $where);

            $subject_id = Theme_Service_Subject::addSubject($info);

            if (!$subject_id) $this->output(-1, '操作失败');

            $file_subject = array();
            $info ['value'] = explode(',', html_entity_decode($info['value']));
            foreach ($info['value'] as $key => $value) {
                $file_subject[$key]['id'] = '';
                $file_subject[$key]['file_id'] = $value;
                $file_subject[$key]['subject_id'] = $subject_id;
            }


            $result = Theme_Service_SubjectFile::batchAdd($file_subject);
            echo $result;
        }

        exit;
    }

    /**
     *
     * Enter description here ...
     */
    public function edit_postAction() {
        $info = $this->getPost(
                array('id', 'title', 'img', 'img_adv01', 'img_adv02', 'img_adv03', 'value',
                    'type_id', 'descrip', 'sort', 'is_publish', 'publish_conn',
                    'pre_publish', 'sub_type_id')
        );
        if (strtotime($info['pre_publish']) > time()) {
            $info['pre_publish'] = strtotime($info['pre_publish']);
            if ($info['is_publish']) {
//预发布发专题，push消息;
                $info['is_publish'] = 1;
            } else {
//预发布发专题，不push消息;
                $info['is_publish'] = 0;
            }
        } else {
//不预发布；
            $info['pre_publish'] = strtotime($info['pre_publish']);
            if ($info["is_publish"]) {
                $info['pre_publish'] = strtotime($info['pre_publish']);
                $this->addPush($info['id']);
            }
        }

//专题是广告专题;
        if ($info['sub_type_id'] == 9) {
            $info["catagory_id"] = $info['sub_type_id'];
            $img = $info['img'];
            if ($info['img_adv01']) $img .= "," . $info['img_adv01'];
            if ($info['img_adv02']) $img .= "," . $info['img_adv02'];
            if ($info['img_adv03']) $img .= "," . $info['img_adv03'];
            $info['img'] = $img;
        }
        $info = $this->_cookData($info);



//更新专题
        if ($info["type_id"] < 30) {
            $ret = Theme_Service_Subject ::updateSubject($info, intval($info['id']));
            if (!$ret) $this->output(-1, '操作失败');
//文件
            $file_subject = array();
            $info ['value'] = explode(',', html_entity_decode($info['value']));
            foreach ($info['value'] as $key => $value) {
                $file_subject[$key]['id'] = '';
                $file_subject[$key]['file_id'] = $value;
                $file_subject[$key]['subject_id'] = $info['id'];
            }
//$file_subject = array_reverse($file_subject , true);
            $ret = Theme_Service_SubjectFile::deleteBySubjectId($info['id']);
            $result = Theme_Service_SubjectFile::batchAdd($file_subject);
            if (!$result) $this->output(-1, '操作失败');
            $this->output(0, '操作成功.');
        } else {
            $ret = Theme_Service_Subject ::updateSubject($info, intval($info['id']));
            if (!$ret) $this->output(-1, '操作失败');
            $type = $info ['type_id'];
            $vals = $type - 10;
            $data = array("type_id" => $vals);
            $where = array("type_id" => $type);
            Theme_Service_Subject ::updateBySubject($data, $where);

            Theme_Service_Subject ::updateBySubject($where, array("id" => $info['id']));

            $file_subject = array();
            $info ['value'] = explode(',', html_entity_decode($info['value']));
            foreach ($info['value'] as $key => $value) {
                $file_subject[$key]['id'] = '';
                $file_subject[$key]['file_id'] = $value;
                $file_subject[$key]['subject_id'] = $info['id'];
            }
//$file_subject = array_reverse($file_subject , true);
            $ret = Theme_Service_SubjectFile::deleteBySubjectId($info['id']);
            $result = Theme_Service_SubjectFile::batchAdd($file_subject);
            $this->output(0, '操作成功.');
        }

//如果当前专题被置顶，需要清除以前专题的置顶标志
        /* $type = $info['type_id'];
          if (($type == Theme_Service_Subject::$subject_type_ids['top1']) || ($type == Theme_Service_Subject::$subject_type_ids['top2'])) {
          $oldTopSubject = Theme_Service_Subject::getBy(array('type_id' => array('=' , $type) , 'id' => array('!=' , intval($info['id']))));
          if ($oldTopSubject) {
          $oldTopSubject['type_id'] = Theme_Service_Subject::$subject_type_ids['normal'];
          Theme_Service_Subject::updateSubject($oldTopSubject , $oldTopSubject['id']);
          }
          } */
    }

    /**
     *
     * Enter description here ...
     */
    private function _cookData($info) {

        if (!$info['title']) $this->output(-1, '标题不能为空.');
        if (!$info['img']) $this->output(-1, 'Banner图片不能为空.');
        $info["last_update_time"] = time();
// if (!$info['value']) $this->output(-1, '请选择锁屏文件.');
        return $info;
    }

    /**
     *
     * Enter description here ...
     */
    public function deleteAction() {

        $id = $this->getInput('id');
        $catagory = $this->getInput("subjectCatatory");
        if ($catagory === "pagelist") {
            $info = Theme_Service_Subject::getSubjectPage($id);
        } else {
            $info = Theme_Service_Subject::getSubject($id);
        }


//if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
        Util_File::del(Common ::getConfig('siteConfig', 'attachPath') . $info['img']);

        if ($catagory === "pagelist") {

            Theme_Service_SubjectFile::deleteBySubjectIdpage($id);
            $result = Theme_Service_Subject::deleteSubjectpage($id);
        } else {

            Theme_Service_SubjectFile::deleteBySubjectId($id);
            $result = Theme_Service_Subject::deleteSubject($id);
        }

        if (!$result) $this->output(-1, '操作失败');
        $this->output(0, '操作成功');
    }

    /**
     *
     * Enter description here ...
     */
    public function uploadAction() {
        $imgId = $this->getInput('imgId');
        $this->assign('imgId', $imgId);
        $this->getView()->display('common/upload.phtml');
        exit;
    }

    /**
     *
     * Enter description here ...
     */
    public function uploadAdvAction() {
        $imgId = $this->getInput('imgId');
        $this->assign('imgId', $imgId);
        $this->getView()->display('common/uploadAdv.phtml');

        exit;
    }

    /**
     *
     */
    public function uploadImgAction() {
        $ret = Common::upload('imgFile', 'subject');
        if ($ret['code'] != 0) die(json_encode(array('error' => 1, 'message' => '上传失败！')));
        exit(json_encode(array('error' => 0, 'url' => '/attachs/' . $ret['data'])));
    }

    /**
     *
     * Enter description here ...
     */
    public function upload_postAction() {
        $ret = Common::upload('img', 'subject');
        $imgId = $this->getPost('imgId');
        $this->assign('code', $ret ['data']);
        $this->assign('msg', $ret ['msg']);
        $this->assign('data', $ret['data']);
        $this->assign('imgId', $imgId);
        $this->getView()->display('common/upload.phtml');
        exit;
    }

    /*
     * 编辑新品推荐专题或精品推荐专题，代码来自editAction
     */

    public function editNewSpecialAction() {

//专题的ID
        $type = $this->getInput('type');
        $subject = Theme_Service_Subject::getBy(array('type_id' => array('=', Theme_Service_Subject::$subject_type_ids[$type])));
        $subject_id = $subject['id'];
//$ids：专题中包含的主题ID数组
        $subject_files = Theme_Service_SubjectFile :: getBySubjectId($subject_id, array('id' => 'ASC'));


        $subject_files = Common ::resetKey($subject_files, 'id');
        $ids = array();
        foreach ($subject_files as $key => $value) {
            $ids[] = $value['file_id'];
        }

//$exist_files：专题中的主题文件数组（从数据库中查出，包含完整的主题信息）
//$exist：专题中的主题数组，包含主题ID及显示名称（id.title）
        if ($ids) $exist_files = Theme_Service_File::getByIds($ids);
        $exist_files = Common::resetKey($exist_files, 'id');

        $exist = array();
        foreach ($ids as $key => $value) {
            $exist[$key]['id'] = $exist_files[$value]['id'];
            $exist[$key] ['title'] = $exist_files [$value]['id'] . '.' . $exist_files[$value]['title'];
        }
//$files：未包含在专题中的主题文件数组
//$arr_files：未包含在专题中的主题数组

        $files = Theme_Service_File::getAllFile($ids);

        $arr_files = array();
        foreach ($files as $key => $value) {
            $arr_files[$key]['id'] = $value['id'];
            $arr_files [$key] ['title'] = $value['id'] . '.' . $value['title'];
        } $this->assign('subject_id', $subject_id); //专题ID
        $this->assign('last_update_time', $subject['create_time']); //上次更新时间
        $this->assign('file_ids', implode(',', $ids)); //已选择的主题ID
        $this->assign('files', json_encode($arr_files)); //未选择的主题
        $this->assign('exsit', json_encode($exist)); //已选择的主题
        $this->assign('menu', $this->menu); //页面菜单
        $this->assign('subject_name', $this->subject_type_names[$type]);
    }

    /*
     * 提交对新品推荐专题的修改，代码来自edit_postAction
     */

    public function new_postAction() {
//专题
        $info = $this->getPost(array('id', 'value'));
        $info['create_time'] = time(); //将create_time字段用于保存更新时间


        $ret = Theme_Service_Subject ::updateSubject($info, intval($info['id']));
        if (!$ret) $this->output(-1, '操作失败');
//文件
        $file_subject = array();
        $info ['value'] = explode(',', html_entity_decode($info['value']));
        foreach ($info['value'] as $key => $value) {
            $file_subject[$key]['id'] = '';
            $file_subject[$key]['file_id'] = $value;
            $file_subject[$key]['subject_id'] = $info['id'];
        }


        Theme_Service_SubjectFile::deleteBySubjectId($info['id']);
//$file_subject = array_reverse($file_subject , true);
        $result = Theme_Service_SubjectFile::batchAdd($file_subject);
        if (!$result) $this->output(-1, '操作失败');
        $this->output(
                0, '操作成功.');
    }

    public function specialAction() {
        $this->assign('menu', $this->menu);
    }

    /*
     * 发送专题PUSH消息
     */

    public function addPushAction() {
        $sid = $this->getInput('sid');
        $subject = Theme_Service_Subject::getSubject($sid);
        if ($subject) {
            $queue = Common::getQueue();
            $queue->noRepeatPush('push_msg', $sid);
            $subject['create_time'] = time();
            Theme_Service_Subject ::updateSubject($subject, $sid); //将当前时间存入数据库
            $this->output(0, "操作成功！", '消息已加入发送队列，系统将自动发送。');
        } else $this->
                    output(-1, '无效的专题ID：' . $sid);
    }

    public function addPush($sid) {

        $subject = Theme_Service_Subject::getSubject($sid);
        if ($subject) {
            $queue = Common::getQueue();
            $queue->noRepeatPush('push_msg', $sid);
            $queue->noRepeatPush('send_num', 0);
            $subject['create_time'] = time();

            Theme_Service_Subject ::updateSubject($subject, $sid); //将当前时间存入数据库
            $this->output(0, "操作成功！", '消息已加入发送队列，系统将自动发送。');
        } else $this->output(-1, '无效的专题ID：' . $sid);
    }

    /*
     * 二维数组排序;
     */

    private function arrays_sort($arr, $keys, $type = 'asc') {
        if (!( $type == 'asc' || $type == 'desc' )) die('type error');
        $keysvalue = $new_array = array();

        foreach ($arr as $k => $v) {
            $keysvalue[$k] = $v [$keys];
        }
        if ($type == 'asc') {
            asort($keysvalue);
        } else {
            arsort($keysvalue);
        }
        reset($keysvalue);
        foreach ($keysvalue as $k => $v) {
            $new_array[$k] = $arr[$k];
        }
        return $new_array;
    }

}
