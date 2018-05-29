<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class QaController extends Apk_BaseController {


    public function detailAction() {
        $id = intval($this->getInput('id'));

        if(!$id) $this->output(-1, '参数有误');

        $qus = Gou_Service_QaQus::get($id);

        $qus_info = $aus_list = array();
        if($qus){
            //问贴信息和作者信息
            list($nickname, $avatar) = User_Service_Uid::getUserFmtByUid($qus['uid']);
            $staticroot = Yaf_Application::app()->getConfig()->staticroot;
            if(empty($avatar)) $avatar = sprintf('%s/apps/gou/%s', $staticroot, 'assets/img/head_defualt.png');
            $images = $qus['images'] ? explode(',', $qus['images']):'';
            $attach = Common::getAttachPath();
            array_walk($images, function(&$v, $k, $attach){ $v = $attach . $v; }, $attach);

            //获取问贴列表的有效回答数
            $qus_aus_counts = Gou_Service_QaAus::getAusCount(array($id));
            $qus_aus_counts = Common::resetKey($qus_aus_counts, 'item_id');
            $aus_total = isset($qus_aus_counts[$id]) ? $qus_aus_counts[$id]['total'] : 0;
            unset($qus_aus_counts);

            $qus_info = array(
                'id' => $qus['id'] . '_qus',
                'author_avatar' => $avatar,
                'author_nickname' => $nickname,
                'ans_total' => intval($aus_total) > 999 ? '999+' : $aus_total,
                'title' => html_entity_decode($qus['title']),
                'content' => html_entity_decode($qus['content']),
                'images' => $images
            );

            $condition = array('item_id'=>$qus['id'], 'status'=>array('IN', array(0, 1, 2)));
            $orderBy = array('praise'=>'DESC', 'create_time'=>'DESC');
            list($total, $aus) = Gou_Service_QaAus::getList(1, 5, $condition, $orderBy);
            if($aus){
                //获取回帖作者的UID
                $uids = array_keys(Common::resetKey($aus, 'uid'));
                $author_list = User_Service_Uid::getUsersFmtByUid($uids);

                //获取回帖的父贴作者的UID
                $parent_ids = array_keys(Common::resetKey($aus, 'parent_id'));
                $parent_ids = array_filter($parent_ids);
                $parent_ids = array_unique($parent_ids);
                $parent_cm_list = $parent_uids = array();
                if($parent_ids){
                    $parent_cm_list = Gou_Service_QaAus::getsBy(array('id'=>array('IN', $parent_ids)));
                    $parent_cm_list = Common::resetKey($parent_cm_list, 'id');
                    $parent_uids = array_keys(Common::resetKey($parent_cm_list, 'uid'));
                }
                $parent_author_list = User_Service_Uid::getUsersFmtByUid($parent_uids);
                foreach($parent_cm_list as $key => $item){
                    if(isset($parent_author_list[$item['uid']]))
                        $parent_cm_list[$key]['nickname'] = $parent_author_list[$item['uid']]['nickname'];
                }
                unset($uids);
                unset($parent_ids);
                unset($parent_uids);
                unset($parent_author_list);


                foreach($aus as $item){
                    //设置回帖信息
                    $aus_item = array(
                        'id' => $item['id'],
                        'ans_content' => html_entity_decode($item['content']),
                        'ans_praise' => intval($item['praise']) > 999 ? '999+' : $item['praise'],
                        'ans_time' => date('Y-m-d', $item['create_time']),
                        'jid' => $item['relate_item_id'],
                        'j_title' => ''
                    );

                    //获取跳转问贴
                    if($item['relate_item_id']){
                        $relate_qus = Gou_Service_QaQus::get($item['relate_item_id']);
                        if($relate_qus) $aus_item['j_title'] = html_entity_decode($relate_qus['title']);
                    }

                    //获取回帖作者信息
                    $uid = $item['uid'];
                    if(isset($author_list[$uid])){
                        $aus_item['a_author_nickname'] = $author_list[$uid]['nickname'];
                        if($author_list[$uid]['avatar']){
                            $aus_item['a_author_avatar'] = $author_list[$uid]['avatar'];
                        }else{
                            $aus_item['a_author_avatar'] = sprintf('%s/apps/gou/%s', $staticroot, 'assets/img/head_defualt.png');
                        }
                    }

                    //获取回帖的回帖作者信息
                    $aus_item['from'] = '';
                    if($item['parent_id']){
                        if(isset($parent_cm_list[$item['parent_id']])){
                            $aus_item['from'] = sprintf('%s%s：', '回复', $parent_cm_list[$item['parent_id']]['nickname']);
                        }else{
                            $aus_item['from'] = sprintf('%s%s：', '回复', '匿名');
                        }
                    }
                    array_push($aus_list, $aus_item);
                }
            }
        }

        // down load url
//        $download_url = 'http://goudl.gionee.com/apps/shoppingmall/GN_Gou-banner.apk';
//        if(strpos(Util_Http::getServer('HTTP_USER_AGENT'), 'MicroMessenger') !== false ) {
            $download_url = 'http://a.app.qq.com/o/simple.jsp?pkgname=com.gionee.client';
//        }

        $this->assign('qus', $qus_info);
        $this->assign('download_url', $download_url);
        $this->assign('aus_list', $aus_list);
        $this->assign('title', '问答详情');
    }



}
