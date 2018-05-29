<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 *
 * 划屏游戏
 * @author fanch
 *
 */
class Festival_TouchgameController extends Admin_BaseController {
    public $actions = array(
        'indexUrl' => '/Admin/Festival_Touchgame/index',
        'addUrl' => '/Admin/Festival_Touchgame/add',
        'addPostUrl' => '/Admin/Festival_Touchgame/addPost',
        'editUrl' => '/Admin/Festival_Touchgame/edit',
        'editPostUrl' => '/Admin/Festival_Touchgame/editPost',
        'totalUrl' => '/Admin/Festival_Touchgame/total',
        'exportUrl' => '/Admin/Festival_Touchgame/export',
        'optionUrl' => '/Admin/Festival_Touchgame/option',
        'uploadUrl' => '/Admin/Festival_Touchgame/upload',
        'uploadPostUrl' => '/Admin/Festival_Touchgame/upload_post',
        'uploadImgUrl' => '/Admin/Festival_Touchgame/uploadImg'
        );

    public $perpage = 20;

    public function indexAction(){
        $page = $this->getInput('page');
        $input = $this->getInput(array('name', 'status', 'startTime', 'endTime'));
        if ($page < 1) {
            $page = 1;
        }
        $search = $this->getSearchData($input);
        $orderBy  = array('create_time'=>'DESC');

        list($total, $items) = Festival_Service_TouchGame::getInfoList($page, $this->perpage, $search, $orderBy);
        $this->assign('items', $items);
        $this->assign('total', $total);
        $this->assign('search', $input);
        $url = $this->actions['indexUrl'] . '/?' . http_build_query($input) . '&';
        $this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
    }

    public function addAction(){
    }

    public function addPostAction(){
        $request  = $this->getInput(array('propsConfig','config'));
        $configData= $this->cookConfigData($request['config']);
        $propsConfigData = $this->cookPropsConfigData($request['propsConfig']);
        $configData['props_config'] = json_encode($propsConfigData);
        $configData['create_time'] = time();
        $result = Festival_Service_TouchGame::insertInfo($configData);
        if(!$result) $this->output(-1,  "活动添加失败.");
        $this->output(0, '活动添加成功');
    }

    public function editAction(){
        $id = $this->getInput('id');
        $data = Festival_Service_TouchGame::getByInfo(array('id' => $id));
        $this->assign('info', $data);
    }

    public function editPostAction(){
        $request  = $this->getInput(array('propsConfig','config'));
        $configData= $this->cookConfigData($request['config']);
        $propsConfigData = $this->cookPropsConfigData($request['propsConfig']);
        $configData['props_config'] = json_encode($propsConfigData);
        $id = $configData['id'];
        unset($configData['id']);
        $result = Festival_Service_TouchGame::updateByInfo($configData, array('id'=>$id));
        if(!$result) $this->output(-1,  "活动更新失败.");
        $this->output(0, '活动更新成功');
    }

    public function totalAction(){
        $page = $this->getInput('page');
        $request = $this->getInput(array('id', 'uname', 'status'));
        if ($page < 1) {
            $page = 1;
        }
        $info = Festival_Service_TouchGame::getByInfo(array('id' => $request['id']));
        $search=array('info_id' => $request['id'],'status'=>1);
        if($request['uname']){
            $search['uname'] = array('LIKE', $request['uname']);
        }
        if($request['status']){
            $search['status'] = $request['status'] -1;
        }
        $orderBy  = array('score'=>'DESC', 'create_time'=>'ASC');
        list($total, $items) = Festival_Service_TouchGame::getTotalList($page, 10, $search, $orderBy);
        $this->assign('info', $info);
        $this->assign('items', $items);
        $this->assign('total', $total);
        $this->assign('search', $request);
    }

    public function optionAction(){
        $request = $this->getInput(array('id', 'uuid', 'status'));
        $result = Festival_Service_TouchGame::updateByTotal(array('status'=>$request['status']), array('id'=>$request['id']));
        $this->updateUserChance($request['uuid'], $request['status']);
        if(!$result) $this->output(-1,  "活动更新失败.");
        $this->output(0, '活动更新成功');
    }

