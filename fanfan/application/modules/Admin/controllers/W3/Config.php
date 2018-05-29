<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 */
class W3_ConfigController extends Admin_BaseController {

	public $actions = array(
		'editUrl'   => '/Admin/W3_Config/edit',
	);

	/**
	 * 更新联网配置
	 */
	public function editAction() {
		$args = array(
			'w3_content_len' => FILTER_SANITIZE_STRING,
			'w3_auto_time' => FILTER_SANITIZE_STRING,
			'w3_init_news_id' => FILTER_SANITIZE_STRING,
		);
		$formVals = filter_var_array($_REQUEST, $args);

		if (!empty($_POST)) {
			$tmp      = explode(',', $formVals['w3_auto_time']);
			$tmpT = array();
			foreach ($tmp as $v) {
				if (!preg_match('/^([\d]{2}):([\d]{2})$/', $v)) {
					$this->output(-1, '时间值非法.');
				}

				list($h, $m) = explode(':', $v);
				if ($h >= 1 && $h <= 24 && $m >= 0 && $m <= 59) {
					$k        = $h . ':' . $m;
					$tmpT[$k] = 1;
				} else {
					$this->output(-1, '时间值非法.');
				}
			}
			$t   = array_keys($tmpT);
			$num = count($t);
			sort($t);
			if ($num != 1) {
				$this->output(-1, '时间数量错误,必须1个. 多个时间会导致服务器每个时间点压力过大');
			}

			Widget_Service_Config::setValue('w3_content_len', $formVals['w3_content_len']);
			Widget_Service_Config::setValue('w3_auto_time', implode(',', $t));
			Widget_Service_Config::setValue('w3_init_news_id', $formVals['w3_init_news_id']);
			$this->output(0, '操作成功.');
		}

		$configs = Widget_Service_Config::getAllConfig();
		$this->assign('configs', $configs);

	}
}