<?php

if (!defined('BASE_PATH')) exit('Access Denied!');
//error_reporting(E_ALL);

/**
 *
 * Enter description here ...
 * @author rainkid
 *
 */
class FileController extends Admin_BaseController {

    public $actions = array(
        'listUrl' => '/Admin/File/index',
        'addUrl' => '/Admin/File/add',
        'addPostUrl' => '/Admin/File/add_post',
        'editUrl' => '/Admin/File/edit',
        'editPostUrl' => '/Admin/File/edit_post',
        'editStatusUrl' => '/Admin/File/editstatus',
        'editStatusPostUrl' => '/Admin/File/editstatus_post',
        'deleteUrl' => '/Admin/File/delete',
        'uploadUrl' => '/Admin/File/upload',
        'uploadPostUrl' => '/Admin/File/upload_post',
        'detailUrl' => '/Admin/File/detail',
        'updateUrl' => '/Admin/File/update',
        'updatePostUrl' => '/Admin/File/update_post',
    );
    public $perpage = 10;
    private static $Num = 12;
    private static $pages = 10;
    public $appCacheName = 'APPC_Front_Index_index';
    public $status = array(
        1 => '已提交',
        2 => '未通过',
        3 => '已通过',
        4 => '上架',
        5 => '下架'
    );

