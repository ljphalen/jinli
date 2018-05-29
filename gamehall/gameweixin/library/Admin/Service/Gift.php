<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 
 * @author yinjiayan
 *
 */
class Admin_Service_Gift {
	
    const CODE_TYPE_SINGLE = 1;
    const CODE_TYPE_MULTI = 2;
    
	/**
	 * 
	 * @author yinjiayan
	 * @param number $page
	 * @param number $limit
	 * @param unknown $params
	 * @return multitype:unknown multitype:
	 */
	public static function getList($page = 1, $perpage = 20, $params = array()) {
		if ($page < 1) {
		    $page = 1;
		}
		$start = ($page -1) * $perpage;
		$params['deleted'] = 0;
		self::updateStatus();
		$list = self::getDao()->getList(intval($start), intval($perpage), $params, array('id'=>'DESC'));
		$total = self::getDao()->count($params);
		return array($total, $list); 
	}
	
	private static function updateStatus() {
	    self::getDao()->updateBy(array('status' => 0), array('status' => 1, 'activity_end_time' => array('<', time())));
	}
	
	/**
	 * 
	 * @author yinjiayan
	 * @param unknown $id
	 */
	public static function delete($id) {
	    Common_Service_Base::beginTransaction();
	    $logParams = array(
	                    'gift_bag_id' => $id,
	    );
	    $grabLog = Admin_Service_GiftGrabLog::getBy($logParams);
	    $result = false;
	    if ($grabLog) {
    	    $result = self::getDao()->update(array('deleted' => 1, 'status' => 0), $id);
	    } else {
	        $result = self::getDao()->delete($id);
	    }
	    if (!$result) {
	    	Common_Service_Base::rollBack();
	    	return false;
	    }
	    
	    $delCodeResult = Admin_Service_GiftCode::deleteByBagId($id);
	    if (!$delCodeResult) {
	        Common_Service_Base::rollBack();
	        return false;
	    }
	    
	    Common_Service_Base::commit();
		return true;
	}
	
	public function add($params, $code) {
	    if ($params['code_type'] == Admin_Service_Gift::CODE_TYPE_SINGLE) {
	    	return self::addSingleGife($params, $code);
	    }
	    
	    return self::addMultiGift($params, $code);
	}
	
	private static function addSingleGife($params, $singleCode) {
	    Common_Service_Base::beginTransaction();
	    $params['total'] = 1;
	    $params['residue'] = 1;
	    $ret = self::getDao()->insert($params);
	    if (!$ret) {
	        Common_Service_Base::rollBack();
	        return false;
	    }
	    
	    $id = self::getDao()->getLastInsertId();
	    $installCodeRet = Admin_Service_GiftCode::installSingleCode($id, $singleCode);
	    if (!$installCodeRet) {
	        Common_Service_Base::rollBack();
	        return false;
	    }
	    
	    Common_Service_Base::commit();
	    return true;
	}
	
	private static function addMultiGift($params, $codeFilePath) {
	    Common_Service_Base::beginTransaction();
	    $ret = self::getDao()->insert($params);
	    if (!$ret) {
	        Common_Service_Base::rollBack();
	        return false;
	    }
	    
        $id = self::getDao()->getLastInsertId();
        $installCodeRet = Admin_Service_GiftCode::installByFile($id, $codeFilePath);
        if (!$installCodeRet) {
        	Common_Service_Base::rollBack();
        	return false;
        }
        
        list($newCount) = $installCodeRet;
        if ($newCount > 0) {
            $gift = self::getDao()->get($id);
            $countData = array(
                            'total' => $gift['total'] + $newCount, 
                            'residue' => $gift['residue'] + $newCount
            );
            $updateRet = self::getDao()->update($countData, intval($id)); 
            if (!$updateRet) {
            	Common_Service_Base::rollBack();
            	return false;
            }
        } else {
            Common_Service_Base::rollBack();
            return false;
        }
        
	    Common_Service_Base::commit();
	    return true;
	}
	
	public static function edit($id, $params, $code) {
	    if ($params['code_type'] == Admin_Service_Gift::CODE_TYPE_SINGLE) {
	        return self::editSingleGife($id, $params, $code);
	    }
	    $codeFilePath = $code ? $code : 'none';
	    $result = self::editMultiGift($id, $params, $codeFilePath);
	    if ($result) {
	        @unlink(BASE_PATH.$codeFilePath);
	    }
	    return $result;
	}
	
