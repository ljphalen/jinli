<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 反馈管理
 */
class FeedbackController extends Admin_BaseController {

    public $actions = array(
        'listUrl'     => '/Admin/Feedback/index',
        'msglistUrl'  => '/Admin/Feedback/msglist',
        'msgeditUrl'  => '/Admin/Feedback/msgedit',
		'msgoptUrl'  => '/Admin/Feedback/msgopt',
        'faqlistUrl'  => '/Admin/Feedback/faqlist',
        'faqeditUrl'  => '/Admin/Feedback/faqedit',
        'faqdelUrl'   => '/Admin/Feedback/faqdel',
        'keylistUrl'  => '/Admin/Feedback/keylist',
        'keyeditUrl'  => '/Admin/Feedback/keyedit',
        'keydelUrl'   => '/Admin/Feedback/keydel',
		'keyimportUrl'=> '/Admin/Feedback/keyimport',
    );

    public $perpage = 20;

    public function indexAction() {
        $page    = intval($this->getInput('page'));
        $perpage = $this->perpage;
        $param   = $this->getInput(array('topic_id'));
        $search  = array();
        if ($param['topic_id']) $search['topic_id'] = $param['topic_id'];

        list($total, $reacts) = Gionee_Service_Feedback::getList($page, $perpage, $search);

        $ids = array(0);
        foreach ($reacts as $k => $v) {
            $ids[] = $v['topic_id'];
        }
        $topicInfo = array();
        $field     = array('id', 'title', 'option');
        $where     = array('id' => array("IN", array_unique($ids)));
        list($sum, $topicList) = Gionee_Service_Topic::getElements($field, $where, array('id' => 'DESC'));
        foreach ($topicList as $val) {
            $toption = explode("\n", $val['option']);
            array_push($toption, '其他');
            $topicInfo[$val['id']] = array('name' => $val['title'], 'option' => $toption);
        }

        $url = $this->actions['listUrl'] . '/?' . http_build_query($search) . '&';
        $this->assign('pager', Common::getPages($total, $page, $perpage, $url));
        $this->assign('reacts', $reacts);
        $this->assign('param', $param);
        $this->assign('topic', $topicInfo);
    }

