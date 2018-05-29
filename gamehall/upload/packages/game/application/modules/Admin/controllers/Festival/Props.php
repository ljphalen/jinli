<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 *
 * 道具兑换活动
 * @author fanch
 *
 */
class Festival_PropsController extends Admin_BaseController {

    public $actions = array(
        'indexUrl' => '/Admin/Festival_Props/index',
        'addStep1Url' => '/Admin/Festival_Props/addStep1',
        'addStep2Url' => '/Admin/Festival_Props/addStep2',
        'addStep3Url' => '/Admin/Festival_Props/addStep3',
        'addStep4Url' => '/Admin/Festival_Props/addStep4',
        'addStep1PostUrl' => '/Admin/Festival_Props/addStep1Post',
        'addStep2PostUrl' => '/Admin/Festival_Props/addStep2Post',
        'addStep3PostUrl' => '/Admin/Festival_Props/addStep3Post',
        'addPostUrl' => '/Admin/Festival_Props/addPost',
        'editStep1Url' => '/Admin/Festival_Props/editStep1',
        'editStep2Url' => '/Admin/Festival_Props/editStep2',
        'editStep3Url' => '/Admin/Festival_Props/editStep3',
        'editStep4Url' => '/Admin/Festival_Props/editStep4',
        'editStep1PostUrl' => '/Admin/Festival_Props/editStep1Post',
        'editStep2PostUrl' => '/Admin/Festival_Props/editStep2Post',
        'editStep3PostUrl' => '/Admin/Festival_Props/editStep3Post',
        'editPostUrl' => '/Admin/Festival_Props/editPost',
        'grantUrl' => '/Admin/Festival_Props/grant',
        'grantExportUrl' => '/Admin/Festival_Props/grantExport',
        'sendPrizeUrl' => '/Admin/Festival_Props/sendPrize',
        'exchangeUrl' => '/Admin/Festival_Props/exchange',
        'exchangeDetailUrl' => '/Admin/Festival_Props/exchangeDetail',
        'exchangeExportUrl' => '/Admin/Festival_Props/exchangeExport',
        'addGamesUrl' => '/Admin/Festival_Props/addGames',
        'uploadUrl' => '/Admin/Festival_Props/upload',
        'uploadPostUrl' => '/Admin/Festival_Props/upload_post',
        'uploadImgUrl' => '/Admin/Festival_Props/uploadImg'
    );

    public $perpage = 20;
    public $prizeType = array(
        Festival_Service_BaseInfo::PRIZE_TYPE_ENTITY => '实体',
        Festival_Service_BaseInfo::PRIZE_TYPE_ACOUPON => 'A券',
        Festival_Service_BaseInfo::PRIZE_TYPE_POINT => '积分',
    );
    private $expire = 86400;
    private $festivalInsertData = array();
    private $festivalEditData = array();

    public function indexAction(){
        $page = $this->getInput('page');
        $input = $this->getInput(array('title', 'status', 'startTime', 'endTime'));
        if ($page < 1) $page = 1;
        $search = $this->getSearchData($input);
        list($total, $items) = Festival_Service_BaseInfo::getList($page, $this->perpage, $search);
        $this->assign('items', $items);
        $this->assign('total', $total);
        $this->assign('search', $input);
        $url = $this->actions['indexUrl'].'/?' . http_build_query($input) . '&';
        $this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
    }

    public function addStep1Action(){
        $clientConfig = Common_Service_Version::getClientVersionConfig();
        $this->assign('clientConfig', $clientConfig);
    }

    public function addStep2Action(){
        $infoData = $this->getFormDataTochache('InsertData','baseInfo');
        if(!$infoData){
            $this->redirect($this->actions['addStep1Url']);
            exit;
        }
    }

    public function addStep3Action(){
        //获取组系列组
        $groupData = $this->getFormDataTochache('InsertData','propsData');
        if(!$groupData){
            $this->redirect($this->actions['addStep2Url']);
            exit;
        }
        $propGroup = array();
        foreach ($groupData['props'] as $key => $item){
            $propGroup[] =array(
                'propGroupId'=>$key,
                'groupName'=>$item['groupName']
            ) ;
        }
        $this->assign('propGroup', $propGroup);
    }

    public function addStep4Action(){
        $propsData = $this->getFormDataTochache('InsertData','propsData');
        if(!$propsData){
            $this->redirect($this->actions['addStep3Url']);
            exit;
        }
        $this->assign('propsData', $propsData);
    }

    public function addStep1PostAction(){
        $input = $this->getInput(array('title','startTime','endTime','bannerImg','description','clientVersion', 'status', 'config'));
        $data = $this->isValidStep1Data($input);
        $this->saveFormDataToCache('InsertData','baseInfo', json_encode($data));
        $this->output(0, '继续添加道具');
    }

    public function addStep2PostAction(){
        $input = $this->getInput(array('propsName', 'props'));
        $data = $this->isValidStep2Data($input);
        $this->saveFormDataToCache('InsertData','propsData', json_encode($data));
        $this->output(0, '继续添加游戏');
    }

    public function addStep3PostAction(){
        $input = $this->getInput(array('propsGroup'));
        $data = $this->isValidStep3Data($input);
        $this->saveFormDataToCache('InsertData','propsGroupData', json_encode($data));
        $this->output(0, '继续添加奖品');
    }

