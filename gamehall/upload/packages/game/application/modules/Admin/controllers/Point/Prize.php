<?php
if (!defined('BASE_PATH')) {
    exit('Access Denied!');
}

/**
 * 积分抽奖
 * @author fanch
 *
 */
class Point_PrizeController extends Admin_BaseController {
    public $actions = array(
        'pointShopIndexUrl' => '/Admin/Mall_Goods/index',
        'pointPrizeIndexUrl' => '/Admin/Point_Prize/index',
        'presendIndexUrl' => '/Admin/Mall_Goods/presendIndex',
        'addUrl' => '/Admin/Point_Prize/add',
        'addPostUrl' => '/Admin/Point_Prize/addPost',
        'editUrl' => '/Admin/Point_Prize/edit',
        'editPostUrl' => '/Admin/Point_Prize/editPost',
        'configUrl' => '/Admin/Point_Prize/config',
        'configPostUrl' => '/Admin/Point_Prize/configPost',
        'logUrl' => '/Admin/Point_Prize/log',
        'sendUrl' => '/Admin/Point_Prize/send',
        'exportUrl' => '/Admin/Point_Prize/export',
        'uploadUrl' => '/Admin/Point_Prize/upload',
        'uploadPostUrl' => '/Admin/Point_Prize/uploadPost',
        'blackListUrl' => '/Admin/Point_Prize/blackList',
        'addBlackListUrl' => '/Admin/Point_Prize/addBlackList',
        'addBlackListPostUrl' => '/Admin/Point_Prize/addBlackListPost',
        'delBlackListUrl' => '/Admin/Point_Prize/delBlackList'
    );

    public $perpage = 10;

    public function indexAction() {
        $page = intval($this->getInput('page'));
        if ($page < 1) {
            $page = 1;
        }
        $info = $this->getInput(array('title', 'status', 'start_time', 'end_time'));
        $params = array();
        $startTime = $endTime = Common::getTime();
        if ($info['title']) {
            $params['title'] = array('LIKE', trim($info['title']));
        }
        if ($info['start_time']) {
            $params['start_time'] = strtotime($info['start_time']);
            $startTime = strtotime($info['start_time']);
        }
        if ($info['end_time']) {
            $params['end_time'] = strtotime($info['end_time']);
            $endTime = strtotime($info['end_time']);
        }
        if ($info['status']) {
            switch ($info['status']) {
                case '1': //未开始
                    $params['start_time'] = array('>', $startTime);
                    break;
                case '2': //进行中
                    $params['start_time'] = array('<=', $startTime);
                    $params['end_time'] = array('>=', $endTime);
                    break;
                case '3': //已结束
                    $params['end_time'] = array('<', $endTime);
                    break;
            }
        }

        list($total, $data) = Point_Service_Prize::getList($page, $this->perpage, $params);
        $this->assign('data', $data);
        $this->assign('total', $total);
        $this->assign('search', $info);
        $url = $this->actions['pointPrizeIndexUrl'] . '/?' . http_build_query($info) . '&';
        $this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
    }

    public function addAction() {
        $this->assign('type', $this->type);
    }

    public function editAction() {
        $prizeId = $this->getInput('id');
        $prizeData = Point_Service_Prize::getPrize($prizeId);
        $configData = Point_Service_Prize::getConfig($prizeId);
        $configData = $this->buildPrizeConfigData($configData);
        $this->assign('prize', $prizeData);
        $this->assign('config', $configData);
    }

    public function addPostAction() {
        $prizeInfo = $this->getInput(array('title', 'start_time', 'end_time', 'lotteryMode','img', 'version', 'point', 'descript', 'status'));
        $configInfo = $this->getInput('list');
        $configInfo = Common::resetKey($configInfo, 'index');
        $prizeData = $this->cookData($prizeInfo);

        //重叠区间判断
        $items = Point_Service_Prize::getAllPrize(array('id' => 'DESC'));
        $check = $this->_checkRegion($prizeData, $items);
        if (!$check) {
            $this->output(-1, '添加的时间区间，不能出现重叠。');
        }

        $configData = $this->cookConfigData($configInfo);
        $ret = Point_Service_Prize::save($prizeData, $configData);
        if (!$ret) {
            $this->output(-1, '操作失败');
        }
        $this->output(0, '操作成功');
    }

