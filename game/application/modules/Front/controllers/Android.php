<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author fanch
 *
 */
class AndroidController extends Front_BaseController{
	
	/**
	 * 手机版首页
	 */
	public function indexAction(){
		Common::addSEO($this,'客户端下载页');
		//取得配置文件
		$ami_web_url = Game_Service_Config::getValue('ami_web_url');
		$this->assign('ami_web_url', $ami_web_url);
		
		$ami_web_weixin = Game_Service_Config::getValue('ami_web_weixin');
		$this->assign('ami_web_weixin', $ami_web_weixin);
		
		$ami_sina_weibo = Game_Service_Config::getValue('ami_sina_weibo');
		$this->assign('ami_sina_weibo', $ami_sina_weibo);

		//取得图片
		$ami_imgs = json_decode(Game_Service_Config::getValue('ami_web_imgs'),true);
		$this->assign('imgs', $ami_imgs);
	
	}
}