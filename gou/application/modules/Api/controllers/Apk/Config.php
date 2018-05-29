<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * @author Milo 
 *
 */
class Apk_ConfigController extends Api_BaseController {

    public $versionName = 'Config_Version';

    public function indexAction(){
        $version = intval($this->getInput('version'));
        $uid     = $this->getInput('uid');
        $server_version = Gou_Service_Config::getValue($this->versionName);
        if ($version >= $server_version) {
            $this->output(1, '', array('version'=>$server_version));
        }
        $search['status'] = 1;
        $detailUrl = '/help/detail';

        //  帮助页面配置
        list(, $help) = Gou_Service_ConfigHelp::getList(1, 100, $search);
        $webroot = Common::getWebRoot();
        foreach ($help as &$v) {
            $v['preg'] = html_entity_decode($v['preg']);
            $v['url'] = sprintf('%s%s?id=%s', $webroot, $detailUrl, $v['help_id']);
        }

        // 浏览记录配置
        list(, $history) = Gou_Service_ConfigHistory::getsBy($search);
        foreach ($history as &$h) {
            $h['preg'] = html_entity_decode($h['preg']);
        }

        // 用户信息
        $user = User_Service_Uid::getBy(array('uid'=>$uid));
        $weibo_notice = Gou_Service_Config::getValue('gou_weibo_notice');
        $gou_plugin = Gou_Service_Config::getValue('gou_plugin');
        $cmp_regex = Gou_Service_Config::getValue('cmp_goods_url_regex');
        $is_out_of_date = 1?true:false;
        
        //砍价配置
        $staticroot = Yaf_Application::app()->getConfig()->staticroot;
        $cut = array(
               'order_list'=>$webroot.'/cutorder/list',
                'cut_rule'=>$webroot.'/cut/rule',
                'img_url'=>$staticroot.'/apps/gou/img',
         );
        
        //积分活动配置
        list($score_onoff, $score_date) = Gou_Service_ScoreLog::scoreAvailable();
        $staticroot = Yaf_Application::app()->getConfig()->staticroot;
        $score = array(
            'is_score' => $score_onoff,
            'is_score_date' => $score_date,
            'score_url' => $score_onoff ? $webroot . '/score/profile' : '',
            'img_url'=>$score_onoff ? $staticroot . '/apps/gou/assets/img/score-bg-android.jpg' : $staticroot . '/apps/gou/assets/img/score-default-android.jpg',
        );

        //问答开关
        $is_qa_available = (bool)Gou_Service_Config::getValue('qa_switch');

        //主导航开关
        $nav_main_version = Gou_Service_Config::getValue('Navigation_Version');
        $nav_main = json_decode(Gou_Service_ConfigTxt::getValue('nav_main_txt'), true);
        $is_nav_main_available = isset($nav_main['nav_main_switch']) ? (bool)$nav_main['nav_main_switch'] : false;
        unset($nav_main);

        $site_config = Common::getConfig('siteConfig');

        $ret = array(
            'help'                  => $help,
            'history'               => $history,
            'cmp_goods_url_regex'   => explode(',', $cmp_regex),
            'user'                  => $user,
            'weibo_notice'          => html_entity_decode($weibo_notice),
            "plugin"                => $gou_plugin,
            'is_out_of_data'        => $is_out_of_date,
            'version'               => $server_version,
            'cut'                   => $cut,
            'score'                 => $score,
            'secret_key'            => $site_config['secretKey'],
            'is_qa_available'       => $is_qa_available,
            'nav_main_version'      => $nav_main_version,
            'is_nav_main_available' => $is_nav_main_available,
        );
//        Common::log($ret, 'config.log');
        unset($site_config);
        $this->output(0, '', $ret);
	}
	
	//user
	public function userAction(){
	    $uid = $this->getInput('uid');
	    if(!$uid) $uid = Common::getAndroidtUid();

        list($nickname, $avatar, , $is_edit) = User_Service_Uid::getUserFmtByUid($uid);
        $user = array(
            'avatar'     => $avatar,
            'nickname'   => $nickname,
            'is_edit'    => $is_edit
        );
        //反馈提醒
        $u = Cs_Service_FeedbackUser::getBy(array('uid' => $uid));
        $feedback_tip = !empty($u['has_reply']) ? true : false;
        $kf = Cs_Service_FeedbackKefu::getBy(array(), array('sort' => 'desc'));
        $feedback_avatar = Common::getAttachPath().$kf['avatar'];


        //判断是否有消息提醒
        $user['feedback_tip']  = $feedback_tip;
        $user['feedback_time'] = 10;
        $user['feedback_avatar'] = $feedback_avatar;
        $user['has_msg'] = User_Service_Msg::hasMsg($uid);
	    $this->output(0, '', $user);
	}
	
	/**
	 * 
	 */
    public function helpAction(){
        $version = intval($this->getInput('version'));
        $server_version = Gou_Service_Config::getValue($this->versionName);
        if ($version >= $server_version) {
            $this->output(1, '', array('version'=>$server_version));
        }
        $search['status']=1;
        $detailUrl='/help/detail';
        list(,$list)=Gou_Service_ConfigHelp::getList(1,100,$search);
        $webroot=Common::getWebRoot();
        foreach ($list as &$v) {
            $v['preg']= html_entity_decode($v['preg']);
            $v['url']=sprintf('%s%s?id=%s',$webroot,$detailUrl,$v['help_id']);
        }
        $this->output(0, '', array('list'=>$list,'version'=>$server_version));
	}


    public function historyAction(){
        $version = intval($this->getInput('version'));
        $server_version = Gou_Service_Config::getValue($this->versionName);
        if ($version >= $server_version) {
            $this->output(1, '', array('version'=>$server_version));
        }
        $search['status']=1;
        list(,$list)=Gou_Service_ConfigHistory::getList(1,100,$search);
        foreach ($list as &$v) {
            $v['preg']= html_entity_decode($v['preg']);
        }
        $this->output(0, '', array('list'=>$list,'version'=>$server_version));
	}
}
