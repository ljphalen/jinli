<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 
 * @author yinjiayan
 *
 */
class Admin_Service_GiftCode {
	
    public static function uploadCodeFile() {
        if (($_FILES["file"]["type"] == "text/plain") && ($_FILES["file"]["size"] > 0) && ($_FILES["file"]["size"] < 500000)) {
            if ($_FILES["file"]["error"] > 0) {
                return false;
            } else {
                $filePath = 'data/tmp/';
                $fileName = 'code'.time().'.txt';
                $result = move_uploaded_file($_FILES["file"]["tmp_name"], BASE_PATH.$filePath. $fileName);
                if ($result) {
                	return $filePath.$fileName;
                } else {
                    return false;
                }
            }
        } else {
            return false;
        }
    }
    
    public static function installSingleCode($bagId, $singleCode) {
        $param = array('gift_bag_id' => $bagId);
        $code = self::getDao()->getBy($param);
        if ($code) {
        	if($code['code'] == $singleCode) {
        	    return true;
        	} else {
        	    $data['code'] = $singleCode;
        	    return self::getDao()->update($data, $code['id']);
        	}
        }
        
        $data = $param;
        $data['code'] = $singleCode;
        return self::getDao()->insert($data);
    }
    
    public static function installByFile($bagId, $codeFilePath) {
        $codes = self::getCodesByFile($codeFilePath);
        $exitsCount = 0;
        $newCount = 0;
        $total = count($codes);
        foreach ($codes as $code) {
            $exitsCode = self::getDao()->getBy(array('gift_bag_id' => $bagId, 'code' =>$code));
            if ($exitsCode) {
                $exitsCount ++;
            } else {
                self::getDao()->insert(array('gift_bag_id' => $bagId, 'code' =>$code));
                $newCount ++;
            }
        }
        if ($exitsCount + $newCount == $total) {
            return array($newCount, $exitsCount);
        } else {
        	return false;
        }
    }
    
    private static function getCodesByFile($codeFilePath) {
        $filename = BASE_PATH.$codeFilePath;
        $file = fopen($filename, "r");
        if (!$file) {
            return false;
        }
        $codes = array();
        while(!feof($file)) {
            $line= trim(fgets($file));
            if ($line) {
                $codes[] = $line;
            }
        }
        fclose($file);
        $codes = array_unique($codes);
        return count($codes) > 0 ? $codes : false;
    }
    
	public static function deleteByBagId($giftBagId) {
	    $params = array(
	                    'gift_bag_id' => $giftBagId
	    );
	    return self::getDao()->deleteBy($params);
	}
	
	public static function getBy($params) {
	    return self::getDao()->getBy($params);
	}
	
	public static function count($params) {
	    return self::getDao()->count($params);
	}
	
	public static function update($data, $id) {
	    return self::getDao()->update($data, $id);
	}
	
	public static function updateBy($data, $params) {
	    return self::getDao()->updateBy($data, $params);
	}
	
	/**
	 * 
	 * @author yinjiayan
	 * @return Admin_Dao_Gift
	 */
	private static function getDao() {
		return Common::getDao("Admin_Dao_GiftCode");
	}
}