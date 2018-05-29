<?php
//============================================================+
// File name   : ContractPDF.class.php
// Begin       : 2015-04-05
// Last Update : 2015-04-05
//
// Description : 生成主合同的PDF
//
// Author: TyeeNoprom (https://github.com/noprom)
//
//============================================================+

// 项目框架引入
require_once(dirname(__FILE__).'/tcpdf.php');

class REContractPDF extends TCPDF{

    public $style = array('width' => 0.2, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0));

    public $number = '';

    //自定义页头
    public function Header() {
        $img_logo = K_PATH_IMAGES.'logo.jpg';
        $this->Image($img_logo, 35, 15, 30, 10, 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
        $this->SetFont('simhei', '', 10);
        $this->Cell(115, 18, '协议编号: '.$this->number, 0, false, 'R', 0, '', 0, false, 'M', 'B');
        $this->Line(33, 30, 180, 30, $this->style);
    }

    // 自定义页脚
    public function Footer() {
        $this->SetY(-25);
        $this->SetFont('simhei', 'I', 8);
        $this->Cell(0, 10,$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }

    // 设置背景
    public function setBkg(){
        $bMargin = $this->getBreakMargin();
        $auto_page_break = $this->getAutoPageBreak();
        $this->SetAutoPageBreak(false, 0);
        $img_file = K_PATH_IMAGES.'bg.png';
        $this->Image($img_file, 0, 0, 210, 297, '', '', '', false, 300, '', false, false, 0);
        $this->SetAutoPageBreak($auto_page_break, $bMargin);
        $this->setPageMark();
    }

    public function init($number,$year,$month,$day,$a_company,$b_company,
                         $contract_name,$xu_year,$xu_month,$xu_day,$a_contact,$b_contact){

        $this->number = $number;

        define ('DEFAULT_PAGE_WIDTH', 145);              // 页面最大宽度
        define ('DEFAULT_TITLE_SIZE', 13);               // 段落标题大小
        define ('DEFAULT_BODY_SIZE', 10);                // 正文字体大小
        define ('DEFAULT_LINE_HEIGHT', 5);               // 正文字体大小
        define ('MARGIN_LEFT', 33);                      // 左边距
        define ('MARGIN_RIGHT', 33);                     // 右边距
        define ('SHOW_BORDER', 0);                       // 是否显示边框
        define ('DEFAULT_FONT', 'simhei');               // 默认字体
        define ('HEADER_MARGIN_TOP', 50);                // 头部外边距

        // 默认字体
        $this->SetFont(DEFAULT_FONT, 'B', 14);

        // 设置文档信息
        $title = '金立游戏开发者平台续签合作协议';
        $this->SetCreator(PDF_CREATOR);
        $this->SetAuthor('noprom');
        $this->SetTitle($title);
        $this->SetSubject($title);
        $this->SetKeywords($title);


        // 设置外边距
        $this->SetMargins(MARGIN_LEFT, 40, PDF_MARGIN_RIGHT);
        $this->SetHeaderMargin(HEADER_MARGIN_TOP);
        $this->SetFooterMargin(70);

        // 设置自动分页
        $this->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        // 设置图片伸展方式
        $this->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // 设置背景颜色
        $this->SetFillColor(255, 255, 255);

        // 新增一页
        $this->AddPage();

        // --------------------------------------------------------- //
        // --------------------------------------------------------- //
        // -----------------------【绘制下划线】-------------------- //
        // --------------------------------------------------------- //
        // --------------------------------------------------------- //

        // 设置cell的padding
        $this->setCellPaddings(1, 1, 1, 1);
        $style = array('width' => 0.2, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0));
        $this->Line(138, 62, 172, 62, $style);  // 协议编号
        $this->Line(138, 69, 146, 69, $style);  // 年
        $this->Line(152, 69, 160, 69, $style);  // 月
        $this->Line(163, 69, 172, 69, $style);  // 日

        $this->Line(45, 84, 140, 84, $style);    // 甲方
        $this->Line(45, 90, 140, 90, $style);    // 乙方

        $this->Line(63, 105, 72, 105, $style);    // 年
        $this->Line(76, 105, 83, 105, $style);    // 月
        $this->Line(87, 105, 94, 105, $style);    // 日
        $this->Line(110, 105, 180, 105, $style);    // 游戏名称以及包名
        $this->Line(93, 111, 120, 111, $style);    // 编号

        $this->Line(73, 123, 83, 123, $style);  // 年
        $this->Line(87, 123, 94, 123, $style);  // 月
        $this->Line(98, 123, 105, 123, $style);  // 日

        // --------------------------------------------------------- //
        // --------------------------------------------------------- //
        // -------------------------【合同部分】-------------------- //
        // --------------------------------------------------------- //
        // --------------------------------------------------------- //
        $this->setBkg();

        // 设置标题
        $this->SetFont(DEFAULT_FONT, 'B', 14);
        $this->MultiCell(DEFAULT_PAGE_WIDTH,DEFAULT_LINE_HEIGHT,$title,SHOW_BORDER,'C',0,1,'','',true);
        $this->Ln(8);

        // 设置协议编号等
        $this->SetFont(DEFAULT_FONT, 'B', DEFAULT_BODY_SIZE+1);
        $this->setCellPaddings(1, 1, 15, 1);
        $this->MultiCell(DEFAULT_PAGE_WIDTH,DEFAULT_LINE_HEIGHT,'协议编号: '.$number,SHOW_BORDER,'R',0,1,'','',true);
        $this->setCellPaddings(1, 1, 1, 1);
        $this->MultiCell(DEFAULT_PAGE_WIDTH,DEFAULT_LINE_HEIGHT,'签约时间: '.$year.' 年 '.$month.' 月 '.$day.' 日 ',SHOW_BORDER,'R',0,1,'','',true);
        $this->Ln(8);

        // 设置甲方乙方信息
        $this->SetFont(DEFAULT_FONT, '', DEFAULT_BODY_SIZE);
        $this->MultiCell(DEFAULT_PAGE_WIDTH,DEFAULT_LINE_HEIGHT,'甲 方：深圳市金立通信设备有限公司',SHOW_BORDER,'L',0,1,'','',true);
        $this->MultiCell(DEFAULT_PAGE_WIDTH,DEFAULT_LINE_HEIGHT,'乙 方：'.$b_company,SHOW_BORDER,'L',0,1,'','',true);
        $this->Ln(8);


        // 设置正文
        $this->SetFont(DEFAULT_FONT, '', DEFAULT_BODY_SIZE);
        $this->MultiCell(DEFAULT_PAGE_WIDTH,DEFAULT_LINE_HEIGHT,'鉴于甲、乙双方于 '.$year.' 年 '.$month.' 月 '.$day.' 日签订了 '.$contract_name,SHOW_BORDER,'L',0,1,'','',true);
        $this->MultiCell(DEFAULT_PAGE_WIDTH,DEFAULT_LINE_HEIGHT,'（以下统称“原协议”，合同编号为：'.$number.'）,',SHOW_BORDER,'L',0,1,'','',true);
        $this->MultiCell(DEFAULT_PAGE_WIDTH,DEFAULT_LINE_HEIGHT,'现就原协议的有关事宜补充以下合作条款：',SHOW_BORDER,'L',0,1,'','',true);
        $this->MultiCell(DEFAULT_PAGE_WIDTH,DEFAULT_LINE_HEIGHT,'1.原协议终止日期延长至 '.$xu_year.' 年 '.$xu_day.' 月 '.$xu_day.' 日。',SHOW_BORDER,'L',0,1,'','',true);
        $this->MultiCell(DEFAULT_PAGE_WIDTH,DEFAULT_LINE_HEIGHT,'2.如本协议与原协议约定不一致的，以本协议为准。本协议没有约定的，按原协议执行。',SHOW_BORDER,'L',0,1,'','',true);
        $this->MultiCell(DEFAULT_PAGE_WIDTH,DEFAULT_LINE_HEIGHT,'3.本协议自双方签订之日起生效。一式两份，双方各执一份。',SHOW_BORDER,'L',0,1,'','',true);
        $this->MultiCell(DEFAULT_PAGE_WIDTH,DEFAULT_LINE_HEIGHT,'……………………以下无正文……………………',SHOW_BORDER,'L',0,1,'','',true);
        $this->Ln(20);

        $this->SetFont(DEFAULT_FONT, 'B', 11);
        $this->setCellPaddings(1, 1, 1, 1);
        $this->Cell(DEFAULT_PAGE_WIDTH/2,5,'甲方 '.$a_company,0,0,'L',0,'',0,0,'C','C');
        $this->Cell(DEFAULT_PAGE_WIDTH/2,5,'乙方 '.$b_company,0,1,'L',0,'',0,0,'C','C');
        $this->setCellPaddings(1, 3, 1, 1);
        $this->Cell(DEFAULT_PAGE_WIDTH/2,25,'授权代表（签字）：刘立荣',0,0,'L',0,'',0,0,'T','T');
        $this->Cell(DEFAULT_PAGE_WIDTH/2,25,'授权代表（签字）：'.$b_contact,0,1,'L',0,'',0,0,'T','T');
        $this->Image(dirname(__FILE__).'/ht.png', 50, 150, 50, 50, 'PNG', '', '', false, 300, '', false, false, false, false, false, false);
        $this->lastPage();
    }

}



// ---------------------------------------------------------
// 新建一个PDF文档
//$pdf = new REContractPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
//$pdf->init(
//    'GN20150320-01',
//    '2015',
//    '03',
//    '20',
//    '深圳市金立通信设备有限公司',
//    '苏州蜗牛数字科技股份有限公司',
//    '《开源中国》电子合同',
//    '2016',
//    '03',
//    '20',
//    '刘立荣',
//    '李小龙'
//);
////Close and output PDF document
//$pdf->Output('金立合同.pdf', 'I');
