<?php
include 'common.php';

ini_set('memory_limit', '1G');
$search = array('status' => Widget_Service_Source::STATUS_DOWN_OK);
$total  = Widget_Service_Source::getTotal($search);
if (!$total) {
	exit("nothing get.\n");
}
$ret = Widget_Service_Source::getList(0, 10, $search, array('id' => 'DESC'));


$attachPath  = Widget_Service_Adapter::getSavePath();
$tmpColorArr = explode(',', Widget_Service_Config::getValue('widget_color'));
$totalColor  = count($tmpColorArr);

$list = array();
foreach ($ret as $key => $value) {
	$list[$value['id']] = $value;
}
$ids = array_keys($list);
sort($ids);

//error_log(json_encode($tmpColorArr) . "\n", 3, '/tmp/fanfan_color');

foreach ($ids as $id) {
	$value = $list[$id];

	$oldFile = $attachPath . "/" . $value['img'];
	list($name, $ext) = explode(".", $value['img']);
	$newName = $name . '_p.' . $ext;
	$newFile = $attachPath . "/" . $newName;

	$keyName = 'WIDGET_COLOR_NUM';
	if (file_exists($oldFile)) {
		$totalNum = Common::getCache()->get($keyName);
		$n        = $totalNum % $totalColor;
		//get img color
		$color = Util_Color::getImgColor($oldFile);
		if (!empty($tmpColorArr[$n])) {
			$color = $tmpColorArr[$n];
		}

		//create webp
		$src400x357 = $oldFile . "_400x357." . $ext;
		$new400x357 = $newFile . "_400x357." . $ext;
		if (file_exists($src400x357)) {
			image2webp($src400x357, $new400x357);

			$up = array(
				'color'  => $color,
				'img'    => $newName,
				'status' => Widget_Service_Source::STATUS_OK
			);

			Widget_Service_Source::update($up, $value['id']);

			Common::getCache()->increment($keyName);
			echo "out_iid :{$id}=> " . $value["out_iid"] . " published .\n";
		} else {
			echo "out_iid :{$id}=> " . $value["out_iid"] . " unpublished .\n";
		}

	}
}