	private static function editSingleGife($id, $params, $singleCode) {
	    Common_Service_Base::beginTransaction();
	    $installCodeRet = Admin_Service_GiftCode::installSingleCode($id, $singleCode);
	    if (!$installCodeRet) {
	        Common_Service_Base::rollBack();
	        return false;
	    }
	    $params['total'] = 1;
        $params['residue'] = 1;
	    $updateRet = self::getDao()->update($params, intval($id));
	    if (!$updateRet) {
	        Common_Service_Base::rollBack();
	        return false;
	    }
	     
	    Common_Service_Base::commit();
	    return true;
	}
	
	private static function editMultiGift($id, $params, $codeFilePath='none') {
	    Common_Service_Base::beginTransaction();
	    if ($codeFilePath != 'none') {
	        $newCount = 0;
    	    $installCodeRet = Admin_Service_GiftCode::installByFile($id, $codeFilePath);
            if (!$installCodeRet) {
            	Common_Service_Base::rollBack();
            	return false;
            } else {
                $newCount = $installCodeRet[0];
            }
    	    if ($newCount > 0) {
    	    	$gift = self::getDao()->get($id);
                $params['total'] = $gift['total'] + $newCount;
                $params['residue'] = $gift['residue'] + $newCount;
    	    }
	    }
	    
        $updateRet = self::getDao()->update($params, intval($id));
        if (!$updateRet) {
            Common_Service_Base::rollBack();
            return false;
        }
	    
	    Common_Service_Base::commit();
	    return true;
	}
	
	public static function lockGift($giftId, $giftCodeType, $uuid) {
	    Common_Service_Base::beginTransaction();
        $giftCode = self::getUsableCode($giftId);
        if (!$giftCode) {
            Common_Service_Base::rollBack();
            return false;
        }
        
        if ($giftCodeType == Admin_Service_Gift::CODE_TYPE_MULTI) {
            $lockResult = Admin_Service_GiftCode::update(array('is_usable' => 0), $giftCode['id']);
            if (!$lockResult) {
            	Common_Service_Base::rollBack();
                return false;
            }
            $result = self::getDao()->increment('residue', array('residue' => array('>', 0), 'id' => $giftId), -1);
            if (!$result) {
            	Common_Service_Base::rollBack();
                return false;
            }
        }
        
        $logResult = self::insertCodeToLog($giftId, $giftCode['code'], $uuid);
        if (!$logResult) {
            Common_Service_Base::rollBack();
            return false;
        }
        
        Common_Service_Base::commit();
        return $giftCode;
	}
	
	private static function getUsableCode($giftId) {
	    $param = array(
	                    'gift_bag_id' => $giftId,
	                    'is_usable' => 1
	    );
	    return Admin_Service_GiftCode::getBy($param);
	}
	
	private static function insertCodeToLog($giftId, $giftCodeStr, $uuid) {
	    $giftLogParam = array(
	                    'gift_bag_id' => $giftId,
	                    'owner_uuid' => $uuid
	    );
	    $log = Admin_Service_GiftGrabLog::getBy($giftLogParam);
	    $logResult = false;
	    if ($log) {
    	    $grabLogData = array(
    	                    'code' => $giftCodeStr,
    	                    'status' => Admin_Service_GiftGrabLog::STATUS_LOCK
    	    );
    	    $logResult = Admin_Service_GiftGrabLog::updateBy($grabLogData, $giftLogParam);
	    } else {
	        $logData = array(
	                        'gift_bag_id' => $giftId,
	                        'owner_uuid' => $uuid,
	                        'code' => $giftCodeStr,
	                        'status' => Admin_Service_GiftGrabLog::STATUS_LOCK
	        );
	        $logResult = Admin_Service_GiftGrabLog::install($logData);
	    }
	    return $logResult;
	}
	
	public static function getsBy($params) {
	    return self::getDao()->getsBy($params);
	}
	
	/**
	 * 
	 * @author yinjiayan
	 * @param unknown $id
	 */
	public static function getById($id) {
	    return self::getDao()->get(intval($id));
	}
	
	public static function getGiftUrl($giftId, $openId) {
	    $webRoot = Common::getWebRoot();
	    $sign = self::getGiftLinkSign($giftId, $openId);
	    return $webRoot.'/Front/Gift/gift?token='.$openId.'&id='.$giftId.'&sign='.$sign;
	}
	
	public static function getGiftLinkSign($giftId, $openId) {
	    $giftSecreKey = Common::getConfig('siteConfig', 'giftSecreKey');
	    $paramText = trim($giftId).$giftSecreKey.trim($openId);
	    return md5($paramText);
	}
	
	public static function getMyGiftListUrl($openId) {
	    $webRoot = Common::getWebRoot();
	    return $webRoot.'/Front/Gift/index?token='.$openId;
	}
	
	/**
	 * 
	 * @author yinjiayan
	 * @return Admin_Dao_Gift
	 */
	private static function getDao() {
		return Common::getDao("Admin_Dao_Gift");
	}
}