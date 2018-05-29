<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * H5游戏轻应用
 */
class H5gameController extends Front_BaseController {

    /*
     * 首页
     */
    public function indexAction() {
        Gionee_Service_Log::incrBy(Gionee_Service_Log::TYPE_PV, '1:h5game_index');
        Gionee_Service_Log::toUVByCacheKey(Gionee_Service_Log::TYPE_UV, '1:h5game_index');
        $this->assign('cache', '');
    }

    /*
     * 搜索热词接口
    */
    public function keywordsAction() {
        //热词
        $hotwords = array();
        //默认关键字
        $keywords  = array();
        $json_data = array('hotwords' => $hotwords, 'default' => $keywords);
        $this->output(0, '', $json_data);
    }

    /*
     * 搜索接口
     */
    public function searchAction() {
        $like    = strtolower(trim($this->getInput('like')));
        $configs = Gionee_Service_Config::getAllConfig();
        $search  = array('status' => 1);
        if ($like) $search['tag'] = array('LIKE', $like);
        $num     = Gionee_Service_H5game::getDao()->count($search);
        $orderBy = array('sort' => 'DESC', 'id' => 'DESC');
        if ($num <= 10 && $num > 0) {
            $list        = Gionee_Service_H5game::getDao()->getList(0, $num, $search, $orderBy);
            $total       = Gionee_Service_H5game::getDao()->count($search);
            $search_data = self::_formatList($list);
            $hasnext     = '';

            $search        = array('type_id' => $list[0]['type_id']);
            $usercomm      = Gionee_Service_H5game::getDao()->getList(0, $configs['user_num'], $search, $orderBy);
            $usercomm_data = self::_formatList($usercomm);
        } elseif ($num > 10) {
            $total       = Gionee_Service_H5game::getDao()->count($search);
            $list        = Gionee_Service_H5game::getDao()->getList(0, $configs['search_num'], $search, $orderBy);
            $search_data = self::_formatList($list);

            $hasnext = (ceil(count($list) / $configs['search_num']) - 1) > 0 ? true : false;
        }

        $json_data = array(
            'search'   => $search_data,
            'total'    => $total,
            'usercomm' => $usercomm_data,
            'hasnext'  => $hasnext
        );
        $this->output(0, '', $json_data);
    }

    /*
     * 首页book接口
     */
    public function bookAction() {
        $page     = 1;
        $configs  = Gionee_Service_Config::getAllConfig();
        $app_type = Gionee_Service_H5game::getTypeDao()->getsBy();
        $app_type = Common::resetKey($app_type, 'id');
        $ad_data  = $must_data = $recommend_data = array();
        //广告
        $ad = Gionee_Service_Ad::getCanUseAds(1, 5, array('ad_type' => 9));
        foreach ($ad as $key => $value) {
            $ad_data[$key] = array(
                'id'   => $value['id'],
                'link' => Common::clickUrl($value['id'], 'APP_AD', $value['link']),
                'img'  => Common::getImgPath() . $value['img'],
            );
        }

        //必备
        $where     = array('is_must' => 1, 'status' => 1);
        $orderBy   = array('sort' => 'DESC', 'id' => 'DESC');
        $must      = Gionee_Service_H5game::getDao()->getList(0, $configs['must_num'], $where, $orderBy);
        $must_data = self::_formatList($must);


        //推荐
        $where          = array('is_recommend' => 1, 'status' => 1);
        $total          = Gionee_Service_H5game::getDao()->count($where);
        $recommend      = Gionee_Service_H5game::getDao()->getList(0, $configs['recommend_num'], $where, $orderBy);
        $recommend_data = self::_formatList($recommend);

        $hasnext    = (ceil((int)$total / $configs['recommend_num']) - ($page)) > 0 ? true : false;
        $json_data1 = array(
            'ads'       => $ad_data,
            'must'      => $must_data,
            'recommend' => $recommend_data,
            'hasnext'   => $hasnext
        );

        //排行
        $order     = array('hits' => 'DESC', 'id' => 'DESC');
        $where     = array('status' => 1);
        $total     = Gionee_Service_H5game::getDao()->count($where);
        $rank      = Gionee_Service_H5game::getDao()->getList(0, $configs['ranking_num'], $where, $order);
        $rank_data = self::_formatList($rank);

        $hasnext    = (ceil((int)$total / $configs['ranking_num']) - ($page)) > 0 ? true : false;
        $json_data2 = array('rank' => $rank_data, 'hasnext' => $hasnext);

        //分类
        $type_data = array();
        foreach ($app_type as $key => $value) {
            if ($value['name'] == '专题') {
                $link = '~loop:svt=topicList';
            } else {
                $link = '~loop:svt=cdetail/id/' . $value['id'];
            }
            $type_data[] = array(
                'id'      => $value['id'],
                'name'    => $value['name'],
                'descrip' => Util_String::substr($value['descrip'], 0, 12),
                'img'     => Common::getImgPath() . $value['img'],
                'link'    => $link,
            );
        }
        $json_data3 = array('category' => $type_data);

        //新品
        $order      = array('is_new' => 'DESC', 'id' => 'DESC');
        $where      = array('status' => 1);
        $total      = Gionee_Service_H5game::getDao()->count($where);
        $new        = Gionee_Service_H5game::getDao()->getList($page, $configs['new_num'], $where, $order);
        $new_data   = self::_formatList($new);
        $hasnext    = (ceil((int)$total / $configs['new_num']) - ($page)) > 0 ? true : false;
        $json_data4 = array('news' => $new_data, 'hasnext' => $hasnext);

        $json_data = array('t1' => $json_data1, 't2' => $json_data2, 't3' => $json_data3, 't4' => $json_data4);
        $this->output(0, '', $json_data);
    }

