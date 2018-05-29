<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 用户反馈
 */
class FeedbackController extends Front_BaseController {

    public $actions = array(
        'indexUrl' => '/feedback/index',
        'postUrl'  => '/feedback/post',
    );

    public $perpage = 20;


    public function indexAction() {
        $ch = $this->getInput('ch');
        if ($ch) {
            $this->assign('ch', $ch);
        }
        $this->assign('contact', $this->userInfo ? $this->userInfo['username'] : '');
        $this->assign('refurl', Common::getHttpReferer());
    }

    public function postAction() {
        $info = $this->getPost(array('contact', 'react', 'ch', 'refurl'));
        if (!$info['react']) $this->output(-1, '反馈内容不能为空.');
        if (Util_String::strlen($info['react']) > 500) $this->output(-1, '反馈内容不能大于500字.');
        if ($info['refurl']) {
            $url = $info['refurl'];
        } else {
            $webroot = Common::getCurHost();
            if ($info['ch']) {        //渠道号
                $url = $webroot . '/nav/?ch=' . $info['ch'];
            } else {
                $url = $webroot . '/nav';
            }
        }
        $result = Gionee_Service_React::addReact($info);
        if (!$result) $this->output(-1, '操作失败');

        $this->output(0, '您的宝贵意见反馈成功', array('type' => 'redirect', 'url' => $url));
    }

    /**
     * 新版用户意见反馈
     */
    public function newAction() {
        $order = array('sort' => 'DESC', 'id' => 'ASC');

        $rcKey = 'FEEDBACK_NEW';
        $ret   = Common::getCache()->get($rcKey);
        if ($ret === false) {
            $types = Gionee_Service_ReactType::getsBy(array('parent_id' => 0, 'status' => 1), $order);
            $data  = array();
            if ($types) {
                foreach ($types as $k => $v) {
                    $where          = array('parent_id' => $v['id'], 'status' => 1);
                    $data[$v['id']] = Gionee_Service_ReactType::getsBy($where, array('sort' => "DESC", 'id' => 'ASC'));
                }
            }
            $ret = array($types, $data);
            Common::getCache()->set($rcKey, $ret, 600);
        }

        list($types, $data) = $ret;

        $this->assign('types', $types);
        $this->assign('data', $data);
        $this->assign('refurl', Common::getHttpReferer());
    }

    /**
     * 提交反馈信息
     */
    public function ajaxPostAction() {
        $params = $this->getInput(array('menu_id', 'checked_list', 'react', 'contact', 'ch', 'refurl'));
        if (empty($params['checked_list']) && empty($params['react'])) $this->output('-1', '请填写您要反馈的内容！');
        if (Util_String::strlen($params['react']) > 500) $this->output('-1', '反馈内容不能大于500字');
        $params['content'] = preg_replace("/<([a-za-z]+)[^>]*>/", "<\1>", $params['content']);
        if ($params['refurl']) {
            $url = $params['refurl'];
        } else {
            $webroot = Common::getCurHost();
            if ($params['ch']) {        //渠道号
                $url = $webroot . '/nav/?ch=' . $params['ch'];
            } else {
                $url = $webroot . '/nav';
            }
        }
        $ret = Gionee_Service_React::addReact($params);
        if (!$ret) {
            $this->output('-1', '信息提交失败！');
        }
        $this->output(0, '您的宝贵意见反馈成功', array('type' => 'redirect', 'url' => $webroot . '/activity/tips'));
    }

    public function msgAction() {
        $type = intval($this->getInput('type'));
        if (empty($type)) {
            exit;
        }
        //$uName = Gionee_Service_Feedbackuser::getName();
        //$info  = Gionee_Service_Feedbackuser::getBy(array('name' => $uName));

        $login    = Common_Service_User::checkLogin('', true);
        $userInfo = $login['keyMain'];
        $tel      = !empty($userInfo['username']) ? $userInfo['username'] : 0;
        $uid      = !empty($userInfo['id']) ? $userInfo['id'] : 0;

        $new = true;
        if (!empty($uid)) {//当前3g用户登录了
            $info = Gionee_Service_Feedbackuser::getBy(array('uid' => $uid));
            if (!empty($info)) {//并且存在反馈用户记录中
                Gionee_Service_Feedbackuser::setName($info['name']);
                $new = false;
            }
        }

        if ($new) {//需要添加反馈用户记录
            $uName = Gionee_Service_Feedbackuser::getName();
            $uaArr      = Util_Http::ua();
            $info       = array(
                'model'      => $uaArr['model'],
                'sys_ver'    => $uaArr['sys_ver'],
                'app_ver'    => $uaArr['app_ver'],
                'uuid'       => $uaArr['uuid'],
                'ip'         => $uaArr['ip'],
                'uid'        => $uid,
                'name'       => $uName,
                'created_at' => Common::getTime(),
            );
            $info['id'] = Gionee_Service_Feedbackuser::add($info);
        }


        if (!empty($info['id']) && !empty($tel) && empty($info['tel'])) {
            Gionee_Service_Feedbackuser::set(array('tel' => $tel), $info['id']);
        }

        if (!empty($info['id']) && empty($info['uuid'])) {
            $uaArr = Util_Http::ua();
            Gionee_Service_Feedbackuser::set(array('uuid' => $uaArr['uuid']), $info['id']);
        }

        if (!empty($info['id']) && empty($info['uid']) && !empty($uid)) {
            Gionee_Service_Feedbackuser::set(array('uid' => $uid), $info['id']);
        }

        $this->assign('type', $type);
        $this->assign('title', Gionee_Service_Feedbackmsg::$types[$type] . '_意见反馈');
        $this->assign('prev_url', $this->_prevUrl());
    }

