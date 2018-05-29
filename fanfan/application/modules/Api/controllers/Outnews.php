<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * 翻翻3.0 对外接口
 */
class OutnewsController extends Api_BaseController {

	/**
	 * 新闻id列表接口
	 *
	 * @version 3.0
	 */
	public function listAction() {
		$this->_log(array(__METHOD__, $_REQUEST));

		$params = $this->getInput(array('page', 'size'));

		$search  = array('cp_id' => array('IN', Widget_Service_Cp::$jsonFormatCp));
		$total   = Widget_Service_Source::getTotal();
		$size    = min(max($params['size'], 10), 100);
		$maxPage = ceil($total / $size);
		$page    = min($maxPage, max(1, $params['page']));
		$list    = Widget_Service_Source::getList($page, $size, array($search), array('id' => 'DESC'));
		$tmp     = array();
		foreach ($list as $row) {
			$contentArr = !empty($row['content']) ? json_decode($row['content'], true) : array();
			if ($contentArr) {
				$tmp[] = array(
					'id'          => intval($row['id']),
					'title'       => $row['title'],
					'summary'     => $row['summary'],
					'img'         => Common::getAttachPath() . "/source/" . $row['img'],
					'cp_id'       => $row['cp_id'],
					'content'     => $contentArr,
					'out_link'    => $row['out_link'],
					'create_time' => date('Y-m-d H:i:s', $row['source_time']),
				);
			}

		}

		$data = array('page' => $page, 'size' => $size, 'list' => $tmp);

		//输出结果
		$this->output(0, '', $data);
	}


	/**
	 * 日志记录
	 *
	 * @author william.hu
	 * @param array $arr
	 */
	private function _log($args) {
		if (ENV == 'develop' || ENV == 'test') {
			array_push($args, $_SERVER['HTTP_USER_AGENT']);
			array_push($args, $_SERVER['REMOTE_ADDR']);

			$logFile = '/tmp/fanfan_w3_access_' . date('Ymd');
			$logText = date('Y-m-d H:i:s') . ' ' . json_encode($args) . "\n";
			error_log($logText, 3, $logFile);
		}
	}

}