    public function editPostAction() {
        $prizeInfo = $this->getInput(array('id', 'title', 'start_time', 'end_time', 'lotteryMode', 'img', 'version', 'point', 'descript', 'status'));
        $configInfo = $this->getInput('list');
        $configInfo = Common::resetKey($configInfo, 'index');
        $prizeData = $this->cookData($prizeInfo);

        //重叠区间判断
        $items = Point_Service_Prize::getsByPrize(array('id' => array('!=', $prizeInfo['id'])), array('id' => 'DESC'));
        $check = $this->_checkRegion($prizeData, $items);
        if (!$check) {
            $this->output(-1, '添加的时间区间，不能出现重叠。');
        }
        $configData = $this->cookConfigData($configInfo);
        $ret = Point_Service_Prize::update($prizeData, $configData);
        if (!$ret) {
            $this->output(-1, '操作失败');
        }
        $this->output(0, '操作成功');
    }

    public function logAction() {
        $page = intval($this->getInput('page'));
        if ($page < 1) {
            $page = 1;
        }
        $info = $this->getInput(array('id', 'uname', 'prize_status', 'type', 'send_status', 'prize_start_time', 'prize_end_time', 'send_start_time', 'send_end_time'));
        $params = array();
        $params['prize_id'] = $info['id'];
        if ($info['prize_status']) {
            $params['prize_status'] = $info['prize_status'] - 1;
        }
        if ($info['type']) {
            $configIds = $this->getConfigIds($info['id'], ($info['type'] - 1));
            if (!empty($configIds)) {
                $params['prize_cid'] = array('IN', $configIds);
            } else {
                $params['prize_cid'] = 0;
            }
        }
        if ($info['send_status']) {
            $params['send_status'] = $info['send_status'] - 1;
        }
        if ($info['prize_start_time']) {
            $params['create_time'] = array('>=', strtotime($info['prize_start_time']));
        }
        if ($info['prize_end_time']) {
            $params['create_time'] = array('<=', strtotime($info['prize_end_time']));
        }

        if ($info['prize_start_time'] && $info['prize_end_time']) {
            $params['create_time'] = array(array('>=', strtotime($info['prize_start_time'])), array('<=', strtotime($info['prize_end_time'])));
        }

        if ($info['send_start_time']) {
            $params['send_time'] = array('>=', strtotime($info['send_start_time']));
        }
        if ($info['send_end_time']) {
            $params['send_time'] = array('<=', strtotime($info['send_end_time']));
        }

        if ($info['send_start_time'] && $info['send_end_time']) {
            $params['send_time'] = array(array('>=', strtotime($info['send_start_time'])), array('<=', strtotime($info['send_end_time'])));
        }

        if ($info['uname']) {
            $params['uname'] = array('LIKE', trim($info['uname']));
        }


        $configData = Point_Service_Prize::getConfig($info['id']);
        $configData = Common::resetKey($configData, 'id');
        list($total, $data) = Point_Service_Prize::getListLog($page, $this->perpage, $params);

        $this->assign('config', $configData);
        $this->assign('data', $data);
        $this->assign('total', $total);
        $this->assign('search', $info);
        $url = $this->actions['logUrl'] . '/?' . http_build_query($info) . '&';
        $this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
    }

    private function getConfigIds($prizeId, $type) {
        $temp = array();
        $data = Point_Service_Prize::getsByConfig(array('prize_id' => $prizeId, 'type' => $type));
        foreach ($data as $value) {
            if ($value['type'] == $type) {
                $temp[] = $value['id'];
            }
        }
        return $temp;
    }

    /**
     * 发放操作
     */
    public function sendAction() {
        $logId = $this->getInput('id');
        $result = Point_Service_Prize::updateLog(array('send_status' => 1, 'send_time' => time()), array('id' => $logId));
        if (!$result) {
            $this->output(-1, '操作失败');
        }
        $this->output(0, '操作成功');
    }

