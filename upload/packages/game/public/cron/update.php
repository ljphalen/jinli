<?php
include 'common.php';

$dao = Common::getDao("Resource_Dao_IdxGameResourceVersion");
$result = $dao->getAll();
foreach($result as $key=>$value) {
     if ($value['link'] && strpos($value["link"], "games/new") === false) {
         $link = str_replace("/sources", "", $value['link']);
         $parts = pathinfo($link);

         if (strpos($parts["filename"], "_") !== false) {
             $txt = sprintf("%s/new/%s.txt", $parts["dirname"], $parts["filename"]);
             $n_link = sprintf("%s/new/%s.%s",$parts["dirname"], $parts["filename"],$parts["extension"]);
         } else {
             $txt = sprintf("%s/new/%s_%s_.txt", $parts["dirname"], $parts["filename"], $value["version"]);
             $n_link = sprintf("%s/new/%s_%s_.%s",$parts["dirname"], $parts["filename"], $value["version"],$parts["extension"]);
         }

         if(fopen($txt, "r")) {
             if ($context = file_get_contents($txt)) {
                echo "[ok] ".$value['link']."\n";
                $s = explode("\n", $context);
                $dao->update(array('md5_code'=>$s[4], 'link'=>$n_link), $value['id']);
             }
         } else {
             echo "[!ok] ".$value["link"]."\n";
         }
     }
}
?>