    public function msglistAction() {
        $get = $this->getInput(array('togrid', 'page', 'rows', 'sort', 'order', 'export'));
		$f_status_list = array(2 => '已完成',1 => '跟进中', 0 => '未回复');
		$content_type_list = array(2 => '建议',1 => '投诉', 0 => '反馈');
		$auto_status_list= array(0 => '人工回复', 1 => '自动回复');
        if (!empty($get['togrid'])) {
            $page           = max(intval($get['page']), 1);
            $offset         = !empty($get['rows']) ? $get['rows'] : $this->pageSize;
            $sort           = !empty($get['sort']) ? $get['sort'] : 'id';
            $order          = !empty($get['order']) ? $get['order'] : 'desc';
            $orderBy[$sort] = $order;

            $where = array('flag' => 1);
            foreach ($_POST['filter'] as $k => $v) {

                if ($k == 'start_time') {
                    $where['created_at'][] = array('>=', strtotime($v . ' 00:00:00'));
                } else if ($k == 'end_time') {
                    $where['created_at'][] = array('<=', strtotime($v . ' 23:59:59'));
                } else if ($k == 'username') {
                    $tmp1 = Gionee_Service_User::getsBy(array('username' => $v));
                    $ids  = array();
                    if (!empty($tmp1)) {
                        foreach ($tmp1 as $vv) {
                            $ids[] = intval($vv['id']);
                        }
                    }

                    if ($ids) {
                        $tmp2 = Gionee_Service_Feedbackuser::getsBy(array('uid' => array('IN', $ids)));

                        if (!empty($tmp2)) {
                            $ids2 = array();
                            foreach ($tmp2 as $vvv) {
                                $ids2[] = intval($vvv['id']);
                            }
                            $where['uid'] = array('IN', $ids2);
                        }
                    }
                } else if ($k == 'mark') {
                    if (strlen($v) > 0) {
                        $where[$k] = ($v == 0) ? '' : array('>', 0);
                    }
                } else if ($k == 'content') {
					if (strlen($v) > 0) {
						$where['content'] = array('LIKE', $v);
				    }
				} else if (strlen($v) > 0) {
                    $where[$k] = $v;
                }
            }

            if ($_POST['export']) {
                $rows = array(
                    array(
                        //'游客名',
                        '游客ID',
                        '用户名',
                        '用户ID',
                        '内容',
                        '类别',
                        '渠道',
                        '电话',
                        '地区',
                        '浏览器信息',
                        '回复',
                        '状态',
                        '时间'
                    )
                );

                $arr = Gionee_Service_Feedbackmsg::getsBy($where, $orderBy);
                foreach ($arr as $v) {
                    $fduser   = Gionee_Service_Feedbackuser::get($v['uid']);
                    $area     = Vendor_IP::find($fduser['ip']);
                    $userInfo = array();
                    if (!empty($fduser['uid'])) {
                        $userInfo = Gionee_Service_User::getUser($fduser['uid']);
                    }

                    $fid        = !empty($fduser['id']) ? $fduser['id'] : '';
                    $name       = $fduser['name'];
                    $username   = !empty($userInfo['username']) ? $userInfo['username'] : '';
                    $uid        = !empty($userInfo['id']) ? $userInfo['id'] : '';
                    $type       = Gionee_Service_Feedbackmsg::$types[$v['type']];
                    $adm_type   = Gionee_Service_Feedbackmsg::$admtypes[$v['adm_type']];
                    $tel        = !empty($userInfo['mobile']) ? $userInfo['mobile'] : $fduser['tel'];
                    $area       = implode(',', array($area[1], $area[2]));
                    $info       = $fduser['model'] . '-' . $fduser['app_ver'] . '-' . $fduser['sys_ver'];
                    //$mark       = !empty($v['mark']) ? '已回复' : '未回复';
                    $f_status   =  $f_status_list[$v['f_status']];
                    $created_at = date('y/m/d H:i', $v['created_at']);

                    $replayArr = Gionee_Service_Feedbackmsg::get($v['mark']);
                    $replay    = $replayArr['content'];
                    $rows[]    = array(
                        //$name,
                        $fid,
                        $username,
                        $uid,
                        $v['content'],
                        $type,
                        $adm_type,
                        $tel,
                        $area,
                        $info,
                        $replay,
                       // $mark,
						$f_status,
                        $created_at
                    );
                }

                Common::export($rows, $_POST['filter']['start_time'], $_POST['filter']['end_time'], '用户反馈');
                exit;
            }

            $total = Gionee_Service_Feedbackmsg::getTotal($where);
            $list  = Gionee_Service_Feedbackmsg::getList($page, $offset, $where, $orderBy);
            foreach ($list as $k => $v) {
                $fduser   = Gionee_Service_Feedbackuser::get($v['uid']);
                $area     = Vendor_IP::find($fduser['ip']);
                $userInfo = array();
                if (!empty($fduser['uid'])) {
                    $userInfo = Gionee_Service_User::getUser($fduser['uid']);
                }

                $fid                    = !empty($fduser['id']) ? $fduser['id'] : '';
                $name                   = $fduser['name'];
                $list[$k]['name']       = $name;
                $list[$k]['fid']        = $fid;
                $list[$k]['username']   = !empty($userInfo['username']) ? $userInfo['username'] : '';
                $list[$k]['uid']        = !empty($userInfo['id']) ? $userInfo['id'] : '';
                $list[$k]['type']       = Gionee_Service_Feedbackmsg::$types[$v['type']];
                $list[$k]['adm_type']   = Gionee_Service_Feedbackmsg::$admtypes[$v['adm_type']];
                $list[$k]['tel']        = !empty($userInfo['mobile']) ? $userInfo['mobile'] : $fduser['tel'];
                $list[$k]['ip']         = $fduser['ip'];
                $list[$k]['area']       = implode(',', array($area[1], $area[2]));
                $list[$k]['info']       = $fduser['model'] . '-' . $fduser['app_ver'] . '-' . $fduser['sys_ver'];
                $list[$k]['mark']       = !empty($v['mark']) ? '已回复' : '未回复';
				$list[$k]['content_type']   = $content_type_list[$v['content_type']];
				$list[$k]['auto_status']   = $auto_status_list[$v['auto_status']];
				$list[$k]['f_status'] =  $f_status_list[$v['f_status']];
                $list[$k]['created_at'] = date('y/m/d H:i', $v['created_at']);
            }

            $ret = array(
                'total' => $total,
                'rows'  => $list,
            );
            echo Common::jsonEncode($ret);
            exit;
        }

        $this->assign('filter', $_POST['filter']);
        $this->assign('admtypes', Gionee_Service_Feedbackmsg::$admtypes);
        $this->assign('types', Gionee_Service_Feedbackmsg::$types);
    }