    public function msglistAction() {
        $limit = 10;
        $page  = $this->getInput('page');
        $type  = intval($this->getInput('type'));
        if (empty($type)) {
            exit;
        }

        $userInfo = Gionee_Service_User::getCurUserInfo();
        $info = Gionee_Service_Feedbackuser::getBy(array('uid' => $userInfo['id']));
        //$uName = Gionee_Service_Feedbackuser::getName();
        //$info  = Gionee_Service_Feedbackuser::getBy(array('name' => $uName));


        if (empty($info['id'])) {
            $this->output(-1, '非法进入');
        }
        $where = array('uid' => $info['id'], 'type' => $type);

        $order   = array('created_at' => 'desc');
        $total   = Gionee_Service_Feedbackmsg::getTotal($where);
        $maxPage = max(ceil($total / $limit), 1);
        $page    = min(max(intval($page), 1), $maxPage);

        $tmp    = Gionee_Service_Feedbackmsg::getList($page, $limit, $where, $order);
        $list   = array();
        $nowDay = date('Y-m-d');
        foreach ($tmp as $val) {
            $nowD   = date('Y-m-d', $val['created_at']);
            $list[] = array(
                'time'    => $nowDay == $nowD ? date('H:i', $val['created_at']) : date('m-d H:i', $val['created_at']),
                'content' => $val['flag'] == 2 ? html_entity_decode($val['content']) : $val['content'],
                'flag'    => intval($val['flag']),
                'type'    => $type,
            );
        }

        if ($page == 1) {
            krsort($list);
            $list = array_values($list);
        }

        if ($page == $maxPage) {
            $tmp    = Gionee_Service_Config::getValue('feedback_tip');
            $tmpTip = json_decode($tmp, true);
            if (!empty($tmpTip[$type])) {
                $tipMsg = array(
                    'time'    => '',
                    'content' => $tmpTip[$type],
                    'flag'    => 2,
                    'type'    => $type,
                );
                if ($page == 1) {
                    array_unshift($list, $tipMsg);
                } else {
                    array_push($list, $tipMsg);
                }
            }
        }

        Gionee_Service_Feedbackuser::delNewTip($info['name'], $type);

        $ret = array('list' => $list, 'max_page' => $maxPage, 'cur_page' => $page);
        $this->output(0, '成功', $ret);
    }


