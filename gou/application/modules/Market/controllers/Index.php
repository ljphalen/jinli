<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class IndexController extends Market_BaseController {
	
	public $actions =array(
				'topic' => '/index/topic',
				'tjUrl'=>'/index/tj'
			);
	
	public $perpage = 4;

	public function indexAction() {
		$webroot = Common::getWebRoot();
		
		//$this->assign('subject_keyword', Gou_Service_Config::getValue("gou_subject_keyword"));
		
		//guidetypes
		list(, $guidetypes) = Gou_Service_GuideType::getCanUseGuideTypes(0, 100, array());
		$this->assign('guidetypes', $guidetypes);
		
		$this->assign('cache', Gou_Service_Config::getValue('gou_index_cache'));
		$this->assign('tjUrl', $webroot.$this->actions['tjUrl']);
		//$this->setLoginFromUrl();
	}
	
	public function tjAction(){
		$id = intval($this->getInput('id'));
		$type = $this->getInput('type');
		$url = html_entity_decode(urldecode($this->getInput('_url')));
		if (!id || !$type) return false;
		switch ($type)
		{
			case AD:
				Gou_Service_Ad::updateAdTJ($id);
				break;
			case SUBJECT:
				Gou_Service_Subject::updateSubjectTJ($id);
				break;
			case GUIDE:
				Gou_Service_Guide::updateGuideTJ($id);
				break;
			case CHANNEL:
				Gou_Service_Channel::updateChannelTJ($id);
				break;
			case MALLAD:
				Mall_Service_Ad::updateMalladTJ($id);
					break;
			case NOTICE:
				Gou_Service_Notice::updateMalladTJ($id);
				break;
			case SHOP:
				Client_Service_Shops::updateShopTJ($id);
				break;
			case STORE:
				$ret = Store_Service_Info::getOne($id);
				switch ($ret['info_type'])
				{
					case 1;
					$type_id = 23;
					break;
					case 2;
					$type_id = 24;
					break;
					case 3;
					$type_id = 25;
					break;
				}
				Store_Service_Info::updateTJ($id, $type_id);
				break;
			default:
		}
		
		if(strpos($url, '.gionee.com') !== false) {
			$purl = parse_url($url);
			$lpurl = parse_url(Common::getWebRoot());
			$purl['host'] = $lpurl['host'];
			$url = $this->unparse_url($purl);
			
			if(strpos($url, 't_bi') === false) {
				if (strpos($url, '?') === false) {
					$url = $url.'?t_bi='.$this->getSource();
				} else {
					$url = $url.'&t_bi='.$this->getSource();
				}
			}
		}
		
		$this->redirect($url);
	}
	
	/**
	 * 
	 * @param unknown_type $parsed_url
	 * @return string
	 */
	function unparse_url($parsed_url) {
		$scheme = isset ( $parsed_url ['scheme'] ) ? $parsed_url ['scheme'] . '://' : '';
		$host = isset ( $parsed_url ['host'] ) ? $parsed_url ['host'] : '';
		$port = isset ( $parsed_url ['port'] ) ? ':' . $parsed_url ['port'] : '';
		$user = isset ( $parsed_url ['user'] ) ? $parsed_url ['user'] : '';
		$pass = isset ( $parsed_url ['pass'] ) ? ':' . $parsed_url ['pass'] : '';
		$pass = ($user || $pass) ? "$pass@" : '';
		$path = isset ( $parsed_url ['path'] ) ? $parsed_url ['path'] : '';
		$query = isset ( $parsed_url ['query'] ) ? '?' . $parsed_url ['query'] : '';
		$fragment = isset ( $parsed_url ['fragment'] ) ? '#' . $parsed_url ['fragment'] : '';
		return sprintf ( "%s%s%s%s%s%s%s%s", $scheme, $user, $pass, $host, $port, $path, $query, $fragment );
	}
	
	/**
	 * 
	 */
	public function dtjAction() {
		$url = html_entity_decode(urldecode($this->getInput('_url')));
		$this->redirect($url);
	}
	
	public function clientTJAction(){
		$this->output(0, 'success');
	}
	
	/**
	 * redirect
	 */
	public function redirectAction() {
		$url_id = intval($this->getInput('url_id'));
		$ret = Gou_Service_Url::getUrl($url_id);
		if($ret) {
			//Gou_Service_Url::updateTJ($url_id);
			//$this->redirect(html_entity_decode($ret['url']));
			//hash
			$stat_url = Gou_Service_Url::getShortUrl(Stat_Service_Log::V_MARKET, $ret);
				
			$this->redirect($stat_url);
			exit;
		}
		$webroot = Common::getWebRoot();
		$this->redirect($webroot);
	}
	
	/**
	 * search action
	 */
	public function searchAction() {
		$keyword = trim($this->getInput('keyword'));
		if($keyword) {
			$info = array(
					'keyword'=>$keyword,
					'keyword_md5'=>md5($keyword),
					'create_time'=>Common::getTime(),
					'dateline'=>date('Y-m-d', Common::getTime())
			);
			Client_Service_KeywordsLog::addKeywordsLog($info);
		}
		$url = sprintf('%s%s',Common::getConfig ('apiConfig', 'taobao_search_pid'), urlencode($keyword));
		$this->redirect($url);	
	}
	
	private static function getModel(){
		$ua = Util_Http::getServer('HTTP_USER_AGENT');
		preg_match('/GiONEE-(.*)\//iU', $ua, $matches);
		return $matches[1];
	}
	
	/**
	 * 支付宝接口回调地址
	 */
	public function alipayNotifyAction() {
		$this->output(0, 'success');
	}
	
}
