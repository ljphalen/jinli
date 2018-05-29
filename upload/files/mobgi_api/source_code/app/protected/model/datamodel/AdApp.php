<?php
Doo::loadModel('datamodel/base/AdAppBase');

class AdApp extends AdAppBase{
    public function getPkgByAppkey($appkeyArr){
        if(empty($appkeyArr)){
            return array();
        }
        if(!is_array($appkeyArr)){
            $appkeyArr = array($appkeyArr);
        }
        $sql = "select packagename from ad_app where appkey in ('".implode("','", $appkeyArr)."')";
        $pkgRst =Doo::db()->query($sql)->fetchAll();
        $pkgArr = array();
        if(!empty($pkgRst)){
            foreach ($pkgRst as $pkgItem){
                if(!empty($pkgItem['packagename']) && !in_array($pkgItem['packagename'], $pkgArr)){
                    $pkgArr[] = $pkgItem['packagename'];
                }
            }
        }
        return $pkgArr;
    }
}