    public function exportAction(){
        $request = $this->getInput(array('id', 'uuid'));
        $params = array(
            'info_id'=>$request['id'],
            'uuid'=>$request['uuid']
        );
        $filename = "用户记录_".date('YmdHis', Common::getTime());
        Util_Csv::putHead($filename);

        $title = array(array('uuid', '用户名', '分数', '积分', '获得时间'));
        Util_Csv::putData($title);

        $page=1;
        while(1) {
            list($total, $items) = Festival_Service_TouchGame::getLogsList($page, 100, $params);
            if (!$items) break;

            $temp = array();
            foreach ($items as $key=>$item) {
                $temp[] = array(
                    $item['uuid'],
                    $item['uname'],
                    $item['score'],
                    $item['points'],
                    date('Y-m-d H:i:s', $item['create_time'])
                );
            }
            Util_Csv::putData($temp);
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

    public function uploadImgAction() {
        $ret = Common::upload('imgFile', 'festival');
        if ($ret['code'] != 0) die(json_encode(array('error' => 1, 'message' => '上传失败！')));
        exit(json_encode(array('error' => 0, 'url' => Common::getAttachPath() ."/". $ret['data'])));
    }

    private function cookConfigData($input){
        if (!$input['name']) $this->output(-1,  "活动名称不能为空.");
        if (!$input['descript']) $this->output(-1,  "活动说明不能为空.");
        if (!$input['login_desc']) $this->output(-1,  "登录一句话描述不能为空.");
        $input = $this->cookConfigTimeData($input);
        $input = $this->cookConfigPreheatData($input);
        $input = $this->cookConfigImgData($input);
        $input = $this->cookConfigWaringData($input);
        return $input;
    }

    private function updateUserChance($uuid, $status){
        $cacheKey = $this->getCacheKey();
        $cache = Cache_Factory::getCache();
        $cacheData = $cache->hGet($cacheKey, $uuid);
        if($cacheData){
            $cacheData = json_decode($cacheData, true);
            $expire = strtotime('tomorrow') - time();
            $cacheData['valid'] =  ($status) ? true : false;
            $cache->hSet($cacheKey, $uuid, json_encode($cacheData), $expire);
        }
    }

    private function getCacheKey(){
        $time = time();
        $info = $this->getOnlineInfo($time);
        $cacheKey =  Util_CacheKey::FESTIVAL_TOUCH_GAME.':'.$info['id'];
        return $cacheKey;
    }

    private function getOnlineInfo($time){
        $params = array(
            'preheat_time'=> array('<=', $time),
            'end_time' => array('>=', $time),
            'status' => 1
        );
        $info = Festival_Service_TouchGame::getByInfo($params);
        return $info;
    }

    private function cookPropsConfigData($input){
        if(count($input)<2) $this->output(-1,  "活动道具设置不能低于两个.");
        $out = 0;
        $totalProbability = 0;
        foreach($input as $key => $item){
            $key= $key+1;
            if(!$item['propName']) $this->output(-1,  "第{$key}个道具,道具名称不能为空.");
            if(!$item['propImg']) $this->output(-1,  "第{$key}个道具,道具图片不能为空.");
            if($item['type']){
                if(!$item['probability']) $this->output(-1,  "第{$key}个道具,道具概率不能为空.");
                if(!preg_match('/^[1-9]\d*$/',$item['probability'])) $this->output(-1,  "第{$key}个道具,道具概率请使用0-100的整数.");
                if(!$item['score']) $this->output(-1,  "第{$key}个道具,道具分数值不能为空.");
                if(!$item['point']) $this->output(-1,  "第{$key}个道具,道具积分值不能为空.");
            } else {
                $out++;
            }
            $totalProbability += $item['probability'];
        }
        if(1 != $out) $this->output(-1,  "活动道具设置中减分道具只能有一个.");
        if($totalProbability > 100) $this->output(-1,  "活动道具中概率累计不能超过100.");
        return $input;
    }

    private function getSearchData($input){
        $data = array();
        if($input['title']){
            $data['title'] = $input['title'];
        }

        if($input['status']){
            $data['status'] = $input['status']-1;
        }

        if($input['startTime']){
            $data['preheat_time'] = array('>=', strtotime($input['startTime']));
        }

        if($input['endTime']){
            $data['end_time'] = array('<=', strtotime($input['endTime']));
        }
        return $data;
    }

    /**
     * @param $input
     * @return mixed
     */
    private function cookConfigTimeData(&$input) {
        if (!$input['preheat_time']) {
            $this->output(-1, "预热时间不能为空.");
        }
        if (!$input['start_time']) {
            $this->output(-1, "活动开始时间不能为空.");
        }
        if (!$input['end_time']) {
            $this->output(-1, "活动结束时间不能为空.");
        }
        if (!$input['game_start_time']) {
            $this->output(-1, "游戏开始时间不能为空.");
        }
        if (!$input['game_end_time']) {
            $this->output(-1, "游戏结束时间不能为空.");
        }

        $input['preheat_time'] = strtotime(date('Y-m-d', strtotime($input['preheat_time'])));
        $input['start_time'] = strtotime(date('Y-m-d', strtotime($input['start_time'])));
        $input['end_time'] = strtotime(date('Y-m-d', strtotime($input['end_time'])));
        $input['game_start_time'] = strtotime(date('Y-m-d', strtotime($input['game_start_time'])));
        $input['game_end_time'] = strtotime(date('Y-m-d', strtotime($input['game_end_time'])));

        if ($input['end_time'] <= $input['start_time']) {
            $this->output(-1, "活动结束时间不能小于等于活动开始时间.");
        }
        if ($input['start_time'] < $input['preheat_time']) {
            $this->output(-1, "活动开始时间不能小于预热时间.");
        }
        if ($input['game_start_time'] < $input['start_time']) {
            $this->output(-1, "游戏开始时间不能小于活动开始时间.");
        }
        if ($input['game_end_time'] <= $input['game_start_time']) {
            $this->output(-1, "游戏结束时间不能小于等于游戏开始时间.");
        }
        if ($input['game_end_time'] > $input['end_time']) {
            $this->output(-1, "游戏结束时间不能大于活动结束时间.");
        }

        //重叠区间判断
        if ($input['status']) {
            $params['status'] = 1;
            if ($input['id']) {
                $params['id'] = array('!=', $input['id']);
            }
            $items = Festival_Service_TouchGame::getsByInfo($params);
            $check = $this->checkRegion($input, $items);
            if (!$check) $this->output(-1, '生效时间区间不能与其他活动有重叠。');
        }
        return $input;
    }

    private function checkRegion($info, $items){
        $startTime = strtotime($info['preheat_time']);
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

    /**
     * @param $input
     * @return mixed
     */
    private function cookConfigPreheatData(&$input) {
        if (!$input['preheat_img1']) {
            $this->output(-1, "预热背景图不能为空.");
        }
        if (!$input['preheat_img2']) {
            $this->output(-1, "预热图片素材1不能为空.");
        }
        if (!$input['preheat_img3']) {
            $this->output(-1, "预热图片素材2不能为空.");
        }
        $input['preheat_config'] = json_encode(array('img1' => $input['preheat_img1'], 'img2' => $input['preheat_img2'], 'img3' => $input['preheat_img3']));
        unset($input['preheat_img1']);
        unset($input['preheat_img2']);
        unset($input['preheat_img3']);
        return $input;
    }

    /**
     * @param $input
     * @return mixed
     */
    private function cookConfigImgData(&$input) {
        if (!$input['img_config_background']) {
            $this->output(-1, "活动素材背景图片不能为空.");
        }
        if (!$input['img_config_store']) {
            $this->output(-1, "活动素材记分道具图片不能为空.");
        }
        if (!$input['img_config_rank']) {
            $this->output(-1, "活动素材排行榜图片不能为空.");
        }
        if (!$input['img_config_mall']) {
            $this->output(-1, "活动素材积分商城图片不能为空.");
        }
        if (!$input['img_config_prize']) {
            $this->output(-1, "活动素材积分抽奖图片不能为空.");
        }
        if (!$input['img_config_share']) {
            $this->output(-1, "活动素材分享图片不能为空.");
        }
        $input['img_config'] = json_encode(array('background' => $input['img_config_background'], 'store' => $input['img_config_store'], 'rank' => $input['img_config_rank'], 'mall' => $input['img_config_mall'], 'prize' => $input['img_config_prize'], 'share' => $input['img_config_share']));
        unset($input['img_config_background']);
        unset($input['img_config_store']);
        unset($input['img_config_rank']);
        unset($input['img_config_mall']);
        unset($input['img_config_prize']);
        unset($input['img_config_share']);
        return $input;
    }

    /**
     * @param $input
     * @return mixed
     */
    private function cookConfigWaringData(&$input) {
        if (!$input['waring_score']) {
            $this->output(-1, "活动分数阀值不能为空.");
        }
        if (!$input['waring_point']) {
            $this->output(-1, "活动积分阀值不能为空.");
        }
        $input['waring_config'] = json_encode(array('score' => $input['waring_score'], 'point' => $input['waring_point'],));
        unset($input['waring_score']);
        unset($input['waring_point']);
        return $input;
    }
}