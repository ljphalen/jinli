<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * @author tiansh
 *
 */
class Market_ConfigController extends Api_BaseController {
	
	public function getConfigAction () {
		$passport_config = Common::getConfig('passportConfig');
		$config = array(
				'taobao_cart_url'=>'http://cart.taobao.com',
				'taobao_order_url'=>'http://order.taobao.com',
				'taobao_favorite_url'=>'http://favorite.taobao.com',
				'alipay_url'=>'http://m.alipay.com/appIndex.htm',
				'secret'=>array(
						'gionee_secret'=>$passport_config['gionee']['secret'],
						'qq_secret'=>$passport_config['qq']['secret'],
						'weibo_secret'=>$passport_config['weibo']['secret'],
						'taobao_secret'=>$passport_config['taobao']['secret']
				)
		);
		$this->clientOutput(0, 'success', $config);
	}
}
