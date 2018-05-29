<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * @author tiansh
 *
 */
class Apk_ProdController extends Api_BaseController {
	
	public $perpage = 10;
	public $actions = array(
				'indexUrl'=>'/api/shop/index',
				'detailUrl'=>'/shop/detail',
				'tjUrl'=>'/index/tj'
	);
	public $cacheKey = 'Apk_Shop_index';


	public function indexAction() {
        $page = intval($this->getInput('page'));
        $uid = Common::getAndroidtUid();
        if ($page < 1) $page = 1;
        $webroot = Common::getWebRoot();
        $tjUrl = $webroot . $this->actions['tjUrl'];
        $staticroot = Yaf_Application::app()->getConfig()->staticroot;

        $search = array('status' => 1, 'start_time' => array('<=',time()));
        list($total, $goods) = Mall_Service_Goods::getList($page, $this->perpage, $search);

        $fav = array();
        $num_iids = array_keys(Common::resetKey($goods, 'num_iid'));
        $topApi  = new Api_Top_Service();
        $infos = array();
        if (count($num_iids)) {
            $infos = $topApi->tbkMobileItemsConvert(array('num_iids' => implode(',', $num_iids)));
            if (isset($infos['num_iid'])) $infos = array($infos);
            $infos = Common::resetKey($infos, 'num_iid');
        }
        $cond = array(
            'uid' => $uid,
            'item_id' => array('IN', array_filter($num_iids))
        );
        //fav in list by uid
        list(, $favList) = User_Service_Favorite::getsBy($cond, array());

        $favList = Common::resetKey($favList, 'item_id');
        foreach ($favList as $k => $v) {
            $fav[$k] = $v['id'];
        }

        $data = array();
        $i = 1;
        $pager = ($page - 1) * $this->perpage;
        $num_iids_arr = array_keys($infos);
        $ids = array();
        foreach ($goods as $key => $val) {
            if(!in_array($val['num_iid'], $num_iids_arr)){
                $ids[] = $val['id'];
                continue ;
            }
            $item['id'] = $val['id'];
            $item['is_favorite'] = 0;
            if (!empty($fav[$val['num_iid']])) {
                $item['is_favorite'] = 1;
                $item['fav_id'] = $fav[$val['num_iid']];
            }
            $item['item_id'] = $val['num_iid'];
            $item['title']   = $val['title'];
            $item['price']   = $val['price'];
            $item['img']     = Common::getImageUrl($val['img']);
            $item['url']     = Common::getWebRoot()."/prod/detail?id=".$val['num_iid'];
            $data[] = $item;
            $i++;
        }
        Mall_Service_Goods::updates($ids,array('status'=>0));
        $hasnext = (ceil((int)$total / $this->perpage) - ($page)) > 0 ? true : false;
        $this->output(0, '', array('list' => $data, 'hasnext' => $hasnext, 'curpage' => $page));
	}

}
