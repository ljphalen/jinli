<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * 游戏线上版本数据常驻缓存，数据采用hash结构存储。
 * 应用于前台服务器，提高游戏基本数据的响应速度。
 * @author fanch
 *
 */
class Resource_Service_GameData extends Common_Service_Base{

    /**
     * 通过游戏id获取单条游戏数据
     * @param $gameId
     * @return array
     */
	public static function getGameAllInfo($gameId){
		$data = self::_getGameCacheData($gameId);
		return self::_makeInfoData($data);
	}

    /**
     * 游戏列表中单条游戏数据获取
     * @param $gameId
     * @return array
     */
    public static function getBasicInfo($gameId){
        $data = self::_getGameCacheData($gameId);
        return self::_makeItemBaseData($data);
    }

    /**
	 * 游戏列表中单条游戏附加数据
	 * @param int $gameId
	 * @return  array NULL
	 */
	public static function getExtraInfo($gameId){
		return self::_makeItemExtraData($gameId);
	}

    /**
     * 根据单个游戏获取主次分类数据
     * @param $gameId
     * @return array
     */
    public static function getCategory($gameId){
        $data = self::_getGameCacheData($gameId);
        return self::_makeCategoryData($data);
    }

    /**
     * 游戏列表单条游戏基本数据
     * @param $data
     * @return array
     */
    private static function _makeItemBaseData($data){
        if(empty($data)) return array();
        $img =  $data['big_img'] ? $data['big_img']: ($data['mid_img'] ? $data['mid_img'] : $data['img']);
        $downLink = self::filterDownloadUrl ( $data['link'] );
        $downloadCount = $data['download_status']?$data['default_downloads']:$data['downloads'];
        $downloadCount = Resource_Service_GameListFormat::formartNumber($downloadCount);
        $result= array(
            'img' => Resource_Service_Games::webpProcess($img, $data['webp']),
            'name' => $data['name'],
            'resume'=>$data['resume'],
            'package'=>$data['package'],
            'link'=>$downLink,
            'gameid'=>$data['id'],
            'size'=>$data['size'].'M',
            'category'=>self::_getGameAttributeTitle($data['category_id']),
            'hot' => self::_getGameHotMark($data['hot']),
            'attach' => 0,
            'score' => 0,
            'freedl' => '',
            'reward' => '',
        	'downloadCount'=>$downloadCount,
        );
        return $result;
    }

    public  static function filterDownloadUrl($link) {
		if(!$link){
			return ;
		}
		if(strpos($link, '?') !== false){
			$link = explode('?', $link);
			$downLink = $link[0];
		}else{
			$downLink = $link;
		}
		return $downLink;
	}

    /**
     * 游戏列表单条游戏扩展属性
     * @param $gameId
     * @return array
     */
	private static function _makeItemExtraData($gameId){
        if(empty($gameId)) return array();
        $result = Resource_Service_GameExtraData::getGamesExtraData(array($gameId));
        if(empty($result)) return array();
		$gameExtra = $result[$gameId];
        $gameExtra['score']  = $gameExtra['score'] ? $gameExtra['score']/2 : 0;
		return $gameExtra;
	}

    /**
     * 组装单个游戏的主子分类数据
     * v1.5.6
     * @param $data
     * @return array
     */
    private static function _makeCategoryData($data){
        if(empty($data)) return array();
        //主分类/子分类
        $main = json_decode($data['mainCategory'], true);
        $result['mainCategory'] = array(
            'parent' =>	array(
                'id'=> $main[0],
                'title'=> self::_getGameAttributeTitle($main[0]),
            ),
            'sub'=> array(
                'id'=> $main[1],
                'title'=> self::_getGameAttributeTitle($main[1]),
            )
        );
        //次分类/子分类
        $less = json_decode($data['lessCategory'], true);
        if($less){
            $result['lessCategory'] = array(
                'parent' =>	array(
                    'id'=> $less[0],
                    'title'=> self::_getGameAttributeTitle($less[0]),
                ),
                'sub'=> array(
                    'id'=> $less[1],
                    'title'=> self::_getGameAttributeTitle($less[1]),
                )
            );
        } else {
            $result['lessCategory'] = array();
        }
        return $result;
    }

