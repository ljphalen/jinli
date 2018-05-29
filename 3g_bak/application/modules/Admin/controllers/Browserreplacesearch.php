<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 浏览器收藏图标
 */
class BrowserreplacesearchController extends Admin_BaseController {

    public $actions = array(
        'listUrl' => '/Admin/Browserreplacesearch/list',
        'editUrl' => '/Admin/Browserreplacesearch/edit',
        'delUrl'  => '/Admin/Browserreplacesearch/del',
    );

    public $perpage = 20;

    public function listAction() {
        $get = $this->getInput(array('togrid', 'page', 'rows', 'sort', 'order', 'export'));
        if (!empty($get['togrid'])) {
            $page           = max(intval($get['page']), 1);
            $offset         = !empty($get['rows']) ? $get['rows'] : $this->pageSize;
            $sort           = !empty($get['sort']) ? $get['sort'] : 'id';
            $order          = !empty($get['order']) ? $get['order'] : 'desc';
            $orderBy[$sort] = $order;

            $where = array();
            foreach ($_POST['filter'] as $k => $v) {
                if (strlen($v) > 0) {
                    $where[$k] = array('LIKE', "%{$v}%");
                }
            }

            $total = Browser_Service_ReplaceSearch::getDao()->count($where);
            $start = (max($page, 1) - 1) * $offset;
            $list  = Browser_Service_ReplaceSearch::getDao()->getList($start, $offset, $where, $orderBy);

            foreach ($list as $k => $v) {
                $list[$k]['created_at'] = date('y/m/d H:i', $v['created_at']);
                $list[$k]['updated_at'] = date('y/m/d H:i', $v['updated_at']);
                $list[$k]['status']     = $v['status'] == 1 ? '上线' : '下线';
            }

            $ret = array(
                'total' => $total,
                'rows'  => $list,
            );
            echo Common::jsonEncode($ret);
            exit;
        }

    }

    public function editAction() {
        $id       = $this->getInput('id');
        $postData = $this->getPost(array('id', 'title', 'url', 'replace_url',));
        $now      = time();
        if (!empty($postData['title'])) {

            if (empty($postData['id'])) {
                $postData['created_at'] = $now;
                $ret                    = Browser_Service_ReplaceSearch::getDao()->insert($postData);
            } else {
                $postData['updated_at'] = $now;
                $ret                    = Browser_Service_ReplaceSearch::getDao()->update($postData, $postData['id']);
            }

            Admin_Service_Log::op($postData);
            if ($ret) {
                $this->output(0, '操作成功');
            } else {
                $this->output(-1, '操作失败');
            }
        }

        $info = Browser_Service_ReplaceSearch::getDao()->get($id);
        $this->assign('info', $info);
    }


    public function delAction() {
        $ids = (array)$this->getInput('id');
        foreach ($ids as $id) {
            $info = Browser_Service_ReplaceSearch::getDao()->get($id);
            if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
            $result = Browser_Service_ReplaceSearch::getDao()->delete($id);
            if (!$result) $this->output(-1, '操作失败');
        }
        Admin_Service_Log::op($ids);
        $this->output(0, '操作成功');
    }


    public function exportAction() {
        $list  = Browser_Service_ReplaceSearch::getDao()->getsBy();
        $row   = array();
        $row[] = array('id', 'title', 'url', 'replace_url', 'status');
        foreach ($list as $fields) {
            $row[] = array(
                $fields['id'],
                $fields['title'],
                html_entity_decode($fields['url']),
                html_entity_decode($fields['replace_url']),
                $fields['status'],
            );
        }

        Common::export($row, '', '', "搜索替换" . date('YmdHis'));
    }

    public function importAction() {

        if (!empty($_FILES['data'])) {
            $file   = $_FILES['data']['tmp_name'];
            $fields = array('id', 'title', 'url', 'replace_url', 'status');
            $num    = count($fields);
            $row    = 1;//初始值
            if (($handle = fopen($file, "r")) !== false) {
                while (($data = fgetcsv($handle, 1000, ",")) !== false) {
                    if ($row > 1) {
                        $contents = array();
                        for ($i = 0; $i < $num; $i++) {
                            $contents[$fields[$i]] = $data[$i];
                        }
                        $contents['title'] = iconv('GBK', 'UTF8', $contents['title']);
                        if (!empty($contents['title'])) {
                            $contents['updated_at'] = time();
                            $meta                   = !empty($contents['id']) ? Browser_Service_ReplaceSearch::getDao()->get($contents['id']) : false;
                            if ($meta) {
                                $out = Browser_Service_ReplaceSearch::getDao()->update($contents, $contents['id']);
                            } else {
                                $t = Browser_Service_ReplaceSearch::getDao()->getBy(array('url' => $contents['url']));
                                if ($t['id']) {
                                    continue;
                                }
                                $contents['created_at'] = time();
                                $out                    = Browser_Service_ReplaceSearch::getDao()->insert($contents);//添加
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