    public function historylistAction() {
        $get = $this->getInput(array('uid', 'page', 'rows', 'sort', 'order'));
		$content_type_list = array(2 => '建议',1 => '投诉', 0 => '反馈');
        if (!empty($get['uid'])) {
            $page           = max(intval($get['page']), 1);
            $offset         = !empty($get['rows']) ? $get['rows'] : $this->pageSize;
            $sort           = !empty($get['sort']) ? $get['sort'] : 'id';
            $order          = !empty($get['order']) ? $get['order'] : 'desc';
            $orderBy[$sort] = $order;
            $where = array('uid'=>$get['uid']);
            $total = Gionee_Service_Feedbackmsg::getTotal($where);
            $list  = Gionee_Service_Feedbackmsg::getList($page, $offset, $where, $orderBy);
            foreach ($list as $k => $v) {
                $list[$k]['created_at'] = date('y/m/d H:i', $v['created_at']);
                $list[$k]['type']       = Gionee_Service_Feedbackmsg::$types[$v['type']];
                $list[$k]['auto_status_desc'] = $v['auto_status']==1 ? '自动回复' : '人工回复';
                if($list[$k]['flag']==1){
                  $list[$k]['content']='<b>用户:'.$list[$k]['content'].'</b>';
				}else{
                  $list[$k]['content']='回复:'.$list[$k]['content'];
				}
                $list[$k]['content_type_desc']   = $content_type_list[$list[$k]['content_type']];
            }

            $ret = array(
                'total' => $total,
                'rows'  => $list
            );
            echo Common::jsonEncode($ret);
            exit;
        }
    }

