<?php

if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * Enter description here ...
 * @author rainkid
 *
 */
class FileTypeController extends Admin_BaseController {

    public $actions = array(
        'listUrl' => '/Admin/FileType/index',
        'addUrl' => '/Admin/FileType/add',
        'addPostUrl' => '/Admin/FileType/add_post',
        'editUrl' => '/Admin/FileType/edit',
        'editPostUrl' => '/Admin/FileType/edit_post',
        'deleteUrl' => '/Admin/FileType/delete',
    );
    public $perpage = 20;

    /**
     *
     * Enter description here ...
     */
    public function indexAction() {
        $where = "1=1";
        $targs = Theme_Service_FileType::getAllFileType($where)[1];
        $subtargs = Theme_Service_FilesedType::getAll();



        $this->assign("subtargs", $subtargs);
        $this->assign("targs", $targs);

        $this->assign("meunOn", "zt_zTtagAdmin");
    }

    //6.0.3
    public function seaveTargAction() {
        $typeName = $this->getPost("name")?$this->getPost("name") : false;
        $tid = $this->getPost("tid")?$this->getPost("tid") : false;
        $imageurl = $this->getPost("urlImage")?$this->getPost("urlImage") : false;
        $sort = $this->getPost("sort")?$this->getPost("sort") : 0;
        $setdata = "";

        if ($tid) {
            if ($typeName) $setdata["name"] = $typeName;
            if ($imageurl) $setdata["img"] = $imageurl;
            if ($sort) $setdata["sort"] = $sort;
            if (empty($setdata)) return null;
            Theme_Service_FileType::updateFileType($setdata, $tid);
        } else {
            //  $filedname = 'w_type_name,w_type_image,w_type_time';
            // $valname = "'$typeName','$imageurl'," . time();

            $data = array("name" => $typeName, "img" => $imageurl, "sort" => $sort);
            $res = Theme_Service_FileType::addFileType($data);
            
            if ($res) echo json_encode(array("opt" => "insert"));
            exit;
        }
    }

    //v6.0.3
    public function setThemeSubTargAction() {
        $typeName = ($this->getPost("name") != 'undefined') ? $this->getPost("name") : false;
        $tid = ($this->getPost("id") != 'undefined') ? $this->getPost("id") : false;
        $sort = ($this->getPost("sort") != 'undefined') ? intval($this->getPost("sort")) : false;
        if ($tid) {
            $setData = "ctime=" . time();
            if ($typeName) $setData .= ",name='$typeName'";
            if ($sort) $setData .=",sort=" . $sort;
            $where = "id=$tid";

            Theme_Service_FilesedType::updatesubtarg($where, $setData);
        } else {
            $filed = "name,ctime";
            $setData = "'$typeName', " . time();
            $res = Theme_Service_FilesedType::setType($filed, $setData);
            if ($res) echo json_encode(array("opt" => "insert"));
        }
        exit;
    }

    /**
     *
     * Enter description here ...
     */
    public function editAction() {
        $id = $this->getInput('id');
        $info = Theme_Service_FileType::getFileType(intval($id));
        $this->assign("uploadUrl", '/Admin/Subject/upload');
        $this->assign('info', $info);
    }

    /**
     *
     * Enter description here ...
     */
    public function addAction() {
        $this->assign("uploadUrl", '/Admin/Subject/upload');
    }

    /**
     *
     * Enter description here ...
     */
    public function add_postAction() {
        $info = $this->getPost(array('name', 'sort', 'descript', "img"));
        $info = $this->_cookData($info);
        $series = Theme_Service_FileType::getFileTypeByName($info['name']);
        if ($series) $this->output(-1, $info['name'] . '已存在');
        $result = Theme_Service_FileType::addFileType($info);
        if (!$result) $this->output(-1, '操作失败');
        Theme_Service_FileType::updateVersion();
        $this->output(0, '操作成功');
    }

    /**
     *
     * Enter description here ...
     */
    public function edit_postAction() {
        $info = $this->getPost(array('id', 'name', 'sort', 'descript', "img"));

        $info = $this->_cookData($info);

        $series = Theme_Service_FileType::getFileTypeByName($info['name']);
        if ($series && $series['id'] != $info['id']) $this->output(-1, $info['name'] . '已存在');
        $ret = Theme_Service_FileType::updateFileType($info, intval($info['id']));
        if (!$ret) $this->output(-1, '操作失败');
        Theme_Service_FileType::updateVersion();
        $this->output(0, '操作成功.');
    }

    /**
     *
     * Enter description here ...
     */
    private function _cookData($info) {
        if (!$info['name']) $this->output(-1, '名称不能为空.');
        if ($info['sort']) {
            if (!is_numeric($info['sort']) || strpos($info['sort'], ".") !== false || $info['sort'] < 0) $this->output(-1, '排序只能输入整数.');
        }
        if (!$info["img"]) {
            unset($info["img"]);
        }
        return $info;
    }

    /**
     *
     * Enter description here ...
     */
    public function deleteAction() {
        $id = $this->getPost('id');
        /* $info = Theme_Service_FileType::getFileType($id);
          if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
          //检查分类下是否有文件
          $file_types = Theme_Service_IdxFileType::getByTypeId($id);
          if ($file_types) $this->output(-1, '仍然有解锁属于该分类，不能删除');
         */
        $result = Theme_Service_FileType::deleteFileType($id);
        if (!$result) $this->output(-1, '操作失败');
        Theme_Service_FileType::updateVersion();
        $this->output(0, '操作成功');
    }

    //主题二级标签del;
    public function delTargsubAction() {
        $id = intval($this->getPost('tid'));
        $res = Theme_Service_FilesedType::delsubTarg($id);
        if (!$res) $this->output(-1, '操作失败');
        echo $res;
        exit;
    }

}
