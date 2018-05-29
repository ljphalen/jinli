<?php

if (!defined('BASE_PATH')) exit('Access Denied!');

//6.0.2没有使用该controller
class CatagoryserverController extends Front_BaseController {

    public function indexAction() {
        $res = Theme_Service_FileType::getAllFileType();
        $res_json = json_encode(array("count" => $res[0], "typeinfo" => $res[1]));
        print_r($res_json);
        exit;
    }

    public function listAction() {
        $cid = $this->getInput("catagoryid");

        $file_ids_ixs = Theme_Service_IdxFileType::getIdxFileType($cid);
        $fild_ids = $this->mk_fileids($file_ids_ixs);

        $res = Theme_Service_File::get_filesids_type($fild_ids);
        if ($res) {
            foreach ($res as $val) {
                $themeids[] = $val["id"];
            }
        } else {
            $themeids = '';
        }

        echo json_decode($themeids);
        exit;
    }

    private function mk_fileids($data) {
        if (!is_array($data)) return 0;

        foreach ($data as $val) {
            $result[] = $val['file_id'];
        }

        return $result;
    }

}
