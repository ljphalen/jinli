<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * h5游戏管理后台
 */
class H5gameController extends Admin_BaseController {

    public $actions = array(
        'listUrl'     => '/Admin/H5game/list',
        'editUrl'     => '/Admin/H5game/edit',
        'delUrl'      => '/Admin/H5game/del',
        'typelistUrl' => '/Admin/H5game/typelist',
        'typeeditUrl' => '/Admin/H5game/typeedit',
        'typedelUrl'  => '/Admin/H5game/typedel',
        'uploadUrl'   => '/Admin/H5game/upload',
        'uploadPostUrl' => '/Admin/App/upload_post',
        'catelistUrl' => '/Admin/H5game/catelist',
        'cateeditUrl' => '/Admin/H5game/cateedit',
        'catedelUrl'  => '/Admin/H5game/catedel',

        'exportUrl'  => '/Admin/H5game/export',
        'importUrl'  => '/Admin/H5game/import',
    );

    static $headerrow = array(
        'id'           => 'ID',
        'name'         => '名称',
        'type_id'      => '类型',
        'descrip'      => '描述',
        'link'         => '链接',
        'color'        => '底色',
        'img'          => '图标',
        'img2'         => '图标2',
        'star'         => '指数',
        'hits'         => '点击量',
        'is_new'       => '新应用[1是|0否]',
        'is_must'      => '必备[1是|0否]',
        'is_recommend' => '推荐[1是|0否]',
        'status'       => '状态[1开|0关]',
        'sub_time'     => '时间',
        'sort'         => '排序'
    );

    public $perpage = 20;
    public $types; //分类

    public function init() {
        parent::init();
        $this->types = Gionee_Service_H5game::getTypeDao()->getsBy();

    }

    public function listAction() {
        $page    = intval($this->getInput('page'));
        $perpage = $this->perpage;
        $param   = $this->getInput(array('type_id', 'order_by', 'name'));

        //排序方式
        $order_by = $param['order_by'] ? $param['order_by'] : 'id';

        $search = array();

        if ($param['type_id'] != '') $search['type_id'] = $param['type_id'];
        if ($param['name'] != '') $search['name'] = array('LIKE', $param['name']);
        $or = array($order_by => 'DESC', 'id' => 'DESC');

        $page     = max(1, $page);
        $total    = Gionee_Service_H5game::getDao()->count($search);
        $recmarks = Gionee_Service_H5game::getDao()->getList(($page - 1) * $perpage, $perpage, $search, $or);
        $this->assign('recmarks', $recmarks);
        $url = $this->actions['listUrl'] . '/?' . http_build_query($search) . '&';
        $this->assign('pager', Common::getPages($total, $page, $perpage, $url));
        $this->assign('types', Common::resetKey($this->types, 'id'));
        $this->assign('order_by', $order_by);
        $this->assign('param', $param);
    }

    public function editAction() {
        $id = $this->getInput('id');

        $info = $this->getPost(array(
            'id',
            'name',
            'tag',
            'type_id',
            'theme_id',
            'link',
            'color',
            'img',
            'icon',
            'icon2',
            'default_icon',
            'sort',
            'hits',
            'status',
            'sub_time',
            'is_new',
            'is_recommend',
            'is_must',
            'descrip',
            'star'
        ));
        if (!empty($info['name'])) {
            $info = $this->_cookData($info);
            //名称转拼音
            $py     = new Util_Pinyin();
            $pinyin = $py->getPinyin($info['name']);
            if ($info['tag']) {
                $info['tag'] = $info['name'] . ',' . $pinyin . ',' . $info['tag'];
            } else {
                $info['tag'] = $info['name'] . ',' . $pinyin;
            }

            if (empty($info['id'])) {
                Admin_Service_Access::pass('add');
                $ret = Gionee_Service_H5game::getDao()->insert($info);
            } else {
                Admin_Service_Access::pass('edit');
                $ret = Gionee_Service_H5game::getDao()->update($info, intval($info['id']));
            }

            Admin_Service_Log::op($info);
            if (!$ret) $this->output(-1, '操作失败');
            $this->output(0, '操作成功.');
        }

        $info    = Gionee_Service_H5game::getDao()->get(intval($id));
        $apptype = Gionee_Service_H5game::getTypeDao()->get($info['type_id']);
        $themes  = Gionee_Service_H5game::getCateDao()->getsBy();
        $this->assign('themes', $themes);
        $this->assign('name', $apptype['name']);
        $this->assign('info', $info);
        $this->assign('types', $this->types);
    }


