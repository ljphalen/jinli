<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 专题首页
 */
class TopicController extends Front_BaseController {

    public $actions   = array(
        'indexUrl'    => '/Topic/index',
        'listUrl'     => '/Topic/list',
        'feedbackUrl' => '/Topic/feedback',
    );
    public $perpage   = 10;
    public $cookieKey = '_topic_vote_';

    public function indexAction() {
        $this->forward("front", "topic", "info");
        return false;
    }

    public function infoAction() {
        $id = $this->getInput('id');
        if (!intval($id)) {
            header('Location:/front/topic/list');
        }

        //header("Content-type:text/html;charset=utf-8");
        $key = $this->getSujectInfoKey ( $id );
        $cacheData = Cache_APC::get($key);     
        if (empty($cacheData)) {//主要目的 发push的时候  redis承受不住带宽压力  所以添加apc缓存60s
        	$info = Gionee_Service_Topic::getInfo($id);
        	if (empty($info)) {
        		header('Location:/front/topic/list?id=' . $id);
        		exit;
        	}
        	if($info['type']  == 4){
        		$cacheData = $this->buildSubjectCacheData ($id, $info);
        	}else{
        		$cacheData = $this->buildOldSujectCacheData ($id, $info);
        	}
        	Cache_APC::set($key, $cacheData);
        }	
        
   
        
        if($cacheData['info']['type'] == 4){
        	$cacheData['content'] = $this->buildContentData($cacheData);
        }else{
        	$cacheData['content'] = $this->_buildCurUserVoted($cacheData);
        }            
        foreach ($cacheData as $k => $v) {
        	$this->assign($k, $v);
        }
    }
    
    private function buildContentData($cacheData){
   	
    	$content = Gionee_Service_Topic::getTopicView($cacheData['content']);  
    	$tplData = array(
    			'info'         => $cacheData['info'],
    			'option'       => $cacheData['option'],
    			'optionResult' => $cacheData['optionResult'],
    	);
    	$vote_area = $this->getView()->render('topic/_vote_area_1.phtml', $tplData);
    	$content              = str_ireplace('{vote_area}', $vote_area, $cacheData['content']);
    	return $content;    	
    }

	
	private function buildSubjectCacheData($id, $info){
		
		$content = json_decode($info['content'], true);
		$options = $this->getOptions ( $content );
	
		list($voteTotal, $optionsTotal) = Gionee_Service_Feedback::getFeedbackStat($id, array_keys($options));
	
		$optionResult = $this->getOptionResult ( $id, $options, $optionsTotal );
			
		$colors  = $this->getColorList();
		$webroot = Common::getCurHost();
		$listUrl = Common::clickUrl($id, 'TOPIC_LIST', $webroot . '/topic/list');
	
		$topFuncColDisplay = $this->getTopColorDisplayStatus();		
		$cacheData = array(
				'id'                => $id,
				'option'            => $options,
				'info'              => $info,
				'webroot'           => $webroot,
				'listUrl'           => $listUrl,
				'content'           => $content,
				'colors'            => $colors,
				'topFuncColDisplay' => $topFuncColDisplay,
				'optionResult'      => $optionResult,
		);
		return $cacheData;		
	}

	
	
	private function getOptionResult($id, $options, $optionsTotal) {
		$optionResult = array();
		$cookie          = Util_Cookie::get($this->cookieKey, true);
		foreach ($options as $k => $v) {
			$checked = empty($cookie['id']['t_' . $id.'_'.$k]) ? 0 : 1;
			$optionResult[$k] = array(
					'txt'                 => $options[$k]['option'],
					'img'                 => $options[$k]['img'],
					'voteOptionTotal'     => empty($optionsTotal[$k]) ? 0 : $optionsTotal[$k],
					'option_num'          => $checked,
					
			);
		}
		return $optionResult;
	}


	private function getOptions($content) {
		$options = array();
		foreach ($content as $k => $v) {
			if(array_key_exists('uptxtdownimg', $v)){
				$options[$k + 1] = array('option'=>$v['uptxtdownimg'][0],
										 'img'=>$v['uptxtdownimg'][1]
				) ;
			}
		}
		return $options;
	}

	
	private function getColorList(){
		return Gionee_Service_Topic::$colors;
	}	
	
    private function getTopColorDisplayStatus(){
	  	$topFuncColDisplay = Gionee_Service_Config::getValue('topic_top_func_status');
	  	if(empty($topFuncColDisplay)){
	  		$topFuncColDisplay = 0;
	  	} 
	  	return $topFuncColDisplay;
    }
	
