<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class Local_CategoryController extends Api_BaseController {
	public $perpage = 10;

    /**
     * 客户端本地化分类详情页
     */
    public function categoryInfoAction() {
        $id = $this->getInput('id');
        $webroot = Common::getWebRoot();

        $info = Resource_Service_Attribute::getBy(array('id'=>$id));

        $items = array(
            '0' => array(
                'title'=>'最新',
                'source'=>'new',
                'viewType'=>'NewestListView',
                'url'=>$webroot.'/Api/Local_Category/categoryNewList?id=' . $id,
            ),
            '1' => array(
                'title'=>'最热',
                'source'=>'hot',
                'viewType'=>'HotestListView',
                'url'=>$webroot.'/Api/Local_Category/categoryHotList?id=' . $id,
            )
        );
        $tmp['items'] = $items;
        header("Content-type:text/json");
        exit(json_encode(array(
            'success' => $items  ? true : false ,
            'msg' => '',
            'sign' => 'GioneeGameHall',
            'title'=> $id == 'caini'  ? '猜你喜欢' : html_entity_decode($info['title'], ENT_QUOTES),
            'data' => $tmp,
        )));
    }

    /**
     * 分类本地化最热
     */
    public function categoryHotListAction() {
        $id = intval($this->getInput('id'));
        $page = intval($this->getInput('page'));
        $sp = $this->getInput('sp');
        $imei = end(explode('_',$sp));
        $imcrc = crc32(trim($imei));
        $data = $this->_categoryList($id, $page, $imcrc, 'hot');
        $this->localOutput('','',$data);
    }

    /**
     * 分类本地化最新
     */
    public function categoryNewListAction() {
        $id = intval($this->getInput('id'));
        $page = intval($this->getInput('page'));
        $sp = $this->getInput('sp');
        $imei = end(explode('_',$sp));
        $imcrc = crc32(trim($imei));

        $data = $this->_categoryList($id, $page, $imcrc, 'new');
        $this->localOutput('','',$data);
    }

    private  function _categoryList($id, $page, $imcrc, $sortType) {
        $searchParmas = array(
            Resource_PageCache_Category::PARAMS_ID =>  0,
            Resource_PageCache_Category::PARAMS_PID => $id,
            Resource_PageCache_Category::PARAMS_PAGE => $page,
            Resource_PageCache_Category::PARAMS_PERPAGE => $this->perpage,
            Resource_PageCache_Category::PARAMS_IMEICRC => $imcrc,
            Resource_PageCache_Category::PARAMS_SORTTYPE => $sortType,
            Resource_PageCache_Category::PARAMS_FILTER => $this->filter,
            Resource_PageCache_Category::PARAMS_ACTIONSTR => "/api/local_category/list/{$sortType}/"
        );
        $cacheData = Resource_PageCache_Category::getPageCache($searchParmas);

        if($cacheData){
            list($total, $data) = $cacheData;
        } else {
            list($total, $games) = Resource_PageCache_Category::getSearchList($searchParmas);
            $data = $this->getFormatData($total, $games, $page);
            Resource_PageCache_Category::savePageCache($searchParmas, array($total, $data));
        }
        $hasnext = (ceil((int) $total / $this->perpage) - ($page)) > 0 ? true : false;
        $data = array('list'=>$data, 'hasnext'=>$hasnext, 'curpage'=>$page, 'totalCount'=>$total);
        return $data;
    }

    private function getFormatData($total, $data, $page){
        $result = array();
        $i = 0;
        foreach ($data as $key => $value) {
            $num = $i + (($page - 1) * $this->perpage);
            if ($num >= $total) break;
            if ($value['game_id']) {
                $gameId = $value['game_id'];
            } else {
                $gameId = $value['id'];
            }
            $info = Resource_Service_GameData::getGameAllInfo($gameId);
            //附加属性处理,1：礼包
            $attach = 0;
            if ($info['gift']) $attach = 1;
            $item = array(
                'img' => urldecode($info['img']),
                'name' => html_entity_decode($info['name']),
                'resume' => html_entity_decode($info['resume']),
                'package' => $info['package'],
                'link' => $info['link'],
                'gameid' => $info['id'],
                'size' => $info['size'] . 'M',
                'category' => $info['category_title'],
                'attach' => intval($attach),
                'hot' => Resource_Service_Games::getSubscript($info['hot']),
                'viewType' => 'GameDetailView',
                'score' => $info['client_star'],
                'freedl' => $info['freedl'],
                'reward' => $info['reward']
            );
            $result[] = $item;
            $i++;
        }
        return $result;
    }
}