    public function addPostAction(){
        $input = $this->getInput(array('prizeColumnName', 'prizes'));
        $data = $this->isValidStep4Data($input);
        $this->saveFormDataToCache('InsertData','prizesData', json_encode($data));
        $this->saveInsertDataToDb();
        $this->delFormDataCache('InsertData');
        $this->output(0, '操作成功');
    }

    public function editStep1Action(){
        $festivalId = $this->getInput('id');
        $infoData = $this->getFestivalBaseInfo($festivalId);
        $clientConfig = Common_Service_Version::getClientVersionConfig();
        $this->assign('clientConfig', $clientConfig);
        $this->assign('infoData', $infoData);
    }

    public function editStep2Action(){
        $festivalId = $this->getInput('id');
        $infoData = $this->getFestivalBaseInfo($festivalId);
        $groupData = $this->getPropsGroupData($festivalId);
        $this->assign('infoData', $infoData);
        $this->assign('groupData', $groupData);
    }

    public function editStep3Action(){
        $festivalId = $this->getInput('id');
        $infoData = $this->getFestivalBaseInfo($festivalId);
        $groupData = $this->getPropsGroupData($festivalId);
        $this->assign('infoData', $infoData);
        $this->assign('groupData', $groupData);
    }

    public function editStep4Action(){
        $festivalId = $this->getInput('id');
        $infoData = $this->getFestivalBaseInfo($festivalId);
        $prizesData = $this->getFestivalPrizesData($festivalId);
        $groupData = $this->getPropsGroupData($festivalId);
        $this->assign('infoData', $infoData);
        $this->assign('groupData', $groupData);
        $this->assign('prizesData', $prizesData);
    }

    public function editStep1PostAction(){
        $input = $this->getInput(array('id', 'title', 'startTime','endTime','bannerImg','description','clientVersion', 'status', 'config'));
        $data = $this->isValidStep1Data($input);
        $festivalId = $input['id'];
        $this->saveFormDataToCache('EditData' . $festivalId,'baseInfo', json_encode($data));
        $this->output(0, '继续添加道具');
    }

    public function editStep2PostAction(){
        $input = $this->getInput(array('id', 'propsName', 'props'));
        $data = $this->isValidStep2Data($input);
        $festivalId = $input['id'];
        $this->saveFormDataToCache('EditData' . $festivalId, 'propsData', json_encode($data));
        $this->output(0, '继续添加游戏');
    }

    public function editStep3PostAction(){
        $input = $this->getInput(array('id', 'propsGroup'));
        $data = $this->isValidStep3Data($input);
        $festivalId = $input['id'];
        $this->saveFormDataToCache('EditData' . $festivalId,'propsGroupData', json_encode($data));
        $this->output(0, '继续添加奖品');
    }

    public function editPostAction(){
        $input = $this->getInput(array('id', 'prizeColumnName', 'prizes'));
        $data = $this->isValidStep4Data($input);
        $festivalId = $input['id'];
        $this->saveFormDataToCache('EditData' . $festivalId,'prizesData', json_encode($data));
        $this->saveEditDataToDb($festivalId);
        $this->delFormDataCache('EditData' . $festivalId);
        $this->output(0, '操作成功');
    }

    public function grantAction(){
        $festivalId = $this->getInput('id');
        $groupData = Festival_Service_PropGroup::getsBy(array('festival_id'=>$festivalId));
        $data = array();
        foreach ($groupData as $groupItem){
            $propIds = explode(',', $groupItem['prop_ids']);
            foreach ($propIds as $propId){
                $propData = $this->getPropDataById($propId);
                $params = array('festival_id'=>$festivalId, 'prop_id'=> $propId);
                $grantProps = $this->getGrantProps($params);
                $data[] = array(
                    'groupId' => $groupItem['id'],
                    'groupName' => $groupItem['name'],
                    'propId' => $propId,
                    'propName' => $propData['name'],
                    'grantTotal' => count($grantProps)
                );
            }
        }
        $this->assign('festivalId', $festivalId);
        $this->assign('data', $data);
    }

    public function grantExportAction(){
        $input = $this->getInput(array('festivalId', 'propId'));
        $propData = $this->getPropDataById($input['propId']);
        //excel-head
        $filename = "道具[{$propData['name']}]发放记录_".date('YmdHis', Common::getTime());
        Util_Csv::putHead($filename);
        $title = array(array('uuid', '活动名称', '道具名称','游戏名称', '获得时间'));
        Util_Csv::putData($title);
        //循环分页查询输出
        $page=1;
        $search = array(
            'festival_id'=>$input['festivalId'],
            'prop_id'=> $input['propId']
        );
        $festivalInfo = $this->getFestivalBaseInfo($input['festivalId']);
        while(1){
            list(, $rs) = Festival_Service_PropsGrant::getList($page, $this->perpage, $search);
            if (!$rs) break;
            $tmp = array();
            foreach ($rs as $key=>$item) {
                $gameInfo = Resource_Service_GameData::getBasicInfo($item['game_id']);
                $tmp[] = array(
                    $item['uuid'],
                    $festivalInfo['title'],
                    $propData['name'],
                    $gameInfo['name'],
                    date('Y-m-d H:i:s', $item["create_time"]),
                );
            }
            Util_Csv::putData($tmp);
            $page ++;
        }
        exit;
    }