    /*
     * 分类应用列表接口
     */
    public function typelistAction() {
        $page    = 1;
        $type_id = intval($this->getInput('type_id'));
        Gionee_Service_Log::incrBy(Gionee_Service_Log::TYPE_PV, $type_id . ':h5game_list');
        Gionee_Service_Log::toUVByCacheKey(Gionee_Service_Log::TYPE_UV, $type_id . ':h5game_list');
        $configs   = Gionee_Service_Config::getAllConfig();
        $offset    = $configs['type_num'];
        $type      = Gionee_Service_H5game::getTypeDao()->get($type_id);
        $where     = array('type_id' => $type_id, 'status' => 1);
        $order     = array('sort' => 'DESC', 'id' => 'DESC');
        $total     = Gionee_Service_H5game::getDao()->count($where);
        $app       = Gionee_Service_H5game::getDao()->getList(0, $offset, $where, $order);
        $list      = self::_formatList($app);
        $hasnext   = (ceil((int)$total / $offset) - ($page)) > 0 ? true : false;
        $json_data = array('cate' => $list, 'cateName' => $type['name'], 'hasnext' => $hasnext, 'page' => $page);
        $this->output(0, '', $json_data);
    }


    /*
     * 加载更多接口
     */
    public function moreAction() {
        $configs = Gionee_Service_Config::getAllConfig();
        $type    = $this->getInput('type');
        $page    = intval($this->getInput('page'));
        if ($page < 2) $page = 2;
        $where     = $order = array();
        $themeName = '';
        if ($type == 'recommend') {//推荐
            $offset = $configs['recommend_num'];
            $where  = array('is_recommend' => 1, 'status' => 1);
            $order  = array('sort' => 'DESC', 'id' => 'DESC');
        } elseif ($type == 'rank') {//排行
            $offset = $configs['ranking_num'];
            $where  = array('status' => 1);
            $order  = array('hits' => 'DESC', 'id' => 'DESC');
        } elseif ($type == 'news') { //新品
            $offset = $configs['new_num'];
            $where  = array('status' => 1);
            $order  = array('is_new' => 'DESC', 'id' => 'DESC');
        } elseif ($type == 'cate') { //分类
            $offset  = $configs['type_num'];
            $type_id = $this->getInput('type_id');
            $where   = array('type_id' => $type_id, 'status' => 1);
            $order   = array('id' => 'DESC');
        } elseif ($type == 'theme') {//专题
            $offset    = $configs['theme_num'];
            $theme_id  = $this->getInput('type_id');
            $where     = array('theme_id' => $theme_id, 'status' => 1);
            $theme     = Gionee_Service_H5game::getCateDao()->get($theme_id);
            $themeName = $theme['name'];
        } elseif ($type == 'search') {//搜索
            $offset = $configs['search_num'];
            $like   = strtolower(trim($this->getInput('like')));
            $where  = array('status' => 1);
            if ($like) $where['tag'] = array('LIKE', $like);
        }

        $json_data = array();
        if (!empty($offset)) {
            $total     = Gionee_Service_H5game::getDao()->count($where);
            $tmp_list  = Gionee_Service_H5game::getDao()->getList(($page - 1) * $offset, $offset, $where, $order);
            $type_data = self::_formatList($tmp_list, $themeName);
            $hasnext   = (ceil((int)$total / $offset) - ($page)) > 0 ? true : false;
            $json_data = array($type => $type_data, 'hasnext' => $hasnext, 'page' => $page);
        }

        $this->output(0, '', $json_data);
    }


