<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class TmpController extends Common_BaseController {

	public function serverAction() {
		var_dump($_SERVER);
		exit();
	}

	public function monthAction() {
		Gionee_Service_Receipt::addReceiptData();
		echo 'ok';
		exit();
	}

	public function testAction() {
		Gionee_Service_Receipt::writeIncomeDataToDb();
		exit('ok');
	}

	public function moneyAction() {
		$ofpayReq    = new Vendor_Ofpay();
		$reminedInfo = $ofpayReq->req_queryuserinfo();
		var_dump($reminedInfo);
		exit;
	}

	public function dayAction() {
		Gionee_Service_VoIPUser::collectPerDayExchangeData();

		Gionee_Service_Log::userCenterDayDataReport();
	}

	public function channelAction() {
		Gionee_Service_Baidu::getSearchUrl('333');
		Common::getCache()->delete('3G_SEARCH_TPL');

	}
}
