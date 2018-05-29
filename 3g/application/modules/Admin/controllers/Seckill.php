<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 双十一秒杀预热列表
 */
class SeckillController extends Admin_BaseController {

    public $actions = array(
        'listUrl'   => '/Admin/Seckill/list',
        'delUrl'    => '/Admin/Seckill/del',
        'sendUrl'    => '/Admin/Seckill/send',
		'configUrl'	=>'/Admin/Seckill/log',
    );
    public $perpage = 1000;
    public function listAction() {
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
                    if ($k == 'send_sgn') {
				      // $where['fag_tag'] = array('LIKE', $v);
					  if($v!=-1){
					     $where['send_sgn'] = $v;
					  }
			        }else{
                       $where[$k] = $v;
			        }
                }
            }
            $total = Event_Service_Seckillremind::getTotal($where);
            $list  = Event_Service_Seckillremind::getList($page, $offset, $where, $orderBy);
            foreach ($list as $k => $v) {
                $list[$k]['add_time'] = $v['add_time']=='0'?'--':date('y/m/d H:i', $v['add_time']);
                $list[$k]['send_sgn'] = $v['send_sgn']==1?'已发送':'未发送';
				$list[$k]['send_time'] = $v['send_time']=='0'?'--':date('y/m/d H:i', $v['send_time']);
            }
            $ret = array(
                'total' => $total,
                'rows'  => $list,
            );
            echo Common::jsonEncode($ret);
            exit;
        }
    }

	public  function  configAction(){
		$keys = array('seckill_remind_start_time',
				'seckill_remind_end_time',
				'seckill_remind_status',
                'seckill_remind_jb',
				'seckill_remind_rule',
		);
		$postData = $this->getInput($keys);
		if($postData['seckill_remind_start_time']){
			foreach ($postData as $k => $v) {
				Gionee_Service_Config::setValue($k, $v);
			}
			Event_Service_Activity::getRemindConfigData(true);
			$this->output('0', '编辑成功！');
		}
		$params['3g_key'] = array('IN',$keys);
		$ret = Gionee_Service_Config::getsBy($params);
		foreach ($ret as $k=>$v){
			$data[$v['3g_key']] = $v['3g_value'];
		}
		$this->assign('data', $data);
	}

    public function delAction() {
        @set_time_limit(0);
        $idArr   = (array) $this->getInput('id');
        $i     = 0;
        $succ  = array();
        foreach ($idArr as $id) {
            $ret = Event_Service_Seckillremind::del($id);
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

    public function sendAction() {
        @set_time_limit(0);
        $idArr   = (array) $this->getInput('id');
        $i     = 0;
        $succ  = array();
        $tel = new Vendor_Tel();
        //发短信
        foreach ($idArr as $phone) {
            //$out = $tel->templateSMS($phone, 2934, implode(',', array('1234', 10)));//发短信
            $out = $tel->templateSMS($phone, 15485, "$phone");//发短信
            $out=true;
            if($out){
                $ret = Event_Service_Seckillremind::_getDao()->updateBy(array('send_sgn'=>1,'send_time'=>time()), array('mobile'=>$phone));
                if ($ret) {
                    $i++;
                    $succ[] = $phone;
                }
            }
        }
            if ($i == count($idArr)) {
            $this->output(0, '操作成功');
        } else {
            $this->output(-1, '操作失败', $succ);
        }
    }
}