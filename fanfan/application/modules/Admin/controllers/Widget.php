<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 */
class WidgetController extends Admin_BaseController {

	public $actions = array(
		'editSourceUrl' => '/Admin/Widget/editSource',
		'connPostUrl'   => '/Admin/Widget/connPost',
	);

	public $perpage = 20;

	/**
	 * 1.0版本接口 的数据来源
	 * @author huwei
	 */
	public function editSourceAction() {

		$urlIdArr = $this->getPost('url_id');
		if ($urlIdArr) {
			$urlIds   = array_keys($urlIdArr);
			$urlIdStr = implode(',', $urlIds);
			$ret      = Widget_Service_Config::setValue('widget_urlid_filter', $urlIdStr);
			if ($ret) {
				$this->output(0, '操作成功');
			} else {
				$this->output(-1, '-操作失败.');
			}
		} else {
			$urlIdStr = Widget_Service_Config::getValue('widget_urlid_filter');
			$urlIds   = explode(",", $urlIdStr);
		}

		$cps = Widget_Service_Cp::getAll();
		foreach ($cps as $k => $v) {
			if (!in_array($v['cp_id'], Widget_Service_Source::$CpVer201)) {
				unset($cps[$k]);
			}
		}

		$this->assign('cpurl', $cps);
		$this->assign('urlIds', $urlIds);

	}

	/**
	 *
	 * 联网配置
	 */
	public function connAction() {
		$configs = Widget_Service_Config::getAllConfig();
		$this->assign('configs', $configs);
	}

	/**
	 * 更新联网配置
	 */
	public function connPostAction() {
		$args = array(
			'widget_connect1' => FILTER_SANITIZE_STRING,
			'widget_color'    => FILTER_SANITIZE_STRING,
			'title_len'       => FILTER_SANITIZE_STRING,
			'summary_len'     => FILTER_SANITIZE_STRING,
			'asset_ver'       => FILTER_SANITIZE_STRING,

		);

		$formVals = filter_var_array($_REQUEST, $args);

		if (empty($formVals['widget_connect1'])) {
			$this->output(-1, '时间值不能为空.');
		}

		if (empty($formVals['widget_color'])) {
			$this->output(-1, '颜色不能为空.');
		}

		$tmp      = explode(',', $formVals['widget_connect1']);
		$tmpColor = explode(',', $formVals['widget_color']);

		foreach ($tmpColor as $valC) {
			if (strlen($valC) != 7 || substr($valC, 0, 1) != '#') {
				$this->output(-1, '颜色值非法.[' . $valC . ']');
			}
		}

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

		Widget_Service_Config::setValue('widget_connect1', implode(',', $t));
		Widget_Service_Config::setValue('widget_color', implode(',', $tmpColor));

		Widget_Service_Config::setValue('summary_len', $formVals['summary_len']);
		Widget_Service_Config::setValue('asset_ver', $formVals['asset_ver']);
		Widget_Service_Config::setValue('title_len', $formVals['title_len']);
		$this->output(0, '操作成功.');
	}

	public function webpAction() {
		$uploadfile = '/tmp/fanfanwepbfile';
		if (!empty($_FILES['img']['tmp_name'])) {
			$newFilename = $_FILES['img']['name'] . '.webp';
			image2webp($_FILES['img']['tmp_name'], $uploadfile);
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename=' . $newFilename);
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Content-Length: ' . filesize($uploadfile));
			ob_clean();
			flush();
			readfile($uploadfile);
			exit;
		}

	}
}