    private function _formatList($val, $themeName = '') {
        $app_type = Gionee_Service_H5game::getTypeDao()->getsBy();
        $app_type = Common::resetKey($app_type, 'id');
        $ret      = array();

        foreach ($val as $key => $value) {
            $appType   = !empty($themeName) ? $themeName : $app_type[$value['type_id']]['name'];
            $ret[$key] = array(
                'id'      => $value['id'],
                'link'    => Common::clickUrl($value['id'], 'H5GAME', $value['link']),
                'img'     => Common::getImgPath() . $value['img'],
                'name'    => $value['name'],
                'star'    => $value['star'],
                'descrip' => Util_String::substr($value['descrip'], 0, 12),
                'is_new'  => $value['is_new'],
                'appType' => $appType,
                'addUrl'  => Common::getCurHost() . '/h5game/to?id=' . $value['id']
            );
        }
        return $ret;
    }


    /*
	 * 专题列表接口
	*/
    public function themelistAction() {
        Gionee_Service_Log::incrBy(Gionee_Service_Log::TYPE_PV, 'topic:h5game_list');
        Gionee_Service_Log::toUVByCacheKey(Gionee_Service_Log::TYPE_UV, 'topic:h5game_list');
        $orderBy = array('sort' => 'DESC', 'id' => 'DESC');
        $themes  = Gionee_Service_H5game::getCateDao()->getsBy(array(), $orderBy);
        $list    = array();
        foreach ($themes as $key => $value) {
            $list[$key] = array(
                'id'   => $value['id'],
                'name' => $value['name'],
                'img'  => Common::getImgPath() . $value['icon'],
                'link' => Common::getCurHost() . '/webapp/themeapps?type_id=' . $value['id'],
            );
        }
        $json_data = array('theme' => $list, 'cateName' => '专题');
        $this->output(0, '', $json_data);
    }

    /*
     * 专题应用列表接口
    */
    public function themeappsAction() {
        $page      = 1;
        $theme_id  = intval($this->getInput('type_id'));
        $configs   = Gionee_Service_Config::getAllConfig();
        $offset    = $configs['theme_num'];
        $theme     = Gionee_Service_H5game::getCateDao()->get($theme_id);
        $where     = array('theme_id' => $theme_id, 'status' => 1);
        $order     = array('sort' => 'DESC', 'id' => 'DESC');
        $total     = Gionee_Service_H5game::getDao()->count($where);
        $app       = Gionee_Service_H5game::getDao()->getList(($page - 1) * $offset, $offset, $where, $order);
        $list      = self::_formatList($app, $theme['name']);
        $hasnext   = (ceil((int)$total / $offset) - ($page)) > 0 ? true : false;
        $json_data = array(
            'theme'    => $list,
            'cateName' => $theme['name'],
            'cateImg'  => Common::getImgPath() . $theme['img'],
            'hasnext'  => $hasnext,
            'page'     => $page
        );
        $this->output(0, '', $json_data);

    }


    public function toAction() {
        $id     = intval($this->getInput('id'));
        $appver = $this->getInput('app_ver');
        $arr    = explode('.', $appver);

        $app = Gionee_Service_H5game::getDao()->get($id);
        if (!$id || !$app) $this->output(-1, '非法请求');

        Gionee_Service_Log::incrBy(Gionee_Service_Log::TYPE_PV, $id . ':h5game_to');
        Gionee_Service_Log::toUVByCacheKey(Gionee_Service_Log::TYPE_UV, $id . ':h5game_to');

        if (!empty($appver) && $arr[0] == 4) {
            if ($app['icon2']) {
                $icon = $app['icon2'];
            } else {
                $icon = $app['img'];
            }
        } else {
            if ($app['default_icon']) {
                $icon = Gionee_Service_Config::getValue('default_icon');
            } elseif ($app['icon']) {
                $icon = $app['icon'];
            } else {
                $icon = $app['img'];
            }
        }

        $data = array(
            'appid' => $app['id'],
            'title' => $app['name'],
            'color' => $app['color'],
            'icon'  => Common::getImgContent($icon),
            'url'   => html_entity_decode($app['link'])
        );
        $this->output(0, '', $data);
    }

    /*
     * 统计控制
     */
    public function statsAction() {
        echo '1';
        exit;
    }
}