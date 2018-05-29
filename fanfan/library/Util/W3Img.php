<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 图片处理类库
 *
 * @package utility
 */
class Util_W3Img {
	private $_img = null;

	public function __construct($file) {
		$this->_img = new Imagick($file);
	}

	public function getW() {
		return $this->_img->getimagewidth();
	}

	public function getH() {
		return $this->_img->getimageheight();
	}

	public function getColor() {
		$w = ($this->getW() - 10) / 10;
		$h = ($this->getH() - 10) / 10;
		for ($i = 1; $i <= 10; $i++) {
			$pixel = $this->_img->getImagePixelColor($i * $w, $i * $h);
			$c     = $pixel->getcolor();
			unset($c['a']);
			$color[$i] = $c;
		}
		return $color;
	}

	public function getRGB() {
		$color = $this->getColor();
		$d     = array();
		foreach ($color as $i => $c) {
			$d[$i] = max($c) - min($c);
		}
		asort($d); //sort
		$tmp = array_keys($d);
		$idx = $tmp[9]; //get max val
		$arr = $color[$idx];
		return array(floor($arr['r'] / 2), floor($arr['g'] / 2), floor($arr['b'] / 2));
	}

	public function hexColor($rgb) {
		$val = dechex(($rgb[0] << 16) | ($rgb[1] << 8) | $rgb[2]);
		$ret = str_pad($val, 6, '0', STR_PAD_LEFT);
		return "#" . $ret;
	}

	public function rgb2hex($rgb) {
		return sprintf("#%02X%02X%02X", $rgb[0], $rgb[1], $rgb[2]);
	}

	static public function watermark($file) {
		/**
		$imgType  = strtolower(pathinfo($file, PATHINFO_EXTENSION));
		$new_file = str_replace(".{$imgType}_400x357.{$imgType}", "_water.{$imgType}_400x357.{$imgType}", $file);
		if (file_exists($new_file)) {
			return "exist {$new_file}";
		}
		**/

		$image = new Imagick();
		$image->readImage($file);
		// Open the watermark
		$watermark = new Imagick();
		$waterFile = Common::getConfig("siteConfig", "dataPath") . '/vod_player.png';
		$watermark->readImage($waterFile);
		// Overlay the watermark on the original image
		$ground_w = $image->getimagewidth();
		$ground_h = $image->getimageheight();

		$water_w = $watermark->getimagewidth();
		$water_h = $watermark->getimageheight();

		$posX = ($ground_w - $water_w) / 2;
		$posY = ($ground_h - $water_h) / 2;
		$image->compositeImage($watermark, imagick::COMPOSITE_OVER, $posX, $posY);
		$image->writeImage($file);
		return $file;
	}
}