    public function msgsendAction() {
        $outData = array();
        $msg     = $this->getInput('msg');
        $type    = intval($this->getInput('type'));
        if (empty($type)) {
            exit;
        }

        $userInfo = Gionee_Service_User::getCurUserInfo();
        $info = Gionee_Service_Feedbackuser::getBy(array('uid' => $userInfo['id']));

        //$uName = Gionee_Service_Feedbackuser::getName();
        //$info  = Gionee_Service_Feedbackuser::getBy(array('name' => $uName));
        if (empty($info['id'])) {
            $this->output(-1, '非法进入');
        }

        $msg = trim($msg);

        if (mb_strlen($msg, 'utf-8') < 1) {
            $this->output(-1, '亲，要输入您的问题哦');
        }

        if (mb_strlen($msg, 'utf-8') > 250) {
            $this->output(-1, '太多啦，先把这些发给我吧');
        }


        $userInfo = Gionee_Service_User::getCurUserInfo();
        $uaArr    = Util_Http::ua();
        $data     = array(
            'uid'        => $info['id'],
            'flag'       => 1,//用户消息
            'content'    => $msg,
            'created_at' => Common::getTime(),
            'type'       => $type,
            'adm_type'   => $type,
            'ip'         => $uaArr['ip'],
            '3g_user_id' => !empty($userInfo['id']) ? $userInfo['id'] : 0
        );

       if(!(Gionee_Service_Feedback::isValidData($msg))){
         $data['adm_type']=0;
	   }

		$replayContent = Gionee_Service_Feedback::filter($msg,$type);
		if (!empty($replayContent)) {
            $data['auto_status']=1;
			$data['f_status']=2;
		}
        $ret = Gionee_Service_Feedbackmsg::add($data);
        if ($ret) {
            if (!empty($replayContent)) {
                $data = array(
                    'uid'        => $data['uid'],
                    'flag'       => 2,//自动消息
                    'content'    => $replayContent,
                    'created_at' => Common::getTime() + 1,
                    'type'       => $data['type'],
				   'auto_status' => 1
                );

                $replayId = Gionee_Service_Feedbackmsg::add($data);
                $outData  = array('replayId' => $replayId, 'replayMsg' => html_entity_decode($replayContent));
            }

            if (!empty($userInfo['id'])) {
                $start_time  = array('>=', mktime('0', '0', '0'));
                $end_time    = array('<=', mktime('23', '59', '59'));
                $isGetPoints = User_Service_ExperienceLog::isGetExperiencePoints($userInfo['id'], $start_time, $end_time, 2);
                if (empty($isGetPoints)) {
                    Common_Service_User::increExperiencePoints($userInfo['id'], $userInfo['experience_level'], 2, 1, 8);
                }
            }

            $this->output(0, '成功', $outData);
        }
        $this->output(-1, '失败');

    }

    public function faqtypeAction() {
        $type = intval($this->getInput('type'));
        if (empty($type)) {
            exit;
        }

        $rcKey = 'FEEDBACK_faqtype:' . $type;
        $list  = Common::getCache()->get($rcKey);
        if ($list === false) {
            $list = Gionee_Service_Feedbackfaq::getsBy(array('type' => $type), array('sort' => 'desc'));
            Common::getCache()->set($rcKey, $list, 600);
        }

        $this->assign('type', $type);
        $this->assign('list', $list);
        $this->assign('title', Gionee_Service_Feedbackmsg::$types[$type] . '_意见反馈');
        $this->assign('prev_url', $this->_prevUrl());
    }

    public function faqinfoAction() {
        $type = intval($this->getInput('type'));
        if (empty($type)) {
            exit;
        }
        $id   = intval($this->getInput('id'));
        $info = Gionee_Service_Feedbackfaq::get($id);
        $this->assign('info', $info);
        $this->assign('type', $type);
    }

    public function contactAction() {
        $type = $this->getInput('type');
        if (empty($type)) {
            exit;
        }
        //$userInfo = Gionee_Service_User::getCurUserInfo();
       // $info = Gionee_Service_Feedbackuser::getBy(array('uid' => $userInfo['id']));

        $uName = Gionee_Service_Feedbackuser::getName();
        $info  = Gionee_Service_Feedbackuser::getBy(array('name' => $uName));


        $pData = $this->getPost(array('qq', 'email', 'mobile'));


        if (!empty($pData['mobile']) || !empty($pData['qq']) || !empty($pData['email'])) {
            if (empty($info['id'])) {
                $this->output(-1, '非法进入');
            }

            if (!empty($pData['qq']) && (strlen($pData['qq']) > 12 || strlen($pData['qq']) < 6)) {
                $this->output(-1, '非法QQ号');
            }

            if (!empty($pData['email']) && !filter_var($pData['email'], FILTER_VALIDATE_EMAIL)) {
                $this->output(-1, '非法邮箱');
            }

            if (!empty($pData['mobile']) && !Common::checkIllPhone($pData['mobile'])) {
                $this->output(-1, '非法手机号码');
            }

            $data = array(
                'qq'    => $pData['qq'],
                'email' => $pData['email'],
                'tel'   => $pData['mobile'],
            );

            Gionee_Service_Feedbackuser::set($data, $info['id']);
            $this->output(0, '提交成功');
        }

        $this->assign('title', '联系方式_意见反馈');
        $this->assign('prev_url', $this->_prevUrl());
        $info['tel'] = !empty($info['tel']) ? $info['tel'] : '';
        $this->assign('info', $info);
        $this->assign('type', $type);
    }

    private function _prevUrl() {

        $str = 'javascript:history.go(-1);';
        if (isset($_SERVER['HTTP_REFERER']) && filter_var($_SERVER['HTTP_REFERER'], FILTER_VALIDATE_URL)) {
            $str = $_SERVER['HTTP_REFERER'];
        }
        return 'javascript:history.go(-1);';
    }
}