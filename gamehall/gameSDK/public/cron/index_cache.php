<?php 
include 'common.php';

list($total, $ads) = Client_Service_Ad::getCanUseAds(0, 100, array('ad_ptype'=>1));
if (!$total) exit("nothing todo .\n");

foreach($ads as $key=>$value) {
	Client_Service_IndexAd::cookAd($value, "", 0, false);
	Client_Service_IndexAdI::cookAd($value, "", 0, false);
	Local_Service_IndexAd::cookClientAd($value,"ad4", 0, false);
	echo "ad_id, ", $value['id'], " done.\n";
}

?>

