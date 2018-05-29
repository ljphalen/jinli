<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * @author Milo 
 *
 */
class Ios_ConfigController extends Api_BaseController {

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
            'score_url' => $score_onoff ? $webroot. '/score/profile' : '',
            'img_url'=>$score_onoff ? $staticroot. '/apps/gou/assets/img/score-bg-android.jpg' : $staticroot. '/apps/gou/assets/img/score-default-android.jpg',
        );

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
            'secret_key'            => $site_config['secretKey']
        );
        $this->output(0, '', $ret);
    }
    /**
     * 浏览记录
     */
    public function browseAction(){
        $version = $this->getInput('data_version');
        $server_version = Gou_Service_Config::getValue('History_Config_Version');

        if ($version >= $server_version) {
            $this->emptyOutput(0, '');
        }
        $search['status']=1;
        list(,$list)=Gou_Service_ConfigHistory::getList(1,100,$search);
        foreach ($list as &$v) {
            $v['preg']= html_entity_decode($v['preg']);
        }

        //积分活动配置
        $webroot = Common::getWebRoot();
        $staticroot = Yaf_Application::app()->getConfig()->staticroot;
        list($score_onoff, $score_date) = Gou_Service_ScoreLog::scoreAvailable();
        $score = array(
            'is_score' => $score_onoff,
            'is_score_date' => $score_date,
            'score_url' => $score_onoff ? $webroot.'/score/profile' : '',
            'img_url'=>$score_onoff ? $staticroot.'/apps/gou/assets/img/score-bg-ios.jpg' : $staticroot.'/apps/gou/assets/img/score-default-ios.jpg',
        );

        $site_config = Common::getConfig('siteConfig');

        $this->output(0, '', array('list'=>$list, 'score'=>$score, 'version'=>$server_version, 'secret_key'=>$site_config['secretKey']));
	}

    //user
    public function userAction(){
        $uid = $this->getInput('uid');
        if(!$uid) $uid = Common::getIosUid();
        $user = User_Service_Uid::getBy(array('uid'=>$uid));
        $this->output(0, '', $user);
    }

}