    public function msgeditAction() {
        $id = $this->getInput('id');

        $pData  = $this->getPost(array('msg', 'id', 'adm_type','f_status','content_type', 'token'));
        $points = $this->getInput('experience_points');
        if (!empty($pData['id'])) {

            $info = Gionee_Service_Feedbackmsg::get($pData['id']);
			//$f_status=$pData['f_status'];
            $f_status=2;
			$content_type=$pData['content_type'];

            if (empty($info['mark'])) {
                if (!empty($pData['msg'])) {
                    $data = array(
                        'uid'        => $info['uid'],
                        'flag'       => 2,
                        'content'    => $pData['msg'],
                        'created_at' => Common::getTime(),
                        'type'       => $info['type'],
                    );

                    $newId = Gionee_Service_Feedbackmsg::add($data);
                    if ($newId) {
                        $fduser = Gionee_Service_Feedbackuser::get($info['uid']);
                        Gionee_Service_Feedbackuser::setNewTip($fduser['name'], $info['type']);
                        Gionee_Service_Feedbackmsg::set(array('mark' => $newId,'f_status' => $f_status,'content_type'=>$content_type), $pData['id']);
                    }
                }else{
                  Gionee_Service_Feedbackmsg::set(array('f_status' => $f_status,'content_type'=>$content_type), $pData['id']);
			    }
            } else {
                Gionee_Service_Feedbackmsg::set(array('content' => $pData['msg'],'f_status' => $f_status,'content_type'=>$content_type), $info['mark']);
            }
            //赚送经验值
            if (intval($points) && $info['3g_user_id']) {
                $userInfo = Gionee_Service_User::getUser($info['3g_user_id']);
                Common_Service_User::increExperiencePoints($userInfo['id'], $userInfo['experience_level'], 4, $points, 10);
            }
            Gionee_Service_Feedbackmsg::set(array('f_status' => $f_status,'content_type'=>$content_type,'adm_type' => $pData['adm_type']), $pData['id']);
            $this->output(0, '提交成功');
        }

        $info     = Gionee_Service_Feedbackmsg::get($id);
        $fduser   = Gionee_Service_Feedbackuser::get($info['uid']);
        $area     = Vendor_IP::find($fduser['ip']);
        $userInfo = array();
        if (!empty($fduser['uid'])) {
            $userInfo = Gionee_Service_User::getUser($fduser['uid']);
        }

        $replay = '';
        if (!empty($info['mark'])) {
            $replayArr = Gionee_Service_Feedbackmsg::get($info['mark']);
            $replay    = $replayArr['content'];
        }

        $uid                = !empty($fduser['uid']) ? " ({$fduser['uid']})" : '';
        $info['name']       = "{$fduser['name']}{$uid}";
        $info['tel']        = !empty($userInfo['mobile']) ? $userInfo['mobile'] : $fduser['tel'];
        $info['ip']         = $fduser['ip'];
        $info['area']       = implode(',', array($area[1], $area[2]));
        $info['info']       = "机型:{$fduser['model']}版本:{$fduser['app_ver']}系统:{$fduser['sys_ver']}";
        $info['mark']       = !empty($info['mark']) ? '已回复' : '未回复';
        $info['created_at'] = date('y/m/d H:i', $info['created_at']);


        $info['replay'] = $replay;
        $f_status_list = array(2 => '已完成',1 => '跟进中', 0 => '未回复');
        $this->assign('f_status_list', $f_status_list);
        $content_type_list = array(2 => '建议',1 => '投诉', 0 => '反馈');
        $this->assign('content_type_list', $content_type_list);
        $this->assign('info', $info);
        $this->assign('admtypes', Gionee_Service_Feedbackmsg::$admtypes);
        $this->assign('types', Gionee_Service_Feedbackmsg::$types);
    }

    public function faqlistAction() {
        $get = $this->getInput(array('togrid', 'page', 'rows', 'sort', 'order'));
        if (!empty($get['togrid'])) {
            $page           = max(intval($get['page']), 1);
            $offset         = !empty($get['rows']) ? $get['rows'] : $this->pageSize;
            $sort           = !empty($get['sort']) ? $get['sort'] : 'id';
            $order          = !empty($get['order']) ? $get['order'] : 'asc';
            $orderBy[$sort] = $order;

            $where = array();
            foreach ($_POST['filter'] as $k => $v) {
                if (strlen($v) != 0) {
                    if ($k == 'content') {
				       $where['fag_tag'] = array('LIKE', $v);
			        }else{
                       $where[$k] = $v;
			        }
                }
            }

            $total = Gionee_Service_Feedbackfaq::getTotal($where);
            $list  = Gionee_Service_Feedbackfaq::getList($page, $offset, $where, $orderBy);
            foreach ($list as $k => $v) {
                $list[$k]['created_at'] = date('y/m/d H:i', $v['created_at']);
                $list[$k]['type']       = Gionee_Service_Feedbackmsg::$types[$v['type']];
            }

            $ret = array(
                'total' => $total,
                'rows'  => $list,
            );
            echo Common::jsonEncode($ret);
            exit;
        }

        $this->assign('types', Gionee_Service_Feedbackmsg::$types);
    }

