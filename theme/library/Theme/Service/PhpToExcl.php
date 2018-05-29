<?php

if (!defined('BASE_PATH')) exit('Access Denied!');

class Theme_Service_PhpToExcl extends Common_Service_base {

    private static $titles = array(
        "message" => array(
            "A1" => "设计师", "B1" => "提现金额", "C1" => "个税(元)",
            "D1" => "实际提现金额", "E1" => "申请时间",
        ),
        "psheji" => array(
            "A1" => "姓名", "B1" => "身份证", "C1" => "开户行支行",
            "D1" => "卡号", "E1" => "销售额", "F1" => "平台分成", "G1" => "个人分成",
            "H1" => "本期提现", "I1" => "前期可提现", "J1" => "剩余可提现",
        ),
        "qsheji" => array(
            "A1" => "公司名称", "B1" => "开户行支行",
            "C1" => "卡号", "D1" => "销售额", "E1" => "平台分成", "F1" => "个人分成",
            "G1" => "本期提现", "H1" => "前期可提现", "I1" => "剩余可提现",
        ),
    );
    private static $muns = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M',
        'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'
    );
    private static $fliedStr_message = array("nick_name", "income", "tax", "final_income", "created_time");

    private static function mkdirs($dirs) {
        if (file_exists($dirs)) {
            chmod($dirs, 0777);
        } else {
            mkdir($dirs, 0777, true);
        }
    }

    public static function OutMessage_excl($type = "message", $outdata = array()) {
        if (!$outdata) return 0;
        switch ($type) {
            case 'message':
                $filedStr = self::$fliedStr_message;
                foreach ($outdata as &$v) {
                    $v["created_time"] = date("Y.m.d:H:i:s", $v['created_time']);
                }
                break;
            case 'psheji':
                $filedStr = '';
                break;
            default :
                $filedStr = false;
        }
        self::optExcl($type, $outdata, $filedStr);
    }

    private static function optExcl($type, $outData, $fliedStr) {
        $objPHPExcel = new PHPExcel();

        foreach (self::$titles[$type] as $k => $v) {
            $objPHPExcel->getActiveSheet()->setCellValue($k, $v);
        }
        $arr = $outData;

        for ($i = 2; $i <= count($arr) + 1; $i++) {
            for ($j = 0; $j < count(self::$titles[$type]); $j++) {
                $objPHPExcel->getActiveSheet()->setCellValue(self::$muns[$j] . $i, $arr[$i - 2][$fliedStr[$j]]);
                //echo self::$muns[$j] . $i . "<br/>";
                //echo $arr[$i - 2][$fliedStr[$j]] . "<br/>";
                // echo "==================" . "<br/>";
            }
        }
        // exit;
        $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);

        //创建一个excel
        self::outWeb();
        $objWriter->save("php://output");
    }

    public static function test() {

        // self::$saveAddr = Common::getConfig('siteConfig', 'attachPath'); //网站附件目录
        $objPHPExcel = new PHPExcel();

        /*  $year = date("Y", time());
          $math = date("m", time());
          $day = date("Y-m-d", time());
          $fileName = time() . rand(1, 9999999);
          $dir = self::$saveAddr . DIRECTORY_SEPARATOR . "Excle" . DIRECTORY_SEPARATOR . $year . DIRECTORY_SEPARATOR . $math . DIRECTORY_SEPARATOR . $day . DIRECTORY_SEPARATOR . $fileName . ".xlsx";

          self::mkdirs($dir);
         */
        //  保存excel—2007格式
        // 创建人
        /* $objPHPExcel->getProperties()->setCreator("abc");
          //  最后修改人
          $objPHPExcel->getProperties()->setLastModifiedBy("abcd");
          //   标题
          $objPHPExcel->getProperties()->setTitle("Office 2007 XLSX Test Document");
          //     题目
          $objPHPExcel->getProperties()->setSubject("Office 2007 XLSX Test Document");
          //    描述
          $objPHPExcel->getProperties()->setDescription("我的");
          //    关键字
          $objPHPExcel->getProperties()->setKeywords("office 2007 openxml php");
          //     种类
          $objPHPExcel->getProperties()->setCategory("Test result file");
          //      ——————————————————————————————————————–
          //     设置当前的sheet
          $objPHPExcel->setActiveSheetIndex(0);
          //      设置sheet的name
          $objPHPExcel->getActiveSheet()->setTitle('Simple');
          //      设置单元格的值

         */
        $objPHPExcel->getActiveSheet()->setCellValue('A1', '季度');
        $objPHPExcel->getActiveSheet()->setCellValue('B1', '名称');
        $objPHPExcel->getActiveSheet()->setCellValue('C1', '数量');


        $arr = self::testData();
        for ($i = 2; $i <= 4; $i++) {
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $i, $arr[$i - 2]['name']);
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $i, $arr[$i - 2]['age']);
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $i, $arr[$i - 2]['addr']);
        }
        //     合并单元格
        // $objPHPExcel->getActiveSheet()->mergeCells('A18:E22');
        //     分离单元格
        // $objPHPExcel->getActiveSheet()->unmergeCells('A28:B28');
        //     保护cell
        //  $objPHPExcel->getActiveSheet()->getProtection()->setSheet(true); // Needs to be set to true in order to enable any worksheet protection!
        //   $objPHPExcel->getActiveSheet()->protectCells('A3:E13', 'PHPExcel');
        //      设置格式
