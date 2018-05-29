<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghua
 *
 */
class CategoryController extends Client_BaseController{

    public $actions = array(
        'listUrl' => '/client/category/index',
        'detailUrl' => '/client/category/detail/',
        'indexlUrl' => '/client/index/detail/',
        'tjUrl' => '/client/index/tj'
    );

    public $perpage = 10;
    /**
     *
     * index page view
     */
    public function indexAction() {
        Common::addSEO($this,'分类');
        $this->assign('source', $this->getSource());
        $this->assign('cache', Game_Service_Config::getValue('game_client_cache'));

    }

    public function detailAction(){
        Common::addSEO($this,'分类详情');
        $id = intval($this->getInput('id'));
        $intersrc = $this->getInput('intersrc');
        $page = intval($this->getInput('page'));
        $sp = $this->getInput('sp');
        if ($page < 1) $page = 1;

        if($id == 100){
            $this->assign('title', '全部游戏');
        }else if($id == 101){
            $this->assign('title', '最新游戏');
        }
        //判断游戏大厅版本
        $checkVer = $this->checkAppVersion();
        $searchParmas = array(
            Resource_PageCache_Category::PARAMS_ID =>  0,
            Resource_PageCache_Category::PARAMS_PID => $id,
            Resource_PageCache_Category::PARAMS_PAGE => $page,
            Resource_PageCache_Category::PARAMS_PERPAGE => $this->perpage,
            Resource_PageCache_Category::PARAMS_FILTER => $this->filter,
            Resource_PageCache_Category::PARAMS_CLIENTVERSION => $checkVer,
            Resource_PageCache_Category::PARAMS_ACTIONSTR => "/client/category/detail/",

        );
        $cacheData = Resource_PageCache_Category::getPageCache($searchParmas);
        if($cacheData){
            list($total, $data) = $cacheData;
        } else {
            list($total, $games) = Resource_PageCache_Category::getSearchList($searchParmas);
            $data = $this->getFormatDeatilData($games, $checkVer);
            Resource_PageCache_Category::savePageCache($searchParmas, array($total, $data));
        }
        $hasnext = (ceil((int) $total / $this->perpage) - 1) > 0 ? true : false;
        $this->assign('checkver', $checkVer);
        $this->assign('sp', $sp);
        $this->assign('resource_games', $data);
        $this->assign('hasnext', $hasnext);
        $this->assign('page', $page);
        $this->assign('id', $id);
        $this->assign('source', $this->getSource());
        $this->assign('intersrc', $intersrc);
    }

    /**
     * subject json list
     */
    public function moreCtAction(){
        $id = intval($this->getInput('id'));
        $intersrc = $this->getInput('intersrc');
        $page = intval($this->getInput('page'));
        if ($page < 1) $page = 1;

        if($id == 100){
            $this->assign('title', '全部游戏');
        }else if($id == 101){
            $this->assign('title', '最新游戏');
        }
        //判断游戏大厅版本
        $checkVer = $this->checkAppVersion();
        $searchParmas = array(
            Resource_PageCache_Category::PARAMS_ID =>  0,
            Resource_PageCache_Category::PARAMS_PID => $id,
            Resource_PageCache_Category::PARAMS_PAGE => $page,
            Resource_PageCache_Category::PARAMS_PERPAGE => $this->perpage,
            Resource_PageCache_Category::PARAMS_FILTER => $this->filter,
            Resource_PageCache_Category::PARAMS_CLIENTVERSION => $checkVer,
            Resource_PageCache_Category::PARAMS_ACTIONSTR => "/client/category/more/",
            Resource_PageCache_Category::PARAMS_INTERSRC => $intersrc
        );
        $cacheData = Resource_PageCache_Category::getPageCache($searchParmas);

        if($cacheData){
            list($total, $data) = $cacheData;
        } else {
            list($total, $games) = Resource_PageCache_Category::getSearchList($searchParmas);
            $data = $this->getFormatMoreData($id, $total, $games, $page, $checkVer);
            Resource_PageCache_Category::savePageCache($searchParmas, array($total, $data));
        }

        $hasnext = (ceil((int) $total / $this->perpage) - ($page)) > 0 ? true : false;
        $this->output(0, '', array('list'=>$data, 'hasnext'=>$hasnext, 'curpage'=>$page));
        $this->assign('hasnext', $hasnext);
        $this->assign('page', $page);
        $this->assign('id', $id);
    }