    /**
     * 配置页面
     */
    public function configAction() {
        $data = Game_Service_Config::getValue('point_prize_close');
        $this->assign('closeImg', $data);
    }

    /**
     * 配置提交
     */
    public function configPostAction() {
        $closeImg = $this->getInput('closeImg');
        $result = Game_Service_Config::setValue('point_prize_close', $closeImg);
        if (!$result) {
            $this->output(-1, '操作失败');
        }
        $this->output(0, '操作成功');
    }

    public function blackListAction() {
        $page = intval($this->getInput('page'));
        if ($page < 1) {
            $page = 1;
        }
        $info = $this->getInput(array('uuid'));
        $params = array();
        if ($info['uuid']) {
            $params = array('uuid' => $info['uuid']);
        }
        list($total, $data) = Point_Service_BlackList::getList($page, $this->perpage, $params);
        $this->assign('data', $data);
        $this->assign('total', $total);
        $this->assign('search', $info);
        $url = $this->actions['pointPrizeIndexUrl'] . '/?' . http_build_query($info) . '&';
        $this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
    }

    public function addBlackListAction() {
    }

    public function addBlackListPostAction() {
        $uuids = $this->getInput('uuids');
        $uuidArr = $this->cookBlackListData($uuids);
        foreach($uuidArr as $uuid){
            if($uuid) {
                $item = Point_Service_BlackList::getBy(array('uuid'=>$uuid));
                if(!$item) {
                    $addData = array('uuid' => $uuid, 'operator' => $this->userInfo['username'], 'create_time' => Common::getTime());
                    $result = Point_Service_BlackList::add($addData);
                    if (!$result) {
                        $this->output(-1, '操作失败');
                    }
                }
            }
        }
        $this->output(0, '操作成功');
    }

    public function delBlackListAction() {
        $id = $this->getInput('id');
        if (!$id) {
            $this->output(-1, '参数错误');
        }
        $result = Point_Service_BlackList::delete($id);
        if (!$result) {
            $this->output(-1, '操作失败');
        }
        $this->output(0, '操作成功');
    }

    /**
     * 导出操作
     */
    public function exportAction() {
        $page = intval($this->getInput('page'));
        $info = $this->getInput(array('id', 'uname', 'prize_status', 'type', 'send_status', 'prize_start_time', 'prize_end_time', 'send_start_time', 'send_end_time'));
        if ($page < 1) {
            $page = 1;
        }
        $params = array();
        $params['prize_id'] = $info['id'];
        if ($info['prize_status']) {
            $params['prize_status'] = $info['prize_status'] - 1;
        }
        if ($info['type']) {
            $configIds = $this->getConfigIds($info['id'], ($info['type'] - 1));
            if (!empty($configIds)) {
                $params['prize_cid'] = array('IN', $configIds);
            } else {
                $params['prize_cid'] = 0;
            }
        }
        if ($info['send_status']) {
            $params['send_status'] = $info['send_status'] - 1;
        }
        if ($info['prize_start_time']) {
            $params['create_time'] = array('>=', strtotime($info['prize_start_time']));
        }
        if ($info['prize_end_time']) {
            $params['create_time'] = array('<=', strtotime($info['prize_end_time']));
        }
        if ($info['prize_start_time'] && $info['prize_end_time']) {
            $params['create_time'] = array(array('>=', strtotime($info['prize_start_time'])), array('<=', strtotime($info['prize_end_time'])));
        }
        if ($info['send_start_time']) {
            $params['send_time'] = array('>=', strtotime($info['send_start_time']));
        }
        if ($info['send_end_time']) {
            $params['send_time'] = array('<=', strtotime($info['send_end_time']));
        }
        if ($info['send_start_time'] && $info['send_end_time']) {
            $params['send_time'] = array(array('>=', strtotime($info['send_start_time'])), array('<=', strtotime($info['send_end_time'])));
        }
        if ($info['uname']) {
            $params['uname'] = trim($info['uname']);
        }

        $configData = Point_Service_Prize::getConfig($info['id']);
        $configData = Common::resetKey($configData, 'id');

        //excel-head
        $filename = "抽奖数据_" . date('YmdHis', Common::getTime());
        Util_Csv::putHead($filename);
        $title = array(array('账号', '抽奖时间', '中奖状态', '奖项', '发放状态', '发放时间', '收货人', '联系电话', '收获地址'));
        Util_Csv::putData($title);
        //循环分页查询输出

        while (1) {
            list(, $rs) = Point_Service_Prize::getListLog($page, $this->perpage, $params);
            if (!$rs) {
                break;
            }
            $tmp = array();
            foreach ($rs as $key => $item) {
                $tmp[] = array($item['uname'], date('Y-m-d  H:i:s', $item["create_time"]), ($item['prize_status']) ? '已中奖' : '未中', ($item['prize_status'] == 1) ? $configData[$item['prize_cid']]['title'] : '-', ($item['prize_status'] == 1) ? (($item['send_status']) ? '已发' : '未发') : '-', ($item["send_time"]) ? date('Y-m-d  H:i:s', $item["send_time"]) : '-', ($item['receiver']) ? $item['receiver'] : '-', ($item['mobile']) ? $item['mobile'] : '-', ($item['address']) ? $item['address'] : '-',);
            }
            Util_Csv::putData($tmp);
            $page++;
        }

        exit;
    }

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
    public function uploadPostAction() {
        $ret = Common::upload('img', 'prize');
        $imgId = $this->getPost('imgId');
        $this->assign('code', $ret['data']);
        $this->assign('msg', $ret['msg']);
        $this->assign('data', $ret['data']);
        $this->assign('imgId', $imgId);
        $this->getView()->display('common/upload.phtml');
        exit;
    }

