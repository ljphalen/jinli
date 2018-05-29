<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * @author ryan
 *
 */
class Apk_FeedbackController extends Api_BaseController
{


    public $versionName = 'Feedback_Version';
    //type 0 ask 1 answer
    //status 0 未处理，1 已处理
    public function recordAction()
    {

        $version = intval($this->getInput('version'));
        $server_version = Gou_Service_Config::getValue($this->versionName);

        if ($version >= $server_version) {
            $this->clientOutput(0, '', new stdClass());
        }


        $uid = Common::getAndroidtUid();
        if (!$uid) $this->output(-1, '非法请求.');

        $condition = array('uid' => $uid);
        //获取最新一百条
        list($total, $questions) = Cs_Service_Feedback::getList(1, 100, $condition, array('id' => 'desc'));
        $questions = array_reverse($questions);

        //获取链接列表
        $link_ids = array_keys(Common::resetKey($questions, 'link_id'));
        list(, $links) = Cs_Service_FeedbackLink::getsBy(array('id' => array('IN', $link_ids)));
        $links = Common::resetKey($links, 'id');

        //获取客服列表
        $kf_ids = array_keys(Common::resetKey($questions, 'kf_id'));
        list(, $kfs) = Cs_Service_FeedbackKefu::getsBy(array('id' => array('IN', $kf_ids)));
        $kfs = Common::resetKey($kfs, 'id');
        array_walk($kfs, function (&$v) {
            $v['avatar'] = Common::getAttachPath() . $v['avatar'];
        });

        $kf = Cs_Service_FeedbackKefu::getBy(array(), array('sort' => 'desc'));
        $default_kf['id'] = $kf['id'];
        $default_kf['avatar'] = Common::getAttachPath() . $kf['avatar'];
        $default_kf['nickname'] = $kf['nickname'];

        $links = Common::resetKey($links, 'id');
        foreach ($questions as $k => $v) {
            $content = html_entity_decode($v['content']);
            $item = array();
            $item['id'] = $v['id'];
            $item['type'] = $v['type'] > 0 ? 1 : $v['type'];
            $item['status'] = $v['status'];
            $item['answer_id'] = $v['answer_id'];
            $item['content'] = $content;
            if ($item['type'] == 0) {
                $items[] = $item;
                if ($k == 0 && $total < 100) {
                    $kf = Cs_Service_FeedbackKefu::getBy(array(), array('sort' => 'desc'));
                    $autorly['type'] = 1;
                    $autorly['content'] = "Hi，小惠正在马不停蹄地赶来给亲回复的路上，亲先喝杯水，扭扭脖子，伸伸懒腰~";
                    $autorly['kf'] = $default_kf;
                    $items[] = $autorly;
                }
                continue;
            }
            if ($v['link_id']) {
                $item['link']['url'] = html_entity_decode($links[$v['link_id']]['url']);
                $item['link']['name'] = html_entity_decode($links[$v['link_id']]['name']);
            }
            $item['kf'] = empty($kfs[$v['kf_id']]) ? $default_kf : $kfs[$v['kf_id']];
            $items[] = $item;
        }

        $this->output(0, '', array('list' => $items, 'version' => $server_version));
    }

    public function addAction()
    {
        Gou_Service_Config::setValue($this->versionName, Common::getTime());

        $uid  = Common::getAndroidtUid();
        $data = $this->getInput(array('content','sign'));
        $info = $this->_cookData($data);

        //添加记录
        $ret = Cs_Service_Feedback::add($info);
        if(!$ret){
            $this->output(-1, '提交失败.',$info);
        }


        //设置提醒
        $cache = Common::getCache();
        $num = $cache->get('feedback_tip');
        $num += 1;
        $cache->set("feedback_tip", $num, Common::getTime() + 3600 * 24);
        //默认客服
        $kf = Cs_Service_FeedbackKefu::getBy(array(), array('sort' => 'desc'));
        $dkf['avatar']   = Common::getAttachPath().$kf['avatar'];
        $dkf['nickname'] = $kf['nickname'];

        //更新用户状态
        $user = Cs_Service_FeedbackUser::getBy(array('uid'=>$uid));
        $u['uid']       = $uid;
        $u['last_time'] = Common::getTime();
        $u['time']      = $user['time']+1 ;
        $u['has_new']   = 1 ;

        if(empty($user)){
            //第一次 提示
            $kf = Cs_Service_FeedbackKefu::getBy(array(), array('sort' => 'desc'));
            $msg['msg']            = "Hi，小惠正在马不停蹄地赶来给亲回复的路上，亲先喝杯水，扭扭脖子，伸伸懒腰~";
            $msg['kf']             = $dkf;
            $info['tip']           = $msg;
            //更新用户
            Cs_Service_FeedbackUser::add($u);
            $this->output(0, '提交成功.', $info);
        }

        //自动回复
        $is_auto   = Gou_Service_Config::getValue('feedback_auto_reply');
        $auto_text = Gou_Service_Config::getValue('feedback_auto_reply_text');
        $auto = array();
        if($is_auto&&$user['is_auto']==0){
            $auto         = array();
            $ret          = Cs_Service_Feedback::add(array('content' => $auto_text, 'type' => 3, 'uid' => $uid,'status'=>1));
            $auto['msg']  = $auto_text;
            $auto['kf']   = $dkf;
            $info['tip']  = $auto;
            $u['is_auto'] = 1;
        }

        Cs_Service_FeedbackUser::update($u,$user['id']);

        $this->output(0, '提交成功.', $info);

    }


    public function cleanTipAction()
    {
        $uid  = Common::getAndroidtUid();
        $user = Cs_Service_FeedbackUser::getBy(array('uid' => $uid));
        $ret = Cs_Service_FeedbackUser::update(array('has_reply'=>0),$user['id']);
        $this->output(0,'',$ret);
    }



    private function _cookData($info)
    {
        $uid = Common::getAndroidtUid();
        if (!$uid) $this->output(-1, '非法请求.');
        //验证签名
        $site_config = Common::getConfig('siteConfig','secretKey');
        $encrypt_str = $uid . 'NTQzY2JmMzJhYTg2N2RvY3Mva2V5';
        if (md5($encrypt_str) !== $info['sign']) $this->output(-1, '非法请求.');
        unset($site_config);
        if (!$info['content']) $this->output(-1, '内容不能为空.');
        $item['uid']        = $uid;
//        $item['status']     = 0;
        $item['model']      = Common::getApkClientModel();
        $item['version']    = Common::getApkClientVersion();
        $item['content']    = html_entity_decode($info['content']);
        return $item;
    }
}