    /**
     *
     * Enter description here ...
     */
    public function indexAction() {
        $page = intval($this->getInput('page'))? : 1;

        $type = $this->getInput("tid")? : 0;
        $this->assign("meunOn", "zt_myThemes");
        $limit = ($page - 1) * self::$Num;
//如果当前登录的用户是设计师，则只显示本人的文件列表

        if ($type) {
            $where = "status=$type and ";
            $where_count = "status=$type and ";
        } else {
// $where = "";
// $where_count = "";
        }
        if ($this->userInfo['groupid'] == 1) {
            $uid = $this->userInfo['uid'];
            $where_count .= "user_id=$uid";
            $where .= "user_id=$uid order by id DESC limit $limit," . self::$Num;
        } else {
            $where_count .= "1=1";
            $where .= "1=1 order by id DESC limit $limit," . self::$Num;
        }
        $count = Theme_Service_File::getByWhere($where_count, "count(*) as count ")[0];
        $res = Theme_Service_File::getByWhere($where);

        $this->showPages($count['count'], $page, self::$Num, 10, $type);

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

        $this->assign("idx", $idx);
        $this->assign("usergroup", $this->userInfo['groupid']);
        $this->assign("alltype", $alltype);
        $this->assign("type", $type);
        $this->assign("themeimage", $file_imgs_catg);
        $this->assign("theme", $res);
        $this->assign("status", $this->status);
    }

//6.0.3
    public function addtwoAction() {
        $themeid = $this->getInput("themeId");
        $where = "id=$themeid order by create_time DESC limit 1";
        $flied = "*";
        $themeinfo = Theme_Service_File::getbyWhere($where, $flied)[0];
        $imgs = Theme_Service_FileImg::getByFileId($themeinfo["id"]);
        //显示为jpg格式的图片，兼容各浏览器
        foreach ($imgs as $k => $img) {
            $imgs[$k] = str_replace('.webp', '.jpg', $img);
        }
        $themetype = Theme_Service_IdxFileType::getByFileId($themeinfo["id"]);
        $filetype = Theme_Service_FileType::getAllFileType();

        $lineType = $this->mk_typelist($filetype[1]);

        $themetypeId = $themetype[0]['type_id'];
        $selecttype = array($themetypeId, $lineType[$themetypeId]["name"]);

        $this->assign("selecttype", $selecttype);
        $this->assign("themetype", $themetype);
        $this->assign("filetype", $filetype[1]);
        $this->assign("themeinfo", $themeinfo);
        $this->assign("imgs", $imgs);
        $this->assign("meunOn", "zt_myThemes");
    }

//6.0.3
    private function mk_typelist($type) {
        foreach ($type as $v) {
            $tem[$v['id']] = $v;
        }

        return $tem;
    }

//6.0.3
    public function editFileAction() {
        $themeid = $this->getInput("themeid")? : 1;
        $where = "id= $themeid";
        $flied = "*";
        $themeinfo = Theme_Service_File::getbyWhere($where, $flied)[0];
        $imgs = Theme_Service_FileImg::getByFileId($themeinfo["id"]);
        foreach ($imgs as $k => $img) {
            $imgs[$k] = str_replace('.webp', '.jpg', $img);
        }
        $themetype = Theme_Service_IdxFileType::getByFileId($themeinfo["id"]);
        $filetype = Theme_Service_FileType::getAllFileType();
        $lineType = $this->mk_typelist($filetype[1]);

        $themetypeId = $themetype[0]['type_id'];
        $selecttype = array($themetypeId, $lineType[$themetypeId]["name"]);

        $this->assign("selecttype", $selecttype);
        $this->assign("themetype", $themetype);
        $this->assign("filetype", $filetype[1]);
        $this->assign("themeinfo", $themeinfo);
        $this->assign("imgs", $imgs);

        $this->assign("meunOn", "zt_myThemes");
    }

//6.0.3
    public function updatetypeAction() {
        $typeid = $this->getPost("typeid");
        $themeid = $this->getPost("themeid");
        $text = $this->getPost("txt_editor");
        $priceType = $this->getPost("priceType");
        $price = $this->getPost('price');
        $comment = $this->getPost('price_comment');

        if (!empty($priceType) && $priceType == 'free') {
            $price = 0;
        }

        //当前主题
        $theme = Theme_Service_File::get($themeid);
        $ori_price = $theme['price'];

        $file_data = array("descript" => $text);
        if (empty($theme['price_id'])) {
            //添加页面
            $data = array(
                'is_default' => 1,
                'status' => 1,
                'uid' => $this->userInfo['uid'],
                'product_id' => $themeid,
                'current_price' => 0,
                'apply_price' => $price,
                'comment' => $comment,
                'created_time' => time(),
            );
            Theme_Service_PriceChangeLog::insert($data);

            $file_data['price'] = $price;
        } elseif ($ori_price != $price) {
            //是编辑页面, 并且价格有变更, 则提交价格变更申请
            //插入一条价格信息
            $data = array(
                'uid' => $this->userInfo['uid'],
                'product_id' => $themeid,
                'current_price' => $ori_price,
                'apply_price' => $price,
                'comment' => $comment,
                'created_time' => time(),
            );
            Theme_Service_PriceChangeLog::insert($data);
        }

        $themeName = $this->getPost("cname");
        $groupId = $this->userInfo['groupid'];
        $nick_name = $this->userInfo['nick_name'];
        $userId = $this->userInfo["uid"];
        $logsData = array("name" => $themeName,
            "groupId" => $groupId,
            "auth" => $nick_name,
            "status" => 1,
            "upload_time" => time(),
            "themeId" => $themeid,
            "authId" => $userId,
        );

        Theme_Service_LogsMsg::setData($logsData);

        Theme_Service_File::updateFile($file_data, $themeid);
        Theme_Service_IdxFileType::deleteByFileId($themeid);
        $res = Theme_Service_IdxFileType::setfileIdx($typeid, $themeid);

        echo $res;
        exit;
    }

    private function mk_typeids($types) {
        foreach ($types as $v) {
            $tem[$v["id"]] = $v["name"];
        }
        return $tem;
    }

