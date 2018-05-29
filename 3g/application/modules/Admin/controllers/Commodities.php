<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class CommoditiesController extends Admin_BaseController {

    /**
     * 用户中心----金币兑换商品模块
     * @var unknown
     */
    public $actions = array(
        'indexUrl'      => '/Admin/Commodities/index',
        'addUrl'        => '/Admin/Commodities/add',
        'addPostUrl'    => '/Admin/commodities/addPost',
        'editUrl'       => '/Admin/Commodities/edit',
        'editPostUrl'   => '/Admin/Commodities/editPost',
        'deleteUrl'     => '/Admin/Commodities/delete',
        'uploadUrl'     => '/Admin/Commodities/upload',
        'uploadPostUrl' => '/Admin/Commodities/upload_post',
        'summaryUrl'    => '/Admin/Commodities/summary',
        'orderUrl'      => '/Admin/Commodities/order',
        'importUrl'     => '/Admin/Commodities/import',
        'exportUrl'     => '/Admin/Commodities/export',
    );

    public $pageSize = 20;

    public $types = array(
        '1' => '虚拟商品',
        '2' => '实物商品',
    );

    public function indexAction() {
        $postData = $this->getInput(array('page', 'status', 'cat_id'));
        $page     = max($postData['page'], 1);
        $where    = array();
        if (isset($postData['status']) && $postData['status'] >= 0) {
            $where['status'] = $postData['status'];
        }
        if (!empty($postData['cat_id'])) {
            $where['cat_id'] = $postData['cat_id'];
        }
        $where['event_flag'] = 0;
        list($total, $data) = User_Service_Commodities::getList($page, $this->pageSize, $where, array('id' => 'DESC'));
        foreach ($data as $k => $v) {
            $category             = User_Service_Category::get($v['cat_id']);
            $data[$k]['cat_name'] = $category['name'];
        }
        $catList = User_Service_Category::getsBy(array("status" => 1, "group_id" => 3), array("id" => 'DESC'));
        $this->assign('cateList', $catList);
        $this->assign('data', $data);
        $this->assign('statusList', array('0' => '关闭', '1' => '开启'));
        $this->assign('status', $postData['status']);
        $this->assign('cat_id', $postData['cat_id']);
        $this->assign('pager', Common::getPages($total, $page, $this->pageSize, $this->actions['indexUrl'] . "/?"));
        $this->assign('goodsType', $this->types);
    }

    public function editAction() {
        $id   = $this->getInput('id');
        $data = array();
        if (intval($id)) {
            $data = User_Service_Commodities::get($id);
            if ($data['goods_type'] == 1) {//为虚拟商品才查询相关信息
                $cardMsg         = User_Service_CardInfo::get($data['card_info_id']);
                $cardList        = User_Service_CardInfo::getsBy(array('type_id' => $cardMsg['type_id']));
                $subVirtualTypes = User_Service_CardInfo::getCetegory(array('group_type' => $data['virtual_type_id']), array("type_id"));
                $this->assign('subVirtualTypes', $subVirtualTypes);
                $this->assign('cardList', $cardList);
                $this->assign('cardMsg', $cardMsg);
            }
        }
        $catList = User_Service_Category::getsBy(array('status' => 1, 'group_id' => 3), array(
            'sort' => 'DESC',
            'id'   => 'DESC'
        ));
        $this->assign('category', $catList);
        $virtualTypes = Common::getConfig('userConfig', 'virtual_type_list');
        $this->assign('virtualTypes', $virtualTypes);
        $this->assign('goodsType', $this->types);
        $this->assign('data', $data);
    }

    public function editPostAction() {
        $postData                = $this->getInput(array(
            'id',
            'cat_id',
            'name',
            'link',
            'price',
            'start_time',
            'end_time',
            'number',
            'is_special',
            'goods_type',
            'scores',
            'sort',
            'image',
            'status',
            'card_info_id',
            'virtual_type_id',
            'title',
            'event_flag',
        	'num_ratio',
        	'show_number',
        ));
        $postData['description'] = htmlspecialchars($_POST['description']);
        if (intval($postData['card_info_id'])) {
            $cardInfo = User_Service_CardInfo::get($postData['card_info_id']);
            /* if ($cardInfo['card_value'] != $postData['price']) {
                $this->output('-1', '商品单价和面值不相同！');
            } */
            /* 			if (($cardInfo['card_value'] * 20) > $postData['scores']) {
                            $this->output('-1', '你确定所填的金币合理吗(金币数>=20倍面值)？');
                        } */
        }
        /* if(empty($postData['image'])){
             $this->output('-1','请选择图片！');
         } */

        if (intval($postData['id'])) {
            $postData['edit_time'] = time();
            $postData['edit_user'] = $this->userInfo['username'];
            $res                   = User_Service_Commodities::update($postData, $postData['id']);
        } else {
            $res = User_Service_Commodities::add($postData);
        }
        if ($res) {
            Admin_Service_Log::op($postData);
            User_Service_Commodities::getGoodsList(true);
            $this->output('0', '编辑成功！');
        } else {
            $this->output('-1', '编辑失败！');
        }
    }

    public function deleteAction() {
        $id  = $this->getInput('id');
        $res = User_Service_Commodities::delete($id);
        if ($res) {
            Admin_Service_Log::op($id);
            User_Service_Commodities::getGoodsList(true);
            $this->output('0', '编辑成功！');
        } else {
            $this->output('-1', '编辑失败！');
        }
    }

    // ajax 获取可以设置用户等级权限的商品信息
    public function ajaxGetDataAction() {
        $cat_id = $this->getInput('type');
        if (!$cat_id) {
            echo json_encode(array('key' => '-1', 'msg' => '请选择正确的商品类别！'));
            exit;
        }
        $params['status'] = 1;
        //$params['is_special'] 	=1;
        $params['start_time'] = array('<=', time());
        $params['end_time']   = array('>=', time());
        $params['cat_id']     = $cat_id;
        $dataList             = User_Service_Commodities::getsBy($params, array('sort' => 'DESC', 'id' => "DESC"));
        if ($dataList) {
            $result = array('key' => '1', 'msg' => $dataList);
        } else {
            $result = array('key' => '-1', 'msg' => '没有合适的商品或其它错误，请检查！');
        }
        echo json_encode($result);
    }

    //ajax得到礼品卡分类信息
    public function AjaxGetCardMsgAction() {
        $postData = $this->getInput(array('type_id', 'group_type'));
        if ($postData['type_id']) {
            $data = User_Service_CardInfo::getsBy(array('type_id' => $postData['type_id']));
        } else {
            $params = array();
            $group  = array('type_id');
            if ($postData['group_type']) {
                $params['group_type'] = $postData['group_type'];
            }
            $data = User_Service_CardInfo::getCetegory($params, $group);
        }
        $this->output('0', '', ($data));
    }

    //订单管理
    public function orderAction() {
        $page = $this->getInput('page');
        list($total, $dataList) = User_Service_Order::getList($page, $this->pageSize, array(), array('id' => 'DESC'));
        foreach ($dataList as $k => $v) {
        }
        $this->assign('data', $dataList);
        $status = Common::getConfig('userConfig', 'statusFlag');
        $this->assign('status', $status);
        $this->assign('pager', Common::getPages($total, $page, $this->pageSize, $this->actions['orderUrl'] . "/?"));

    }

    //数据导入
    public function importAction() {
        $postData = $this->getInput(array('page', 'type_id', 'group_type'));
        $page     = max($postData['page'], 1);
        if (!empty($_FILES['data'])) {
            $file = $_FILES['data']['tmp_name'];
            $res  = $this->_importData($file);
            $this->output('0', '操作成功！');
        }
        $params = array();
        if (!empty($postData['group_type'])) {
            $params['group_type'] = $postData['group_type'];
        }
        if (!empty($postData['type_id'])) {
            $params['type_id'] = $postData['type_id'];
        }
        list($total, $dataList) = User_Service_CardInfo::getList($page, $this->pageSize, $params, array('card_value' => 'ASC'));
        $this->assign('dataList', $dataList);
        $this->assign('pager', Common::getPages($total, $page, $this->pageSize, $this->actions['importUrl'] . "?"));
        $this->assign('config', Common::getConfig('userConfig', 'ofpay_api_log'));
        $types = User_Service_CardInfo::getCetegory(array(), array('type_id'));
        $this->assign('types', $types);
        $this->assign('params', $postData);
    }

    //数据导出
    public function exportAction() {
        $res = $this->_exportData();
        exit();
    }

    //上传图片
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
    public function upload_postAction() {
        $ret   = Common::upload('img', 'commondity');
        $imgId = $this->getPost('imgId');
        $this->assign('imgId', $imgId);
        $this->assign('data', $ret['data']);
        $this->assign('code', $ret['code']);
        $this->getView()->display('common/upload.phtml');
        exit;
    }

    public function _importData($file) {
        $fields = array('id', 'group_type', 'type_id', 'type_name', 'card_id', 'card_name', 'card_value', 'ext');
        $num    = count($fields);
        $row    = 1;//初始值
        if (($handle = fopen($file, "r")) !== false) {
            while (($data = fgetcsv($handle, 1000, ",")) !== false) {
                if ($row > 1) {
                    $contents = array();
                    for ($i = 0; $i < $num; $i++) {
                        $contents[$fields[$i]] = $data[$i];
                    }
                    $contents['type_name'] = iconv('GBK', 'UTF8', $contents['type_name']);
                    $contents['card_name'] = iconv('GBK', 'UTF8', $contents['card_name']);
                    if (!empty($contents['id']) && !empty($contents['card_id']) && !empty($contents['type_id'])) { //更新
                        $meta = User_Service_CardInfo::get($contents['id']);
                        if ($meta) {
                            $out = User_Service_CardInfo::update($contents['id'], $contents);
                        } else {
                            $out = User_Service_CardInfo::add($contents);//添加
                        }
                    } else {
                        unset($contents['id']);
                        $out = User_Service_CardInfo::add($contents);//添加
                    }
                }
                $row++;
            }
        }
        fclose($handle);
    }


    private function _exportData() {
        $filename = '礼品卡信息' . date('YmdHis') . '.csv';
        $list     = User_Service_CardInfo::getAll();
        $headers  = array('id', 'group_type', 'type_id', 'type_name', 'card_id', 'card_name', 'card_value', 'ext');
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment;filename=' . $filename);
        $fp = fopen('php://output', 'w');

        fputcsv($fp, $headers);
        foreach ($list as $fields) {
            $row = array(
                $fields['id'],
                $fields['group_type'],
                $fields['type_id'],
                $fields['type_name'] = iconv('UTF8', 'GBK', $fields['type_name']),
                $fields['card_id'],
                $fields['card_name'] = iconv('UTF8', 'GBK', $fields['card_name']),
                $fields['card_value']
            );
            fputcsv($fp, $row);
        }
        fclose($fp);
        exit;
    }
}