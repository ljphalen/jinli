<?php

if (!defined('BASE_PATH')) exit('Access Denied!');

class DetailController extends Front_BaseController {

    public $actions = array(
        'detailUrl' => '/detail/index',
        'downUrl' => '/detail/down',
    );

    public function indexAction() {
        $id = intval($this->getInput('id'));
        $tid = intval($this->getInput('tid'));
        $pam = $this->getInput('pam');
        $pre = $this->getInput('pre');
        $next = $this->getInput('next');
        $pt = $this->getInput('pt');
        $subject_id = intval($this->getInput('sid'));
        $source = $this->getInput('source');
        $orderby = $this->getInput('orderby');
        if (!$orderby) $orderby = 'sort';


        $info = Theme_Service_File::getFile($id);
        $webroot = Yaf_Application::app()->getConfig()->webroot;
        $pt = Util_Cookie::get('THEME_PT', true);


        if (!$info || $info['id'] == 0 || $info['status'] != 4) {
            $this->redirect($webroot . '?pt=' . $pt);
        }

        //参数
        $params = json_decode(Util_Cookie::get('THEME_PARAMS', true), true);
        if (isset($params['package'])) {
            $package_type = $params['package'];
        } else {
            $package_type = $this->getInput('package');
        }
        if ($package_type == "v1" || !$package_type) {
            $para['package_type'] = 1;
            $para['resulution'] = $params['resulution'];
        }
        if ($package_type == "v2") {
            $para['package_type'] = 2;
        }

        $para['status'] = 4;


        // print_r($para);


        if ($tid) {
            $type_files = Theme_Service_IdxFileType::getByTypeId($tid);
            $type_files = Common::resetKey($type_files, 'file_id');
            $type_file_ids = array_keys($type_files);
        }

        //rom 版本号
        $rom = Theme_Service_Rom::getRomByName($params['rom_version']);
        $file_rom = Theme_Service_IdxFileRom::getByRomId($rom['id']);
        $rom_file_ids = array();
        foreach ($file_rom as $key => $value) {
            $rom_file_ids[] = $value['file_id'];
        }

        //比较
        /*  $not_in_ids = array();
          $in_ids = array();
          if ($type_file_ids && $rom_file_ids) {
          $in_ids = array_diff($type_file_ids , $rom_file_ids);
          }elseif ($rom_file_ids) {
          $not_in_ids = $rom_file_ids;
          }elseif ($type_file_ids) {
          $in_ids = $type_file_ids;
          }
         */


        if ($subject_id) {
            //从专题进入列表页,只显示专题下的主题;
            $subject = Theme_Service_Subject::getSubject(intval($subject_id));
            $subject_files = Theme_Service_SubjectFile::getBySubjectId($subject_id, array());
            foreach ($subject_files as $key => $value) {
                $s_file_ids[] = $value['file_id'];
            }
            //取专题下的主题
            $files = Theme_Service_File::getCanuseFiles(0, 100000, $s_file_ids, '', $para);


            if ($files[1]) {
                foreach ($files[1] as $values) {
                    $file_ids[] = $values['id'];
                }
            }
        } else {
            //取所有主题;
            if ($orderby == "id") $orderby = "down";
            $file_ids = Theme_Service_File::get_allFiles_id($para, "DESC", $orderby);
            if ($tid) {
                $file_ids = $this->mk_tid_indis($tid, $file_ids);
            }
        }


        // echo "<br/>";
        // print_r($file_ids);
        // echo "<hr/>";
        //上一个
        $haspre = 1;
        $pre_file = array();

        $postions = array_search($id, $file_ids);
        if ($postions === 0) {
            $haspre = 0;
            $pre_id = -1;
        } else {
            $pre_id = $file_ids[$postions - 1];
        }



        if (in_array($pre_id, $file_ids)) {
            $pre_file = Theme_Service_File::getFile($pre_id);
        }

        if ($pre_file && $source) {
            $pre_file['file'] = $pre_file['file'] . '&source=' . $source;
        }

        if (!$pre_file) {
            $pre_id = -1;
            $haspre = 0;
            $next = 1;
        }

        //下一个
        $hasnext = 1;
        if (count($file_ids) == $postions + 1) {
            $hasnext = 0;
            $next_id = -1;
        } else {
            $next_id = $file_ids[$postions + 1];
        }

        // echo "p_id=" . $pre_id . "<br/>";
        // echo "n_id=" . $next_id . '<br/>';
        $next_file = Theme_Service_File::getFile($next_id);

        if (!$next_file) {
            $hasnext = 0;
            $next = 0;
            $next_id = -1;
            // $pre_id = 1;
        }
        if ($next_file && $source) $next_file['file'] = $next_file['file'] . '&source=' . $source;
        if ($pam && in_array($pam, array('next', 'pre'))) {

            if ($pam == 'next') {

                if ($next_id < 0) $this->redirect($webroot . '?pt=' . $pt);
                $fileid = $next_id;
                if ((count($file_ids) - 2 ) == $postions) {
                    $hasnext = 0;
                }
                $haspre = 1;
            } else if ($pam == 'pre') {
                if ($pre_id < 0) {
                    $this->redirect($webroot . '?pt=' . $pt);
                }
                $fileid = $pre_id;

                if ($postions <= 1) {
                    $haspre = 0;
                }
                $hasnext = 1;
            } else {
                $this->redirect($webroot . '?pt=' . $pt);
            }
            $file = Theme_Service_File::getFile($fileid);
            $file['file'] = $source ? $file['file'] . '&source=' . $source : $file['file'];

            $downloadroot = Yaf_Application::app()->getConfig()->downloadroot;
            $url = sprintf('%s/detail?id=%d&pre=%d&next=%d&idTheme=%d&titleTheme=%s&dlurl=%s&sinceID=%d&update_time=%d&orderby=%s&tid=%d&source=%s&sid=%d&pt=%s', $webroot, $fileid, $haspre, $hasnext, $fileid, Util_String::utf82unicode($file['title']), $webroot . $this->actions['downUrl'] . '/' . $file['id'] . '_' . $pt, $file['since'], $file['packge_time'], $orderby, $tid, $source, $subject_id, $pt);

            $this->redirect($url);
        }

        //pic

        $file_imgs = Theme_Service_FileImg::getByFileId($id);


        $imgs = array();
        $positoin = array('pre_face' => 0, 'pre_lockscreen' => 1, 'pre_icon1' => 2, 'pre_icon2' => 3);
        foreach ($file_imgs as $key => $value) {
            $img_info = pathinfo($value['img']);
            $filename = $img_info['filename'];
            if (in_array($filename, array_flip($positoin))) $imgs[$positoin[$filename]] = array('img' => $value['img'], 'name' => $filename);
        }

        //预览大图的分辨率
        $res = '';
        $siteAttachPath = Common::getConfig('siteConfig', 'attachPath'); //  ../attachs/theme/attachs/
        $siteAttachPath = rtrim($siteAttachPath, '/'); //  ../attachs/theme/attachs
        $res_full_scale = 'full-scale';
        if ((count($imgs) > 0 ) && array_key_exists(0, $imgs)) {// 确认找到了图片
            $img_dir = pathinfo($imgs[0]['img'], PATHINFO_DIRNAME); //  /file/201305/yb5u161912
            if (file_exists($siteAttachPath . $img_dir . '/' . $res_full_scale))//检查full-scale目录是否存在
                $res = '&res=' . $res_full_scale; // &res=full-scale
        }

        //更新点击量
        Theme_Service_File::updateFile(array('hit' => $info['hit'] + 1), $info['id']);


        //上一页
        $pre_url = $pre_file ? sprintf('%s/detail?id=%d&pre=%d&next=%d&pam=pre', $webroot, $info['id'], 1, $next_file ? 1 : 0) : '';
        $next_url = $next_file ? sprintf('%s/detail?id=%d&pre=%d&next=%d&pam=next', $webroot, $info['id'], $pre_file ? 1 : 0, 1) : '';


        // print_r($pre_url);
        //  print_r($next_url);
        //print_r($imgs);

        $this->assign('info', $info);
        $this->assign('preUrl', $pre_url);
        $this->assign('nextUrl', $next_url);
        $this->assign('files', $files);
        $this->assign('pre_file', $pre_file);
        $this->assign('next_file', $next_file);
        $this->assign('imgs', $imgs);
        $this->assign('total', count($files));
        $this->assign('backUrl', $this->getInput('refer') ? $this->getInput('refer') : $webroot);
        $this->assign('imagesUrl', $webroot . '/detail/image?themeid=' . $id . $res);
        $this->assign('themeid', $id);
    }