    /**
     * 抽奖活动参数过滤
     * @param array $info
     * @return array
     */
    private function cookData($info) {
        if (!$info['title']) {
            $this->output(-1, '抽奖活动名称不能为空.');
        }
        if (!$info['start_time']) {
            $this->output(-1, '抽奖活动开始时间不能为空.');
        }
        if (!$info['end_time']) {
            $this->output(-1, '抽奖活动结束时间不能为空.');
        }
        $info['start_time'] = strtotime($info['start_time']);
        $info['end_time'] = strtotime($info['end_time']);
        if ($info['end_time'] <= $info['start_time']) {
            $this->output(-1, '开始时间不能小于结束时间.');
        }
        if (!$info['img']) {
            $this->output(-1, '抽奖活动图片不能为空.');
        }
        if (!$info['point']) {
            $this->output(-1, '抽奖活动消耗积分数不能为空.');
        }
        if (!$info['descript']) {
            $this->output(-1, '抽奖活动描述不能为空.');
        }
        $info['type'] = $info['lotteryMode'];
        unset($info['lotteryMode']);
        return $info;
    }

    private function _checkRegion($info, $items) {
        $flag = true;
        if (!$items) {
            return $flag;
        }
        foreach ($items as $value) {
            if ((intval($info['start_time']) <= intval($value['end_time'])) && (intval($value['start_time']) <= intval($info['end_time']))) {
                $flag = false;
                break;
            }
        }
        return $flag;
    }

    private function cookBlackListData($uuids){
        if(trim($uuids)==""){
            $this->output(-1, 'uuid不能为空');
        }
        $uuidArr  = explode('|',trim($uuids));
        if(empty($uuidArr)){
            $this->output(-1, 'uuid不能为空');
        }
        return $uuidArr;
    }

    private function cookConfigData($info) {
        $maxRate = Point_Service_Prize::$_maxRate;
        $temp = array();
        $flag = $sumRate = 0;
        for ($i = 1; $i <= 8; $i++) {
            $temp[] = $this->cookConfigItem($info[$i], $i);
            if ($info[$i]['type'] == 0) {
                $flag++;
            }
            $sumRate+=$info[$i]['probability'];
        }
        if ($flag == 0) {
            $this->output(-1, "奖项至少要配置一个未中的奖品.");
        }
        if ($flag == 8) {
            $this->output(-1, "奖项不能全部都是未中的奖品.");
        }
        if ($sumRate > $maxRate) {
            $this->output(-1, "奖项概率累计不能超过{$maxRate}.");
        }
        return $temp;
    }

