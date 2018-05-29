<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class TopicController extends Front_BaseController {
	
    /**
     * topic
     */
    public function indexAction() {
    	$id = intval($this->getInput('id'));
    	$topic = Gou_Service_Topic::getTopic($id);
        $str=htmlspecialchars_decode($topic['content']);
        $pattern = '/<img\s.*src=[\"\'](.*)[\"\']/isU';
        preg_match($pattern,$str,$match);
        $shareurl = sprintf('%s/topic/share',Common::getWebRoot());
        if(!empty($match[1])) $topic['share_pic'] =$match[1];
    	Gou_Service_Topic::updateTopicTJ($topic['id']);
    	$this->assign('shareurl', $shareurl);
    	$this->assign('topic', $topic);
    	$this->assign('title', $topic['title']);
    }

    /**
     * share
     */
    public function shareAction() {
        $id = intval($this->getInput('id'));
        $type = $this->getInput('type');
        $topic = Gou_Service_Topic::getTopic($id);

        $webroot = Common::getWebRoot();
        if(!$topic) $this->redirect($webroot);
        if(!in_array($type, array('qq', 'weibo'))) $this->redirect($webroot);

        $qq_share_url = Common::getConfig('apiConfig','share_qq_url');
        $weibo_share_url = Common::getConfig('apiConfig','share_weibo_url');

        $pattern = '/<img\s.*src=[\"\'](.*)[\"\']/isU';
        preg_match($pattern,$topic['content'],$match);
        if(!empty($match[1])) $topic['share_pic'] =$match[1];
        $itemurl = $webroot.'/topic?id='.$topic['id'];
        $pic = '';
        if(!empty($topic['share_pic'])) $pic = '&pic='.$topic['share_pic'];
        if($type == 'qq') {
            $url = $qq_share_url.'?url='.urlencode($itemurl).'
            &title='.$topic['title'].'&summary='.$topic['share_content'];
        }
        if($type == 'weibo') {
            $url = $weibo_share_url.'?url='.urlencode($itemurl).'
            &title='.$topic['share_content'];
        }
        $url = $url.$pic;
        Gou_Service_Topic::updateShare($id);
        $this->redirect($url);
    }
}