    /**
     *
     * Enter description here ...
     */
    public function editAction() {
        if ($this->userInfo['groupid'] != 1) $this->redirect($this->actions['listUrl']);
//分类
        list(, $file_type) = Theme_Service_FileType::getAllFileType();
        $id = $this->getInput('id');
        $info = Theme_Service_File::getFile(intval($id));
//图片
        $file_imgs = Theme_Service_FileImg::getByFileId(intval($id));
        $imgs = '';
        foreach ($file_imgs as $key => $value) {
            $imgs .= Util_String::strlen($imgs) ? ', ' . $value['img'] : $value['img'];
        }
        $info['imgs'] = $imgs;

        $file_types = Theme_Service_IdxFileType::getByFileId($info['id']);

        $this->assign('info', $info);
        $this->assign('file_type', Common::resetKey($file_type, 'id'));
        $this->assign('file_types', Common::resetKey($file_types, 'type_id'));
        $this->assign('groupid', $this->userInfo['groupid']);
    }

    /**
     *
     * Enter description here ...
     */
    public function addAction() {
// if ($this->userInfo['groupid'] != 1) $this->redirect($this->actions['listUrl']);
//分类
        $themeid = intval($this->getInput("themeid")) ? $this->getInput("themeid") : false;
        if ($themeid) {
            $this->assign("themeid", $themeid);
            $this->assign("path", "/Admin/File/upload_postEdit?themeId=" . $themeid . "&");
        } else {
            $this->assign("path", "/Admin/File/upload_post?");
        }
        $this->assign("meunOn", "zt_uploadThemes");
        list(, $file_type) = Theme_Service_FileType::getAllFileType();

        $this->assign('file_type', Common::resetKey($file_type, 'id'));
    }

    /**
     *
     * Enter description here ...
     */
    public function add_postAction() {
        if ($this->userInfo['groupid'] != 1) $this->redirect($this->actions['listUrl']);
        $info = $this->getPost(array('title', 'file', 'descript', 'designer', 'resulution',
            'min_version', 'max_version', 'font_size', 'android_version', 'rom_version',
            'channel', 'lock_style', 'file_size', 'imgs', 'file_type', 'since', 'packge_time',
            'package_type', "style"));


        $info['user_id'] = $this->userInfo['id'];


        $info = $this->_cookData($info);
        list($totals, $sort_values) = Theme_Service_File::getList(1, 1, '', 'sort', 'DESC');
        list($totals, $sinceid) = Theme_Service_File::getByWhere("since=$info[since]");


        if ($sinceid) $this->output(-1, '主题包已存在！' . $sinceid[id] . "_" . $sinceid["title"]);
//文件
        $file_data = array(
            'user_id' => $this->userInfo['uid'],
            'title' => $info['title'],
            'file' => $info['file'],
            'descript' => $info['descript'],
            'designer' => $info['designer'],
            'resulution' => $info['resulution'],
            'min_version' => $info['min_version'],
            'max_version' => $info['max_version'],
            'font_size' => $info['font_size'],
            'android_version' => $info['android_version'],
            'rom_version' => $info['rom_version'],
            'channel' => $info['channel'],
            'lock_style' => $info['lock_style'],
            'file_size' => $info['file_size'],
            'since' => $info['since'],
            // 'sort' => $sort_values[0]['sort'] + 1 ,
            'sort' => 0,
            'packge_time' => $info['packge_time'],
            'package_type' => $info['package_type'],
            "style" => $info['style'],
        );

        $user = array(
            'user_id' => $this->userInfo['uid'],
            'username' => $this->userInfo['username']
        );

        $result = Theme_Service_File::add($user, $file_data, $info['file_type'], $info['imgs']);
        if (!$result) $this->output(-1, '操作失败');

        Theme_Service_File::updateVersion();
        $this->output(0, '操作成功');
    }

