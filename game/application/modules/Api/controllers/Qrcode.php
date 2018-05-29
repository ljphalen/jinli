<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class QrcodeController extends Api_BaseController {
	
	public function indexAction() {
		Header("Content-type: image/png");
		$p = $this->getInput('p');
		$url = urldecode($p);
		$img = Common::generateQRfromLocal($url,false);
		echo $img;
	} 
	
	public function monindexAction() {
		Header("Content-type: image/png");
		$p = $this->getInput('p');
		$url = urldecode($p);
		$img = Common::generateQRfromLocal($url,false);
		echo $img;
	}
	
}