    public function exchangeAction(){
        $festivalId = $this->getInput('id');
        $prizesData = Festival_Service_Prizes::getsBy(array('festival_id'=>$festivalId));
        $data = array();
        foreach ($prizesData as $prizeItem){
            $exchangesData = $this->getPrizeExchanges($festivalId, $prizeItem['id']);
            $exchangeTotal =count($exchangesData);
            $data[]=array(
                'prizeId' => $prizeItem['id'],
                'prizeName' => $prizeItem['name'],
                'prizeTotal' => $prizeItem['total'],
                'exchangeTotal' => $exchangeTotal,
                'availableTotal' => $this->getAvailablePrizeTotal($festivalId, $prizeItem['id'])
            );
        }
        $this->assign('festivalId', $festivalId);
        $this->assign('data', $data);
    }

    public function exchangeDetailAction(){
        $page = $this->getInput('page');
        if ($page < 1) $page = 1;
        $input = $this->getInput(array('festivalId', 'prizeId'));
        $prizeData = $this->getPrizeDataById($input['prizeId']);
        $search = array(
            'festival_id'=>$input['festivalId'],
            'prize_id'=> $input['prizeId']
        );
        list($total, $exchangeData) = Festival_Service_PrizeExchanges::getList($page, $this->perpage, $search);

        $this->assign('prizeData', $prizeData);
        $this->assign('total', $total);
        $this->assign('exchangeData', $exchangeData);
        $url = $this->actions['indexUrl'].'/?' . http_build_query($input) . '&';
        $this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
    }

    public function exchangeExportAction(){
        $input = $this->getInput(array('festivalId', 'prizeId'));
        $prizeData = $this->getPrizeDataById($input['prizeId']);

        //excel-head
        $filename = "兑换[{$prizeData['name']}]记录_".date('YmdHis', Common::getTime());
        Util_Csv::putHead($filename);
        $title = array(array('uuid', '奖品', '兑奖联系人','电话号码', '收货地址', '发放状态', '兑换时间'));
        Util_Csv::putData($title);
        //循环分页查询输出
        $page=1;
        $search = array(
            'festival_id'=>$input['festivalId'],
            'prize_id'=> $input['prizeId']
        );
        while(1){
            list(, $rs) = Festival_Service_PrizeExchanges::getList($page, $this->perpage, $search);
            if (!$rs) break;
            $tmp = array();
            foreach ($rs as $key=>$item) {
                $tmp[] = array(
                    $item['uuid'],
                    $prizeData['name'],
                    ($prizeData['prize_type']==1) ? ($item['contact'] ? $item['contact'] : '--') : '--',
                    ($prizeData['prize_type']==1) ? ($item['phone'] ? $item['phone'] : '--') : '--',
                    ($prizeData['prize_type']==1) ? ($item['address'] ? $item['address'] : '--') : '--',
                    ($item['status']) ? '已发放' : '未发放',
                    date('Y-m-d H:i:s', $item["create_time"]),
                );
            }
            Util_Csv::putData($tmp);
            $page ++;
        }
        exit;
    }

    public function sendPrizeAction(){
        $id = $this->getInput('id');
        $result = Festival_Service_PrizeExchanges::updateBy(array('status'=>1),array('id'=>$id));
        if (!$result) $this->output(-1, '操作失败');
        $this->output(0, '操作成功');
    }

    //-------------------------查询记录-导出数据--------------------------
    private function getPropDataById($propId){
        $propData =  Festival_Service_Props::getBy(array('id'=>$propId));
        return $propData;
    }

    private function getPrizeDataById($prizeId){
        $prizeData =  Festival_Service_Prizes::getBy(array('id'=>$prizeId));
        return $prizeData;
    }

    private function getGrantProps($params){
        $grantData = Festival_Service_PropsGrant::getsBy($params);
        return $grantData;
    }

    private function getAvailablePrizeTotal($festivalId, $prizeID){
        $prizeData = Festival_Service_Prizes::getBy(array('id'=> $prizeID));
        $prizePropIds = explode(',', $prizeData['condition']);
        $availableTotal = 0;
        $uuidArr = array();
        foreach ($prizePropIds as $key => $propId){
            $params = array('festival_id'=>$festivalId, 'prop_id'=> $propId, 'remain_total' => array('>', 0));
            if($key != 0){
                $params['uuid'] = array('IN', $uuidArr);
            }
            $data = Festival_Service_PropsTotals::getsBy($params);
            if(!$data){
                $availableTotal = 0;
                break;
            }
            $data = Common::resetKey($data, 'uuid');
            $uuidArr = array_unique(array_keys($data));
            $availableTotal = count($uuidArr);
        }
        return $availableTotal;
    }

    private function getPrizeExchanges($festivalId, $prizeID){
        $exchangesData = Festival_Service_PrizeExchanges::getsBy(array('festival_id'=>$festivalId, 'prize_id'=> $prizeID));
        return $exchangesData;
    }