    private function getFormatDeatilData($data, $checkVer){
        $result = array();
        foreach ($data as $key => $value) {
            if ($value['game_id']) {
                $gameId = $value['game_id'];
            } else {
                $gameId = $value['id'];
            }
            $info = Resource_Service_GameData::getGameAllInfo($gameId);
            if ($info) {
                if ($checkVer >= 2) {
                    //增加评测信息
                    $info['pc_info'] = Client_Service_IndexAdI::getEvaluationByGame($info['id']);
                    //增加礼包信息
                    $info['gift_info'] = Client_Service_IndexAdI::haveGiftByGame($info['id']);
                }
                $result[] = $info;
            }
        }
        return $result;
    }

    private function getFormatMoreData($id, $total, $data, $page, $checkVer){
        $result = array();
        $webroot = Common::getWebRoot();
        $i = 0;
        foreach ($data as $key => $value) {
            $num = $i + (($page - 1) * $this->perpage);
            if ($num >= $total && $id == '101') break;
            if ($value['game_id']) {
                $gameId = $value['game_id'];
            } else {
                $gameId = $value['id'];
            }
            $info = Resource_Service_GameData::getGameAllInfo($gameId);
            $intersrc = 'CATEGORY' . $id . '_GID' . $info['id'];
            $href = urldecode($webroot . $this->actions['indexlUrl'] . '?id=' . $info['id'] . '&pc=1&intersrc=' . $intersrc . '&t_bi=' . $this->getSource());
            if ($checkVer >= 2) {
                //加入评测链接
                $evaluationId = Client_Service_IndexAdI::getEvaluationByGame($info['id']);
                $evaluationUrl = '';
                if ($evaluationId) {
                    $evaluationUrl = ',评测,' . $webroot . '/client/evaluation/detail/?id=' . $evaluationId . '&pc=3&intersrc=' . $intersrc . '&t_bi=' . $this->getSource();
                }
                //附加属性处理
                $attach = array();
                if ($evaluationId) array_push($attach, '评');
                if (Client_Service_IndexAdI::haveGiftByGame($info['id'])) array_push($attach, '礼');
            }
            $item = array(
                'id' => $info['id'],
                'name' => $info['name'],
                'resume' => html_entity_decode($info['resume'], ENT_QUOTES),
                'size' => $info['size'] . 'M',
                'link' => Common::tjurl($this->actions['tjUrl'], $info['id'], $intersrc, $info['link']),
                'alink' => urldecode($webroot . $this->actions['indexlUrl'] . '?id=' . $info['id'] . '&intersrc=' . $intersrc . '&t_bi=' . $this->getSource()),
                'img' => urldecode($info['img']),
                'profile' => $info['name'] . ',' . $href . ',' . $info['id'] . ',' . $info['link'] . ',' . $info['package'] . ',' . $info['size'] . ',' . 'Android' . $info['min_sys_version_title'] . ',' . $info['min_resolution_title'] . '-' . $info['max_resolution_title'],
            );
            if ($checkVer >= 2) {
                //js a 标签 data-infpage 参数数据
                $data_info = '游戏详情,' . $href . ',' . $info['id'];
                $item['profile'] = $evaluationId ? $data_info . $evaluationUrl : $data_info;
                $item['attach'] = ($attach) ? implode(',', $attach) : '';
                $item['device'] = $info['device'];
                $item['data-type'] = 1;
            }
            $result[] = $item;
            $i++;
        }
        return $result;
    }
}