    public function faqeditAction() {
        $id       = $this->getInput('id');
        $postData = $this->getPost(array('id', 'title', 'content','faq_tag' ,'type', 'url', 'sort'));
        $now      = time();
        if (!empty($postData['title'])) {

            if (empty($postData['title'])) {
                $this->output(-1, '输入名称');
            }

            if (empty($postData['content'])) {
                $this->output(-1, '输入内容');
            }
            if (empty($postData['faq_tag'])) {
                $this->output(-1, '输入标签');
            }

            if (empty($postData['type'])) {
                $this->output(-1, '输入渠道');
            }

            if (empty($postData['id'])) {
                $postData['created_at'] = $now;
                $ret                    = Gionee_Service_Feedbackfaq::add($postData);
            } else {
                $ret = Gionee_Service_Feedbackfaq::set($postData, $postData['id']);
            }

            if ($ret) {
                $this->output(0, '操作成功');
            } else {
                $this->output(-1, '操作失败');
            }
        }

        $info = Gionee_Service_Feedbackfaq::get($id);
        $this->assign('info', $info);
        $this->assign('types', Gionee_Service_Feedbackmsg::$types);

    }

    public function faqdelAction() {
	    $idArr   = (array) $this->getInput('id');
        $i     = 0;
        $succ  = array();
        foreach ($idArr as $id) {
            $ret = Gionee_Service_Feedbackfaq::del($id);
            if ($ret) {
                $i++;
                $succ[] = $id;
            }
        }
        if ($i == count($idArr)) {
            $this->output(0, '操作成功');
        } else {
            $this->output(-1, '操作失败', $succ);
        }
    }

    public function msgoptAction() {
        $ids   = $this->getPost('id');
		$data=array();
		$f_status  = $this->getPost('f_status');
		$adm_type  = $this->getPost('adm_type');
		if($f_status!=-1){
            $data['f_status'] = $f_status;
		}
		if($adm_type!=-1){
            $data['adm_type'] = $adm_type;
		}
        $idArr = explode('#', $ids);
        $i     = 0;
        $succ  = array();
        foreach ($idArr as $id) {
			$ret = Gionee_Service_Feedbackmsg::set($data, $id);
            if ($ret) {
                $i++;
                $succ[] = $id;
            }
        }
        if ($i == count($idArr)) {
            $this->output(0, '操作成功');
        } else {
            $this->output(-1, '操作失败', $succ);
        }
    }

    public function configAction() {
        $feedback_tip = $this->getPost('feedback_tip');
        if (!empty($feedback_tip[1])) {
            Gionee_Service_Config::setValue('feedback_tip', Common::jsonEncode($feedback_tip));
            $this->output(0, '操作成功');
        }
        $this->assign('types', Gionee_Service_Feedbackmsg::$types);
        $tmp = Gionee_Service_Config::getValue('feedback_tip');
        $this->assign('feedback_tip', json_decode($tmp, true));
    }


    public function keylistAction() {
        $get    = $this->getInput(array('togrid', 'page', 'rows', 'sort', 'order'));
        $togrid = !empty($get['togrid']) ? true : false;
        if ($togrid) {
            $page           = max(intval($get['page']), 1);
            $offset         = !empty($get['rows']) ? $get['rows'] : $this->pageSize;
            $sort           = !empty($get['sort']) ? $get['sort'] : 'id';
            $order          = !empty($get['order']) ? $get['order'] : 'desc';
            $orderBy[$sort] = $order;
            $where          = array();
            foreach ($_POST['filter'] as $k => $v) {
                if (strlen($v) != 0) {
                    if ($k == 'content') {
				       $where['key_tag'] = array('LIKE', $v);
			        }else{
                       $where[$k] = $v;
			        }
                }
            }

            if ($_POST['export']) {
                $rows = array(
                    array(
                        '名称',
                        '内容',
                        '标签',
                        '渠道',
                        '状态'
                    )
                );

                $arr = Gionee_Service_Feedback::getKeyDao()->getsBy($where, $orderBy);
                foreach ($arr as $v) {

                    $rows[]    = array(
                        $v['name'],
                        $v['content'],
                        $v['key_tag'],
                        $v['type'],
                        $v['status']
                    );
                }
                Common::export($rows, '', '', '关键字');
                exit;
            }

            $total = Gionee_Service_Feedback::getKeyDao()->count($where);
            $list  = Gionee_Service_Feedback::getKeyDao()->getList(($page - 1) * $offset, $offset, $where, $orderBy);
            foreach ($list as $k => $v) {
                $list[$k]['updated_at'] = date('y/m/d H:i', $v['updated_at']);
                $list[$k]['created_at'] = date('y/m/d H:i', $v['created_at']);
                $list[$k]['status']     = ($v['status'] == '1') ? '开启' : '关闭';
                $list[$k]['type']       = Gionee_Service_Feedbackmsg::$types[$v['type']];


            }
            $ret = array(
                'total' => $total,
                'rows'  => $list,
            );
            echo json_encode($ret);
            exit;
        }
		$this->assign('types', Gionee_Service_Feedbackmsg::$types);
    }