    //------------------编辑操作核心代码------------------------------------
    private function initEditData($festivalId){
        $this->festivalEditData['baseInfo'] =  $this->getFormDataTochache('EditData'. $festivalId, 'baseInfo');
        $this->festivalEditData['propsData'] =  $this->getFormDataTochache('EditData'. $festivalId, 'propsData');
        $this->festivalEditData['groupsData'] =  $this->getFormDataTochache('EditData'. $festivalId, 'propsGroupData');
        $this->festivalEditData['prizesData'] = $this->getFormDataTochache('EditData'. $festivalId, 'prizesData');
    }

    private function saveEditDataToDb($festivalId){
        $this->initEditData($festivalId);
        $this->saveEditBaseInfoToDb();
        $this->saveEditPropsToDb();
        $this->saveEditPrizesToDb();
        $this->saveEditGameProps();
    }

    /**
     * 更新兑换活动基本信息
     */
    private function saveEditBaseInfoToDb(){
        $baseInfo = $this->festivalEditData['baseInfo'];
        $propsData = $this->festivalEditData['propsData'];
        $prizesData = $this->festivalEditData['prizesData'];
        $data = array(
            'title' => $baseInfo['title'],
            'start_time' => strtotime($baseInfo['startTime']),
            'end_time' => strtotime($baseInfo['endTime']),
            'banner_img' => $baseInfo['bannerImg'],
            'description' => $baseInfo['description'],
            'client_versions' => $baseInfo['clientVersion'],
            'status' => $baseInfo['status'],
            'prop_name' => $propsData['propsName'],
            'prize_column_name' => $prizesData['prizeColumnName'],
            'config' => json_encode($baseInfo['config'])
        );
        $updateResult = Festival_Service_BaseInfo::updateBy($data, array('id'=>$baseInfo['id']));
        if(!$updateResult) $this->output(-1, '更新道具活动基本信息失败.');
    }

    /**
     * 更新道具/道具组到数据库
     */
    private function saveEditPropsToDb(){
        $festivalId = $this->festivalEditData['baseInfo']['id'];
        $propsData = $this->festivalEditData['propsData'];
        $groupsData = $this->festivalEditData['groupsData'];
        $addData = $propGroup = array();
        foreach ($propsData['props'] as $key => $items){
            $this->saveEditPropToDb($festivalId, $key, $items['items']);
            $propGroup = $groupsData['propsGroup'][$key];
            $data = array(
                'name'=>$items['groupName'],
                'img' => $propGroup['groupImg'],
                'game_ids' => html_entity_decode($propGroup['gameIds']),
            );
            $updateResult = Festival_Service_PropGroup::updateBy($data, array('id' => $items['groupId']));
            if(!$updateResult) $this->output(-1, "兑换活动{$festivalId}道具系列{$key}信息更新失败.");
            $this->festivalEditData['groupsData'][$key]['gameIds'] = html_entity_decode($propGroup['gameIds']);
        }
    }

    /**
     * 更新编辑的单个道具项
     * @param int $festivalId
     * @param int $groupKey
     * @param array $data
     * @return array
     */
    private function saveEditPropToDb($festivalId, $groupKey, $data){
        $propIds = array();
        foreach ($data as $key => $item){
            $addData = array(
                'name'=>$item['propName'],
                'img'=> $item['propImg'],
                'gray_img'=> $item['propGrayImg'],
                'probability'=>$item['probability'],
                'interval'=>$item['interval']
            );
            $updateResult =Festival_Service_Props::updateBy($addData, array('id'=>$item['propId']));
            if(!$updateResult) $this->output(-1, "兑换活动{$festivalId}道具系列{$groupKey} 道具元素{$key}信息更新失败.");
        }
    }

    /**
     * 更新奖项信息
     */
    private function saveEditPrizesToDb(){
        $festivalId = $this->festivalEditData['baseInfo']['id'];
        $prizesData = $this->festivalEditData['prizesData'];
        foreach ($prizesData['prizes'] as $key => $item){
            $condition = html_entity_decode($item['prizeCondition']);
            $data = array(
                'name' => $item['prizeName'],
                'sort' => $item['prizeSort'],
                'img' => $item['prizeImg'],
                'icon' => $item['prizeIcon'],
                'total' => $item['prizeTotal'],
                'denomination' => ($item['prizeType']==3) ? $item['denomination2'] : $item['denomination1'],
                'start_time' => $item['startTime'],
                'end_time' => $item['endTime'],
                'rule' => $item['prizeRule'],
                'condition' => $condition,
            );
            $updateResult = Festival_Service_Prizes::updateBy($data, array('id' => $item['prizeId']));
            if(!$updateResult) $this->output(-1, "兑换活动{$festivalId}奖品{$key}信息更新失败.");
            $this->festivalEditData['prizesData']['prizes'][$key]['prizeCondition'] = $condition;
        }
    }

    /**
     * 更新游戏-道具关联数据到数据库
     */
    private function saveEditGameProps(){
        $festivalId = $this->festivalEditData['baseInfo']['id'];
        $groupsData = $this->festivalEditData['groupsData'];
        $data = array();
        foreach ($groupsData['propsGroup'] as $item){
            $gameIds = html_entity_decode($item['gameIds']);
            $gameIds = explode(',', $gameIds);
            foreach ($gameIds as $gameId){
                $data[$gameId] = $this->getGroupPropIds($festivalId, $item['groupId']);
            }
        }
        $this->saveEditGamePropsToDb($data);
    }

