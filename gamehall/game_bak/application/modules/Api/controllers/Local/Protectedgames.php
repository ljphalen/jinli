<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class Local_ProtectedgamesController extends Api_BaseController {
    public $perpage = 50;

    /**
     * 获取需要清洗渠道的游戏列表
     */
    public function getListAction() {

        $featureEnabled = Game_Service_Config::getValue('clear_games_from_other_appstore');
        if (!$featureEnabled) {
            $this->localOutput(0,'feature disable', array());
            return;
        }

        $apkArray = $this->getApkArray();

        $games = $this->filterProtectedGames($apkArray);

        if (count($games) == 0) {
            $this->localOutput(0, 'filterProtectedGames return empty', array());
            return;
        }

        $gamesResourceKeyByPackage = $this->getGamesResourceByPackage($games);
        if (count($gamesResourceKeyByPackage) == 0) {
            $this->localOutput(0, 'getGamesResourceByPackage return empty', $gamesResourceKeyByPackage);
            return;
        }

        $games = $this->filterGamesFrom3rdAppStore($games, $gamesResourceKeyByPackage);
        if (count($games) == 0) {
            $this->localOutput(0, 'filterGamesFrom3rdAppStore return empty', array());
            return;
        }

        $games = $this->filterGamesSignature($games, $gamesResourceKeyByPackage);
        if (count($games) == 0) {
            $this->localOutput(0, 'filterGamesSignature return empty', array());
            return;
        }

        $games = $this->filterGamesVersion($games, $gamesResourceKeyByPackage);
        $this->localOutput(0, 'filterGamesVersion', $games);
    }

    private function parseAppArray($inputStr) {
        $appInfoList = explode('|', $inputStr);
        $appArray = array();
        foreach($appInfoList as $infoKey => $infoValue) {
            $infoItems = explode(';', $infoValue);
            $appArray[] = array(
                    'packageName' => $infoItems[0],
                    'packageMD5' => $infoItems[1],
                    'versionCode' => $infoItems[2],
                    'appName' => $infoItems[3],
                    'signatureMD5' => $infoItems[4],
            );
        }

        return $appArray;
    }

    private function getApkArray() {
        $clientVersion = $this->getInput('version');
        $appList = $this->getInput('appList');
        if (!$appList) {
            $this->localOutput(-1, "appList is empty", array());
        }

        $appArray = $this->parseAppArray($appList);

        $result = $this->resetKey($appArray, 'packageName', 'versionCode');

        return $result;
    }

    private function filterProtectedGames($apkArray) {
        $protectedGames = array();
        list($total, $importantGames) = Client_Service_ImportantGame::getAll();

        foreach($importantGames as $key => $value) {
            if (array_key_exists($value['package'], $apkArray)) {
                $protectedGames[$value['package']] = $apkArray[$value['package']];
            }
        }
        return $protectedGames;
    }

    private function getGamesResourceByPackage($packagesKeyByPackage) {
        $gamesResource = array();
        $packages = array_keys($packagesKeyByPackage);
        foreach($packages as $key => $value) {
            $packagesCrc = crc32($value);
            $params = array('packagecrc' =>  $packagesCrc);
            $gamesResource[] = Resource_Service_Games::getGameAllInfo($params);
        }

        $gamesResourceKeyByPackage = $this->resetKey($gamesResource, 'package', 'version_code');
        return $gamesResourceKeyByPackage;
    }

    private function filterGamesFrom3rdAppStore($protectedGamesKeyByPackage, $gamesResourceKeyByPackage) {
        $gamesFrom3rdAppStore = array();
        foreach($protectedGamesKeyByPackage as $key => $value) {
            if ($value['packageMD5'] != $gamesResourceKeyByPackage[$key]['md5_code']) {
                $gamesFrom3rdAppStore[$key] = $protectedGamesKeyByPackage[$key];
            }
        }
        return $gamesFrom3rdAppStore;
    }

    private function filterGamesSignature($gamesFrom3rdAppStore, $gamesResourceKeyByPackage) {
        $gamesWithSameSignature = array();

        foreach($gamesFrom3rdAppStore as $key => $value) {
            if($gamesResourceKeyByPackage[$key]['signature_md5']
                    && strcasecmp($value['signatureMD5'], $gamesResourceKeyByPackage[$key]['signature_md5']) != 0) {
                continue;
            }
            $gamesWithSameSignature[$key] = $value;
        }
        return $gamesWithSameSignature;
    }

    private function filterGamesVersion($gamesToReplaceKeyByPackage, $gamesResourceKeyByPackage) {
        $gamesToReplace = array();
        foreach($gamesToReplaceKeyByPackage as $key => $value) {
            $versionCodeFromClient = $value['versionCode'];
            if ($gamesResourceKeyByPackage[$key]['version_code'] >= $versionCodeFromClient) {
                $gameInfo = array();
                $gameInfo['packageName'] = $gamesResourceKeyByPackage[$key]['package'];
                $gameInfo['appName'] = $gamesResourceKeyByPackage[$key]['name'];
                $gameInfo['versionCode'] = $gamesResourceKeyByPackage[$key]['version_code'];
                $gameInfo['versionName'] = $gamesResourceKeyByPackage[$key]['version'];
                $gameInfo['url'] =  $gamesResourceKeyByPackage[$key]['link'];
                $gameInfo['gameId'] =  $gamesResourceKeyByPackage[$key]['id'];
                $gameInfo['size'] =  $gamesResourceKeyByPackage[$key]['size'];
                $gamesToReplace[] = $gameInfo;
            }
        }
        return $gamesToReplace;
    }

    private function resetKey($source, $keyField, $compareBy) {
        if (!is_array($source)) return array();
        $result = array();
        foreach ($source as $key=>$value) {
            if (!is_array($value)) {
                continue;
            }

            $newKey = trim($value[$keyField]);

            if (!isset($newKey)) {
                continue;
            }

            if (!array_key_exists($newKey, $result)) {
                $result[$newKey] = $value;
            } else if ($value[$compareBy] > $result[$newKey][$compareBy]) {
                $result[$newKey] = $value;
            }
        }
        return $result;
    }
}
