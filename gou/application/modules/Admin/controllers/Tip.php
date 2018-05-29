<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class TipController extends Admin_BaseController {

	public function getQaTipAction() {
        $cache = Common::getCache();
        $server_qus_last_time = intval($cache->get("qa_qus_last_time"));
        $client_qus_last_time = intval(Util_Cookie::get('qa_qus_last_time'));
        if(!empty($server_qus_last_time)){
            if(empty($client_qus_last_time) || $server_qus_last_time > $client_qus_last_time){
                Util_Cookie::set('qa_qus_last_time', $server_qus_last_time, false, Common::getTime()+3600*24);
                echo json_encode(array('type'=>'qa','time' => date('Y-m-d H:i:s', $server_qus_last_time)));
                exit;
            }
        }
        exit;
	}
	public function getFeedbackTipAction() {
        $cache = Common::getCache();
        $new_num =$cache->get('feedback_tip');
        $num = Util_Cookie::get('feedback_tip');
        if($new_num>$num){
            Util_Cookie::set('feedback_tip', $new_num, false, Common::getTime()+3600*24);
            $url = "/Admin/Cs_Feedback_Qa/index";
            echo json_encode(array('num'=>$new_num,'url'=>$url));
            exit;
        }
        Util_Cookie::set('feedback_tip', $new_num, false, Common::getTime()+3600*24);
        echo json_encode(array('num'=>0,'url'=>''));
        exit;
	}

}
