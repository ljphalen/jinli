<?php
//============================================================+
// File name   : REContractPDF.class.php
// Begin       : 2015-04-05
// Last Update : 2015-04-05
//
// Description : 生成一个续签合同的PDF
//
// Author: TyeeNoprom (https://github.com/noprom)
//
//============================================================+

// 项目框架引入
require_once(dirname(__FILE__).'/tcpdf.php');

class REContractPDF extends TCPDF{
//    private $number;        // 合同编号
//    private $year;          // 年
//    private $month;         // 月份
//    private $day;           // 日
//
//    private $a_contact;     // 甲方联系人
//    private $a_email;       // 甲方联系人邮箱
//    private $b_name;        // 乙方
//    private $b_address;     // 乙方联系地址
//    private $b_contact;     // 乙方联系人
//    private $b_email;       // 乙方联系人邮箱
//
//    private $app_name;      // App名称
//    private $package;       // App包名
//    private $share_ratio;   // 分成比例
//    private $shui_type;     // 税费率类型
//
//    private $account_name;  // 开户名称
//    private $account_bank;  // 开户银行
//    private $account_key;   // 银行账号

//    public function __contract($number,$year,$month,$day,$a_contact,$a_email,$b_name,$b_address,
//                               $b_contact,$b_email,$app_name,$package,$share_ratio,$shui_type,
//                               $account_name,$account_bank,$account_key){
//        $number = $number;
//        $year = $year;
//        $month = $month;
//        $day = $day;
//        $a_contact = $a_contact;
//        $a_email = $a_email;
//        $b_name = $b_name;
//        $b_address = $b_address;
//        $b_contact = $b_contact;
//        $b_email = $b_email;
//        $app_name = $app_name;
//        $package = $package;
//        $share_ratio = $share_ratio;
//        $shui_type = $shui_type;
//        $account_name = $account_name;
//        $account_bank = $account_bank;
//        $account_key = $account_key;
//    }


    public function init($number,$year,$month,$day,$b_company,
                         $endyear,$endmonth,$endday,$name){


        // 设置文档信息
        $title = '金立游戏开发者平台续签合作协议';
        $this->SetCreator(PDF_CREATOR);
        $this->SetAuthor('noprom');
        $this->SetTitle($title);
        $this->SetSubject($title);
        $this->SetKeywords($title);


        // 设置外边距
        $this->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $this->SetHeaderMargin(PDF_MARGIN_HEADER);
        $this->SetFooterMargin(PDF_MARGIN_FOOTER);

        // 设置自动分页
        $this->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        // 设置图片伸展方式
        $this->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // 默认字体
        $defaultFont = 'stsongstdlight';

        // 设置背景颜色
        $this->SetFillColor(255, 255, 255);

        // 新增一页
        $this->AddPage();

        // 设置cell的padding
        $this->setCellPaddings(1, 1, 1, 1);

        $style = array('width' => 0.2, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0));
        $this->Line(144, 41, 170, 41, $style);  // 协议编号
        $this->Line(144, 47, 170, 47, $style);  // 签约时间

        $this->Line(46, 54, 120, 54, $style);    // 甲方
        $this->Line(46, 63, 120, 63, $style);    // 甲方联系地址

        $this->Line(64, 81, 74, 81, $style);    // 年
        $this->Line(76, 81, 86, 81, $style);    // 月
        $this->Line(89, 81, 97, 81, $style);    // 日

        $this->Line(110, 81, 180, 81, $style);    // 合同名称
        $this->Line(88, 86, 113, 86, $style);     // 合同编号

        $this->Line(74, 94, 84, 94, $style);    // 年
        $this->Line(86, 94, 93, 94, $style);    // 月
        $this->Line(96, 94, 103, 94, $style);   // 日

        $this->Line(46, 135, 93, 135, $style);    // 甲方
        $this->Line(105, 135, 180, 135, $style);  // 乙方
        $this->Line(63, 152, 93, 152, $style);     // 甲方签字
        $this->Line(122, 152, 180, 152, $style);     // 乙方签字

        // --------------------------------------------------------- //
        // --------------------------------------------------------- //
        // -------------------------【合同部分】-------------------- //
        // --------------------------------------------------------- //
        // --------------------------------------------------------- //


        // 设置标题
        $this->SetFont($defaultFont, 'B', 14);
        $this->MultiCell(180,5,$title,0,'C',0,1,'','',true);

        // 设置协议编号等
        $this->SetFont($defaultFont, 'B', 11);
        $this->setCellPaddings(1, 1, 25, 1);
        $this->MultiCell(180,5,'协议编号:  '.$number,0,'R',0,1,'','',true);
        $this->MultiCell(180,5,'签约时间:  '.$year.'年'.$month.'月'.$day.'日',0,'R',0,1,'','',true);

        $this->setCellPaddings(20, 1, 1, 1);
        // 设置甲方信息
        $this->SetFont($defaultFont, '', 10);
        $this->MultiCell(180,5,'甲 方：深圳市金立通信设备有限公司',0,'L',0,1,'','',true);
        $this->Ln(3);

        // 设置乙方信息
        $this->SetFont($defaultFont, '', 10);
        $this->MultiCell(180,5,'乙 方：'.$b_company,0,'L',0,1,'','',true);
        $this->Ln(3);

        // 设置正文
        $this->SetFont($defaultFont, '', 10);
        $this->setCellPaddings(15, 1, 1, 1);
        $this->MultiCell(180,200,'

        鉴于甲、乙双方于  '.$year.'  年    '.$month.'    月  '.$day.'   日签订了'.$name.'
        (以下统称“原协议”,合同编号为: '.$number.'),
        现就原协议的有关事宜补充以下合作条款:
        1.原协议终止日期延长至  '.$year.'  年  '.$month.'  月  '.$day.'  日。
        2.如本协议与原协议约定不一致的,以本协议为准。本协议没有约定的,按原协议执行。
        3.本协议自双方签订之日起生效。一式两份，双方各执一份。


        ………………………………………以下无正文………………………………………



        甲方：深圳市金立通信设备有限公司    乙方：'.$b_company.'



        授权代表(签字)：刘立荣                              授权代表(签字)：
        ',0,'L',0,1,'','',true);
        $this->Image(dirname(__FILE__).'/ht.png', 35, 120, 60, 60, 'PNG', '', '', false, 300, '', false, false, 1, false, false, false);
        $this->lastPage();
    }
}



// ---------------------------------------------------------
// ---------------------------------------------------------
// ---------------------------------------------------------
// ----------------------使用方式如下-----------------------
// ---------------------------------------------------------
// ---------------------------------------------------------
// ---------------------------------------------------------
// ---------------------------------------------------------


// 新建一个PDF文档
//$pdf = new REContractPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
//$pdf->init(
//    'GN20150320-01',
//    '2015',
//    '03',
//    '20',
//    '深圳市金立通信设备有限公司',
//    '苏州蜗牛数字科技股份有限公司'
//);
//$pdf->Output('金立游戏开发者平台续签合作协议.pdf', 'I');
