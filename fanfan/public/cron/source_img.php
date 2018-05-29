<?php
include 'common.php';

ini_set('memory_limit', '1G');

$total = Widget_Service_Source::getTotal($search);
if (!$total) exit("nothing get.\n");


$search = array('status' => Widget_Service_Source::STATUS_DOWN_IMG);
$ret    = Widget_Service_Source::getList(0, 30, $search, array('id' => 'DESC'));

$path = Widget_Service_Adapter::getSavePath();

$imgs = array();
foreach ($ret as $key => $value) {
	$imgs[$value['id']] = $value['img'];
}

$fileList = Widget_Service_Adapter::downImages($imgs, "file", $path);

foreach ($ret as $key => $value) {
	$id     = $value['id'];
	$upData = array(
		'status' => Widget_Service_Source::STATUS_DORP
	);
	$out    = "id " . $id . " discard.\n";

	$fName = isset($fileList[$id]) ? $fileList[$id] : '';
	//error_log("{$id}:{$fName}\n", 3, '/tmp/fanfan_fname');
	if (!empty($fName) && !_isFilterIllPic($fName)) {
		$fullName      = $path . '/' . $fName;
		$upData['img'] = $fName;

		//$imgType = strtolower(pathinfo($fullName, PATHINFO_EXTENSION));
		//$bakName = $path . '/' . $fName . '_scale.' . $imgType;
		//$scaleFile = Util_Image::scale($fullName, $bakName, 400, 357);
		$scaleFile = '';
		$imgFile   = !empty($scaleFile) ? $scaleFile : $fullName;

		//如果是视频 采用原来缩放方式
		$fix       = in_array($value['cp_id'], array(Widget_Service_Cp::CP_SOHU_VOD, Widget_Service_Cp::CP_SOHU_VOD_L)) ? 0 : 1;
		$thumbFile = Widget_Service_Adapter::mkThumbs($imgFile, 400, 357, $fName, $value['url_id'], $fix);
		if (file_exists($thumbFile)) {
			$im                 = new Util_W3Img($thumbFile);
			$rgb                = $im->getRGB();
			$upData['w3_color'] = $im->hexColor($rgb);
			//Widget_Service_Adapter::mkThumbs($thumbFile, 656, 578, $fName);

			$upData['status'] = Widget_Service_Source::STATUS_DOWN_OK;
			$out              = "id " . $id . " updated .\n";

			if (in_array($value['cp_id'], array(Widget_Service_Cp::CP_SOHU_VOD, Widget_Service_Cp::CP_SOHU_VOD_L))) {
				Util_W3Img::watermark($thumbFile);
			}
		}

		//error_log($fName . ':' . $out, 3, '/tmp/fanfan_img_' . date('Ymd'));
	}

	Widget_Service_Source::update($upData, $id);
	echo $out;

}

/**
 * 过滤非法图标
 */
function _isFilterIllPic($fName) {
	//凤凰网默认图标
	$old = 'fbf5997fabd8f3785259e4e29b4a4f94';
	if (stristr($fName, $old)) {
		return true;
	}
	return false;
}