    private function _cookData($info) {
        if (!$info['name']) $this->output(-1, '名称不能为空.');
        if (!$info['link']) $this->output(-1, '链接地址不能为空.');
        if (!$info['star']) $this->output(-1, '星级请填写1-5之间的数字.');
        if ($info['star'] > 5 || $info['star'] < 1) $this->output(-1, '星级请填写1-5之间的数字.');
        if (!$info['color']) $this->output(-1, '颜色不能为空.');
        if (!$info['img']) $this->output(-1, '图片不能为空.');
        if (!$info['sub_time']) $this->output(-1, '发布时间不能为空.');
        if (strpos($info['link'], 'http://') === false || !strpos($info['link'], 'https://') === false) {
            $this->output(-1, '链接地址不规范.');
        }
        $info['tag']      = strtolower($info['tag']);
        $info['sub_time'] = strtotime($info['sub_time']);
        return $info;
    }

    public function delAction() {
        Admin_Service_Access::pass('del');
        $id   = $this->getInput('id');
        $info = Gionee_Service_H5game::getDao()->get($id);
        if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
        Util_File::del(Common::getConfig('siteConfig', 'attachPath') . $info['img']);
        $result = Gionee_Service_H5game::getDao()->delete($id);
        Admin_Service_Log::op($id);
        if (!$result) $this->output(-1, '操作失败');
        $this->output(0, '操作成功');
    }

    public function uploadAction() {
        $imgId = $this->getInput('imgId');
        $this->assign('imgId', $imgId);
        $this->getView()->display('common/upload.phtml');
        exit;
    }

    public function upload_postAction() {
        $ret   = Common::upload('img', 'App');
        $imgId = $this->getPost('imgId');
        $this->assign('code', $ret['data']);
        $this->assign('msg', $ret['msg']);
        $this->assign('data', $ret['data']);
        $this->assign('imgId', $imgId);
        $this->getView()->display('common/upload.phtml');
        exit;
    }

    /**
     * 导出轻应用内容
     */
    public function exportAction() {
        $filename = 'webapp_' . date('YmdHis') . '.csv';
        $list     = Gionee_Service_H5game::getDao()->getsBy();
        //header( 'Content-Type: text/csv; charset=utf-8' );
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment;filename=' . $filename);
        $fp = fopen('php://output', 'w');

        foreach (self::$headerrow as $k => $v) {
            self::$headerrow[$k] = iconv('UTF8', 'GBK', self::$headerrow[$k]);
        }

        fputcsv($fp, array_values(self::$headerrow));
        foreach ($list as $fields) {
            $fields['descrip'] = str_replace(',', '，', $fields['descrip']);

            $fields['name']    = iconv('UTF8', 'GBK', $fields['name']);
            $fields['descrip'] = iconv('UTF8', 'GBK', $fields['descrip']);

            $row = array(
                $fields['id'],
                $fields['name'],
                $fields['type_id'],
                $fields['descrip'],
                $fields['link'],
                $fields['img'],
                $fields['star'],
                $fields['hits'],
                $fields['is_new'],
                $fields['is_must'],
                $fields['is_recommend'],
                $fields['status'],
                date('Y-m-d H:i:s', $fields['sub_time']),
                $fields['sort'],
            );
            fputcsv($fp, $row);
        }
        fclose($fp);
        exit;
    }

    /**
     * 导入轻应用内容
     */
    public function importAction() {
        if (!empty($_FILES['webappcsv']['tmp_name'])) {
            $file  = $_FILES["webappcsv"]['tmp_name'];
            $row   = 1;
            $num   = count(self::$headerrow);
            $filed = array_keys(self::$headerrow);
            if (($handle = fopen($file, "r")) !== false) {
                while (($data = fgetcsv($handle, 1000, ",")) !== false) {
                    if ($row > 1) {
                        $info = array();
                        for ($c = 0; $c < $num; $c++) {
                            $k        = $filed[$c];
                            $info[$k] = $data[$c];
                        }

                        $info['name']    = iconv('GBK', 'UTF8', $info['name']);
                        $info['descrip'] = iconv('GBK', 'UTF8', $info['descrip']);

                        $py          = new Util_Pinyin();
                        $pinyin      = $py->getPinyin($info['name']);
                        $info['tag'] = $info['name'] . ',' . $pinyin;

                        if (!empty($info['id']) && !empty($info['name'])) {
                            $tmpRow = Gionee_Service_H5game::getDao()->get($info['id']);
                            if (!empty($tmpRow['id'])) {
                                $ret = Gionee_Service_H5game::getDao()->update($info, $info['id']);
                            } else {
                                $ret = Gionee_Service_H5game::getDao()->insert($info);
                            }
                        }
                    }
                    $row++;

                }
                fclose($handle);
            }

            $this->output(0, '操作成功');

            exit;
        }
    }


