<?php
if (!defined('BASE_PATH'))    exit('Access Denied!');
/**
 * 礼包开发者平台对接接口
 * @author fanch
 *
 */
class Developer_GiftController extends Api_BaseController{
     const ADD_GIFT_INFO_TAG = 'ADD_GIFT_INFO';
     const ADD_GIFT_CODES_TAG = 'ADD_GIFT_CODES';
     const LOG_FILE = 'open_gift.log';
    
    /**
     * 添加礼包基本信息
     */
    public function addInfoAction(){
        $inputData = $this->getInput(array('gameId', 'giftName','content', 'method', 'effectStartTime', 'effectEndTime', 'sign'));
        Util_Log::info(self::ADD_GIFT_INFO_TAG, self::LOG_FILE, array('添加礼包请求参数：', $inputData));
        $addData = $this->cookInfoData($inputData);
        
        $addItem = array(
                'game_id'=> $addData['gameId'],
                'name' => $addData['giftName'],
                'content' => base64_decode($addData['content']),
                'method' => base64_decode($addData['method']),
                'effect_start_time' => $addData['effectStartTime'],
                'effect_end_time' => $addData['effectEndTime'],
                'use_start_time' => $addData['effectStartTime'],
                'use_end_time' => $addData['effectEndTime'],
                'status' => Client_Service_Gift::GIFT_STATE_CLOSEED,
                'game_status' => Resource_Service_Games::STATE_ONLINE
        );
        
        $giftId = Client_Service_Gift::addGiftBaseInfo($addItem);
        if(!$giftId){
            Util_Log::err(self::LOG_TAG, self::LOG_FILE, array('添加礼包数据：', $addItem));
            $this->output(-1, '礼包添加失败');
        }
        
        $data = array('giftId' => $giftId, 'createTime' => time());
        $this->output(0, '', $data);
    }
    
    /**
     * 添加礼包激活码
     */
    public function addCodeAction(){
        $inputData = $this->getInput(array('gameId', 'giftId','isLast', 'giftCodes', 'sign'));
        Util_Log::info(self::ADD_GIFT_CODES_TAG, self::LOG_FILE, array('添加礼包激活码请求参数：', $inputData));
        $codeData = $this->cookCodeData($inputData);
        
        $response = array();
        $addCode = array(
                'gift_id' => $codeData['giftId'],
                'game_id' => $codeData['gameId'],
        );
        
        $codes = json_decode(base64_decode($codeData['giftCodes']), true);
        foreach ($codes as $item){
            $addCode['activation_code'] = $item;
            $addResult = Client_Service_Giftlog::addGiftlog($addCode);
            $response[$item]= $addResult ? 1 : 0;
        }
        
        if($codeData['isLast']){
           $updateResult = Client_Service_Gift::updateBy(array('status' => Client_Service_Gift::GIFT_STATE_OPENED), array('id' => $codeData['giftId']));
           if(!$updateResult){
               Util_Log::err(self::LOG_TAG, self::LOG_FILE, array('礼包激活码添加完毕更新礼包开启状态错误。'));
           }
           Client_Service_Gift::updateGiftDataToCache($codeData['giftId']);
        }
        
        $data = array('giftCodes' => $response, 'createTime' => time());
        $this->output(0, '', $data);
    }
    
    /**
     * 接口请求数据有效性处理
     * @param array $data
     */
    private function cookInfoData($data){
        if(!$data['gameId']) $this->output(-1, '游戏ID不能为空.');
        if(!$this->isValidGame($data['gameId'])) $this->output(-1, '游戏不存在.');
        if(!$data['giftName']) $this->output(-1, '礼包名称不能为空.');
        if(!$data['content']) $this->output(-1, '礼包内容不能为空.');
        if(!$data['method']) $this->output(-1, '礼包使用说明不能为空.');
        if(!$data['effectStartTime']) $this->output(-1, '礼包生效开始时间不能为空.');
        if(!$data['effectEndTime']) $this->output(-1, '礼包生效结束时间不能为空.');
        $verifyResult = $this->verifySign($data);
        if(!$verifyResult) $this->output(-1, '非法请求.');
        return $data;
    }
    
    /**
     * 接口请求数据有效性处理
     * @param array $data
     */
    private function cookCodeData($data){
        if(!$data['gameId']) $this->output(-1, '游戏ID不能为空.');
        if(!$this->isValidGame($data['gameId'])) $this->output(-1, '游戏不存在.');
        if(!$data['giftId']) $this->output(-1, '礼包ID不能为空.');
        if(!$data['giftCodes']) $this->output(-1, '礼包激活码不能为空.');
        $codes = json_decode(base64_decode($data['giftCodes']), true);
        if(empty($codes)){
            $this->output(-1, '礼包激活码不能为空.');
        }
        $verifyResult = $this->verifySign($data);
        if(!$verifyResult) $this->output(-1, '非法请求.');
        return $data;
    }
    
    /**
     * 有效的游戏判断
     * @param unknown $gameId
     * @return boolean
     */
    private function isValidGame($gameId){
        $gameInfo = Resource_Service_GameData::getBasicInfo($gameId);
        if(!$gameInfo) return false;
        return true;
    }
    
   /**
    * 校验签名
    * @param array $data
    */
    private function verifySign($data){
        $pubKey = Common::getConfig("siteConfig", "rsaPubFile");
    	$rsa = new Util_Rsa();
    	$result = $rsa->verifySign($data, $data['sign'], $pubKey);
    	return $result;
    }

}