    private function getGroupPropIds($festivalId, $groupId){
        $propGroup = Festival_Service_PropGroup::getBy(array('festival_id'=>$festivalId, 'id'=> $groupId));
        return $propGroup['prop_ids'];
    }

    private function saveEditGamePropsToDb($data){
        $updateGamesIds = $addGamesIds = $delGamesIds = array();
        $festivalId = $this->festivalEditData['baseInfo']['id'];
        $gameIds = array_keys($data);
        //查询活动下所有的关联游戏id
        $oldData = Festival_Service_GameProps::getsBy(array('festival_id'=>$festivalId));
        $oldData = Common::resetKey($oldData, 'game_id');
        $oldGameIds = array_unique(array_keys($oldData));
        //更新的游戏ID
        $updateGamesIds =array_intersect($oldGameIds, $gameIds);
        if(!empty($updateGamesIds)){
            $this->updateGamePropsToDb($updateGamesIds, $festivalId, $data);
        }
        //新增的游戏ID
        $addGamesIds = array_diff($gameIds, $updateGamesIds);
        if(!empty($addGamesIds)){
            $this->insertGamePropsToDb($addGamesIds, $festivalId, $data);
        }
        //删除的游戏ID
        $delGamesIds = array_diff($oldGameIds, $updateGamesIds);
        if(!empty($delGamesIds)){
            $this->delGamePropsToDb($delGamesIds, $festivalId);
        }
    }

    /**
     * 新增游戏-道具关联数据
     */
    private function insertGamePropsToDb($gameIds, $festivalId, $data){
        foreach ($gameIds as $gameId){
            $add = array(
                'game_id' => $gameId,
                'festival_id' => $festivalId,
                'prop_ids' => $data[$gameId],
                'create_time' => Common::getTime()
            );
            $result = Festival_Service_GameProps::insert($add);
            if(!$result) $this->output(-1, "兑换活动{$festivalId}游戏{$gameId}关联道具信息添加失败.");
        }
    }

    /**
     * 更新游戏-道具关联数据
     */
    private function updateGamePropsToDb($gameIds, $festivalId, $data){
        foreach ($gameIds as $gameId){
            $update = array(
                'prop_ids' => $data[$gameId],
            );
            $result = Festival_Service_GameProps::updateBy($update, array('game_id'=>$gameId, 'festival_id'=>$festivalId));
            if(!$result) $this->output(-1, "兑换活动{$festivalId}游戏{$gameId}关联道具信息更新失败.");
        }
    }

    /**
     * 删除游戏-道具关联数据
     */
    private function delGamePropsToDb($gameIds, $festivalId){
        $result = Festival_Service_GameProps::deleteBy(array('game_id'=>array('IN',$gameIds), 'festival_id'=>$festivalId));
        if(!$result) $this->output(-1, "兑换活动{$festivalId}游戏关联道具信息删除失败.");
    }

    //----------保存新增数据-----------
    private function initInsertData(){
        $this->festivalInsertData['baseInfo'] =  $this->getFormDataTochache('InsertData', 'baseInfo');
        $this->festivalInsertData['propsData'] =  $this->getFormDataTochache('InsertData', 'propsData');
        $this->festivalInsertData['groupsData'] =  $this->getFormDataTochache('InsertData', 'propsGroupData');
        $this->festivalInsertData['prizesData'] = $this->getFormDataTochache('InsertData', 'prizesData');
    }

    private function saveInsertDataToDb(){
        $this->initInsertData();
        $this->saveInsertBaseInfoToDb();
        $this->saveInsertPropsToDb();
        $this->saveInsertPrizesToDb();
        $this->saveInsertGameProps();
    }

    /**
     * 保存兑换活动基本信息
     */
    private function saveInsertBaseInfoToDb(){
        $baseInfo = $this->festivalInsertData['baseInfo'];
        $propsData = $this->festivalInsertData['propsData'];
        $prizesData = $this->festivalInsertData['prizesData'];
        $data = array(
            'title' => $baseInfo['title'],
            'start_time' => strtotime($baseInfo['startTime']),
            'end_time' => strtotime($baseInfo['endTime']),
            'banner_img' => $baseInfo['bannerImg'],
            'description' => $baseInfo['description'],
            'client_versions' => $baseInfo['clientVersion'],
            'status' => $baseInfo['status'],
            'prop_name' => $propsData['propsName'],
            'prize_column_name' => $prizesData['prizeColumnName'],
            'config' => json_encode($baseInfo['config']),
            'create_time' => Common::getTime()
        );
        $newId = Festival_Service_BaseInfo::insert($data);
        if(!$newId) $this->output(-1, '添加道具活动基本信息失败.');
        $this->festivalInsertData['baseInfo']['id']= $newId;
    }

