<?php

class HeaderHelper {
	function nickname() {
// 		import("@.Client.AccountsClient");
		loadClient(array('Accounts'));
		$uid = AccountsClient::checkAuth ();
		if($uid){
			$accInfo = AccountsClient::getInfoById($uid);
			$nickname = $accInfo['nickname'];
			return $nickname;
		}
		return 0;
	}
}

?>