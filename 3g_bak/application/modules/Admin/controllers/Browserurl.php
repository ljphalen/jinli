<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 浏览器url控制
 */
class BrowserurlController extends Admin_BaseController {

    public $actions = array(
        'listUrl'       => '/Admin/Browserurl/index',
        'editUrl'       => '/Admin/Browserurl/edit',
        'confUrl'       => '/Admin/Browserurl/conf',
        'editPostUrl'   => '/Admin/Browserurl/edit_post',
        'deleteUrl'     => '/Admin/Browserurl/delete',
        'uploadUrl'     => '/Admin/Browserurl/upload',
        'uploadPostUrl' => '/Admin/Browserurl/upload_post',
        'seriesListUrl' => '/Admin/Browserurl/serieslist',
        'seriesEditUrl' => '/Admin/Browserurl/seriesedit',
        'seriesDelUrl'  => '/Admin/Browserurl/seriesdel',
        'modelListUrl'  => '/Admin/Browserurl/modellist',
        'modelEditUrl'  => '/Admin/Browserurl/modeledit',
        'modelDelUrl'   => '/Admin/Browserurl/modeldel',
        'brandListUrl'  => '/Admin/Browserurl/brandlist',
        'brandEditUrl'  => '/Admin/Browserurl/brandedit',
        'brandDelUrl'   => '/Admin/Browserurl/branddel',


    );

    public $perpage = 20;

    /**
     * type 1 搜索配置
     * type 2 推荐网址
     * type 3 网址库
     */
    public function indexAction() {
        $page = intval($this->getInput('page'));
        $type = intval($this->getInput('type'));
        $app  = $this->getInput('app');
        if (empty($type)) {
            $type = 1;
        }

        $perpage = $this->perpage;
        $where   = array(
            'operation' => array('<', 1),
            'type'      => $type
        );
        if (!empty($app)) {
            $where['app'] = array('&', $app);
        }
        list($total, $list) = Gionee_Service_Browserurl::getList($page, $perpage, $where, array('id' => 'DESC'));

        $url = $this->actions['listUrl'] . '?type=' . $type . '&app=' . $app . '&';
        $this->assign('pager', Common::getPages($total, $page, $perpage, $url));
        $this->assign('list', $list);
        $this->assign('type', $type);
        $this->assign('app', $app);
    }

    public function editAction() {
        $id   = $this->getInput('id');
        $type = intval($this->getInput('type'));
        $info = Gionee_Service_Browserurl::get(intval($id));
        if (empty($info['type'])) {
            $info['type'] = $type;
        }
        $this->assign('info', $info);
    }

    public function edit_postAction() {
        $info = $this->getPost(array(
            'id',
            'name',
            'type',
            'icon',
            'show_url',
            'url',
            'sort',
            'icon_url',
            'filter_app'
        ));
        $info = $this->_cookData($info);

        $info['app'] = array_sum($info['filter_app']);

        $info['updated_at'] = time();
        $info['operation']  = 0;
        if (empty($info['id'])) {
            Admin_Service_Access::pass('add');
            $info['created_at'] = time();
            $ret                = Gionee_Service_Browserurl::add($info);
        } else {
            Admin_Service_Access::pass('edit');
            $ret = Gionee_Service_Browserurl::update($info, intval($info['id']));
        }
        Admin_Service_Log::op($info);

        if (!$ret) {
            $this->output(-1, '操作失败');
        }
        $this->upVer($info['type']);
        $this->output(0, '操作成功.');
    }

    private function _cookData($info) {
        if (empty($info['name']) || mb_strlen($info['name']) > 30) {
            $this->output(-1, '名称非法');
        }

        if (empty($info['icon'])) {
            $this->output(-1, '图标不能为空');
        }

        if (empty($info['url'])) {
            $this->output(-1, '地址不能为空');
        }

        if (!stristr($info['url'], 'http')) {
            $this->output(-1, '地址非法');
        }


        return $info;
    }

    public function deleteAction() {
        Admin_Service_Access::pass('del');
        $id   = $this->getInput('id');
        $info = Gionee_Service_Browserurl::get($id);
        if ($info && $info['id'] == 0) {
            $this->output(-1, '无法删除');
        }
        Admin_Service_Log::op($id);
        Util_File::del(Common::getConfig('siteConfig', 'attachPath') . $info['icon']);
        $info['operation'] = 1;
        if ($info['type'] == 1) {
            $ret = Gionee_Service_Browserurl::delete($info['id']);
        } else {
            $ret = Gionee_Service_Browserurl::update($info, intval($info['id']));
        }

        if (!$ret) {
            $this->output(-1, '操作失败');
        }
        $this->upVer($info['ver']);
        $this->output(0, '操作成功');
    }