	private function buildOldSujectCacheData($id, $info) {		 
            $content = json_decode($info['content']);
            $content = Gionee_Service_Topic::getTopicView($content);       
            //去掉多余的换行
            $options = array();
            foreach ($info['option'] as $k => $v) {
                $options[$k + 1] = trim($v);
            }
                       
            $optionResult = array();
            list($sum, $res) = Gionee_Service_Feedback::getFeedbackStat($id, array_keys($options));
           
            foreach ($options as $k => $v) {
                $optionResult[$k] = array(
                    'txt'     => $options[$k],
                    'val'     => empty($res[$k]) ? 0 : $res[$k],
                    'percent' => empty($res[$k]) ? 0 : (bcdiv($res[$k], $sum, 4) * 100) . '%',
                );
            }
                               
            $colors  = $this->getColorList();
            $webroot = Common::getCurHost();
            $listUrl = Common::clickUrl($id, 'TOPIC_LIST', $webroot . '/topic/list');
            
            $topFuncColDisplay = $this->getTopColorDisplayStatus();

            $voteData               = $this->_getVotedStat($id, $options);
            $info['option_num'] = $voteData['checked'];       

            $cacheData = array(
                'id'                => $id,
                'option'            => $options,
                'info'              => $info,
                'voteData'          => $voteData,
                'webroot'           => $webroot,
                'listUrl'           => $listUrl,
                'content'           => $content,
                'colors'            => $colors,
                'topFuncColDisplay' => $topFuncColDisplay,
                'optionResult'      => $optionResult,
            );            
            return $cacheData;
	}

	private function getSujectInfoKey($id) {
		$key       = "Apc_Tipic_Info:{$id}";
		return $key;
	}

    /**
     * 检测用户是否投票
     * @param $cacheData
     *
     * @return mixed
     */
    private function _buildCurUserVoted($cacheData) {
        $tplData = array(
            'info'         => $cacheData['info'],
            'hasVote'      => $this->_checkVoted($cacheData['id']),
            'option'       => $cacheData['option'],
            'optionResult' => $cacheData['optionResult'],
        );

        $vote_area = '';
        if (count($cacheData['info']['option']) == 2) {
            $vote_area = $this->getView()->render('topic/_vote_area_1.phtml', $tplData);
        } else if (count($cacheData['info']['option']) > 2) {
            $vote_area = $this->getView()->render('topic/_vote_area_2.phtml', $tplData);
        }    
        $content              = str_ireplace('{vote_area}', $vote_area, $cacheData['content']);
        return $content;
    }

    private function _checkVoted($id) {
        $hasVote = false;
        $cookie  = Util_Cookie::get($this->cookieKey, true);
        if (!empty($cookie) && $cookie && is_array($cookie['id'])) {
            if (in_array('t' . $id, array_keys($cookie['id'])) && date('Y-m-d', $cookie['stamp']) == date('Y-m-d')) {
                $hasVote = true;
            }
        }
        return $hasVote;
    }

    //获取投票的统计数据
    private function _getVotedStat($id, $options) {
        $temp = $statData = array();
        list($sum, $res) = Gionee_Service_Feedback::getFeedbackStat($id, array_keys($options));
        foreach ($options as $key => $val) {
            $statData[$key] = empty($res[$key]) ? 0 : (round(($res[$key] / $sum) * 100, 0)) . '%';
        }
        $cookie          = Util_Cookie::get($this->cookieKey, true);      
        $temp['checked'] = empty($cookie['id']['t' . $id]) ? 0 : $cookie['id']['t' . $id];
        $temp['percent'] = $statData;
        return $temp;
    }

    /**
     * 专题列表
     */
    public function listAction() {
        $id        = $this->getInput('id');
        $params    = array('status' => 1);
        $orderBy   = array('id' => 'DESC');
        $rs        = Common::getCache();
        $rkey      = '3G:TOPIC:LIST';
        $topicList = $rs->get($rkey);
        if (empty($topicList)) {
            $topicList = Gionee_Service_Topic::getsBy($params, $orderBy);
            $webroot   = Common::getCurHost();
            foreach ($topicList as $k => $v) {
                $topicList[$k]['url'] = Common::clickUrl($v['id'], 'TOPIC', $webroot . '/topic/info?id=' . $v['id']);
                if (!empty($v['img'])) {
                    $topicList[$k]['img'] = Common::getImgPath() . $v['img'];
                }
            }
            $rs->set($rkey, $topicList, Common::T_TEN_MIN);
        }
        $topFuncColDisplay = Gionee_Service_Config::getValue('topic_top_func_status');
        $this->assign('topFuncColDisplay', $topFuncColDisplay);
        $this->assign('topics', $topicList);
        $this->assign('id', intval($id));
    }