    /**
     *
     * Enter description here ...
     */
    public function edit_postAction() {

        if ($this->userInfo['groupid'] != 1) $this->redirect($this->actions['listUrl']);
        $info = $this->getPost(array('id', 'title', 'file', 'descript', 'designer', 'resulution', 'min_version', 'max_version', 'font_size', 'android_version', 'rom_version', 'channel', 'lock_style', 'file_size', 'imgs', 'file_type', 'since', 'packge_time', 'price', 'priceType'));
        $info = $this->_cookData($info);

        if (!empty($info['priceType']) && $info['priceType'] == 'free') {
            $price = 0;
        } else {
            $price = $info['price'];
        }
        $file_data = array(
            'title' => $info['title'],
            'file' => $info['file'],
            'descript' => $info['descript'],
            'designer' => $info['designer'],
            'resulution' => $info['resulution'],
            'min_version' => $info['min_version'],
            'max_version' => $info['max_version'],
            'font_size' => $info['font_size'],
            'android_version' => $info['android_version'],
            'rom_version' => $info['rom_version'],
            'channel' => $info['channel'],
            'lock_style' => $info['lock_style'],
            'file_size' => $info['file_size'],
            'since' => $info['since'],
            'packge_time' => $info['packge_time'],
            'update_time' => Common::getTime(),
            'price' => $price,
        );

//分类
        $type_data = array();
        foreach ($info['file_type'] as $key => $value) {
            $type_data[$key]['id'] = '';
            $type_data[$key]['file_id'] = $info['id'];
            $type_data[$key]['type_id'] = $value;
        }

//图片
        $arr_img = explode(',', html_entity_decode($info['imgs']));
        $img_data = array();
        foreach ($arr_img as $key => $value) {
            $img_data[$key]['id'] = '';
            $img_data[$key]['file_id'] = $info['id'];
            $img_data[$key]['img'] = $value;
        }

//记录日志
        $log_data = array(
            'uid' => $this->userInfo['uid'],
            'username' => $this->userInfo['username'],
            'message' => $this->userInfo['username'] . '修改了文件：<a href=/Admin/File/detail/?id=' . $info['id'] . '>' . $info['title'] . '</a>',
            'file_id' => $info['id']
        );

//给测试发消息
        $message_data = array();
        $group_id = 2;
//消息内容
        list(, $users) = Admin_Service_User::getList(1, 20, array('groupid' => $group_id));
        if ($users) {
            foreach ($users as $key => $value) {
                $message_data[$key]['id'] = '';
                $message_data[$key]['uid'] = $value['uid'];
                $message_data[$key]['content'] = $this->userInfo['username'] . '修改了文件：<a href=/Admin/File/detail/?id=' . $info['id'] . '>' . $info['title'] . '</a>';
                $message_data[$key]['status'] = 0;
                $message_data[$key]['create_time'] = Common::getTime();
            }
        }
        $result = Theme_Service_File::transactionUpdate($info['id'], $file_data, $type_data, $img_data, $log_data, $message_data);
        if (!$result) $this->output(-1, '操作失败');
        Theme_Service_File::updateVersion();
        $this->output(0, '操作成功.');
    }

    /**
     *
     * Enter description here ...
     */
    public function editstatusAction() {

        if (!in_array($this->userInfo['groupid'], array(2, 3))) $this->redirect($this->actions['listUrl']);
        $id = $this->getInput('id');
        $info = Theme_Service_File::getFile(intval($id));

//日志
        list(, $logs) = Admin_Service_AdminLog::getList(1, 100, array('file_id' => $info['id']));

//不同的用户组操作的权限不一样

        if ($this->userInfo['groupid'] == 2) {
            unset($this->status[4]);
            unset($this->status[5]);
        } elseif ($this->userInfo['groupid'] == 3) {
            if ($info['status'] == 3) {
                unset($this->status[1]);
                unset($this->status[5]);
            } else {
                unset($this->status[1]);
                unset($this->status[2]);
                unset($this->status[3]);
            }
        } else {
            $status = array();
        }

        $this->assign('info', $info);
        $this->assign('logs', $logs);
        $this->assign('status', $this->status);
        $this->assign('groupid', $this->userInfo['uid']);
    }