    public function uploadAction() {
        $imgId = $this->getInput('imgId');
        $this->assign('imgId', $imgId);
        $this->assign('size', 100);
        $this->getView()->display('common/upload.phtml');
        exit;
    }

    public function upload_postAction() {
        $ret   = Common::upload('img', 'App', 100);
        $imgId = $this->getPost('imgId');
        $this->assign('code', $ret['data']);
        $this->assign('msg', $ret['msg']);
        $this->assign('data', $ret['data']);
        $this->assign('imgId', $imgId);
        $this->assign('size', 100);
        $this->getView()->display('common/upload.phtml');
        exit;
    }


    public function serieslistAction() {
        $page    = max(intval($this->getInput('page')), 1);
        $perpage = $this->perpage;

        list($total, $list) = Gionee_Service_Browserseries::getList($page, $perpage, array(), array('created_at' => 'DESC'));
        $url = $this->actions['seriesListUrl'];
        $this->assign('pager', Common::getPages($total, $page, $perpage, $url));
        $this->assign('list', $list);
        $brand = Gionee_Service_Browserbrand::allArr();
        $this->assign('brand', $brand);
    }

    public function serieseditAction() {
        $id   = intval($this->getInput('id'));
        $info = $this->getPost(array('id', 'name', 'value', 'brand_id'));
        if (!empty($info['name'])) {
            if (empty($info['id'])) {
                unset($info['id']);
                $info['created_at'] = time();

                $ret = Gionee_Service_Browserseries::add($info);
            } else {
                $ret = Gionee_Service_Browserseries::update($info, $info['id']);
            }

            Admin_Service_Log::op($info);
            if (!$ret) {
                $this->output(-1, '-操作失败.');
            }
            $this->output(0, '操作成功');
            exit;
        }

        $info = Gionee_Service_Browserseries::get($id);
        $this->assign('info', $info);
        $brand = Gionee_Service_Browserbrand::allArr();
        $this->assign('brandList', $brand);
        $tmp  = Gionee_Service_Config::getValue('BrowserUrlConf');
        $conf = json_decode($tmp, true);
        $this->assign('conf', $conf);
    }

    public function seriesdelAction() {
        $id   = $this->getInput('id');
        $info = Gionee_Service_Browserseries::get($id);

        if ($info && $info['id'] == 0) {
            $this->output(-1, '无法删除');
        }
        Admin_Service_Log::op($id);
        $ret = Gionee_Service_Browserseries::delete($info['id']);

        if (!$ret) {
            $this->output(-1, '操作失败');
        }
        $this->output(0, '操作成功');
    }

    public function modellistAction() {
        $page    = max(intval($this->getInput('page')), 1);
        $perpage = $this->perpage;

        list($total, $list) = Gionee_Service_Browsermodel::getList($page, $perpage, array(), array('created_at' => 'DESC'));
        $url = $this->actions['modelListUrl'];
        $this->assign('pager', Common::getPages($total, $page, $perpage, $url));
        $this->assign('list', $list);
        $series = Gionee_Service_Browserseries::allArr();
        $this->assign('series', $series);
    }

    public function modeleditAction() {
        $id   = intval($this->getInput('id'));
        $info = $this->getPost(array('id', 'name', 'dpi', 'series_id'));
        if (!empty($info['name'])) {
            if (empty($info['id'])) {
                unset($info['id']);
                $info['created_at'] = time();

                $ret = Gionee_Service_Browsermodel::add($info);
            } else {
                $ret = Gionee_Service_Browsermodel::update($info, $info['id']);
            }
            Admin_Service_Log::op($info);
            if (!$ret) {
                $this->output(-1, '-操作失败.');
            }
            $this->output(0, '操作成功');
            exit;
        }

        $info = Gionee_Service_Browsermodel::get($id);
        $this->assign('info', $info);
        $tmp  = Gionee_Service_Config::getValue('BrowserUrlConf');
        $conf = json_decode($tmp, true);
        $this->assign('conf', $conf);
        $series = Gionee_Service_Browserseries::allArr();
        $this->assign('seriesList', $series);
    }

    public function modeldelAction() {
        $id   = $this->getInput('id');
        $info = Gionee_Service_Browsermodel::get($id);
        if ($info && $info['id'] == 0) {
            $this->output(-1, '无法删除');
        }
        Admin_Service_Log::op($id);
        $ret = Gionee_Service_Browsermodel::delete($info['id']);

        if (!$ret) {
            $this->output(-1, '操作失败');
        }
        $this->output(0, '操作成功');
    }