    /**
     * 兼容版本游戏数据组装
     * @param $data
     * @return array
     */
    private static function _makeInfoData($data){
        if(empty($data)) return array();
        $data['link'] = self::filterDownloadUrl ( $data['link'] );
        $downloadCount = $data['download_status']?$data['default_downloads']:$data['downloads'];
        $downloadCount = Resource_Service_GameListFormat::formartNumber($downloadCount);
        $result = array(
            'id' => $data['id'],
            'appid' => $data['appid'],
            'sort' => $data['sort'],
            'name' => html_entity_decode($data['name']),
            'resume' => html_entity_decode($data['resume']),
            'label' => $data['label'],
            'img' => $data['img'],
            'mid_img' => $data['mid_img'],
            'big_img' => $data['big_img'],
            'language' => $data['language'],
            'package' => $data['package'],
            'packagecrc' => $data['packagecrc'],
            'price' => $data['price'],
            'company' => $data['company'],
            'descrip' => $data['descrip'],
            'tgcontent' => $data['tgcontent'],
            'create_time' => $data['create_time'],
            'online_time' => $data['online_time'],
            'status' => $data['status'],
            'hot' => $data['hot'],
            'cooperate' => $data['cooperate'],
            'developer' => $data['developer'],
            'certificate' => $data['certificate'],
            'secret_key' => $data['secret_key'],
            'api_key' => $data['api_key'],
            'agent' => $data['agent'],
            'level' => $data['level'],
            'downloads' => $data['downloads'],
            'webp' => $data['webp'],
            'op_time' => $data['op_time'],
            'size' => $data['size'],
            'version' => $data['version'],
            'md5_code' => $data['md5_code'],
            'link' => $data['link'],
            'min_sys_version' => $data['min_sys_version'],
            'min_resolution' => $data['min_resolution'],
            'max_resolution' => $data['max_resolution'],
            'version_code' => $data['version_code'],
            'vcreate_time' => $data['vcreate_time'],
            'update_time' => $data['update_time'],
            'changes' => $data['changes'],
            'version_id' => $data['version_id'],
            'category_title' => self::_getGameAttributeTitle($data['category_id']),
            'category_id' => $data['category_id'],
            'min_resolution_title' => '240*320',
            'max_resolution_title' => '1080*1920',
            'min_sys_version_title' => '1.6',
            'hot_title' =>  self::_getGameAttributeTitle($data['hot']),
            'hot_id' => $data['hot'],
            'price_title' => self::_getGameAttributeTitle($data['price']),
            'device' => $data['device'],
            'signature_md5' => $data['signature_md5'],
            'gimgs' => json_decode($data['gimgs'], true),
            'simgs' => json_decode($data['simgs'], true),
            'hdimgs' => json_decode($data['hdimgs'], true),
        	'downloadCount'=>$downloadCount,
        );
        //icon 特殊处理必须放到末尾
        $icon = $data['big_img'] ? $data['big_img']: ($data['mid_img'] ? $data['mid_img'] : $data['img']);
        $result['img'] = Resource_Service_Games::webpProcess($icon, $data['webp']);
        //截图处理
        $screenImg = self::getScreenImg($result);
        $result['gimgs'] = self::getScreenWebpImg($screenImg, $data['webp']);
        $result['simgs'] = self::getScreenWebpImg($result['simgs'], $data['webp']);

        //历史版本兼容数据
        $result['infpage']  = sprintf("%s,%s,%s,%s,Android%s,%s-%s",
            $data['id'],
            $data['link'],
            $data['package'],
            $data['size'],
            '1.6',
            '240*320',
            '1080*1920');
        //评分数据
        $extra = self::_makeItemExtraData($data['id']);
        $result['client_star']  = $extra['score'] ? $extra['score'] : 0;
        $result['web_star']  = $extra['score'] ? $extra['score']*2 : 0;
        //免流量标识
        $result['freedl'] = $extra['freedl'];
        //有奖标识
        $result['reward'] = $extra['reward'];
        //礼包标识
        $result['gift'] = $extra['gift'];
        return $result;
    }