    /**
     * 保存道具/道具组到数据库
     */
    private function saveInsertPropsToDb(){
        $festivalId = $this->festivalInsertData['baseInfo']['id'];
        $propsData = $this->festivalInsertData['propsData'];
        $groupsData = $this->festivalInsertData['groupsData'];
        $addData = $propGroup = array();
        foreach ($propsData['props'] as $key => $items){
            $propIds = $this->saveInsertPropToDb($festivalId, $key, $items['items']);
            $propGroup = $groupsData['propsGroup'][$key];
            $addData = array(
                'name'=>$items['groupName'],
                'img' => $propGroup['groupImg'],
                'festival_id'=> $festivalId,
                'prop_ids' => implode(',', $propIds),
                'game_ids' => html_entity_decode($propGroup['gameIds']),
                'create_time' => Common::getTime()
            );
            $groupId = Festival_Service_PropGroup::insert($addData);
            if(!$groupId) $this->output(-1, "兑换活动{$festivalId}道具系列{$key}信息添加失败.");
            $this->festivalInsertData['groupsData']['propsGroup'][$key]['groupId'] = $groupId;
            $this->festivalInsertData['groupsData']['propsGroup'][$key]['gameIds'] = html_entity_decode($propGroup['gameIds']);
            $this->festivalInsertData['groupsData']['propsGroup'][$key]['propIds'] = implode(',', $propIds);
        }
    }

    /**
     * 保存新增奖项
     */
    private function saveInsertPrizesToDb(){
        $festivalId = $this->festivalInsertData['baseInfo']['id'];
        $prizesData = $this->festivalInsertData['prizesData'];
        foreach ($prizesData['prizes'] as $key => $item){
            $condition = $this->getFormPrizeCondition(html_entity_decode($item['prizeCondition']));
            $condition = implode(',', $condition);
            $addData = array(
                'prize_type' => $item['prizeType'],
                'name' => $item['prizeName'],
                'sort' => $item['prizeSort'],
                'festival_id' => $festivalId,
                'img' => $item['prizeImg'],
                'icon' => $item['prizeIcon'],
                'total' => $item['prizeTotal'],
                'denomination' => ($item['prizeType']==3) ? $item['denomination2'] : $item['denomination1'],
                'start_time' => $item['startTime'],
                'end_time' => $item['endTime'],
                'rule' => $item['prizeRule'],
                'condition' => $condition,
                'create_time' => Common::getTime()
            );
            $prizeId = Festival_Service_Prizes::insert($addData);
            if(!$prizeId) $this->output(-1, "兑换活动{$festivalId}奖品{$key}信息添加失败.");
            $this->festivalInsertData['prizesData']['prizes'][$key]['prizeCondition'] = $condition;
            $this->festivalInsertData['prizesData']['prizes'][$key]['prizeId'] = $prizeId;
        }
    }

    /**
     * 获取条件对应的道具ID
     * @param str $conditionStr
     * @return array
     */
    private function getFormPrizeCondition($conditionStr){
        $condition = array();
        $conditionArr = explode(',', $conditionStr);
        $propsData = $this->festivalInsertData['propsData']['props'];
        foreach($conditionArr as $item){
            $index = explode('_', $item);
            $condition[] = $propsData[$index[0]]['items'][$index[1]]['propId'];
        }
        return $condition;
    }

    /**
     * 保存新增游戏-道具关联数据到数据库
     */
    private function saveInsertGameProps(){
        $groupsData = $this->festivalInsertData['groupsData'];
        foreach ($groupsData['propsGroup'] as $item){
            $this->saveInsertGamePropsToDb($item['gameIds'], $item['propIds']);
        }
    }

    private function saveInsertGamePropsToDb($gameIds, $propIds){
        $festivalId = $this->festivalInsertData['baseInfo']['id'];
        $gameIds = explode(',', $gameIds);
        foreach ($gameIds as $gameId){
            $addData = array(
                'game_id' => $gameId,
                'festival_id' => $festivalId,
                'prop_ids' => $propIds,
                'create_time' => Common::getTime()
            );
            $result = Festival_Service_GameProps::insert($addData);
            if(!$result) $this->output(-1, "兑换活动{$festivalId}游戏{$gameId}关联道具信息添加失败.");
        }
    }

    /**
     * 保存新增单个道具项
     * @param int $festivalId
     * @param int $groupKey
     * @param array $data
     * @return array
     */
    private function saveInsertPropToDb($festivalId, $groupKey, $data){
        $propIds = array();
        foreach ($data as $key => $item){
            $addData = array(
                'name'=>$item['propName'],
                'img'=>$item['propImg'],
                'gray_img'=>$item['propGrayImg'],
                'festival_id'=>$festivalId,
                'probability'=>$item['probability'],
                'interval'=>$item['interval'],
                'create_time' => Common::getTime()
            );
            $propId =Festival_Service_Props::insert($addData);
            if(!$propId) $this->output(-1, "兑换活动{$festivalId}道具系列{$groupKey} 道具元素{$key}信息添加失败.");
            $this->festivalInsertData['propsData']['props'][$groupKey]['items'][$key]['propId'] = $propId;
            $propIds[] = $propId;
        }
        return $propIds;
    }

    //-------添加游戏控件---------
    public function addGamesAction() {
        $input = $this->getInput(array('addGameId', 'groupId'));
        $games = $this->initGroupGames($input['groupId']);
        $this->assign('addGameId', $input['addGameId']);
        $this->assign('games', $games);
        $this->getView()->display('common/addgames.phtml');
        exit;
    }

