<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class VodController extends Front_BaseController {

	public function indexAction() {
		$a      = new Vendor_Vod();
		$nodeId = !empty($_GET['nodeId']) ? $_GET['nodeId'] : 10392624;
		$code   = !empty($_GET['c']) ? $_GET['c'] - 1 : 0;
		//$code = 1;
		$t   = !empty($_GET['t']) ? $_GET['t'] : 4;
		$day = !empty($_GET['day']) ? $_GET['day'] : 0;
		$no  = !empty($_GET['no']) ? $_GET['no'] : 0;

		$rcKey = 'VOD:' . $nodeId;
		$data  = Common::getCache()->get($rcKey);
		if (empty($data)) {
			$ret  = $a->getLivingList($nodeId, $day);
			$data = array();
			foreach ($ret['programs'] as $val) {
				$c                       = $a->getMediaList($nodeId, $val['contId']);
				$d                       = $c['mediaList'][$code];
				$b                       = $a->getPlayUrl($nodeId, $val['contId'], $d, $t);
				$data[$val['startTime']] = array(
					'contId'      => $val['contId'],
					'name'        => $val['name'],
					'day'         => date('Y-m-d', strtotime($val['startTime'])),
					'startTime'   => $val['startTime'],
					'endTime'     => $val['endTime'],
					'playUrl_ext' => $b['playUrl'] . "&playbackbegin=" . date('YmdHis', strtotime($val['startTime'])) . "&playbackend=" . date('YmdHis', strtotime($val['endTime'])),
					'playUrl'     => $b['playUrl'],
					'code'        => $d,
				);
			}
			ksort($data);
			Common::getCache()->set($rcKey, $data, 86400);
		}


		$this->assign('day', $day);
		$this->assign('data', $data);
		$this->assign('no', $no);
		$this->assign('nodeId', $nodeId);
		$this->assign('tvs', self::$tvs);

		$tvs_list = call_user_func_array('array_merge', array_values(self::$tvs));
		$this->assign('tvs_list', array_flip($tvs_list));
	}

	static $tvs = array(
		'BTV'  => array(
			'BTV财经' => '10349739',
			'BTV科教' => '10349738',
			'BTV青少' => '10349740',
			'BTV生活' => '10349737',
			'BTV体育' => '10349733',
			'BTV文艺' => '10349735',
			'BTV新闻' => '10349736',
			'BTV影视' => '10349734',
		),
		'CCTV' => array(
			'CCTV1'    => '10242980',
			'CCTV11'   => '10302056',
			'CCTV12'   => '10302057',
			'CCTV2'    => '10242984',
			'CCTV3'    => '10242981',
			'CCTV4'    => '10242982',
			'CCTV6'    => '10242974',
			'CCTV7'    => '10302058',
			'CCTVNews' => '10302059',
			'CCTV少儿'   => '10302060',
			'CCTV新闻'   => '10242734',
			'CCTV音乐'   => '10302061',
		),
		'地方卫视' => array(
			'安徽卫视'  => '10271938',
			'兵团卫视'  => '10349746',
			'东方卫视'  => '10242746',
			'东南卫视'  => '10271896',
			'法制在线'  => '10361384',
			'甘肃卫视'  => '10302065',
			'广东卫视'  => '10536754',
			'广西卫视'  => '10301641',
			'贵州卫视'  => '10271948',
			'河北卫视'  => '10539317',
			'河南卫视'  => '10271946',
			'黑龙江卫视' => '10301645',
			'湖北卫视'  => '10271947',
			'湖南卫视'  => '10311687',
			'吉林卫视'  => '10539336',
			'江苏卫视'  => '10271943',
			'康巴卫视'  => '10349753',
			'辽宁卫视'  => '10539319',
			'旅游卫视'  => '10301642',
			'内蒙古卫视' => '10302063',
			'宁夏卫视'  => '10302064',
			'青海卫视'  => '10302068',
			'山东教育'  => '10349752',
			'山东卫视'  => '10271939',
			'山西卫视'  => '10539320',
			'陕西卫视'  => '10302067',
			'天津卫视'  => '10271937',
			'西藏卫视'  => '10301643',
			'香港卫视'  => '10349766',
			'新疆卫视'  => '10271942',
			'延边卫视'  => '10349754',
			'云南卫视'  => '10302062',
			'重庆卫视'  => '10539335',
		),
		'地方高清' => array(
			'江苏卫视高清' => '10446323',
			'天津卫视高清' => '10446341',
			'东方卫视高清' => '10446848',
			'湖南卫视高清' => '10392624',
		),
	);


	static $columnList = array(
		2 => '地方卫视',
		1 => 'CCTV',
		3 => '各地频道',
		4 => '地方高清',
		5 => '外文频道',
	);

	public function listAction() {
		Gionee_Service_Log::pvLog('vod_list');
		$group = $this->getInput('group');
		$group = !empty($group) ? $group : 2;
		$t_bi  = $this->getSource();
		Gionee_Service_Log::incrBy(Gionee_Service_Log::TYPE_VOD_PV, 'g' . $group);
		Gionee_Service_Log::toUVByCacheKey(Gionee_Service_Log::TYPE_VOD_UV, 'g' . $group, $t_bi);
		$columnList = self::$columnList;

		$rcKey = 'VOD_LIST:' . $group;
		$list  = Common::getCache()->get($rcKey);
		if (empty($list)) {
			$list = Gionee_Service_VodChannel::getsBy(array('type' => $group));
			$now  = time();
			foreach ($list as $k => $v) {
				$where   = array('channel_id' => $v['channel_id'], 'day' => date('Y-m-d'));
				$tmpList = Gionee_Service_VodContent::getsBy($where);

				$info = array();
				$i    = 0;
				foreach ($tmpList as $kk => $vv) {
					$info = $vv;
					$i    = $kk;
					if ($now <= strtotime($vv['end_time']) && $now >= strtotime($vv['start_time'])) {
						break;
					}
				}

				$list[$k]['cur']  = $info;
				$nextI            = $i + 1;
				$list[$k]['next'] = isset($tmpList[$nextI]) ? $tmpList[$nextI] : array();
			}
			Common::getCache()->set($rcKey, $list, 600);
		}


		$title = $columnList[$group];
		$this->assign('title', $title);
		$this->assign('group', $group);
		$this->assign('list', $list);
		$this->assign('column_list', $columnList);
	}

	public function infoAction() {

		$id   = $this->getInput('id');
		$t_bi = $this->getSource();
		Gionee_Service_Log::incrBy(Gionee_Service_Log::TYPE_VOD_PV, $id);
		Gionee_Service_Log::toUVByCacheKey(Gionee_Service_Log::TYPE_VOD_UV, $id, $t_bi);

		$rcKey = 'VOD_INFO:' . $id;
		$info  = Common::getCache()->get($rcKey);
		if (empty($info)) {
			$info = Gionee_Service_VodChannel::get($id);
			Common::getCache()->set($rcKey, $info, 600);
		}

		$rcKey = 'VOD_INFO_LIST:' . $info['channel_id'];
		$list  = Common::getCache()->get($rcKey);
		if (empty($list)) {
			$where = array('channel_id' => $info['channel_id'], 'day' => date('Y-m-d'));
			$list  = Gionee_Service_VodContent::getsBy($where);
			if (empty($list) && !empty($info['channel_id'])) {
				$list = Gionee_Service_VodContent::getListByChannelId($info['channel_id']);
			}
			Common::getCache()->set($rcKey, $list, 600);
		}

		$this->assign('title', $info['name']);
		$this->assign('info', $info);
		$this->assign('list', $list);
	}

	public function downimgAction() {
		$list = Gionee_Service_VodChannel::getsBy();
		$path = Common::getConfig('siteConfig', 'attachPath') . '/vod/';
		foreach ($list as $key => $value) {
			$file = $path . $value['id'] . '.jpg';
			if (!file_exists($file)) {
				echo "download img for " . $value['id'] . "\n";
				@mkdir($path, 0777, true);
				$content = file_get_contents($value['img']);
				file_put_contents($file, $content);
				echo $file . "<br>";
			}
		}
		exit;
	}

	public function upAction() {
		Gionee_Service_VodContent::run();
		exit;
	}

}