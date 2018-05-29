<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class Local_GameinfoController extends Api_BaseController {
    const GAME_SOURCE_BAIDU = 'baidu';
    public $perpage = 10;
    private $mDetailsData = array();
    private $mAdditionalData = array();

    public function getDetailsAction() {
        list($gameId, $from) = $this->parseGameIdInput();

        $gameInfo = $this->getGameResourceById($gameId, $from);
        $this->fillBasicInfomation($gameInfo);
        $this->fillBannerList($gameInfo);

        if (self::GAME_SOURCE_BAIDU != $from) {
            $this->fillRecommend($gameId);
            $this->fillActivities($gameId);
            $this->fillForumWebSite($gameId);
        }

        $this->localOutput(0, 'game infomation', $this->mDetailsData);
    }

    public function getAdditionalInfoAction() {
        list($gameId, $from) = $this->parseGameIdInput();
        if (self::GAME_SOURCE_BAIDU == $from) {
            $this->localOutput(-1, 'we have no additional infomation of baidu games');
        }
        $this->fillGifts($gameId);
        $this->fillStrategies($gameId);
        $this->fillNews($gameId);
        $this->localOutput(0, 'game additional infomation', $this->mAdditionalData);
    }

    /*Client request this url for latest download url*/
    public function getDownloadInfoAction() {
        list($gameId, $from) = $this->parseGameIdInput();

        if (self::GAME_SOURCE_BAIDU == $from) {
            $this->localOutput(-1, 'baidu game not supported by this api');
        }

        $version = Resource_Service_Games::getGameVersionInfo($gameId);
        if (!$version) {
            $message = 'there is no this game: ' . $gameId;
            $this->localOutput(-1, $message);
        }

        $downloadInfo[Util_JsonKey::GAME_ID] = '' . $gameId;
        $downloadInfo[Util_JsonKey::VERSION_CODE] = $version['version_code'];
        $downloadInfo[Util_JsonKey::VERSION_NAME] = $version['version'];

        if (strnatcmp($this->getInput('type'), 'split') == 0) {
        	$game = $this->getPackageGameInfo($gameId);
        	if ($game) {
	        	$downloadInfo[Util_JsonKey::DOWN_URL] = $this->getDiffPackageUrl($game, $this->getInput('md5'));
        	} else {
        		$downloadInfo[Util_JsonKey::DOWN_URL] = '';
        	}
        } else {
        	$downloadInfo[Util_JsonKey::DOWN_URL] = $version['link'];
        }

        $this->localOutput(0, '', $downloadInfo);
    }
    
    private function getDiffPackageUrl($game, $md5Code) {
    	if (!$md5Code) {
    		return '';
    	}
    	
    	$oldVersionId = $game['versions'][$md5Code];
    	if ($oldVersionId) {
    		$diffPackageInfo = $game['diff'][$oldVersionId];
    		if($diffPackageInfo) {
    			return $diffPackageInfo['link'];
    		}
    	}
    	return '';
    }
    
    private function getPackageGameInfo($gameId) {
    	$cache = Cache_Factory::getCache();
    	$gkey = Util_CacheKey::GAME_PACKAGE_DIFF_INFO . $gameId;
    	$game = $cache->get($gkey);
    	return $game;
    }

    private function fillGifts($gameId) {
        $top3List = array();

        $search = array('status' => 1, 'game_status'=>1);
        $search['effect_start_time'] = array('<=', Common::getTime());
        $search['effect_end_time'] = array('>=', Common::getTime());
        $search['game_id'] = $gameId;

        $page = 1;
        $limit = 3;

        list($total, $gifts) = Client_Service_Gift::getList($page, $limit, $search);

        $item = array();
        foreach($gifts as $key=>$value) {
            $remains = intval(Client_Service_Giftlog::getGiftlogByStatus(0, $value['id']));

            $item['title'] = html_entity_decode($value['name'], ENT_QUOTES);
            $item['id'] = $value['id'];
            $item['remains'] = $remains;
            $item['totalCount'] = Client_Service_Gift::getGiftTotal($value['id']);;
            $top3List[] = $item;
        }

        $this->mAdditionalData['gifts'] = $top3List;
    }

    private function fillStrategies($gameId) {
        $top3List = array();

        $strategies = $this->getArticles($gameId, Client_Service_News::ARTICLE_TYPE_STRATEGY);

        $item = array();
        foreach($strategies as $key=>$value){
            $item['title'] = html_entity_decode($value['title'], ENT_QUOTES);
            $item['id'] = $value['id'];
            $item['summary'] = html_entity_decode($value['resume'], ENT_QUOTES);
            $item['timeStamp'] = $value['create_time'];
            $top3List[] = $item;
        }

        $this->mAdditionalData['stragegies'] = $top3List;
    }

    private function fillNews($gameId) {
        $top3List = array();

        $news = $this->getArticles($gameId, Client_Service_News::ARTICLE_TYPE_NEWS);

        $item = array();
        foreach($news as $key=>$value){
            $item['title'] = html_entity_decode($value['title'], ENT_QUOTES);
            $item['id'] = $value['id'];
            $item['summary'] = html_entity_decode($value['resume'], ENT_QUOTES);
            $item['timeStamp'] = $value['create_time'];
            $top3List[] = $item;
        }

        $this->mAdditionalData['news'] = $top3List;
    }

    private function getArticles($gameId, $articleType) {
        $search = array();
        $search['status'] = 1;
        $search['ntype'] = $articleType;
        $search['game_id'] = $gameId;
        $search['create_time']  = array('<=', Common::getTime());

        $sortBy = array('sort'=>'DESC','create_time'=>'DESC','id' =>'DESC');

        $page = 1;
        $limit = 3;
        list($total, $articles) = Client_Service_News::getList($page, $limit, $search, $sortBy);

        return $articles;
    }

    private function parseGameIdInput() {
        $gameId = intval($this->getInput('gameId'));
        $from = $this->getInput('from');

        if(!$gameId) {
            $this->localOutput(-1, 'not found game id', array());
        }

        return array($gameId, $from);
    }

    private function getGameResourceById($gameId, $from) {
        $gameInfo = array();
        if (self::GAME_SOURCE_BAIDU == $from) {
            $gameInfo = $this->getDetailsFromBaidu($gameId);
        } else {
            $gameInfo = Resource_Service_GameData::getGameAllInfo($gameId);
        }
        if (!$gameInfo) {
            $message = 'there is no this game: ' . $gameId;
            $this->localOutput(-1, $message, array());
        }
        return $gameInfo;
    }

    private function getDetailsFromBaidu($gameId) {
        $baiduApi = new Api_Baidu_Game();
        $gameInfo = $baiduApi->getInfo($gameId, self::GAME_SOURCE_BAIDU);

        if (empty($gameInfo) || !$gameInfo['id']) {
            return null;
        }

        $gameInfo['category_title'] = $gameInfo['category'];
        $gameInfo['client_star'] = 0;
        $gameInfo['update_time'] = strtotime($gameInfo['updatetime']);
        $gameInfo['summary'] = $gameInfo['descrip'];
        $gameInfo['version'] = $gameInfo['apply_version'];

        return $gameInfo;
    }

    private function fillBasicInfomation($gameInfo) {
        $this->mDetailsData['gameId'] = $gameInfo['id'];
        $this->mDetailsData['iconUrl'] = $gameInfo['img'];
        $this->mDetailsData['name'] = html_entity_decode($gameInfo['name'], ENT_QUOTES);
        $this->mDetailsData['category'] = html_entity_decode($gameInfo['category_title'], ENT_QUOTES);
        $this->mDetailsData['fileSize'] = $gameInfo['size'] . 'M';
        $this->mDetailsData['language'] = html_entity_decode($gameInfo['language'], ENT_QUOTES);
        $this->mDetailsData['score'] = $gameInfo['client_star'];
        $this->mDetailsData['versionName'] = $gameInfo['version'];
        $this->mDetailsData['updateTime'] = $gameInfo['update_time'];
        $this->mDetailsData['publisher'] = html_entity_decode($gameInfo['developer'], ENT_QUOTES);
        $this->mDetailsData['editorWords'] = $this->fillStyle($gameInfo['tgcontent']);
        $this->mDetailsData['summary'] = $this->fillStyle($gameInfo['descrip']);
    }

    private function fillBannerList($gameInfo) {
        $thumbnails = $gameInfo['simgs'];
        $fullPicture = $gameInfo['gimgs'];
        $this->mDetailsData['bannerList'] = array(
                                            "thumbnails" => $thumbnails,
                                            "fullPicture" => $fullPicture);
    }

    private function fillStyle($data){
    	if(!$data) return "";
    	$content = html_entity_decode($data, ENT_QUOTES);
    	//去除html空白处理
    	$subject = strip_tags($content, '<img><a>');
    	$pattern = array('/\s/','/&nbsp;/i');//去除空白跟空格
    	$text = preg_replace($pattern, '', $subject);
    	if(empty($text)) return "";
    
    	$html = <<<str
    	<style>
    		html,body,div,span,h1,h2,h3,h4,h5,h6,p,dl,dt,dd,ol,ul,li,a,em,img,small,strike,strong,form,label,canvas,footer,header,nav,output{
    			margin:0; padding:0;
    		}
    		.ui-editor {
  				word-break: break-all;
    			line-height: 1.2rem;
			}
			.ui-editor i, .ui-editor em {
  				font-style: italic !important;
			}
			.ui-editor b {
  				font-weight: bold !important;
			}
			.ui-editor u {
  				text-decoration: underline !important;
			}
			.ui-editor s {
  				text-decoration: line-through !important;
			}
			.ui-editor ul li {
  				list-style: initial;
  				margin-left: 1rem !important;
			}
			.ui-editor ol li {
  				list-style: decimal;
  				margin-left: 1rem !important;
			}
			.ui-editor span, .ui-editor p, .ui-editor h1, .ui-editor h2, .ui-editor h3, .ui-editor h4, .ui-editor h5 {
  				white-space: normal !important;
			}
			.ui-editor span, .ui-editor p {
  				line-height: 1.2rem;
			}
			.ui-editor img {
  				padding-top: 5px;
 				max-width: 100% !important;
  				width: auto;
  				height: auto;
  				display: block;
 				margin: 0 auto;
			}
			.ui-editor table {
  				margin: 4px 0;
  				max-width: 300px !important;
			}
			.ui-editor h1, .ui-editor h2, .ui-editor h3 {
  				font-size: 1.2rem !important;
  				line-height: 1.5rem;
			}
			.ui-editor h4, .ui-editor h5, .ui-editor h6 {
  				font-size: 1.2rem !important;
  				line-height: 1.3rem;
			}
    	</style>
    	<div class="ui-editor" style='font-size:13px; color:#777777;'>
str;
    	$html.= $content.'</div>';
    	return base64_encode($html);
    }
    
    private function fillRecommend($gameId) {
        $games = array();

        $recommendGameIds = Client_Service_Recommend::getRecommendGames(
                array('GAMEC_RESOURCE_ID'=>$gameId));
        if(!$recommendGameIds){
            return;
        }

        foreach($recommendGameIds as $key=>$value){
            $gameInfo = array();
            $gameInfo = Resource_Service_GameData::getGameAllInfo($value['GAMEC_RECOMEND_ID']);
            if(!$gameInfo) {
                continue;
            }
            if($gameInfo['status']) {
                $games[] = array('gameId' => $gameInfo['id'],
                                'iconUrl' => $gameInfo['img'],
                                'name' => $gameInfo['name'],
                                'packageName' => $gameInfo['package']);
            }
        }

        if (count($games) != 0) {
            $this->mDetailsData['recommend'] = $games;
        }
    }

    private function fillActivities($gameId) {

        $query['game_id'] = $gameId;
        $query['start_time'] = array('<=',Common::getTime());
        $query['end_time'] = array('>=',strtotime('-7 day'));
        $query['status'] = 1;
        $orderBy = array('sort'=>'DESC','start_time'=>'DESC','id'=>'DESC');
        $page = 1;
        $limit = 3;
        list($count, $activities) = Client_Service_Hd::getList(
                                        $page, $limit, $query, $orderBy);
        if($count == 0) {
            return;
        }

        $activityArr = array();
        $webroot = Common::getWebRoot ();
        $format = 'Y年n月j';
        foreach($activities as $key => $value) {
            $activityUrl = $webroot
                .'/client/Activity/addetail/?id='
                . $value['id'];
            $activityArr[] = array(
                            'title' => html_entity_decode($value['title'], ENT_QUOTES),
                            'contentId' => $value['id'],
                            Util_JsonKey::TIME => date($format, $value['start_time']).'-'.date($format, $value['end_time']),
                            Util_JsonKey::AWARD_DES => $value['award'] ? html_entity_decode($value['award'], ENT_QUOTES) : ''
            );
        }

        if (count($activityArr) != 0) {
            $this->mDetailsData['activityItems'] = $activityArr;
        }
    }

    private function fillForumWebSite($gameId) {
        $query['game_id'] = $gameId;
        $now = Common::getTime();
        $query['status'] = 1;
        $query['start_time'] = array('<=',$now);
        $query['end_time'] = array('>=',$now);
        $bbsInfo = Bbs_Service_Bbs::getBy($query);
        $url = $bbsInfo['url'];

        if ($url) {
            $webroot = Common::getWebRoot ();
            $formUrl = $webroot
                        .'/client/bbs/block?id='
                        .$gameId
                        .'&fromapp=game';
            $this->mDetailsData['forumUrl'] = $formUrl;
        }
    }
}
