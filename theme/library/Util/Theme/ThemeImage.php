<?php

class Util_Theme_ThemeImage {

    private static $webroot;
    private static $webrootdown;
    private static $downloadroot;

    private static function __inits() {
        self::$webroot = Yaf_Application::app()->getConfig()->fontcroot;
        self::$webrootdown = Yaf_Application::app()->getConfig()->webroot;
        self::$downloadroot = Yaf_Application::app()->getConfig()->downloadroot;
    }

    /**
     * @param type $thememData
     * @param type $image
     * @param type $all_img
     * @return string
     */
    public static function mk_themeImage_data($thememData, $image = array(), $all_img = array()) {
        self::__inits();
        foreach ($thememData as $key => $v) {
            $res[$key]["id"] = $v["id"];
            $res[$key]["title"] = $v["title"];
            $res[$key]["descript"] = stripcslashes($v["descript"]);
            $res[$key]['designer'] = $v['designer'];
            $res[$key]["since"] = $v["since"];
            $res[$key]["likes"] = $v["likes"];
            $res[$key]["package"] = "V" . $v["package_type"];
            $res[$key]["sort"] = $v["sort"];
            $res[$key]["file_size"] = $v["file_size"];
            $res[$key]["last_update_time"] = $v["create_time"];
            $res[$key]["down_count"] = $v["down"];
            $res[$key]["down_path"] = self::$webrootdown . "/detail/down/" . $v["id"] . "_";
            $res[$key]["hit"] = $v["hit"];
            $res[$key]["style"] = $v["style"];
            $res[$key]["file"] = self::$downloadroot . $v["file"];

            $imageid = $v["id"];
            $res [$key]["hit"] = $v["hit"];
            $img_name = array("pre_face.webp", 'pre_lockscreen.webp', 'pre_icon1.webp', 'pre_icon2.webp');
            if ($v['is_faceimg']) {
                $res [$key]["image"] = self::$webroot . '/attachs/theme' . $image[$imageid]["pre_face_small"];
            } else {
                $res [$key]["image"] = self::$webroot . '/attachs/theme' . $image[$imageid]["pre_face_s"];
            }

            if ($v['package_type'] == 3) {
                $img_name = array(
                    "pre_face.webp",
                    'pre_lockscreen.webp',
                    'pre_icon1.webp',
                    'pre_icon2.webp',
                    'pre_icon3.webp',
                    'pre_icon4.webp',
                    'pre_icon5.webp',
                    'pre_icon6.webp',
                    'pre_icon7.webp',
                    'pre_icon8.webp',
                    'pre_icon9.webp',
                    'pre_icon10.webp',
                );
            }
            $i = 0;
            foreach ($all_img as $ks => $vals) {
                if ($imageid == $vals["file_id"]) {
                    $str_tmp = strrpos($vals['img'], "/");
                    $tmp_url = substr($vals['img'], 0, $str_tmp + 1);
                    $tmp_name = substr($vals["img"], $str_tmp + 1);
// $res[$key]["list_imgs"]["imgs"][] = $this->webroot . "/attachs/theme" . $vals["img"];
                    if ($tmp_name != "pre_face_small.webp") {
                        $res[$key]["list_imgs"]["imgs"][$i] = self::$webroot . "/attachs/theme" . $tmp_url . $img_name[$i];
                        $res[$key]["list_imgs"]["full_imgs"][$i] = self::$webroot . "/attachs/theme" . $tmp_url . "full-scale/" . $img_name[$i];
                        $i++;
                    }
                }
            }
        }

        return $res;
    }

}