    private static function getScreenImg($gameInfo){
        $hdImg = $gameInfo['hdimgs'];
        $apkVer = Yaf_Registry::get("apkVersion");
        if (strnatcmp($apkVer, '1.6.0') < 0){
            return $gameInfo['gimgs'];
        } else {
            return ($gameInfo['hdimgs'] && (count($gameInfo['hdimgs']) == count($gameInfo['gimgs']))) ? $gameInfo['hdimgs'] : $gameInfo['gimgs'];
        }

    }

    /**
     * @param $webpFlag
     * @param $result
     * @param $gimgs
     * @return array
     */
    private static function getScreenWebpImg($imgData, $webpFlag) {
        $result = array();
        foreach ($imgData as $img) {
            $result[] = Resource_Service_Games::webpProcess($img, $webpFlag);
        }
        return $result;
    }

    public function getOffLineGameIcon($gameId) {
        $gameInfo = Resource_Service_Games::getBy(array('id'=>$gameId));
        $icon = $gameInfo['big_img'] ? $gameInfo['big_img']: ($gameInfo['mid_img'] ? $gameInfo['mid_img'] : $gameInfo['img']);
        return Resource_Service_Games::webpProcess($icon, $flag=0);
    }


    /**
     * 游戏角标处理
     * @param int $id 游戏角标hot 字段id
     * @return string
     */
    private static function _getGameHotMark($id) {
        if(!$id) return 0;
        $ret = Resource_Service_Attribute::get($id);
        return intval($ret['parent_id']);
    }

    /**
     * 获取游戏属性标题
     * @param $attributeId
     * @return string
     */
    private static function _getGameAttributeTitle($attributeId){
        $result = '';
        if ($attributeId <= 0) return $result;
        $item = Resource_Service_AttributeCache::getAttributeByid($attributeId);
        if($item) $result = $item['title'];
        return $result;
    }

    /**
     * @param $gameList
     * @return array|bool
     */
    public static function getFillGameInfoData($gameList) {
        if(is_array($gameList)) {
            return false;
        }
        foreach ($gameList as $ke=>$va){
            $gameInfo = Resource_Service_GameData::getBasicInfo($va['game_id']);
            $extalInfo = Resource_Service_GameData::getExtraInfo($va['game_id']);
            if($gameInfo){
                $data[] = array(
                    'viewType'=>'GameDetailView',
                    'ad_id'=>$va['id'],
                    'gameId'=>$va['game_id'],
                    'img'=>$gameInfo['img'],
                    'name'=>html_entity_decode($gameInfo['name']),
                    'size'=>$gameInfo['size'].'M',
                    'link'=>$gameInfo['link'],
                    'resume'=>html_entity_decode($gameInfo['resume']),
                    'package'=>$gameInfo['package'],
                    'category'=>$gameInfo['category'],
                    'hot'=>$gameInfo['hot'],
                    'score'=>$extalInfo['score'],
                    'freedl'=>$extalInfo['freedl'],
                    'reward'=>$extalInfo['reward'],
                    'attach'=>$extalInfo['attach'],
                	'downloadCount'=>$gameInfo['downloadCount']
                );
            }
        }
        return $data;
    }


    /**
     * 游戏缓存数据
     * @param int $gameId
     * @return array
     */
    private static function _getGameCacheData($gameId){
        $data = Resource_Service_GameCache::getGameInfoFromCache($gameId);
        return $data;
    }
}