    private function initGroupGames($groupId){
        if(!$groupId) {
            return array();
        }
        $groupData = Festival_Service_PropGroup::getBy(array('id'=>$groupId));
        $gameIds = explode(',', $groupData['game_ids']);
        $data = array();
        foreach ($gameIds as $gameId){
            $gameInfo = Resource_Service_GameData::getGameAllInfo($gameId);
            if(!$gameInfo){
                continue;
            }
            $data[] = array(
                'gameId' => $gameId,
                'gameName' => $gameInfo['name'],
                'gameCategory' => $gameInfo['category_title'],
                'gameIcon' => $gameInfo['img'],
                'gameSize' => $gameInfo['size'],
                'gameVersion' => $gameInfo['version']
            );
        }
        return $data;
    }

    //-------------添加图片控件-----------------
    public function uploadAction() {
        $imgId = $this->getInput('imgId');
        $this->assign('imgId', $imgId);
        $this->getView()->display('common/upload.phtml');
        exit;
    }

    public function upload_postAction() {
        $ret = Common::upload('img', 'festival');
        $imgId = $this->getPost('imgId');
        $this->assign('code' , $ret['data']);
        $this->assign('msg' , $ret['msg']);
        $this->assign('data', $ret['data']);
        $this->assign('imgId', $imgId);
        $this->getView()->display('common/upload.phtml');
        exit;
    }

    /**
     * 编辑器中上传图片
     */
    public function uploadImgAction() {
        $ret = Common::upload('imgFile', 'festival');
        if ($ret['code'] != 0) die(json_encode(array('error' => 1, 'message' => '上传失败！')));
        exit(json_encode(array('error' => 0, 'url' => Common::getAttachPath() ."/". $ret['data'])));
    }

    //---------首页搜索条件-----------------
    private function getSearchData($input){
        $data = array();
        if($input['title']){
            $data['title'] = array('LIKE', $input['title']);
        }

        if($input['status']){
            $data['status'] = $input['status']-1;
        }

        if($input['startTime']){
            $data['start_time'] = array('>=', strtotime($input['startTime']));
        }

        if($input['endTime']){
            $data['end_time'] = array('<=', strtotime($input['endTime']));
        }
        return $data;
    }

    //----------编辑页面数据初始化----------
    private function getFestivalBaseInfo($festivalId){
        $data = Festival_Service_BaseInfo::getBy(array('id'=>$festivalId));
        return $data;
    }

    private function getFestivalPrizesData($festivalId){
        $prizesData = Festival_Service_Prizes::getsBy(array('festival_id'=>$festivalId));
        $data = array();
        foreach ($prizesData as $item){
            $propIds = explode(',', $item['condition']);
            $data[] = array(
                'data'=>$item,
                'propNames'=>$this->getPrizePropNames($propIds)
            );
        }
        return $data;
    }

    private function getPrizePropNames($propIds){
        $data = array();
        foreach ($propIds as $propId){
            $propInfo = Festival_Service_Props::getBy(array('id'=>$propId));
            $data[] = $propInfo['name'];
        }
        return implode(',', $data);
    }

    private function getPropsGroupData($festivalId){
        $data = array();
        $groupData = Festival_Service_PropGroup::getsBy(array('festival_id'=>$festivalId));
        foreach ($groupData as $groupItem){
            $propIds = explode(',', $groupItem['prop_ids']);
            $gameIds = explode(',', $groupItem['game_ids']);
            $data[]=array(
                'data'=>$groupItem,
                'props'=>$this->getGroupPropsData($propIds),
                'gameNames'=>$this->getGroupGameNames($gameIds)
            );
        }
        return $data;
    }

    private function getGroupPropsData($propIds){
        $propsData = Festival_Service_Props::getsBy(array('id'=>array('in', $propIds)));
        return $propsData;
    }

    private function getGroupGameNames($gameIds){
        $data = array();
        foreach ($gameIds as $gameId){
            $gameInfo = Resource_Service_GameData::getGameAllInfo($gameId);
            $data[] = $gameInfo['name'];
        }
        return implode(',', $data);
    }

    //-----------------cache存储开始------------------------
    private function saveFormDataToCache($action, $key, $data){
        $ckey = $this->getFormCacheKey($action);
        $cacheObj = $this->getCache();
        $cacheObj->hSet($ckey,$key,$data, $this->expire);
    }

    private function getFormDataTochache($action, $key=''){
        $ckey = $this->getFormCacheKey($action);
        $cacheObj = $this->getCache();
        if($key){
            $data = $cacheObj->hGet($ckey, $key);
        } else {
            $data = $cacheObj->hGetAll($ckey);
        }
        return json_decode($data, true);
    }

    private function delFormDataCache($action){
        $ckey = $this->getFormCacheKey($action);
        $cacheObj = $this->getCache();
        $cacheObj->delete($ckey);
    }

    private function getFormCacheKey($action){
        $loginUser = $this->userInfo;
        $ckey = ":Festival:{$action}:{$loginUser['uid']}";
        return $ckey;
    }

    private function getCache(){
        $cacheObj = Cache_Factory::getCache(Cache_Factory::ID_REMOTE_REDIS);
        return $cacheObj;
    }