    public function brandlistAction() {
        $page    = max(intval($this->getInput('page')), 1);
        $perpage = $this->perpage;

        list($total, $list) = Gionee_Service_Browserbrand::getList($page, $perpage, array(), array('created_at' => 'DESC'));
        $url = $this->actions['brandListUrl'];
        $this->assign('pager', Common::getPages($total, $page, $perpage, $url));
        $this->assign('list', $list);
    }

    public function brandeditAction() {
        $id   = intval($this->getInput('id'));
        $info = $this->getPost(array('id', 'name', 'area', 'lang'));
        if (!empty($info['name'])) {
            if (empty($info['id'])) {
                unset($info['id']);
                $info['created_at'] = time();

                $ret = Gionee_Service_Browserbrand::add($info);
            } else {
                $ret = Gionee_Service_Browserbrand::update($info, $info['id']);
            }
            Admin_Service_Log::op($info);
            if (!$ret) {
                $this->output(-1, '-操作失败.');
            }
            $this->output(0, '操作成功');
            exit;
        }

        $tmp  = Gionee_Service_Config::getValue('BrowserUrlConf');
        $conf = json_decode($tmp, true);
        $this->assign('conf', $conf);
        $info = Gionee_Service_Browserbrand::get($id);
        $this->assign('info', $info);

    }

    public function branddelAction() {
        $id   = $this->getInput('id');
        $info = Gionee_Service_Browserbrand::get($id);
        if ($info && $info['id'] == 0) {
            $this->output(-1, '无法删除');
        }

        $ret = Gionee_Service_Browserbrand::delete($info['id']);

        if (!$ret) {
            $this->output(-1, '操作失败');
        }
        $this->output(0, '操作成功');
    }

    public function confAction() {
        $keys = array('dpi', 'area', 'lang');
        if (!empty($_POST['dpi'])) {
            $tmp = array();
            foreach ($keys as $k) {
                $tmp[$k] = explode(',', $_POST[$k]);
            }

            Gionee_Service_Config::setValue('BrowserUrlConf', json_encode($tmp));
            $this->output(0, '操作成功');
            exit;
        }

        $ret  = Gionee_Service_Config::getValue('BrowserUrlConf');
        $info = json_decode($ret, true);
        $this->assign('info', $info);

    }

    /**
     * update_version
     */
    private function upVer($type) {
        Gionee_Service_Config::setValue('BrowserUrl:' . $type, Common::getTime());
    }


    public function exportAction() {
        $type  = $this->getInput('type');
        $list  = Gionee_Service_Browserurl::getsBy(array('type' => $type, 'operation' => 0));
        $row   = array();
        $row[] = array('id', 'name', 'url', 'show_url', 'sort', 'type', 'app');
        foreach ($list as $fields) {
            $row[] = array(
                $fields['id'],
                $fields['name'],
                html_entity_decode($fields['url']),
                html_entity_decode($fields['show_url']),
                $fields['sort'],
                $fields['type'],
                $fields['app'],
            );
        }

        Common::export($row, '', '', "网址库" . date('YmdHis'));
    }

    public function importAction() {

        if (!empty($_FILES['data'])) {
            $file   = $_FILES['data']['tmp_name'];
            $fields = array('id', 'name', 'url', 'show_url', 'sort', 'type', 'app');
            $num    = count($fields);
            $row    = 1;//初始值
            if (($handle = fopen($file, "r")) !== false) {
                while (($data = fgetcsv($handle, 1000, ",")) !== false) {
                    if ($row > 1) {
                        $contents = array();
                        for ($i = 0; $i < $num; $i++) {
                            $contents[$fields[$i]] = $data[$i];
                        }
                        $contents['name'] = iconv('GBK', 'UTF8', $contents['name']);
                        if (!empty($contents['name'])) {
                            $meta                   = !empty($contents['id']) ? Gionee_Service_Browserurl::get($contents['id']) : false;
                            $contents['updated_at'] = time();
                            if ($meta) {
                                $out = Gionee_Service_Browserurl::update($contents, $contents['id']);
                            } else {
                                $contents['created_at'] = time();
                                $out                    = Gionee_Service_Browserurl::add($contents);//添加
                            }

                        }

                    }
                    $row++;
                }
            }
            fclose($handle);
            $this->output('0', '添加成功');
        }

    }
}