    private function cookConfigItem($info, $i) {
        $temp = array();
        if ($info['cId']) {
            $temp['id'] = $info['cId'];
        }
        $temp['pos'] = $info['index'];
        $temp['type'] = $info['type'];
        $temp['sub_type'] = $info['leastType'];
        if (!$info['prizeName']) {
            $this->output(-1, "位置{$i} 奖品名称不能为空.");
        }
        $temp['title'] = $info['prizeName'];
        if (!$info['smallPoster']) {
            $this->output(-1, "位置{$i} 奖品小图片不能为空.");
        }
        $temp['small_img'] = $info['smallPoster'];
        if (!$info['bigPoster']) {
            $this->output(-1, "位置{$i} 奖品大图片不能为空.");
        }
        $temp['img'] = $info['bigPoster'];
        if (intval($info['probability']) < 0) {
            $this->output(-1, "位置{$i} 奖品概率不能小于0.");
        }
        $temp['probability'] = $info['probability'];
        if (intval($info['leastDate']) < 0) {
            $this->output(-1, "位置{$i} 奖品最小时间间隔不能小于0.");
        }
        $temp['min_space'] = $info['leastDate'];
        if (intval($info['maxQuantity']) < 0) {
            $this->output(-1, "位置{$i} 奖品最大发放数量不能小于0.");
        }
        $temp['max_win'] = $info['maxQuantity'];
        if (intval($info['lotteryNumber']) < 0) {
            $this->output(-1, "位置{$i} 奖品抽奖次数不能小于0.");
        }
        $temp['max_times'] = $info['lotteryNumber'];

        switch ($info['type']) {
            case '2':
                if (!isset($info['amount'])) {
                    $this->output(-1, "位置{$i} A券数量不能为空.");
                }
                if ($info['amount'] <= 0) {
                    $this->output(-1, "位置{$i} A券数量不能小于等于0.");
                }
                $temp['amount'] = $info['amount'];
                if (!isset($info['validityPeriod'])) {
                    $this->output(-1, "位置{$i} A券有效期不能为空.");
                }
                if ($info['validityPeriod'] <= 0) {
                    $this->output(-1, "位置{$i} A券有效期不能小于等于0.");
                }
                $temp['day'] = $info['validityPeriod'];
                break;
            case '3':
                if (!isset($info['amount'])) {
                    $this->output(-1, "位置{$i} 积分数量不能为空.");
                }
                if ($info['amount'] <= 0) {
                    $this->output(-1, "位置{$i} 积分数量不能小于等于0.");
                }
                $temp['amount'] = $info['amount'];
                break;
            case '0':
                if($info['leastType'] == '1'){
                    if (!isset($info['integralNumber'])) {
                        $this->output(-1, "位置{$i} 最低奖项积分数量不能为空.");
                    }
                    if ($info['integralNumber'] <= 0) {
                        $this->output(-1, "位置{$i} 最低奖项积分数量不能小于等于0.");
                    }
                    $temp['sub_amount'] = $info['integralNumber'];
                }
        }
        return $temp;
    }

    private function buildPrizeConfigData($data){
        $result = array();
        foreach($data as $item){
            $result[]=array(
                'cId' => $item['id'],
                'prizeName' => $item['title'],
                'index' => $item['pos'],
                'bigPoster' => $item['img'],
                'smallPoster' => $item['small_img'],
                'integralNumber' => $item['sub_amount'],
                'type' => $item['type'],
                'leastType' => $item['sub_type'],
                'amount' => $item['amount'],
                'validityPeriod' => $item['day'],
                'probability' => $item['probability'],
                'leastDate' => $item['min_space'],
                'maxQuantity' => $item['max_win'],
                'lotteryNumber' => $item['max_times'],
            );
        }
        return $result;
    }

}