    /**
     * 导入轻应用图标
     *
     * @param string type 图标字段名称
     */
    public function picimportAction() {
        $type = $this->getInput('type');

        $tmp = array();
        if (in_array($type, array('icon', 'icon2'))) {
            $tmpDir = '/tmp/webapppic/';
            if (!is_dir($tmpDir)) {
                mkdir($tmpDir, 0777, true);
            }

            if (!empty($_FILES['res_file'])) {
                $fileInfo = $_FILES['res_file'];
                $zip      = new ZipArchive;
                $zip->open($fileInfo['tmp_name']);
                $zip->extractTo($tmpDir);
                $zip->close();


                $attachPath = Common::getConfig('siteConfig', 'attachPath');
                $tmpPath    = '/App/' . $type . '/' . date('Ym') . '/';
                $savePath   = $attachPath . $tmpPath;
                if (!is_dir($savePath)) {
                    mkdir($savePath, 0777, true);
                }

                $files = array_diff(scandir($tmpDir), array('..', '.'));
                foreach ($files as $f) {
                    $fn   = basename($f, '.png');
                    $info = Gionee_Service_H5game::getDao()->getBy(array('name' => $fn));
                    if (!empty($info['id'])) {
                        $tmpName = crc32($info['id']) . '.png';
                        $icon    = $tmpPath . $tmpName;
                        $newFile = $savePath . $tmpName;
                        $up      = $icon;
                        if ($icon != $info[$type]) {
                            copy($tmpDir . $f, $newFile);
                            Gionee_Service_H5game::getDao()->update(array($type => $icon), $info['id']);
                            $up = $newFile;
                        }
                        $tmp[] = $f . ' ' . $up;
                    } else {
                        $tmp[] = $f . ' no info';
                    }
                }
            }
        }

        $this->assign('files', $tmp);
        $this->assign('type', $type);
    }


    public function typelistAction() {

        $types = Gionee_Service_H5game::getTypeDao()->getsBy();
        $this->assign('types', $types);
    }

    public function typeeditAction() {
        $id   = $this->getInput('id');
        $info = $this->getPost(array('id', 'name', 'sort', 'img', 'descrip'));
        if (!empty($info['name'])) {
            $info = $this->_typecookData($info);

            if (empty($info['id'])) {
                $ret = Gionee_Service_H5game::getTypeDao()->insert($info);
            } else {
                $ret = Gionee_Service_H5game::getTypeDao()->update($info, intval($info['id']));
            }

            Admin_Service_Log::op($info);
            if (!$ret) $this->output(-1, '操作失败');
            $this->output(0, '操作成功.');
        }


        $info = Gionee_Service_H5game::getTypeDao()->get(intval($id));
        $this->assign('info', $info);
    }


    private function _typecookData($info) {
        if (!$info['name']) $this->output(-1, '名称不能为空.');
        $ret = Gionee_Service_WebAppType::getBy(array('id' => array('!=', $info['id']), 'name' => $info['name']));
        if ($ret) $this->output(-1, '名称已经存在，请修改！');
        if (!$info['descrip']) $this->output(-1, '描述不能为空.');
        if (!$info['img']) $this->output(-1, '图片不能为空.');
        return $info;
    }

    public function typedelAction() {
        $id   = $this->getInput('id');
        $info = Gionee_Service_WebAppType::getApptype($id);
        if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
        $result = Gionee_Service_WebAppType::deleteApptype($id);
        Admin_Service_Log::op($id);
        if (!$result) $this->output(-1, '操作失败');
        $this->output(0, '操作成功');
    }


    public function catelistAction() {
        $page    = intval($this->getInput('page'));
        $perpage = $this->perpage;
        $total   = Gionee_Service_H5game::getCateDao()->count();
        $types   = Gionee_Service_H5game::getCateDao()->getList((max($page,1) - 1) * $perpage, $perpage);
        $this->assign('types', $types);
        $this->assign('pager', Common::getPages($total, $page, $perpage, $this->actions['listUrl'] . '/?'));
    }

    public function cateeditAction() {
        $id   = $this->getInput('id');
        $info = $this->getPost(array('id', 'name', 'icon', 'img', 'sort'));

        if (!empty($info['name'])) {
            $info = $this->_catecookData($info);
            if (empty($info['id'])) {
                $ret = Gionee_Service_H5game::getCateDao()->insert($info);
            } else {
                $ret = Gionee_Service_H5game::getCateDao()->update($info, intval($info['id']));
            }

            if (!$ret) $this->output(-1, '操作失败');
            $this->output(0, '操作成功.');
        }

        $info = Gionee_Service_H5game::getCateDao()->get(intval($id));
        $this->assign('info', $info);
    }


    private function _catecookData($info) {
        if (!$info['name']) $this->output(-1, '名称不能为空.');
        $ret = Gionee_Service_H5game::getCateDao()->getBy(array('id' => array('!=', $info['id']), 'name' => $info['name']));
        if ($ret) $this->output(-1, '名称已经存在，请修改！');
        if (!$info['icon']) $this->output(-1, '图标不能为空.');
        if (!$info['img']) $this->output(-1, '图片不能为空.');
        return $info;
    }


    public function catedelAction() {
        $id   = $this->getInput('id');
        $info = Gionee_Service_H5game::getCateDao()->get($id);
        if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
        $result = Gionee_Service_H5game::getCateDao()->delete($id);
        if (!$result) $this->output(-1, '操作失败');
        $this->output(0, '操作成功');
    }

}