    /**
     * Enter description here ...
     */
    public function editstatus_postAction() {
        if (!in_array($this->userInfo['groupid'], array(2, 3))) {

            $this->output(-1, '无权限操作.');
        }
        if (!in_array($this->userInfo['groupid'], array(2, 3))) {

            $this->redirect($this->actions['listUrl']);
        }
        list($totals, $sort_values) = Theme_Service_File::getList(1, 1, '', 'sort', 'DESC');
        $info = $this->getPost(array('id', 'status', 'message', 'ostatus', "more"));

///批量处理;
        if ($info['more']) {

            $optData = $this->mk_mutData($info['id'], $info);
            $lens = count($optData);
            $i = 0;
            foreach ($optData as $datas_num) {
//是否最后一条;
                $i ++;
                $type = ($i != $lens) ? 1 : 0;

                if ($info['status'] == 4) {
                    list($totals, $sort_values) = Theme_Service_File::getList(1, 1, '', 'sort', 'DESC');
                    $datas_num['sort'] = $sort_values[0]['sort'] + 1;
                } else {
                    $datas_num['sort'] = 0;
                }


                $this->set_status($datas_num, $type);
            }
        } else {
//单条操作

            if ($info['status'] == 4) {
                list($totals, $sort_values) = Theme_Service_File::getList(1, 1, '', 'sort', 'DESC');
                $info['sort'] = $sort_values[0]['sort'] + 1;
            } else {
                $info['sort'] = 0;
            }

            $this->set_status($info);
        }
    }

//批量删除
    public function moreDel_postAction() {
        $info = $this->getPost(array("id"));
        $optData = $this->mk_mutData($info['id']);
        $lens = count($optData);
        $i = 0;

        foreach ($optData as $values) {
//是否最后一条;
            $i ++;
            $type = ($i != $lens) ? 1 : 0;
            $this->del_Datas($values, $type);
        }
    }

    private function set_status(array $info, $type = 0) {
        if ($info['status'] == 2 && !$info['message']) {
            $this->output(-1, '请填写未通过原因.');
        }

//有修改才操作数据库
        if ($info['status'] != $info['ostatus'] || $info['message']) {

            $file_data = array('status' => $info['status'], 'sort' => $info['sort']);
//上架状态
            if ($info['status'] == 4) $file_data['open_time'] = Common::getTime();

            $file = Theme_Service_File::getFile($info['id']);
//记录日志
            $message = $this->userInfo['username'] . '将文件 "<a href=/Admin/File/detail/?id=' . $info['id'] . '>' . $file['title'] . '</a>"状态更新为"' . $this->status[$info['status']] . '"';
            if ($info['message']) $message .= ',备注：' . $info['message'];
            $log_data = array(
                'uid' => $this->userInfo['uid'],
                'username' => $this->userInfo['username'],
                'message' => $message,
                'file_id' => $info['id']
            );

//发送消息--确定发送对象

            $group_id = 0;
            $message_data = array();

            if ($info['status'] == 1) {
                $group_id = 2;
            } elseif ($info['status'] == 2) {
                $group_id = 1;
            } elseif ($info['status'] == 3) {
                $group_id = 3;
            } else {
                $group_id = 0;
            }
//消息内容
            if ($group_id) {
                list(, $users) = Admin_Service_User::getList(1, 20, array('groupid' => $group_id));
                $content = '文件"<a href=/Admin/File/detail/?id=' . $info['id'] . '>' . $file['title'] . '</a>"审核"' . $this->status[$info['status']] . '"';
                if ($info['message']) $content .= ',原因：' . $info['message'];
                if ($users) {
                    foreach ($users as $key => $value) {
                        $message_data[$key]['id'] = '';
                        $message_data[$key]['uid'] = $value['uid'];
                        $message_data[$key]['content'] = $content;
                        $message_data[$key]['status'] = 0;
                        $message_data[$key]['create_time'] = Common::getTime();
                    }
                }
            }

            $result = Theme_Service_File::editStatus($info['id'], $info['status'], $file_data, $log_data, $message_data);
            if (!$result) {
                $this->output(-1, '操作失败');
            }
        }
        Theme_Service_File::updateVersion();
        if (!$type) {
            $this->output(0, '操作成功');
        }
    }

