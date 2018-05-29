<?php
if (! defined('BASE_PATH')) exit('Access Denied!');

class SearchController extends Game_BaseController {
    
    public $actions = array(
                    'indexUrl' => '/search/index', 
                    'outUrl' => '/search/out', 
                    'detailtUrl' => '/index/detail', 
                    'engineUrl' => '/search/engine', 
                    'enginetUrl' => '/search/enginet', 
                    'tjUrl' => '/index/tj' 
    );
    public $perpage = 10;
    
    /**
     */
    public function indexAction() {
        //关键字
        $intersrc = $this->getInput('intersrc');
        $apikword = Resource_Service_Games::getKeyword();
        $this->assign('apikword', $apikword[0]);
        //热词列表
        $apihot = Resource_Service_Games::getHots();
        foreach ( $apihot as $key => $value ) {
            $apiList[$key]['name'] = html_entity_decode($value, ENT_QUOTES);
            $apiList[$key]['href'] = '/search/list?keyword=' . trim(html_entity_decode($value, ENT_QUOTES)) . '&intersrc=hsearch&action=2&t_bi=' . $this->getSource();
        }
        $this->assign('apihot', json_encode($apiList));
        $stype = $this->getInput('stype');
        list (  , $keywords ) = Resource_Service_Keyword::getCanUseResourceKeywords(0, 1, array(
                        'ktype' => 2 
        ));
        $this->assign('defaultKeyword', trim($keywords[0]['name']));
        $this->assign('stype', $stype);
        $this->assign('intersrc', $intersrc);
        $this->assign('bi', $this->getSource());
    }
    
    public function listAction() {
        $keyword = html_entity_decode($this->getInput('keyword'));
        $action = true;
        $filter_key = Game_Service_Config::getValue('game_search_filter');
        $filter_key = explode('|', $filter_key);
        if (in_array($keyword, $filter_key)) $action = false;
        $searchOption = array(
                        'searchFrom' => Api_Search_Query::Search_From_Default, 
                        'searchAction' => intval($this->getInput('action')) 
        );
        $localGames = Api_Search_Query::getSearchList('', $this->perpage, $keyword, $searchOption);
        $this->mOptionSearch = array(
                        'localGameList' => $localGames['data'], 
                        'action' => $action, 
                        'keyword' => $keyword 
        );
        $searchList = Api_Search_Query::handleSearchList($this->mOptionSearch);
        $gameList = $this->handleMobileApiH5Data($searchList);
        $hasnext = (ceil((int) $searchList['total'] / $this->perpage) - 1) > 0 ? true : false;
        $data = array(
                        'list' => (array) $gameList, 
                        'from' => $searchList['from'], 
                        'hasNext' => $hasnext, 
                        'curPage' => 1, 
                        'resum' => $searchList['resum'], 
                        'totalCount' => $searchList['total'], 
                        'href' => '/search/list?t_bi=' . $this->getSource(), 
                        'searchMoreUrl' => '/Api/Local_Search/searchMore?type=h5', 
                        'ajaxUrl' => '/Api/Local_Search/searchList?type=h5&keyword=' . $keyword 
        );
        $this->assign('list', json_encode($data));
        $this->assign('keywords', $keyword);
    }
    
    private function handleMobileApiH5Data($searchList) {
        $webroot = Common::getWebRoot();
        $i = 1;
        foreach ( $searchList['gamelist'] as $key => $value ) {
            if ($searchList['from'] == 'gn') {
                $value = Resource_Service_GameData::getGameAllInfo($value['id']);
            }
            $href = urldecode($webroot . $this->actions['detailtUrl'] . '?stype=' . $this->getInput('stype') . '&from=' . $searchList['from'] . '&id=' . $value['id'] . '&gname=' . $value['name'] . '&keyword=' . $this->mOptionSearch['keyword'] . '&intersrc=' . $this->mOptionSearch['intersrc'] . '&t_bi=' . $this->getSource());
            $data = array(
                            'id' => $value['id'], 
                            'name' => $value['name'], 
                            'category' => $value['category_title'],
                            'resume' => html_entity_decode($value['resume'], ENT_QUOTES), 
                            'size' => $value['size'] . 'M', 
                            'link' => Util_Statist::getDownloadUrl($value['id'], $value['link'], $i), 
                            'href' => urldecode($webroot . $this->actions['detailtUrl'] . '?stype=' . $this->getInput('stype') . '&from=' . $searchList['from'] . '&id=' . $value['id'] . '&gname=' . $value['name'] . '&keyword=' . $this->mOptionSearch['keyword'] . '&intersrc=isearchI' . $i . '_gamedetail' . $value['id'] . '&t_bi=' . $this->getSource()), 
                            'img' => ($value['from'] ? $value['img'] : urldecode($value['img'])), 
                            'profile' => $value['name'] . ',' . $href . ',' . $value['id'] . ',' . $value['link'] . ',' . $value['package'] . ',' . $value['size'] . ',' . 'Android' . $value['version'] . ',' . $value['min_resolution'] . '-' . $value['max_resolution'] 
            );
            $i ++;
            $gameList[] = $data;
        }
        return $gameList;
    }
    
    private function getPage() {
        return intval($this->getInput('page')) < 1 ? 1 : intval($this->getInput('page'));
    }

}