// Set cell number formats

        /* $objPHPExcel->getActiveSheet()->getStyle('E4')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE);
          $objPHPExcel->getActiveSheet()->duplicateStyle($objPHPExcel->getActiveSheet()->getStyle('E4'), 'E5:E13');
          //    设置宽width
          // Set column widths
          $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
          $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(12);
          //     设置font
          $objPHPExcel->getActiveSheet()->getStyle('B1')->getFont()->setName('Candara');
          $objPHPExcel->getActiveSheet()->getStyle('B1')->getFont()->setSize(20);
          $objPHPExcel->getActiveSheet()->getStyle('B1')->getFont()->setBold(true);
          $objPHPExcel->getActiveSheet()->getStyle('B1')->getFont()->setUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);
          $objPHPExcel->getActiveSheet()->getStyle('B1')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_WHITE);
          $objPHPExcel->getActiveSheet()->getStyle('E1')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_WHITE);
          $objPHPExcel->getActiveSheet()->getStyle('D13')->getFont()->setBold(true);
          $objPHPExcel->getActiveSheet()->getStyle('E13')->getFont()->setBold(true);
          //     设置align
          $objPHPExcel->getActiveSheet()->getStyle('D11')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
          $objPHPExcel->getActiveSheet()->getStyle('D12')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
          $objPHPExcel->getActiveSheet()->getStyle('D13')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
          $objPHPExcel->getActiveSheet()->getStyle('A18')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_JUSTIFY);
         */
        $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);

        //创建一个excel
        // $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
        self::outWeb();

        $objWriter->save("php://output");
    }

//输出到浏览器;
    private static function outWeb() {

        $year = date("Y", time());
        $math = date("m", time());
        $day = date("Y-m-d", time());
        $fileName = time() . rand(1, 9999999) . ".xlsx";
        // $dir = self::$saveAddr . DIRECTORY_SEPARATOR . "Excle" . DIRECTORY_SEPARATOR . $year . DIRECTORY_SEPARATOR . $math . DIRECTORY_SEPARATOR . $day . DIRECTORY_SEPARATOR . $fileName . ".xlsx";

        ob_end_clean();
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header('Content-Disposition:inline;filename="' . $fileName . '"');
        header("Content-Transfer-Encoding: binary");
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Pragma: no-cache");
    }

    private static function testData() {
        $array = array(
            array("name" => "123", "cache" => 40, "tax" => 3, "tcache" => 37, 'date' => 32345654),
            array("name" => "56789", "cache" => 10, "tax" => 6, "tcache" => 4, 'date' => 32345654),
            array("name" => "qwd3456", "cache" => 30, "tax" => 3, "tcache" => 27, 'date' => 32345654),
            array("name" => "3456zsx", "cache" => 60, "tax" => 3, "tcache" => 57, 'date' => 32345654),
            array("name" => "3edksi", "cache" => 22, "tax" => 2, "tcache" => 20, 'date' => 32345654),
        );
        return $array;
    }

}
