<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * H5天天特价跳转处理
 * @author huangsg
 *
 */
class TejiaController extends App_BaseController {
	
	public function indexAction(){
		
	}
	
	/**
	 * '1'=>'梦芭莎',
		'2'=>'移淘',
		'3'=>'掌购',
		'4'=>'麦包包',
		'5'=>'拍鞋网',
	 * 统计商品点击跳转
	 * @return boolean
	 */
	public function redirectAction(){
		$id = $this->getInput('id');
		$channel_type = $this->getInput('channel_type');
		if (empty($id)) return false;
		$goodsInfo = Client_Service_Channelgoods::getGoods($id);
		if (empty($goodsInfo)) return false;
		Client_Service_Channelgoods::updateGoodsHits($id, 'hits_h5');	//更新点击量
		$goodsUrl = html_entity_decode($goodsInfo['link']);
		$goodsUrl .= strpos($goodsUrl, '?') === false ? '?' : '&';	//追加渠道号码

        //需要特殊处理
        if ($channel_type == 6) {
            $goodsUrl = str_replace('gionee3', 'gionee7', html_entity_decode($goodsInfo['link']));
        }

        $configs = Common::getConfig('tejiaConfig');
        $config = $configs[$channel_type];
        $goodsUrl = sprintf($config['apk_formart'], $goodsUrl);
        $this->redirect($goodsUrl);
	}
}