    /**
     * 喜欢加一
     */
    public function likeAction() {
        $id = $this->getInput('id');
        if ($id) {
            $info = Gionee_Service_Topic::getInfo($id);
            if ($info) {
                $info['like_num'] = $info['like_num'] + 1;
                Gionee_Service_Topic::update(array('like_num' => $info['like_num']), $info['id']);
                $info = Gionee_Service_Topic::getInfo($info['id'], true);
                $this->output(0, '操作成功');
            }
        }
        $this->output(-1, '操作失败');
    }

    /**
     * 用户反馈
     */
    public function feedbackAction() {
        $info                = $this->getPost(array('topic_id', 'option_num', 'answer', 'contact'));
        $info['user_flag']   = $this->getInput('t_bi');
        $info['create_time'] = time();
        $info['ip']          = Util_Http::getClientIp();

        $cookie = Util_Cookie::get($this->cookieKey, true);
        if (!empty($cookie) && $cookie && is_array($cookie['id'])) {
            if (in_array('t' . $info['topic_id'], array_keys($cookie['id'])) && date('Y-m-d', $cookie['stamp']) == date('Y-m-d')) {
                $this->output(-1, '你今天已经投过票了');
            }
        }

        if (empty($info['option_num'])) {
            $this->output(-1, '请选择要投票的项');
        }

        $topic = Gionee_Service_Topic::getInfo($info['topic_id']);
        if (empty($topic)) {
            $this->output(-1, '没有这专题');
        }


        $topicOption = $topic['option'];
        if (empty($info['option_num']) && empty($info['answer'])) {
            $this->output(-1, '反馈内容不能为空');
        }

        $where = array(
            'topic_id'   => $info['topic_id'],
            'option_num' => $info['option_num']
        );
        list($total, $detail) = Gionee_Service_Feedback::getsBy($where);
        if (!$detail) {
            $result = Gionee_Service_Feedback::add($info);
        } else {
            if ($info['option_num'] == '100') {//如果是用户填写反馈时，直接添加
                $result = Gionee_Service_Feedback::add($info);
            } else {
                $upData = array(
                    'create_time' => time(),
                    'num'         => $detail[0]['num'] + 1
                );
                $result = Gionee_Service_Feedback::updateApp($upData, $detail[0]['id']);
            }
        }
        if ($result) {
            //记录cookie
            $newCookie                          = !is_array(array_keys($cookie['id'])) ? array() : $cookie['id'];
            $newCookie['t' . $info['topic_id']] = $info['option_num'];
            $cookieData                         = array('id' => $newCookie, 'stamp' => time());
            Util_Cookie::set($this->cookieKey, $cookieData, true, time() + 86400);
            //获取投票的统计数据
            $temp = array();
            foreach ($topicOption as $k => $v) {
                $options[$k + 1] = $v;
            }
            $optionResult = array();
            list($sum, $res) = Gionee_Service_Feedback::getFeedbackStat($info['topic_id'], array_keys($options));
            foreach ($options as $k => $v) {
                $txt            = explode('，', $options[$k]);
                $opTxtt         = $txt[0];
                $optionResult[] = array(
                    'txt'     => $opTxtt,
                    'val'     => empty($res[$k]) ? 0 : $res[$k],
                    'percent' => empty($res[$k]) ? 0 : (bcdiv($res[$k], $sum, 4) * 100) . '%',
                );
            }
            $temp['checked'] = $info['option_num'];
            $temp['list']    = $optionResult;

            $userInfo = Gionee_Service_User::getCurUserInfo();
            if (!empty($userInfo)) {
                $param = array(
                    'uid'  => $userInfo['id'],
                    'gid'  => $info['topic_id'],
                    'type' => 3
                );
                $count = User_Service_ExperienceLog::count($param);
                if (!intval($count)) {
                    Common_Service_User::increExperiencePoints($userInfo['id'], $userInfo['experience_level'], 3, 1, 9, $info['topic_id']);
                }
            }
            $this->output(0, '操作成功', $temp);
        } else {
            $this->output(-1, '操作失败');
        }
    }


    //有信电话
    public function yxAction() {
        $callback = Common::getCurHost();
        $url      = Api_Gionee_Oauth::requestToken($callback . '/nav');
        $this->assign('url', $url);
    }

    //有信专题二
    public function youxinAction() {

    }

}