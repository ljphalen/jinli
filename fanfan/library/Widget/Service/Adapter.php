<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class Widget_Service_Adapter {

	/**
	 *
	 * @param array $cpUrlInfo
	 * @return array
	 */
	static public function getParams($cpUrlInfo) {
		$params           = array();
		$cpUrlInfo['url'] = html_entity_decode($cpUrlInfo["url"]);

		$callMethod = '_params_' . $cpUrlInfo['cp_id'];
		if (method_exists('Widget_Service_Parse', $callMethod)) {
			$params = Widget_Service_Parse::$callMethod($cpUrlInfo);
		}

		return $params;
	}


	static public function getData($cpUrlInfo) {
		$params = self::getParams($cpUrlInfo);
		if (!stristr($cpUrlInfo['url'], 'http')) {
			return false;
		}
		$url   = html_entity_decode($cpUrlInfo['url']);
		$isXml = false;
		switch ($cpUrlInfo['cp_id']) {
			case Widget_Service_Cp::CP_SOHU_RSS:
				$isXml = 1;
				break;
			case Widget_Service_Cp::CP_YSS:
				$isXml = 1;
				break;
		}

		$data = self::getResponse($url, $params, $isXml);
		return $data;
	}

	/**
	 *
	 * @param array $cpUrlInfo
	 * @return boolean
	 */
	static public function done($data, $cpUrlInfo, &$out = '') {
		$md5Str = md5(json_encode($data));
		if (true || $cpUrlInfo['md5_data'] != $md5Str) {
			$callMethod = '_cook_' . $cpUrlInfo['cp_id'];

			$newData = array();
			if (method_exists('Widget_Service_Parse', $callMethod)) {
				$newData = Widget_Service_Parse::$callMethod($data, $cpUrlInfo);
			}

			// 写入数据
			$ret = array();
			if (count($newData)) {
				/**
				$outIIDs = array_keys($newData);
				$rows    = Widget_Service_Source::getsBy(array('out_iid' => array('IN', $outIIDs)));
				foreach ($rows as $v) {
					$out .= "OLD:{$v['out_iid']}:{$v['cp_id']}-{$v['out_id']}:{$v['title']}=>{$v['id']}\n";
					$valData = $newData[$v['out_iid']];
					if (in_array($v['cp_id'], array(114, 111, 116))) {//校正程序代码逻辑
						$u = array(
							'out_link' => $valData['out_link'],
							'content'  => $valData['content'],
							'url'      => $valData['url'],
							'mark'     => $valData['mark'],
						);
						Widget_Service_Source::update($u, $v['id']);
					} else if (in_array($v['cp_id'], array(105, 110))) {//校正程序代码逻辑
						Widget_Service_Source::update(array('content' => $valData['content']), $v['id']);
					}
					unset($newData[$v['out_iid']]);
				}
				**/

				foreach ($newData as $valData) {
					$info = Widget_Service_Source::getByTitle($valData['title']);
					if (empty($info['id'])) { //标题去重
						$id = Widget_Service_Source::add($valData);
						$out .= "NEW:{$valData['title']}:{$id}:{$valData['out_iid']}:{$valData['cp_id']}-{$valData['out_id']}\n";
						$ret[] = $id;
					} else {
						//校正程序代码逻辑
						$out .= "HAS:{$info['title']}:{$info['id']}:({$info['out_iid']}:{$info['cp_id']}-{$info['out_id']})";
						/**
						if ($valData['out_iid'] != $info['out_iid']) {
							Widget_Service_Source::update(array('out_iid' => $valData['out_iid']), $info['id']);
							$out .= "=>({$valData['out_iid']}:{$valData['cp_id']}-{$valData['out_id']})";
							error_log("out_id:{$valData['out_id']}-{$info['out_id']},out_iid:{$valData['out_iid']}-{$info['out_iid']},cp_id:{$valData['cp_id']}-{$info['cp_id']},id:{$info['id']},title:{$info['title']},date:".date('Y-m-d H:i:s', $valData['create_time']).'-'.date('Y-m-d H:i:s', $info['create_time'])."\n", 3, '/tmp/fanfan_mysql_' . date('Ymd'));
						}
						**/
						$out .= "\n";
					}
				}
			}

			Widget_Service_Cp::update(array('last_time' => time(), 'md5_data' => $md5Str), $cpUrlInfo['id']);
			return $ret;
		}
		return true;
	}


	/**
	 * 下载图片
	 * @param array $data
	 * @param string $name
	 * @param string $savepath
	 */
	static public function downImages($list, $name, $savepath) {

		$data = array();
		if (!file_exists(self::tmpPath())) {
			$old = umask(0);
			mkdir(self::tmpPath(), 0777, true);
			umask($old);
		}

		$mh = new Util_Http_CurlMulti();
		foreach ($list as $id => $url) {
			$curl = new Util_Http_Curl($url);
			$curl->Options("get", array());
			$hd = $curl->getHttpHandler();
			$mh->addHandler($id, $hd);
		}
		$result = $mh->execute();

		$dir = $savepath . "/" . date('Ymd');
		if (!file_exists($dir)) {
			$old = umask(0);
			mkdir($dir, 0777, true);
			umask($old);
		}

		foreach ($list as $id => $url) {
			$tmp_file = self::tmpPath() . "/" . md5($url);

			if (!file_exists($tmp_file)) {
				file_put_contents($tmp_file, $result[$id]);
			}
			$extension = end(explode("/", mime_content_type($tmp_file)));
			if (in_array($extension, self::getAllow())) {
				$filename = md5($url) . "." . $extension;
				$fullFile = $dir . "/" . $filename;
				if (@copy($tmp_file, $fullFile)) {
					$data[$id] = date('Ymd') . "/" . $filename;
				}

			}
		}
		return $data;
	}


	/**
	 * 生成缩略图
	 * @param array $data
	 * @param int $dstW
	 * @param int $dstH
	 * @param string $name
	 * @param string $isProportion
	 */
	static public function mkThumbs($srcFile, $dstW, $dstH, $name, $urlId = 0, $fix = 1) {
		$dstFile = false;
		if (file_exists($srcFile)) {

			$imgType   = strtolower(pathinfo($srcFile, PATHINFO_EXTENSION));
			$thumbName = $name . "_" . $dstW . "x" . $dstH . "." . $imgType;

			list($srcW, $srcH, $type, $attr) = getimagesize($srcFile);

			$srcIm = null;
			if ($imgType == 'gif') {
				$srcIm = imagecreatefromgif($srcFile);
			} else if ($imgType == 'jpg' || $imgType == 'jpeg') {
				$srcIm = imagecreatefromjpeg($srcFile);
			} else if ($imgType == 'png') {
				$srcIm = imagecreatefrompng($srcFile);
			}

			if ($srcIm == null) {
				return false;
			}

			//如果图片高度小于100 视为无效图片
			if ($srcH < 100) {
				return false;
			}

			$srcX = $srcY = $dstX = $dstY = 0;

			//图像截取
			$dsDivision  = $srcH / $srcW;
			$fixDivision = $dstH / $dstW;

			if ($dsDivision > $fixDivision) {
				$tmp     = $srcW * $fixDivision;
				$diffDiv = $dsDivision - $fixDivision;
				//高比较大的图片 可能是人物 需要从顶部开始
				//urlID = 5 i时尚明星 最好r=10
				$r = ($diffDiv > 0.3) ? 8 : 2;
				if ($urlId == 5) { //urlID = 5 i时尚明星 最好r=10
					$r = 10;
				}
				$srcY = round(($srcH - $tmp) / $r);
				//echo implode(',', array('div',$diffDiv, $srcH ,$tmp,$srcH - $tmp))."\n";
				$srcH = $tmp;
			} else {
				$tmp  = $srcH / $fixDivision;
				$srcX = round(($srcW - $tmp) / 2);
				$srcW = $tmp;
			}

			//echo implode(',', array('args',$dsDivision, $fixDivision, $srcX, $srcY, $srcW, $srcH, $dstW, $dstH))."\n";
			//$arr = array($srcFile, $dstX, $dstY, $srcX, $srcY, $dstW, $dstH, $srcW, $srcH);
			//error_log(implode(',',$arr)."\n",3,'/tmp/fanfan_thumb_'.date('Ymd'));

			if ($fix) {
				if ($srcH < $dstH || $srcW < $dstW) {//图片小于规格 只裁剪
					if ($srcH > $srcW) {
						$dstW = $srcW;
						$dstH = floor($srcW * $fixDivision);
					} else {
						$dstH = $srcH;
						$dstW = floor($srcH / $fixDivision);
					}
				}
			}

			if (function_exists("imagecopyresampled")) {
				$dstIm = imagecreatetruecolor($dstW, $dstH);
				imagecopyresampled($dstIm, $srcIm, $dstX, $dstY, $srcX, $srcY, $dstW, $dstH, $srcW, $srcH);
			} else {
				$dstIm = imagecreate($dstW, $dstH);
				imagecopyresized($dstIm, $srcIm, $dstX, $dstY, $srcX, $srcY, $dstW, $dstH, $srcW, $srcH);
			}

			if (function_exists('imagecolorallocate') && function_exists('imagecolortransparent')) {
				//$black = imagecolorallocate($dstIm, 0, 0, 0);
				//imagecolortransparent($dstIm, $black);
			}

			$dstFile = self::getSavePath() . "/" . $thumbName;

			if ($imgType == 'gif') {
				imagegif($dstIm, $dstFile);
			} else if ($imgType == 'jpg' || $imgType == 'jpeg') {
				imagejpeg($dstIm, $dstFile, 100);
			} else if ($imgType == 'png') {
				imagepng($dstIm, $dstFile);
			}

			imagedestroy($srcIm);
			imagedestroy($dstIm);
		}
		return $dstFile;
	}

	/**
	 *
	 * @param unknown_type $files
	 */
	static public function getImgColors($files) {
	}

	static public function getAllow() {
		return array('jpg', 'jpeg', 'png');
	}

	static function tmpPath() {
		return "/tmp/adapter";
	}

	static public function getSavePath() {
		return realpath(Common::getConfig("siteConfig", "attachPath")) . "/source";
	}

	/**
	 * 获取接口数据
	 *
	 * @param string $url
	 * @param array $params
	 * @return array
	 */
	static public function getResponse($url, $params = array(), $isXml = false) {
		$ret    = array();
		$url    = trim($url);
		$curl   = new Util_Http_Curl($url);
		$result = $curl->get($params);

		$headers = get_headers($url, 1);
		if ($headers['Content-Encoding'] == 'gzip') {
			$result = gzdecode($result);
		}

		if (!empty($result)) {
			if ($isXml) {
				$ret = Util_XML2Array::createArray($result);
			} else {
				$ret = json_decode($result, true);
			}
		}
		return $ret;
	}

	static public function formatContentBy114($content = '') {
		$tmp = array();
		preg_match_all('/<\img src="(.*)"\/>/Use', $content, $m);
		foreach ($m[0] as $k => $v) {
			$content = str_replace($v, "<p>{$m[1][$k]}</p>", $content);
		}

		preg_match_all('/<p>(.*)<\/p>/Use', $content, $matches);
		if ($matches[1]) {
			foreach ($matches[1] as $val) {
				$v = str_replace("　", '', trim($val));
				if (!empty($v)) {
					if (substr($v, 0, 4) == 'http') {
						$tmp[] = array('type' => 2, 'value' => $v);
					} else {
						$tmp[] = array('type' => 1, 'value' => $v);
					}
				}
			}
		}

		return $tmp;
	}

	static public function formatContentBy105($content = '') {
		$tmp = array();
		preg_match_all('/<p>(.*)<\/p>/Use', $content, $matches);
		if ($matches[1]) {
			foreach ($matches[1] as $val) {
				$v = str_replace("　", '', trim($val));
				preg_match_all('/<\!--\$image="(.*)"-->/Use', $v, $match);
				if (!empty($match[0])) {
					if (!empty($match[1][0])) {
						$tmp[] = array('type' => 2, 'value' => trim($match[1][0]));
					}

					$t = str_replace($match[0], '', $v);
					if (!empty($t)) {
						$tmp[] = array('type' => 1, 'value' => $t);
					}
				} else {
					preg_match_all('/<img src="(.*)" \/>/', $v, $matchimg);
					if (!empty($matchimg[0])) {
						$v = str_replace($matchimg[0], '', $v);
					}
					if (!empty($v)) {
						$tmp[] = array('type' => 1, 'value' => trim($v));
					}

				}
			}
		}
		return $tmp;
	}

	static public function formatContentToView($contents) {
		$maxLimit = Widget_Service_Config::getValue('w3_content_len');
		$tmpLimit = 0;
		$str      = '';
		foreach ($contents as $val) {
			if ($val['type'] == 1 && !empty($val['value'])) {
				$str .= "<div class='ptxt'>{$val['value']}</div>";
				$tmpLimit += mb_strlen($val['value'], 'utf8');
			} else if ($val['type'] == 2 && !empty($val['value'])) {
				$tmpV = trim($val['value'], '"');
				$str .= "<div class='pimg'><img src='{$tmpV}' /> </div>";
			}

			if ($maxLimit > 0 && $tmpLimit >= $maxLimit) {
				break;
			}
		}
		return $str;
	}

	static public function formatContentToText($content) {
		$maxLimit = Widget_Service_Config::getValue('w3_content_len');
		if ($maxLimit > 0) {
			$ret = mb_substr($content, 0, $maxLimit, 'utf8');
		} else {
			$ret = $content;
		}

		return $ret;
	}
}


function unichr($i) {
	return iconv('UCS-4LE', 'UTF-8', pack('V', $i));
}
