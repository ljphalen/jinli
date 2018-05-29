<?php
$num = $_POST["num"];
$t = "{images :[";
for($i=0;$i<($num-1);$i++)
{
	//echo $num;
	$src=rand(1,84);
	$src=(string)$src;
	if(strlen($src)  == 1) {$src = "00" . $src;}
	else if(strlen($src) == 2) {$src = "0" . $src;}
	$img = imagecreatefromjpeg("img/P_".$src.".jpg");
	$arr = imagesy($img);
	$t = $t."{src : $src,height:$arr},";
}
	$src=rand(1,84);
	$src=(string)$src;
	if(strlen($src)  == 1) {$src = "00" . $src;}
	else if(strlen($src) == 2) {$src = "0" . $src;}
	$arr = getimagesize("img/P_"."023".".jpg");
$t=$t."{src : $src,height:$arr[1]}]}";
echo $t;
//echo "{images : [{src : 12312}, {src : 12312},{src : 12312}]}";
?>