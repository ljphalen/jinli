<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghau
 *
 */
class Client_PushController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Client_Push/index',
	);
	
	/**
	 *
	 * Enter description here ...
	 */
	public function indexAction() {
	}
	
	/**
	 * add form page show
	 */
	public function get_nameAction() {
		$temp = array();
		$webroot = Common::getIniConfig("webroot");
		$game_time = Game_Service_Config::getValue('game_time');
		$s = $this->getInput(array('ptype', 'id','link','title','content'));
		$resolution = Resource_Service_Attribute::getsBy(array('at_type'=>4,'status'=>1));
		$resolution = Common::resetKey($resolution, 'id');
		$sys_version = Resource_Service_Attribute::getsBy(array('at_type'=>5,'status'=>1));
		$sys_version = Common::resetKey($sys_version, 'id');
		if($s['ptype'] == '1'){ //外链
			$redirect = html_entity_decode($s['link']);
			if (strpos(html_entity_decode($redirect), '?') !== false) {
				$redirect.=sprintf('&intersrc=%s', 'PUSH');
			} else {
				$redirect.=sprintf('?intersrc=%s', 'PUSH');
			}
			$tmp = array(
					"viewType"=>'Link',
					"source"=>'link',
					"param"=>array(
							'hashTime'=>$game_time,
							'url'=>$redirect)
			);
			$temp['gs']  = "browser|".$s['title']."|".$s['content']."|".$redirect."|".json_encode($tmp);
		} else if($s['ptype'] == '2'){   //分类
			$info = Resource_Service_Attribute::getBy(array('id'=>$s['id']));
			$tmp = array(
					"title"=>$info['title'],
					"url"=>$webroot."/client/category/detail/?id=".$s['id']."&intersrc=PUSH_CATEGORY".$s['id']
			);
			$tmp1 = array(
					"viewType"=>'CategoryDetailView',
					"source"=>'category'.$s['id'],
					"param"=>array(
							'hashTime'=>$game_time,
							'contentId'=>$s['id'])
			);
			$temp['gs']  = "game|".$s['title']."|".$s['content']."|gamelist|".$info['title']."|".$webroot."/client/category/detail/?id=".$s['id']."&intersrc=PUSH_CATEGORY".$s['id']."|".json_encode($tmp)."|".json_encode($tmp1);
		} else if($s['ptype'] == '3'){   //专题
			$info = Client_Service_Subject::getSubject($s['id']);
			$tmp = array(
					"title"=>$info['title'],
					"url"=>$webroot."/client/subject/detail/?id=".$s['id']."&intersrc=PUSH_SUBJECT".$s['id']
			);
			$tmp1 = array(
					"viewType"=>'TopicDetailView',
					"source"=>'subject'.$s['id'],
					"param"=>array(
							'hashTime'=>$game_time,
							'contentId'=>$s['id'])
			);
			$params = Game_Api_Util_SubjectUtil::getClientApiSubjectParams($info);
			if($params['subViewType']) {
			    $tmp1["param"]['subViewType'] = $params['subViewType'];
			    $tmp1["param"]['url'] = $params['url'];
                $tmp1["param"]['source'] = $params['source'];
			}
			$temp['gs']  = "game|".$s['title']."|".$s['content']."|gamelist|".$info['title']."|".$webroot."/client/subject/detail/?id=".$s['id']."&intersrc=PUSH_SUBJECT".$s['id']."|".json_encode($tmp)."|".json_encode($tmp1);
		} else if($s['ptype'] == '4'){   //游戏详情
			$info = Resource_Service_Games::getGameAllInfo(array('id'=>$s['id']));
			$tmp = array(
					"title"=>'游戏详情',
					"gameId"=>$info['id'],
					"url"=>sprintf("%s/client/index/detail/?id=%d&intersrc=%s&pc=%s", $webroot, $s['id'],  "PUSH_GAME".$s['id'], 1)
			);
			$tmp1 = array(
					    "viewType"=>'GameDetailView',
					    "source"=>'gamedetail'.$s['id'],
					    "param"=>array(
					    		    'hashTime'=>$game_time,
									'gameId'=>$s['id'],
									'package' => $info['package']
					    		)
				);
			//新版本push
			$pcjson = "|".json_encode($tmp)."|".json_encode($tmp1);
			
			$temp['gs']  = "game|".$s['title']."|".$s['content']."|details|".$info['name']."|".$webroot."/client/index/detail/?id=".$s['id']."&intersrc=PUSH_GAME".$s['id']."&pc=1|".$info['id']."|".$info['link']."|".$info['package']."|".$info['size']."|".'Android'.$info['min_sys_version_title']."|".$info['min_resolution_title']."-".$info['max_resolution_title'].$pcjson;
		} else if($s['ptype'] == '5'){ //礼包
			$params = array('status' => 1, 'game_status'=>1);
			$params['effect_start_time'] = array('<=', Common::getTime());
			$params['effect_end_time'] = array('>=', Common::getTime());
			$params['id'] = $s['id'];
			$info = Client_Service_Gift::getBy($params);
			$tmp = array(
						'viewType'=>'GiftDetailView',
					    'source'=>'giftdetail'.$s['id'],
	    				'param'=> array(
									'contentId'=>$info['id'],
	    						    'hashTime'=>$game_time,
									'gameId'=>$info['game_id']),
					);
			$temp['gs']  = "game|".$s['title']."|".$s['content']."|gamelist|".$info['name']."|".$webroot."/client/gift/detail/?id=".$s['id']."&intersrc=PUSH_GIFT".$s['id']."|".json_encode($tmp);
		} else if($s['ptype'] == '6'){  //活动
			$params['start_time'] = array('<=',Common::getTime());
    	    $params['end_time'] = array('>=',Common::getTime());
    	    $params['status'] = 1;
			$params['id'] = $s['id'];
			list(, $info)  = Client_Service_Hd::getList(1, 1, $params); 
			$info = $info[0];
			$tmp = array(
					 	'viewType'=>'ActivityDetailView',
					    'source'=>'eventdetail'.$s['id'],
	    				'param'=> array(
									'contentId'=>$info['id'],
	    						    'hashTime'=>$game_time,
									'gameId'=>$info['game_id']),
					);
			$temp['gs']  = "game|".$s['title']."|".$s['content']."|gamelist|".$info['title']."|".$webroot."/client/activity/hdetail/?id=".$s['id']."&intersrc=PUSH_ACTIVITY".$s['id']."|".json_encode($tmp);
		}
		$this->output(0, '', array('list'=>$temp));
	}
	
}
