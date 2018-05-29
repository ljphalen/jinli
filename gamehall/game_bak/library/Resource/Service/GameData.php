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
    const REWARD_EXPIRE = 60;
	/**
	 * 通过游戏id获取单条游戏数据
	 * @param int $gameId
	 */
	public static function getGameAllInfo($gameId){
		$data = self::_getGameCacheData($gameId);
		return self::_makeInfoData($data);
	}

	/**
	 * 游戏列表中单条游戏数据获取
	 * @param int $gameId
	 */
	public static function getBasicInfo($gameId){
		$data = self::_getGameCacheData($gameId);
		return self::_makeItemBaseData($data);
	}
	
	/**
	 * 游戏列表中单条游戏附加数据
	 * @param int $gameId
	 * @return multitype:string number array NULL
	 */
	public static function getExtraInfo($gameId){
		return self::_makeItemExtraData($gameId);
	}
	
	/**
	 * 根据单个游戏获取主次分类数据
	 * @param int $gameId
	 */
	public static function getCategory($gameId){
		$data = self::_getGameCacheData($gameId);
		return self::_makeCategoryData($data);
	}
	
	/**
	 * 游戏列表单条游戏基本数据
	 * @param array $data
	 * @return 
	 */
	private static function _makeItemBaseData($data){
		if(empty($data)) return array();
		$img =  $data['big_img'] ? $data['big_img']: ($data['mid_img'] ? $data['mid_img'] : $data['img']);
		
		$downLink = self::filterDownloadUrl ( $data['link'] );
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
	 * @param array $data
	 * @return 
	 */
	private static function _makeItemExtraData($gameId){
		$result = array(
			'attach' => self::_getGameGift($gameId),
			'freedl' => self::_getFreedlFlag($gameId),
			'reward' => self::_getRewardFlag($gameId)
		);
		$score = self::_getGameScore($gameId);
		$result['score']  = $score ? $score/2 : 0;
		return $result;
	}
	
	/**
	 * 组装单个游戏的主子分类数据
	 * v1.5.6
	 * @param int $gameId
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
     * @param array $data
     */
    private static function _makeInfoData($data){
    	if(empty($data)) return array();
    	$data['link'] = self::filterDownloadUrl ( $data['link'] );
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
    	);
    	//icon 特殊处理必须放到末尾
    	$icon = $data['big_img'] ? $data['big_img']: ($data['mid_img'] ? $data['mid_img'] : $data['img']);
    	$result['img'] = Resource_Service_Games::webpProcess($icon, $data['webp']);
    	//大图处理
    	$gimgs = $simgs = array();
    	foreach($result['gimgs'] as $value) {
    		$gimgs[]= Resource_Service_Games::webpProcess($value, $data['webp']);
    	}
    	$result['gimgs'] = $gimgs;
    	//小图处理
    	foreach($result['simgs'] as $value) {
    		$simgs[]= Resource_Service_Games::webpProcess($value, $data['webp']);
    	}
    	$result['simgs'] = $simgs;
    	
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
    	$score = self::_getGameScore($data['id']);
    	$result['client_star']  = $score ? $score/2 : 0;
    	$result['web_star']  = $score ? $score : 0;
    	//免流量标识
    	$result['freedl'] = self::_getFreedlFlag($data['id']);
    	//有奖标识
    	$result['reward'] = self::_getRewardFlag($data['id']);
    	//礼包标识
    	$result['gift'] = self::_getGameGift($data['id']);
    	return $result;
    }
    
    public function getOffLineGameIcon($gameId) {
        $gameInfo = Resource_Service_Games::getBy(array('id'=>$gameId));
        $icon = $gameInfo['big_img'] ? $gameInfo['big_img']: ($gameInfo['mid_img'] ? $gameInfo['mid_img'] : $gameInfo['img']);
        return Resource_Service_Games::webpProcess($icon, $flag=0);
    }

    /**
     * 游戏礼包标识
     * @param $gameId
     * @return
     */
    private static function _getGameGift($gameId){
    	$ckey = ":" . $gameId . ":gift";
    	$gift = self::_getCache()->get($ckey);
    	if($gift === false){
    		$result = Client_Service_IndexAdI::haveGiftByGame($gameId);
    		$gift = $result ? 1 : 0;
    		self::_getCache()->set($ckey, $gift, 60); //2分钟缓存
    	}
    	return $gift;
    }
    
    /**
     * 有奖下载 标识
     * @param $gameId
     * @return
     */
    public static function _getRewardFlag($gameId){
    	$apkVer = Yaf_Registry::get("apkVersion");
    	//版本低于1.5.5不支持直接返回
    	if (strnatcmp($apkVer, '1.5.5') < 0) return '';
    	
    	if (strnatcmp($apkVer, '1.5.8') < 0){
    	    $cacheKey = ":reward";
    	} else {
    	    $cacheKey = ":newReward";
    	}
    	
    	$newCkey = ":" . $gameId . $cacheKey;
    	$cacheRewardMark = self::_getCache()->get($newCkey);
    	if($cacheRewardMark){
    	    return $cacheRewardMark;
    	}
    	
		list($ticketCount,$isSendTicket) = self::getSendTicketActivity($gameId);
		if(strnatcmp($apkVer, '1.5.8') < 0){
		    if(!$isSendTicket) {
		        return '';
		    }
		    $cacheRewardMark = 'aticket';
		} else {
		    $cacheRewardMark = self::getGameReward($gameId, $isSendTicket, $ticketCount);
		}
	    self::_getCache()->set($newCkey, $cacheRewardMark, self::REWARD_EXPIRE);
    	
    	return $cacheRewardMark;
    }
    
    public static function updateRewardFlagCache($gameId){
        $cacheRewardKey = ":" . $gameId . ":reward";
        $cacheNewRewardKey = ":" . $gameId . ":newReward";
        
        list($ticketCount,$isSendTicket) = self::getSendTicketActivity($gameId);
        $cacheRewardMark = $isSendTicket ? 'aticket' : '';
        self::_getCache()->set($cacheRewardKey, $cacheRewardMark, self::REWARD_EXPIRE);
        
        $cacheNewRewardMark = self::getGameReward($gameId, $isSendTicket, $ticketCount);
        self::_getCache()->set($cacheNewRewardKey, $cacheNewRewardMark, self::REWARD_EXPIRE);
    }
    
    private static function searchTicketActivity(){
          return Client_Service_TaskHd::getsUnionGamesHds();
    }
    
    private static function getSendTicketActivity($gameId){
        $gameData = self::_getGameCacheData($gameId);
        $cooperate = $gameData['cooperate'];
        if($cooperate != Resource_Service_Games::COMBINE_GAME){
            return array(0, false);
        }
        
        //检查当前在线的联运游戏活动
        $items = self::searchTicketActivity();
        if(!$items){
            return array(0, false);
        }
        
        $rewordCount = 0;
        foreach($items as $value){
            $ruleContent = json_decode($value['rule_content'],true);
            if($value['game_object'] == Client_Service_TaskHd::TARGET_GAME_GAME_ALL){
                $rewordCount += $ruleContent['denomination'];
            }else if($value['game_object'] == Client_Service_TaskHd::TARGET_GAME_SINGLE_SUBJECT){
                $isGameExistSubject = self::checkGameInSuject($gameId, $value['subject_id']);
                if ($isGameExistSubject) {
                   $rewordCount += $ruleContent['denomination'];
                }
            }else if($value['game_object'] == Client_Service_TaskHd::TARGET_GAME_GAMEID_LIST){
                $gameIds = json_decode($value['game_object_addition_info'],true);
                $rewordIds = $gameIds['game_list'];
                if(in_array($gameId, $rewordIds)){
                    $rewordCount += $ruleContent['denomination'];
                }
               
            }
       }
       $isSendTicket = $rewordCount ? true : false;
      return array($rewordCount, $isSendTicket);
    }
    
    private static function getGameReward($gameId, $isSendTicket, $ticketCount){
        $parmes = $rewardMarkRArray = $rewardMark = array();
        if ($isSendTicket) {
            $rewardMark = array(
                    'rewardTypeCount' => Client_Service_GiftActivity::ONE_PRESENT,//'1',
                    'remindDes' => '安装登录游戏后，即可获得'.$ticketCount.'A券！',
                    'rewardStatisId' => Client_Service_GiftActivity::ATICKET_SIGN, //'3'
                    );
        }
        
        $parmes['game_id'] = $gameId;
        $parmes['status']= Client_Service_GiftActivity::GIFT_STATE_OPENED;
        $currentTime = strtotime(date('Y-m-d H:00:00'));
        $parmes['effect_start_time'] = array('<=', $currentTime);
        $parmes['effect_end_time'] = array('>', $currentTime);
        $giftActivityInfos = Client_Service_GiftActivity::getsBy($parmes);
        if(!$giftActivityInfos){
            return $isSendTicket ? $rewardMark : '';
        }
        
        $rewardMarkRArray['install'] = 0;
        $rewardMarkRArray['loginGame'] = 0;
        foreach($giftActivityInfos as $key=>$value){
            if($value['activity_type'] == Client_Service_GiftActivity::INSTALL_DOWNLOAD_GAME_SEND_GIFT){
                $rewardMarkRArray['install']++;
            } else {
                $rewardMarkRArray['loginGame']++;
            }
        }
        
         if($rewardMarkRArray['install'] == 1 && $isSendTicket && !$rewardMarkRArray['loginGame']){
                 $rewardMark = array(
                         'rewardTypeCount' => Client_Service_GiftActivity::TWO_PRESENT,//'2',
                         'remindDes' => '成功安装游戏后，既有机会获得限量礼包！\n安装登陆游戏后，即可获得'.$ticketCount.'券！',
                         'rewardStatisId' => Client_Service_GiftActivity::INSTALL_GAME_ATICKET_SIGN,//'5'
                 );
                 return $rewardMark;
         }
         
         if($rewardMarkRArray['install'] && !$isSendTicket && !$rewardMarkRArray['loginGame']){
             $rewardMark = array(
                     'rewardTypeCount' => Client_Service_GiftActivity::ONE_PRESENT,//'1',
                     'remindDes' => '成功安装游戏后，即有机会获得限量礼包！ ',
                     'rewardStatisId' => Client_Service_GiftActivity::INSTALL_GAME_SIGN,//'1'
             );
             return $rewardMark;
         }
         
         if($rewardMarkRArray['loginGame'] && $isSendTicket && !$rewardMarkRArray['install']){
             $rewardMark = array(
                     'rewardTypeCount' => Client_Service_GiftActivity::TWO_PRESENT,//'2',
                     'remindDes' => '安装登录游戏后，即可获得'.$ticketCount.'券，并有机会获得限量礼包！ ',
                     'rewardStatisId' => Client_Service_GiftActivity::LOGIN_GAME_ATICKET_SIGN,//'6'
             );
             return $rewardMark;
         }
         
         if($rewardMarkRArray['loginGame'] && !$isSendTicket && !$rewardMarkRArray['install']){
             $rewardMark = array(
                     'rewardTypeCount' => Client_Service_GiftActivity::ONE_PRESENT,//'1',
                     'remindDes' => '安装登录游戏后，即有机会获得限量礼包！',
                     'rewardStatisId' => Client_Service_GiftActivity::LOGIN_GAME_SIGN,//'2'
             );
             return $rewardMark;
         }
         
         if($rewardMarkRArray['loginGame'] && !$isSendTicket && $rewardMarkRArray['install']){
             $rewardMark = array(
                     'rewardTypeCount' => Client_Service_GiftActivity::TWO_PRESENT,//'2',
                     'remindDes' => '安装登录游戏后，即有机会获得限量礼包！\n成功安装游戏后，即有机会获得限量礼包！',
                     'rewardStatisId' => Client_Service_GiftActivity::INSTALL_LOGIN_GAME_SIGN,//'4'
             );
             return $rewardMark;
         }
         
         if($rewardMarkRArray['loginGame'] && $isSendTicket && $rewardMarkRArray['install']){
             $rewardMark = array(
                     'rewardTypeCount' => Client_Service_GiftActivity::THREE_PRESENT,//'3',
                     'remindDes' => '成功安装游戏后，即有机会获得限量礼包！\n安装登录游戏后，即可获得'.$ticketCount.'A券，并有机会获得限量礼包！',
                     'rewardStatisId' => Client_Service_GiftActivity::INSTALL_LOGIN_GAME_ATICKET_SIGN,//'7'
             );
             return $rewardMark;
         }
        
       
        
    }
    
    /**
     * 判断游戏是否在专题中
     * @param int $gameId
     * @param int $subjectId
     * @return boolean
     */
    private static function checkGameInSuject($gameId, $subjectId){
    	//判断该专题是否在线
    	$ret = Client_Service_Subject::getOnlineSubject($subjectId);
    	if(!$ret) return false;
    	$params['subject_id']  = $subjectId;
    	$params['game_status'] = 1;
    	$params['resource_game_id'] = $gameId;
    	//取出该专题中该游戏是否存在
    	list(, $games) = Client_Service_Game::getSubjectBySubjectId($params);
    	if(!$games) return false;
    	return true;
    }
    
    /**
     * 获取游戏游戏评分
     * @param int $gameId
     * @return
     */
    private static function _getGameScore($gameId){
    	$ckey = ":" . $gameId . ":score";
    	$score = self::_getCache()->get($ckey);
    	if($score === false){
    		$result = Resource_Service_Score::getByScore(array('game_id' => $gameId));
    		$score = $result['score'] ? $result['score'] : 0;
    		self::_getCache()->set($ckey, $score, 60); //1个小时变更一次
    	}
    	return $score;
    }
    
    /**
     * 游戏角标处理
     * @param int $id 游戏角标hot 字段id
     * @return string
     */
    private static function _getGameHotMark($id) {
    	if(!$id) return '';
    	$result = 0;
    	if (ENV == 'product') {
    		//正式
    		switch ($id) {
    			case 29:   //最新
    				$result = '1';
    			  	break;
    			case 30:  //最热
    			    $result = '2';
    			    break;
    			case 31:        //首发
    			   $result = '3';
    			   break;
    			case 102:        //活动
    				$result = '4';
    				break;
    		}
    	} else {
    		//测试
    		switch ($id) {
    			case 104:        //最新
    				$result = '1';
    				break;
    			case 105:        //最热
    				$result = '2';
    				break;
    			case 106:        //首发
    				$result = '3';
    				break;
    			case 116:        //活动
    				$result = '4';
    				break;
    		}
    	}
    	
    	return $result;
    }
    
    
    /**
     * 获取游戏的免流量标识
     * @param int $gameId
     */
    private static function _getFreedlFlag($gameId){
    	$apkVer = Yaf_Registry::get("apkVersion");
    	//版本低于1.5.3免流量不支持直接返回
    	if (strnatcmp($apkVer, '1.5.3') < 0) return '';
    	$freedlStr = '';
    	$tmp = array();
    	//当前进行的免流量活动增加5分钟缓存
    	$ckey='-freedl-now-hd';
    	$cache = Cache_Factory::getCache();
    	$has = $cache->exists($ckey);
    	if(!$has){
    		//当前时间戳
    		$todayTime = Common::getTime();
    		//获取今天当前进行中的活动
    		$data = Freedl_Service_Hd::getsByFreedl(array('status' => 1, 'start_time' => array('<=', $todayTime), 'end_time' => array('>=', $todayTime)), array('id' => 'ASC'));
    		$cache->set($ckey, $data, 60);
    	}else{
    		$data = $cache->get($ckey);
    	}
    	//没有活动直接返回
    	if(!$data) return "";
    
    	foreach ($data as $value){
    		switch($value['htype']){
    			case 1 ://广东移动专区免流量
    				$flag = Freedl_Service_Hd::checkFreedlGame($value['id'], $gameId);
    				if($flag) $tmp[] = 'cmcc19_' . $value['id'];
    				break;
    			case 2 ://广东联通全站免流量
    				$flag = Freedl_Service_Cugd::checkFreedlGame($gameId);
    				if($flag) $tmp[] = 'cu19_' . $value['id'];
    				break;
    		}
    	}
    	$freedlStr = implode('|', $tmp);
    	return $freedlStr;
    }
	
	/**
	 * 获取游戏属性标题
	 * @param int $attributeId
	 */
	private static function _getGameAttributeTitle($attributeId){
		$result = '';
		$item = Resource_Service_AttributeCache::getAttributeByid($attributeId);
		if($item) $result = $item['title'];
		return $result;
	}
	
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
						'attach'=>$extalInfo['attach']
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
	
	/**
	 * 获取cache实例
	 */
	private static function _getCache() {
		return Cache_Factory::getCache();
	}
}
