<?php

class Theme_Service_FileAdmin extends Common_Service_Base {

    private static $DaoName = "Theme_Dao_File";
    private static $Num = 12;
    private static $pages = 10;

    private static function _getDao() {
        return Common::getDao(self::$DaoName);
    }

    public static function getWheres() {
        $sql = "theme_file where id>300";
        $res = self::_getDao()->selectedDataDao($sql, "*", true);

        return $res;
    }

    public static function themeFileShows($type, $search, $sedType, $status, $status_opt, $limit = '') {
        $status = $status ? $status : '0';
        if ($type) {
            $res = Theme_Service_IdxFileType::getByTypeId($type);
            $themeids = self::mk_catagoryids($res);
            $ids = implode(",", $themeids);
            $where = " id in($ids) and status $status_opt $status  order by id DESC  limit $limit," . self::$Num;
            $wherecount = " id in($ids) and status $status_opt $status  order by sort DESC";
            $filedscount = "count(*)as count";
        } else {
            $where = "status $status_opt $status order by id DESC  limit $limit," . self::$Num;
            $wherecount = "status $status_opt $status order by sort DESC";
            $filedscount = "count(*)as count";
        }
        if ($sedType) {
            $fileids = Theme_Service_IdxFilesedType::getSedTypeByTypeid($sedType);
            if ($fileids) {
                $sedthemeids = self::mk_catagoryids($fileids);

                if ($themeids) $sedthemeids = array_intersect($themeids, $sedthemeids);
                $sedids = implode(",", $sedthemeids);
                if ($sedids) {
                    $where = " id in($sedids) and status>=$status  order by id DESC  limit $limit," . self::$Num;
                    $wherecount = " id in($sedids) and status>=$status  order by sort DESC";
                    $filedscount = "count(*)as count";
                } else {
                    $where = "  status<0  order by id DESC  limit $limit," . self::$Num;
                    $wherecount = " status<0  order by sort DESC";
                    $filedscount = "count(*)as count";
                }
            }
        }


        if ($search) {
            $where = "title like '%$search%' order by id DESC ";
            $wherecount = "title like '%$search%' ";
            $filedscount = "count(*)as count";
        }

        $count = Theme_Service_File::getByWhere($wherecount, "count(*) as count ")[0];
        $res = Theme_Service_File::getByWhere($where);

        return array($count, $res);
    }

    private static function mk_catagoryids($res) {
        foreach ($res as $v) {
            $tem[] = $v['file_id'];
        }
        return $tem;
    }

}
