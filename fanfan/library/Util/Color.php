<?php
if (!defined('BASE_PATH')) exit ('Access Denied!');

/**
 * cookie class
 *
 * @author rainkid
 *
 */
class Util_Color {
	/**
	 *
	 * @param unknown_type $filename
	 * @return Ambigous <string, unknown>
	 */
	static public function getImgColor($filename) {
		$preview_width = $preview_height = 10;
		$size          = GetImageSize($filename);
		$scale         = 1;
		if ($size [0] > 0)
			$scale = min($preview_width / $size [0], $preview_height / $size [1]);
		if ($scale < 1) {
			$width  = floor($scale * $size [0]);
			$height = floor($scale * $size [1]);
		} else {
			$width  = $size [0];
			$height = $size [1];
		}
		$image_resized = imagecreatetruecolor($width, $height);
		if ($size [2] == 1) {
			$image_orig = imagecreatefromgif($filename);
		}
		if ($size [2] == 2) {
			$image_orig = imagecreatefromjpeg($filename);
		}
		if ($size [2] == 3)
			$image_orig = imagecreatefrompng($filename);
		imagecopyresampled($image_resized, $image_orig, 0, 0, 0, 0, $width, $height, $size [0], $size [1]);
		$im        = $image_resized;
		$imgWidth  = imagesx($im);
		$imgHeight = imagesy($im);
		for ($y = 0; $y < $imgHeight; $y++) {
			for ($x = 0; $x < $imgWidth; $x++) {
				$index            = imagecolorat($im, $x, $y);
				$Colors           = imagecolorsforindex($im, $index);
				$Colors ['red']   = intval((($Colors ['red']) + 15) / 32) * 32;
				$Colors ['green'] = intval((($Colors ['green']) + 15) / 32) * 32;
				$Colors ['blue']  = intval((($Colors ['blue']) + 15) / 32) * 32;
				if ($Colors ['red'] >= 256) {
					$Colors ['red'] = 240;
				}
				if ($Colors ['green'] >= 256) {
					$Colors ['green'] = 240;
				}
				if ($Colors ['blue'] >= 256) {
					$Colors ['blue'] = 240;
				}
				$hexarray [] = substr("0" . dechex($Colors ['red']), -2) . substr("0" . dechex($Colors ['green']), -2) . substr("0" . dechex($Colors ['blue']), -2);
			}
		}
		$hexarray = array_count_values($hexarray);
		natsort($hexarray);
		$hexarray = array_reverse($hexarray, true);

		$total = count($hexarray);
		$ret   = array('r' => 0, 'g' => 0, 'b' => 0);
		foreach ($hexarray as $key => $value) {
			$rgb = self::hColor2RGB("#" . $key);
			$ret['r'] += $rgb['r'];
			$ret['g'] += $rgb['g'];
			$ret['b'] += $rgb['b'];
		}

		$ret['r'] = ceil($ret['r'] / $total);
		$ret['g'] = ceil($ret['g'] / $total);
		$ret['b'] = ceil($ret['b'] / $total);

		$ret_c = array(
			'r' => $ret ['r'] - 30,
			'g' => $ret ['g'],
			'b' => $ret ['b']
		);
		$ret_2 = array(
			$ret['r'] => 'r',
			$ret['g'] => 'g',
			$ret['b'] => 'b'
		);

		$maxk = $ret_2 [max($ret)];
		$mink = $ret_2 [min($ret)];
		$ret_c [$maxk] += ceil($ret_c [$maxk] * 0.9);
		$ret_c [$mink] -= ceil($ret_c [$mink] * 0.9);
		return self::RGBToHex(sprintf("rgb(%d,%d,%d)", $ret_c['r'], $ret_c['g'], $ret_c['b']));
	}

	/**
	 * RGB转 十六进制
	 *
	 * @param $rgb RGB颜色的字符串
	 *            如：rgb(255,255,255);
	 * @return string 十六进制颜色值 如：#FFFFFF
	 */
	public static function RGBToHex($rgb) {
		$regexp   = "/^rgb\(([0-9]{0,3})\,\s*([0-9]{0,3})\,\s*([0-9]{0,3})\)/";
		$re       = preg_match($regexp, $rgb, $match);
		$re       = array_shift($match);
		$hexColor = "#";
		$hex      = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'A', 'B', 'C', 'D', 'E', 'F');
		for ($i = 0; $i < 3; $i++) {
			$r     = null;
			$c     = $match [$i];
			$hexAr = array();
			while ($c > 16) {
				$r = $c % 16;
				$c = ($c / 16) >> 0;
				array_push($hexAr, $hex [$r]);
			}
			array_push($hexAr, $hex [$c]);
			$ret  = array_reverse($hexAr);
			$item = implode('', $ret);
			$item = str_pad($item, 2, '0', STR_PAD_LEFT);
			$hexColor .= $item;
		}
		return $hexColor;
	}

	/**
	 * 十六进制转 RGB
	 *
	 * @param string $hexColor
	 *            十六颜色 ,如：#ff00ff
	 * @return array RGB数组
	 */
	public static function hColor2RGB($hexColor) {
		$color = str_replace('#', '', $hexColor);
		if (strlen($color) > 3) {
			$rgb = array(
				'r' => hexdec(substr($color, 0, 2)),
				'g' => hexdec(substr($color, 2, 2)),
				'b' => hexdec(substr($color, 4, 2))
			);
		} else {
			$color = str_replace('#', '', $hexColor);
			$r     = substr($color, 0, 1) . substr($color, 0, 1);
			$g     = substr($color, 1, 1) . substr($color, 1, 1);
			$b     = substr($color, 2, 1) . substr($color, 2, 1);
			$rgb   = array(
				'r' => hexdec($r),
				'g' => hexdec($g),
				'b' => hexdec($b)
			);
		}
		return $rgb;
	}
}