    private function del_Datas($id, $type = 0) {

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
        if (!$type) {
            $this->output(0, '操作成功');
        }
    }

    /**
     *
     * @param type $data
     */
    private function mk_mutData($data, array $info = array()) {
        $ids = explode(" ", $data);
        if (!$info) {
            array_pop($ids);
            return $ids;
        }
        $optData = array();
        foreach ($ids as $values) {
            if ($values) {
                $optData [] = array(
                    "id" => $values,
                    "status" => $info['status'],
                    "message" => '批量操作',
                    'ostatus' => $info['ostatus']
                );
            }
        }
        return $optData;
    }

    /**
     *
     * Enter description here ...
     */
    private function _cookData($info) {
        if (!$info['file']) $this->output(-1, '请上传zip文件.');
        if (!$info['file_type']) $this->output(-1, '请选择分类.');
        return $info;
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

    /**
     *
     * Enter description here ...
     */
    public function detailAction() {
        $id = $this->getInput('id');
        $info = Theme_Service_File::getFile(intval($id));
        if (!$info || !$id) $this->redirect($this->actions['listUrl']);
//分类
        $file_types = Theme_Service_IdxFileType::getByFileId($info['id']);

        $ids = array();
        foreach ($file_types as $key => $value) {
            $ids[$key] = $value['type_id'];
        }
        $file_type = Theme_Service_FileType::getByIds($ids);

        $str_type = '';
        foreach ($file_type as $key => $value) {
            $str_type .= strlen($str_type) ? ',' . $value['name'] : $value['name'];
        }
        $info['file_type'] = $str_type;

//图片
        $file_imgs = Theme_Service_FileImg::getByFileId($info['id']);
//日志
        list(, $logs) = Admin_Service_AdminLog::getList(1, 100, array('file_id' => $info['id']));
        $this->assign('info', $info);
        $this->assign('imgs', $file_imgs);
        $this->assign('info', $info);
        $this->assign('logs', $logs);
        $this->assign('group', $this->userInfo['groupid']);
    }

    /**
     * 编辑附加信息
     * Enter description here ...
     */
    public function updateAction() {
        if ($this->userInfo['groupid'] != 3) $this->redirect($this->actions['listUrl']);
        $id = $this->getInput('id');
        $info = Theme_Service_File::getFile(intval($id));
        $typeids_tmp = Theme_Service_IdxFileType::getByFileIds(array($id));
        foreach ($typeids_tmp as $v) {
            $typeids[] = $v['type_id'];
        }
        list(, $file_type) = Theme_Service_FileType::getAllFileType();


        $this->assign('file_type', Common::resetKey($file_type, 'id'));
        $this->assign('typeids', $typeids);

//rom
        list(, $roms) = Theme_Service_Rom::getAllRom();
//idx_file_rom
        $idx_file_rom = Theme_Service_IdxFileRom::getByFileId($id);

        $this->assign('info', $info);
        $this->assign('idx_file_rom', Common::resetKey($idx_file_rom, 'rom_id'));
        $this->assign('roms', Common::resetKey($roms, 'id'));
        $this->assign('groupid', $this->userInfo['groupid']);
    }

    /**
     * 编辑附加信息
     * Enter description here ...
     */
    public function update_postAction() {
        if ($this->userInfo['groupid'] != 3) $this->redirect($this->actions['listUrl']);
        $info = $this->getPost(array('id', 'down', 'file_rom', 'sort', 'file_type'));


        $file_data = array('down' => $info['down'], 'sort' => $info['sort']);

//rom
        $rom_data = array();
        foreach ($info['file_rom'] as $key => $value) {
            $rom_data[$key]['id'] = '';
            $rom_data[$key]['file_id'] = $info['id'];
            $rom_data[$key]['rom_id'] = $value;
        }
        $result = Theme_Service_File::edit($info['id'], $file_data, $rom_data);
        if (!$result) $this->output(-1, '操作失败');
        if ($info['file_type']) {
            Theme_Service_IdxFileType::deleteByFileId($info["id"]);
            Theme_Service_IdxFileType::setfileIdx($info["file_type"], $info["id"]);
        }


        Theme_Service_File::updateVersion();
        $this->output(0, '操作成功');
    }

    /**
     *
     * Enter description here ...
     */
    public function uploadAction() {

        $imgId = $this->getInput('imgId');
        $this->assign('imgId', $imgId);
    }

    /**
     * v6.0.3
     * Enter description here ...
     */
    public function upload_postAction() {
        $ret = Theme_Service_File::uploadFile('file', 'file');
        //face_samll预览图是否存在;
        foreach ($ret['data']['img_data'] as $tem) {
            $d = strpos($tem, "pre_face_small");
            if ($d) {
                $ret['data']['file_data']['is_faceimg'] = 1;
                break;
            } else {
                $ret['data']['file_data']['is_faceimg'] = 0;
            }
        }



        $res = $this->uploadDataDB($ret['data']['file_data'], $ret['data']['img_data']);
        print_r($res);
        exit;
    }

    /**
     * v6.0.3
     * Enter description here ...
     */
    public function upload_postEditAction() {
        $ret = Theme_Service_File::uploadFile('file', 'file');

        $themeId = $this->getInput("themeId");

        $res = $this->uploadDataDB_updates($ret['data']['file_data'], $themeId, $ret['data']['img_data']);
        print_r($res);
        exit;
    }

    //更跟主题包;
    private function uploadDataDB_updates($info, $themeid, $imgs) {
        if ($info['verson'] == "V2") {
            $package_type = 2;
        } else if ($info['verson'] == "V3") {
            $package_type = 3;
        } else {
            $package_type = 3;
        }
        $file_data = array(
            'user_id' => $this->userInfo['uid'],
            'title' => $info['title'],
            'file' => $info['file'],
            'Ename' => $info['e_name'],
            'descript' => $info['descript'],
            'designer' => $info['designer'],
            'resulution' => $info['resulution'],
            'min_version' => $info['min_version'],
            'max_version' => $info['max_version'],
            'font_size' => $info['font_size'],
            'android_version' => $info['android_version'],
            'rom_version' => $info['rom_version'],
            'channel' => $info['channel'],
            'lock_style' => $info['lock_style'],
            'file_size' => $info['file_size'],
            'since' => $info['since'],
            "package_type" => $package_type,
            'packge_time' => $info['packge_time'],
            'crate_time' => time(),
        );

        $result = Theme_Service_File::updateFile($file_data, $themeid);

        if ($result) {
            Theme_Service_FileImg::deleteByFileId($themeid);
        }
        foreach ($imgs as $key => $value) {
            $img_data[$key]['id'] = '';
            $img_data[$key]['file_id'] = $themeid;
            $img_data[$key]['img'] = $value;
        }
        $img_ret = Theme_Service_FileImg::batchAdd($img_data);
        echo $img_ret;
    }

//上传入库;
    private function uploadDataDB($info, $imgs) {
        if ($info['verson'] == "V2") {
            $package_type = 2;
        } else if ($info['verson'] == "V3") {
            $package_type = 3;
        } else {
            $package_type = 3;
        }

        list($totals, $sort_values) = Theme_Service_File::getList(1, 1, '', 'sort', 'DESC');
        $file_data = array(
            'user_id' => $this->userInfo['uid'],
            'title' => $info['title'],
            'file' => $info['file'],
            'Ename' => $info['e_name'],
            'descript' => $info['descript'],
            'designer' => $info['designer'],
            'resulution' => $info['resulution'],
            'min_version' => $info['min_version'],
            'max_version' => $info['max_version'],
            'font_size' => $info['font_size'],
            'android_version' => $info['android_version'],
            'rom_version' => $info['rom_version'],
            'channel' => $info['channel'],
            'lock_style' => $info['lock_style'],
            'file_size' => $info['file_size'],
            'since' => $info['since'],
            'sort' => $sort_values[0]['sort'] + 1,
            // 'sort' => 0,
            "package_type" => $package_type,
            "style" => $info['style'],
            'is_faceimg' => $info['is_faceimg'],
            'packge_time' => $info['packge_time'],
            'crate_time' => time(),
        );


        $user = array(
            'user_id' => $this->userInfo['uid'],
            'username' => $this->userInfo['username']
        );
        $result = Theme_Service_File::add($user, $file_data, $imgs);

        return $result;
    }

    public function progressAction() {
        $p_key = $this->getInput("progress_key");
        $times = $this->getInput("times");

        $res_time = (time() - $times / 1000) * 20;
        echo $res_time;
        exit;
    }

    /**
     * test
     */
    public function testAction() {
        print_r(Util_File::read('/var/www/theme/data/attachs/file/'));
        exit;
    }

    public function getPackgeTimeAction() {
        $themePath = Common::getConfig('siteConfig', 'themePath');
        $tmpPath = Common::getConfig('siteConfig', 'tmpPath');
        list(, $files) = Theme_Service_File::getList(1, 100, array('packge_time' => 0), 'id', 'DESC');
        foreach ($files as $key => $value) {
            $base_name = basename($value['file'], '.gnz');
            if (file_exists($themePath . $value['file'])) {
                $unfile_path = $tmpPath . dirname($value['file']) . '/' . $base_name;
                $zip = new Util_System();
                $zip->unzipFile($themePath . $value['file'], $unfile_path);
                chmod($unfile_path, 0777);
                $since_properties = $unfile_path . '/since.properties';
                $content = file_get_contents($since_properties);
                $arr_content = explode("\n", $content);
                $packge_time = strtotime(str_replace('#', '', $arr_content[0]));
                Theme_Service_File::updateFile(array('packge_time' => $packge_time), $value['id']);
//Util_Folder::rm($unfile_path, true);
            }
        }
        exit;
    }

    public function createThumbAction() {

        $themePath = Common::getConfig('siteConfig', 'themePath');
        $attachPath = Common::getConfig('siteConfig', 'attachPath');
        $tmpPath = Common::getConfig('siteConfig', 'tmpPath');
        list(, $files) = Theme_Service_File::getList(1, 100, array(), 'id', 'DESC');
        foreach ($files as $key => $value) {
            $base_name = basename($value['file'], '.gnz');
            $dir = dirname($value['file']);
            if (file_exists($themePath . $value['file'])) {
                $unfile_path = $tmpPath . dirname($value['file']) . '/' . $base_name;
                $zip = new Util_System();

                $zip->unzipFile($themePath . $value['file'], $unfile_path);
                chmod($unfile_path, 0777);
                $img_face = $unfile_path . '/other/preview/pre_face.png';
                $img = basename($img_face);

                list($width, $height) = getimagesize($img_face);
                $s_width = 140;
                $s_height = ceil(($height * $s_width) / $width);
                $img_name = explode('.', $img);

                Theme_Service_File::CreateThumb($img_face, $attachPath . 'file' . $dir, $img_name[0] . '_s', $s_width, $s_height, true);
//Util_Folder::rm($unfile_path, true);
            }
        }
        exit;
    }

    public function createFullScaleImagesAction() {
        list($total, $files) = Theme_Service_File::getList();
        if ($total > 0) var_dump($files[0]);
        $this->assign('total', $total);
    }

}