    //-----------------页面输入数据校验------------------------
    private function isValidStep1Data($input){
        //基本信息
        if (!$input['title']) $this->output(-1, '活动名称不能为空.');
        if (!$input['startTime']) $this->output(-1, '活动开始时间不能为空.');
        if (!$input['endTime']) $this->output(-1, '活动结束时间不能为空.');
        if ($input['endTime'] < $input['startTime']) $this->output(-1, '活动开始时间不能大于或等于结束时间.');

        //重叠区间判断
        if ($input['status']) {
            $params['status'] = 1;
            if ($input['id']) {
                $params['id'] = array('!=', $input['id']);
            }
            $items = Festival_Service_BaseInfo::getsBy($params);
            $check = $this->_checkRegion($input, $items);
            if (!$check) $this->output(-1, '生效时间区间不能与其他活动有重叠。');
        }

        if (!$input['bannerImg']) $this->output(-1, '活动广告图不能为空.');
        if (!$input['description']) $this->output(-1, '活动说明不能为空.');
        if (!$input['clientVersion']) $this->output(-1, '游戏大厅参与最低版本不能为空.');
        if (!$input['config']['img1']) $this->output(-1, '兑奖活动配置图片一不能为空.');
        if (!$input['config']['img2']) $this->output(-1, '兑奖活动配置图片二不能为空.');
        if (!$input['config']['img3']) $this->output(-1, '兑奖活动配置图片三不能为空.');
        if (!$input['config']['img4']) $this->output(-1, '兑奖活动配置图片四不能为空.');
        if (!$input['config']['img5']) $this->output(-1, '兑奖活动版本过低图片配置不能为空.');
        return $input;
    }

    private function _checkRegion($info, $items){
        $startTime = strtotime($info['startTime']);
        $endTime = strtotime($info['endTime']);
        $flag = true;
        if(!$items) return $flag;
        foreach ($items as $value){
            if((intval($startTime) <= intval($value['end_time'])) && (intval($value['start_time']) <= intval($endTime))){
                $flag = false;
                break;
            }
        }
        return $flag;
    }

    private function isValidStep2Data($input){
        //道具配置
        if (!$input['propsName']) $this->output(-1, '道具名称不能为空.');
        foreach ($input['props'] as $key => $items){
            if(!$items['groupName']) $this->output(-1, "系列{$key} 名称不能为空.");
            if(!$items['items']) $this->output(-1, "需要配置系列{$key}道具元素.");
            $this->isValidPropItem($key, $items['items']);
        }
        return $input;
    }

    private function isValidPropItem($group, $data){
        foreach ($data as $key => $item){
            if(!$item['propName']) $this->output(-1, "系列{$group} 道具{$key}元素名称不能为空.");
            if(!$item['propImg']) $this->output(-1, "系列{$group} 道具{$key}元素彩色图片不能为空.");
            if(!$item['propGrayImg']) $this->output(-1, "系列{$group} 道具{$key}元素灰色图片不能为空.");
            if(!$item['probability']) $this->output(-1, "系列{$group} 道具{$key}元素概率不能为空.");
            if($item['probability']<1 || $item['probability']>100) $this->output(-1, "系列{$group} 道具{$key}元素概率输入不合法.");
        }
    }

    private function isValidStep3Data($input){
        //游戏配置
        $data = array();
        foreach ($input['propsGroup'] as $key => $item){
            if(!$item['groupImg']) $this->output(-1, "系列{$key} 添加大图不能为空.");
            if(!$item['gameIds']) $this->output(-1, "系列{$key} 添加游戏不能为空.");
            $gameIds = html_entity_decode($item['gameIds']);
            $gameIds =explode(',', $gameIds);
            if(count($data)){
                $result = array_intersect($gameIds, $data);
                if(count($result)) $this->output(-1, "系列{$key} 添加的游戏跟其他的系列有冲突请检查.");
            }
            $data = $gameIds;
        }
        return $input;
    }

    private function isValidStep4Data($input){
        //兑奖配置
        if (!$input['prizeColumnName']) $this->output(-1, '兑奖专区名称不能为空.');
        if(count($input['prizes']) > 9) $this->output(-1, '奖品数量不能超过9个.');
        foreach ($input['prizes'] as $key => $item){
            if(!$item['prizeName']) $this->output(-1, "奖项{$key} 名称不能为空.");
            $this->isValidPrizeType($key, $item);
            if(!$item['prizeTotal']) $this->output(-1, "奖项{$key} 总数量不能为空.");
            if(!$item['prizeRule']) $this->output(-1, "奖项{$key} 兑换规则不能为空.");
            if(!$item['prizeCondition']) $this->output(-1, "奖项{$key} 兑换条件不能为空.");
        }
        return $input;
    }

    private function isValidPrizeType($key, $item){
        switch($item['prizeType']){
            case 2:
                if(!$item['denomination1']) $this->output(-1, "奖项{$key} 奖品A券面额不能为空。");
                if(!$item['startTime']) $this->output(-1, "奖项{$key} 奖品A券开始时间不能为空。");
                if(!$item['endTime']) $this->output(-1, "奖项{$key} 奖品A券结束时间不能为空。");
                break;
            case 3:
                if(!$item['denomination2']) $this->output(-1, "奖项{$key} 奖品积分面额不能为空。");
                break;
        }
    }
}