    public function keyeditAction() {
        $id       = $this->getInput('id');
        $postData = $this->getPost(array('id', 'name', 'content','key_tag','type', 'status',));
        $now      = time();
        if (!empty($postData['name'])) {
            $postData['updated_at'] = $now;

            if (empty($postData['id'])) {
                $postData['created_at'] = $now;
                $ret                    = Gionee_Service_Feedback::getKeyDao()->insert($postData);
            } else {
                $ret = Gionee_Service_Feedback::getKeyDao()->update($postData, $postData['id']);
            }
            Gionee_Service_Feedback::getKeyStr(true);
            Gionee_Service_Feedback::getKeyInfo($postData['name'], true);
            Admin_Service_Log::op($postData);
            if ($ret) {
                Common::getCache()->delete('FEEDBACK_KEY_ALL_TAG:'.$postData['type']);
                $this->output(0, '操作成功');
            } else {
                $this->output(-1, '操作失败');
            }
        }

        $info = Gionee_Service_Feedback::getKeyDao()->get($id);
        $this->assign('info', $info);
		 $this->assign('types', Gionee_Service_Feedbackmsg::$types);
    }

    public function keydelAction() {
        $ids  = (array)$this->getInput('id');
	    foreach($ids as $id) {
		    $ret = Gionee_Service_Feedback::getKeyDao()->delete($id);
		    Admin_Service_Log::op($id);
	    }
       // Gionee_Service_Feedback::getKeyStr(true);
		for($i=1;$i<6;$i++){
		   Common::getCache()->delete('FEEDBACK_KEY_ALL_TAG:'.$i);
		}
        if ($ret) {
            $this->output(0, '操作成功');
        } else {
            $this->output(-1, '操作失败');
        }
    }

	public function keyimportAction() {
		if (!empty($_FILES['keycsv']['tmp_name'])) {
			$file = $_FILES["keycsv"]['tmp_name'];
			$this->_keycsv($file);
			for($i=1;$i<6;$i++){
			   Common::getCache()->delete('FEEDBACK_KEY_ALL_TAG:'.$i);
		    }
			$this->output(0, '操作成功');
			exit;
		}
	}

	private function _keycsv($file) {
		$keyheader = array('name', 'content', 'key_tag', 'type','status');
		$row      = 1;
		$num      = count($keyheader);
		$filed    = $keyheader;
		$types = array(
				1 => '畅聊',
				2 => '个人中心',
				3 => '浏览器',
				4 => '导航',
				5 => '网址大全',
			);
		if (($handle = fopen($file, "r")) !== false) {
			while (($data = fgetcsv($handle, 1000, ",")) !== false) {
				if ($row > 1) {
					$info = array();
					for ($c = 0; $c < $num; $c++) {
						$k        = $filed[$c];
						$info[$k] = $data[$c];
					}
					$info['name']  = iconv('GBK', 'UTF8', $info['name']);
					$info['content'] = iconv('GBK', 'UTF8', $info['content']);
					$info['key_tag'] = iconv('GBK', 'UTF8', $info['key_tag']);
					$columnInfo =Gionee_Service_Feedback::getKeyDao()->getBy(array('name' => $info['name']));
					if (!empty($columnInfo['id'])) {
	                    $info['updated_at'] = time();
						$ret = Gionee_Service_Feedback::getKeyDao()->update($info, $columnInfo['id']);
					} else {
						$info['created_at'] = time();
						$ret = Gionee_Service_Feedback::getKeyDao()->insert($info);
					}
				}
				$row++;
			}
			fclose($handle);
		}
	}

}