    public function downAction() {
        $id = $this->getInput('id');
        $arr_id = explode('_', $id);
        $file_id = $arr_id[0];
        if ($file_id) $info = Theme_Service_File::getFile($file_id);
        if ($file_id && $info && $info['status'] == 4) {
            //更新点击量
            Theme_Service_File::updateFile(array('down' => $info['down'] + 1), $info['id']);
            $downloadroot = Yaf_Application::app()->getConfig()->downloadroot;
            $this->redirect($downloadroot . $info['file']);
            exit;
        }
        exit;
    }

    //与客户端接口，提供大图地址列表
    //URL:
    //    detail/image?theme_id=126&res=full-scale
    //参数：
    //    theme_id：主题ID
    //    res：分辨率，可选的有full-scale, xxhdpi, xhdpi, qhdpi, hdpi, 对应服务器上保存大图的目录，为空时返回缩略图
    //返回：JSON，内容包括：
    //    themeid：主题ID
    //    imageinfo：数组，数组中每一项表示一张图片，包括两个字段：id（图片在数组中的索引），url（图片的地址）
    public function imageAction() {
        //图片路径
        $webroot = Yaf_Application::app()->getConfig()->webroot;
        $attachPath = $webroot . '/attachs/theme/';
        //参数
        $theme_id = $this->getInput('themeid');
        $res = $this->getInput('res');
        if ($res) $res = '/' . $res;
        //返回信息
        $image_info = array();
        //从数据库中查询图片
        $theme_imgs = Theme_Service_FileImg::getByFileId($theme_id);
        /* -----将查询到的结果按顺序存入返回信息数组-----
          //本段实现方式与indexAction一致，问题在于如果四张预定义图片中缺了一张，那么编码后的JSON格式将与预期不一致
          $image_positions = array('pre_face'=>0,'pre_lockscreen'=>1,'pre_icon1'=>2, 'pre_icon2'=>3);//图片的顺序
          foreach($theme_imgs as $key=>$value) {
          $image_path = pathinfo($value['img']);//      $value['img'] : /file/201305/yb5u161912/pre_face.png
          $image_dir = $image_path['dirname'];//           $image_dir : /file/201305/yb5u161912
          $image_filename = $image_path['basename'];//$image_filename : pre_face.png
          $image_name = $image_path['filename'];//        $image_name : pre_face
          if(in_array($image_name, array_flip($image_positions)))
          {
          $image_index = $image_positions[$image_name];
          $image_info[$image_index]  = array('id' => $image_index, 'url' => $attachPath.$image_dir.$res.'/'.$image_filename);
          }
          }
          -----END----- */
        //将查询结果解析成以图片名称为key的数组
        $image_array = array();
        foreach ($theme_imgs as $value) {
            $image_path = pathinfo($value['img']); //将图片的完整路径拆分成多个部分
            $image_name = $image_path['filename']; //取出图片名称（主文件名）
            $image_array[$image_name] = $image_path; //$image_array的key为图片名称，value为拆分后的图片路径
        }
        //提取预先定义好的图片放入返回数组
        $image_positions = array('pre_face', 'pre_lockscreen', 'pre_icon1', 'pre_icon2');
        $image_index = 0;
        foreach ($image_positions as $value) {
            if (array_key_exists($value, $image_array)) {//数据库中有此图片
                $image_info[$image_index] = array('id' => $image_index, 'url' => $attachPath . $image_array[$value]['dirname'] . $res . '/' . $image_array[$value]['basename']);
                $image_index ++;
            }
        }
        //返回JSON
        echo json_encode(array('themeid' => $theme_id, 'imageinfo' => $image_info));
        exit;
    }

    private function mk_tid_indis($tid, $ids) {
        $type_files = Theme_Service_IdxFileType::getByTypeId($tid);
        $type_files = Common::resetKey($type_files, 'file_id');
        $type_file_ids = array_keys($type_files);
        $res = array_intersect($type_file_ids, $ids);
        return $res;
    }

}
