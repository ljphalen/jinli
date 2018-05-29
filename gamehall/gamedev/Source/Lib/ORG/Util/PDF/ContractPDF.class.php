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

class ContractPDF extends TCPDF{

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

    public function init($number,$year,$month,$day,$a_contact,$a_email,$b_name,$b_address,
                         $b_contact,$b_email,$app_name,$package,$share_ratio,$shui_type,
                         $account_name,$account_bank,$account_key){

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
        $title = '金立游戏开发者平台合作协议';
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
        $this->Line(50, 90, 140, 90, $style);    // 甲方联系地址
        $this->Line(47, 97, 140, 97, $style);    // 甲方联系人
        $this->Line(45, 103, 140, 103, $style);    // 甲方联系人邮箱

        $this->Line(45, 117, 140, 117, $style);    // 乙方
        $this->Line(50, 124, 140, 124, $style);      // 乙方联系地址
        $this->Line(47, 130, 140, 130, $style);      // 乙方联系人
        $this->Line(45, 137, 140, 137, $style);      // 乙方联系人邮箱

        $this->Line(33, 172, 180, 172, $style);    // 游戏名称以及包名


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
        $this->MultiCell(DEFAULT_PAGE_WIDTH,DEFAULT_LINE_HEIGHT,'协议编号: '.$len.$number,SHOW_BORDER,'R',0,1,'','',true);
        $this->setCellPaddings(1, 1, 1, 1);
        $this->MultiCell(DEFAULT_PAGE_WIDTH,DEFAULT_LINE_HEIGHT,'签约时间: '.$year.' 年 '.$month.' 月 '.$day.' 日 ',SHOW_BORDER,'R',0,1,'','',true);
        $this->Ln(8);

        // 设置甲方信息
        $this->SetFont(DEFAULT_FONT, '', DEFAULT_BODY_SIZE);
        $this->MultiCell(DEFAULT_PAGE_WIDTH,DEFAULT_LINE_HEIGHT,'甲 方：深圳市金立通信设备有限公司',SHOW_BORDER,'L',0,1,'','',true);
        $this->MultiCell(DEFAULT_PAGE_WIDTH,DEFAULT_LINE_HEIGHT,'联系地址：深圳市福田区深南大道 7008 号阳光高尔夫大厦 15 楼',SHOW_BORDER,'L',0,1,'','',true);
        $this->MultiCell(DEFAULT_PAGE_WIDTH,DEFAULT_LINE_HEIGHT,'联系人：'.$a_contact,SHOW_BORDER,'L',0,1,'','',true);
        $this->MultiCell(DEFAULT_PAGE_WIDTH,DEFAULT_LINE_HEIGHT,'邮箱： '.$a_email,SHOW_BORDER,'L',0,1,'','',true);
        $this->Ln(8);

        // 设置乙方信息
        $this->SetFont(DEFAULT_FONT, '', DEFAULT_BODY_SIZE);
        $this->MultiCell(DEFAULT_PAGE_WIDTH,DEFAULT_LINE_HEIGHT,'乙 方：'.$b_name,SHOW_BORDER,'L',0,1,'','',true);
        $this->MultiCell(DEFAULT_PAGE_WIDTH,DEFAULT_LINE_HEIGHT,'联系地址：'.$b_address,SHOW_BORDER,'L',0,1,'','',true);
        $this->MultiCell(DEFAULT_PAGE_WIDTH,DEFAULT_LINE_HEIGHT,'联系人：'.$b_contact,SHOW_BORDER,'L',0,1,'','',true);
        $this->MultiCell(DEFAULT_PAGE_WIDTH,DEFAULT_LINE_HEIGHT,'邮箱： '.$b_email,SHOW_BORDER,'L',0,1,'','',true);
        $this->Ln(8);

        // 设置正文
        $this->SetFont(DEFAULT_FONT, '', DEFAULT_BODY_SIZE);
        $this->MultiCell(DEFAULT_PAGE_WIDTH,DEFAULT_LINE_HEIGHT,'经甲乙双方充分协商,达成如下合同条款,以兹双方共同信守。',SHOW_BORDER,'L',0,1,'','',true);

        // 第一段标题
        $this->SetFont(DEFAULT_FONT, 'B', DEFAULT_TITLE_SIZE);
        $this->setCellPaddings(1, 1, 1, 1);
        $this->MultiCell(DEFAULT_PAGE_WIDTH,DEFAULT_LINE_HEIGHT,'1.定义',SHOW_BORDER,'L',0,1,'','',true);

        // 第一段
        $this->SetFont(DEFAULT_FONT, '', DEFAULT_BODY_SIZE);
        $this->MultiCell(DEFAULT_PAGE_WIDTH,DEFAULT_LINE_HEIGHT,'1.1 合作产品：指乙方拥有自主知识产权的 Android 简体中文版 ',SHOW_BORDER,'L',0,1,'','',true);
        $this->MultiCell(DEFAULT_PAGE_WIDTH,DEFAULT_LINE_HEIGHT,'《'.$app_name.'》(包名：'.$package.')及其增值服务。 ',SHOW_BORDER,'L',0,1,'','',true);
        $this->MultiCell(DEFAULT_PAGE_WIDTH,DEFAULT_LINE_HEIGHT,'1.2 用户：指通过甲方平台下载合作产品成功运行合作产品的用户。',SHOW_BORDER,'L',0,1,'','',true);
        $this->MultiCell(DEFAULT_PAGE_WIDTH,DEFAULT_LINE_HEIGHT,'1.3 甲方平台：指甲方自身或关联公司或甲方指定的第三方拥有的推广平台。',SHOW_BORDER,'L',0,1,'','',true);
        $this->Ln(8);

        // 第二段标题
        $this->SetFont(DEFAULT_FONT, 'B', DEFAULT_TITLE_SIZE);
        $this->setCellPaddings(1, 1, 1, 1);
        $this->MultiCell(DEFAULT_PAGE_WIDTH,DEFAULT_LINE_HEIGHT,'2.合作内容',SHOW_BORDER,'L',0,1,'','',true);

        // 第二段
        $this->SetFont(DEFAULT_FONT, '', DEFAULT_BODY_SIZE);
        $this->MultiCell(DEFAULT_PAGE_WIDTH,DEFAULT_LINE_HEIGHT,'2.1 乙方向甲方提供合作产品所必要的信息，甲方利用甲方平台为合作产品进行推广服务。',SHOW_BORDER,'L',0,1,'','',true);
        $this->MultiCell(DEFAULT_PAGE_WIDTH,DEFAULT_LINE_HEIGHT,'2.2 就用户在合作产品上进行消费所产生的收益，甲乙双方按本协议第五条约定分成。',SHOW_BORDER,'L',0,1,'','',true);
        $this->MultiCell(DEFAULT_PAGE_WIDTH,DEFAULT_LINE_HEIGHT,'2.3 合同期限自合同签订之日起壹年。',SHOW_BORDER,'L',0,1,'','',true);
        $this->Ln(8);

        // 第三段标题
        $this->SetFont(DEFAULT_FONT, 'B', DEFAULT_TITLE_SIZE);
        $this->setCellPaddings(1, 1, 1, 1);
        $this->MultiCell(DEFAULT_PAGE_WIDTH,DEFAULT_LINE_HEIGHT,'3.乙方权利义务',SHOW_BORDER,'L',0,1,'','',true);

        // 第三段
        $this->SetFont(DEFAULT_FONT, '', DEFAULT_BODY_SIZE);
        $this->MultiCell(DEFAULT_PAGE_WIDTH,DEFAULT_LINE_HEIGHT,'3.1 乙方保证合作产品不侵犯第三方的合法权益,不违反公序良俗,不含黄色、色情、宗教、',SHOW_BORDER,'L',0,1,'','',true);
        $this->MultiCell(DEFAULT_PAGE_WIDTH,DEFAULT_LINE_HEIGHT,'政治等禁止性内容。如有违反此项约定的，由乙方承担全部责任，且乙方保证甲方免于遭受',SHOW_BORDER,'L',0,1,'','',true);
        $this->MultiCell(DEFAULT_PAGE_WIDTH,DEFAULT_LINE_HEIGHT,'损失。',SHOW_BORDER,'L',0,1,'','',true);
        $this->MultiCell(DEFAULT_PAGE_WIDTH,DEFAULT_LINE_HEIGHT,'3.2 乙方保证乙方产品在甲方平台的正常浏览及下载，并合作产品正常运行和维护，对因合',SHOW_BORDER,'L',0,1,'','',true);
        $this->MultiCell(DEFAULT_PAGE_WIDTH,DEFAULT_LINE_HEIGHT,'作产品产生的投诉、纠纷等相关问题自行承担责任。',SHOW_BORDER,'L',0,1,'','',true);

        $this->AddPage();
        $this->setBkg();

        $this->MultiCell(DEFAULT_PAGE_WIDTH,DEFAULT_LINE_HEIGHT,'3.3 乙方更新合作产品版本或停止服务的，需提前15日通知甲方。',SHOW_BORDER,'L',0,1,'','',true);
        $this->MultiCell(DEFAULT_PAGE_WIDTH,DEFAULT_LINE_HEIGHT,'3.4 乙方确保合作游戏产品以及依托合作游戏平台所发布的信息内容不得含有暗扣费、乱扣',SHOW_BORDER,'L',0,1,'','',true);
        $this->MultiCell(DEFAULT_PAGE_WIDTH,DEFAULT_LINE_HEIGHT,'费等违规情形；否则，甲方有权立即终止本协议，且乙方须赔偿甲方所遭受的一切损失。',SHOW_BORDER,'L',0,1,'','',true);
        $this->MultiCell(DEFAULT_PAGE_WIDTH,DEFAULT_LINE_HEIGHT,'3.5 乙方保证，终端用户点击运行合作手机游戏产品时，未经终端用户确认的，不得运行，',SHOW_BORDER,'L',0,1,'','',true);
        $this->MultiCell(DEFAULT_PAGE_WIDTH,DEFAULT_LINE_HEIGHT,'不得收取相关费用:否则，甲方有权立即终止本协议，且乙方须赔偿甲方所遭受的一切损失。',SHOW_BORDER,'L',0,1,'','',true);
        $this->MultiCell(DEFAULT_PAGE_WIDTH,DEFAULT_LINE_HEIGHT,'3.6 乙方保证，在用户所持终端弹出信息窗口的，应当以显著的方式向用户提供关闭或者退',SHOW_BORDER,'L',0,1,'','',true);
        $this->MultiCell(DEFAULT_PAGE_WIDTH,DEFAULT_LINE_HEIGHT,'出窗口的功能标识。',SHOW_BORDER,'L',0,1,'','',true);
        $this->Ln(8);

        // 第四段标题
        $this->SetFont(DEFAULT_FONT, 'B', DEFAULT_TITLE_SIZE);
        $this->setCellPaddings(1, 1, 1, 1);
        $this->MultiCell(DEFAULT_PAGE_WIDTH,DEFAULT_LINE_HEIGHT,'4.甲方权利义务',SHOW_BORDER,'L',0,1,'','',true);

        // 第四段
        $this->SetFont(DEFAULT_FONT, '', DEFAULT_BODY_SIZE);
        $this->MultiCell(DEFAULT_PAGE_WIDTH,DEFAULT_LINE_HEIGHT,'4.1 甲方保证所获得的乙方所有资料,仅限用于合作专区的合作项目,决不用于其他目的（双',SHOW_BORDER,'L',0,1,'','',true);
        $this->MultiCell(DEFAULT_PAGE_WIDTH,DEFAULT_LINE_HEIGHT,'方协商书面同意的除外），否则乙方有权要求甲方赔偿由此引起的一切直接经济损失。',SHOW_BORDER,'L',0,1,'','',true);
        $this->MultiCell(DEFAULT_PAGE_WIDTH,DEFAULT_LINE_HEIGHT,'4.2 如任何第三方在甲方平台发布仿冒合作产品的，甲方应在接到乙方书面通知后 2个工作',SHOW_BORDER,'L',0,1,'','',true);
        $this->MultiCell(DEFAULT_PAGE_WIDTH,DEFAULT_LINE_HEIGHT,'日内下架。',SHOW_BORDER,'L',0,1,'','',true);
        $this->MultiCell(DEFAULT_PAGE_WIDTH,DEFAULT_LINE_HEIGHT,'4.3 甲方在推广合作产品过程中，不得进行不正当竞争行为，包括但不限于以下类型：',SHOW_BORDER,'L',0,1,'','',true);
        $this->MultiCell(DEFAULT_PAGE_WIDTH,DEFAULT_LINE_HEIGHT,'4.3.1 利用协议提供的合作游戏推广权利谋取单方面的不正当利益；',SHOW_BORDER,'L',0,1,'','',true);
        $this->MultiCell(DEFAULT_PAGE_WIDTH+10,5,'4.3.2 采取带有淫秽、色情、赌博、暴力、迷信及危害国家安全等违法或不健康内同的文字、',SHOW_BORDER,'L',0,1,'','',true);
        $this->MultiCell(DEFAULT_PAGE_WIDTH,DEFAULT_LINE_HEIGHT,'图片、音频、视频等诱使广告方式引导用户；',SHOW_BORDER,'L',0,1,'','',true);
        $this->MultiCell(DEFAULT_PAGE_WIDTH,DEFAULT_LINE_HEIGHT,'4.3.3 其他有悖于商业规范和法律规范的行为。',SHOW_BORDER,'L',0,1,'','',true);
        $this->MultiCell(DEFAULT_PAGE_WIDTH,DEFAULT_LINE_HEIGHT,'4.4 乙方授权甲方在对合作产品推广的过程中使用乙方公司名称、商号、商标及合作产品图',SHOW_BORDER,'L',0,1,'','',true);
        $this->MultiCell(DEFAULT_PAGE_WIDTH,DEFAULT_LINE_HEIGHT,'片、影像、视频等的相关权利。甲方行使上述权利时不得损害乙方合法权益，在本协议终止',SHOW_BORDER,'L',0,1,'','',true);
        $this->MultiCell(DEFAULT_PAGE_WIDTH,DEFAULT_LINE_HEIGHT,'之日起20个工作日内撤下所有此类合作产品相关的内容。',SHOW_BORDER,'L',0,1,'','',true);
        $this->Ln(8);
        // 新增一页的下划线
        $this->Line(77, 205, 92, 205, $style);     // 分成比例
        $this->Line(80, 231, 90, 231, $style);     // 税费率


        // 第五段标题
        $this->SetFont(DEFAULT_FONT, 'B', DEFAULT_TITLE_SIZE);
        $this->setCellPaddings(1, 1, 1, 1);
        $this->MultiCell(DEFAULT_PAGE_WIDTH,DEFAULT_LINE_HEIGHT,'5.收入分配与结算',SHOW_BORDER,'L',0,1,'','',true);

        // 第五段
        $this->SetFont(DEFAULT_FONT, '', DEFAULT_BODY_SIZE);
        $this->MultiCell(DEFAULT_PAGE_WIDTH,DEFAULT_LINE_HEIGHT,'5.1 乙方分成收益=用户消费金额*（1-渠道费率）*（1-税费率）*乙方分成比例',SHOW_BORDER,'L',0,1,'','',true);
        $this->MultiCell(DEFAULT_PAGE_WIDTH,DEFAULT_LINE_HEIGHT,'5.2 甲方与乙方分成比例为   '.$share_ratio,SHOW_BORDER,'L',0,1,'','',true);
        $this->MultiCell(DEFAULT_PAGE_WIDTH,DEFAULT_LINE_HEIGHT,'5.3 渠道费率：在用户充值A币或A券的时候产生的渠道费率统一为：3%。',SHOW_BORDER,'L',0,1,'','',true);
        $this->setCellPaddings(8, 1, 1, 1);
        $this->MultiCell(DEFAULT_PAGE_WIDTH,DEFAULT_LINE_HEIGHT,'A币或A券：用于购买Amigo平台上的各种虚拟产品和增值服务，用户先用人民币购买',SHOW_BORDER,'L',0,1,'','',true);
        $this->MultiCell(DEFAULT_PAGE_WIDTH,DEFAULT_LINE_HEIGHT,'A币或A券，然后用A币或A券购买道具，A币或A券：人民币=1：1.',SHOW_BORDER,'L',0,1,'','',true);
        $this->setCellPaddings(1, 1, 1, 1);
        $this->MultiCell(DEFAULT_PAGE_WIDTH,DEFAULT_LINE_HEIGHT,'5.4 甲乙双方同意选择以下第  '.$shui_type.'   种方式计算税费率。',SHOW_BORDER,'L',0,1,'','',true);
        $this->setCellPaddings(8, 1, 1, 1);
        $this->MultiCell(DEFAULT_PAGE_WIDTH,DEFAULT_LINE_HEIGHT,'A.乙方结算时提供的为普通发票，税费率为：6%；',SHOW_BORDER,'L',0,1,'','',true);
        $this->MultiCell(DEFAULT_PAGE_WIDTH,DEFAULT_LINE_HEIGHT,'B.乙方结算时提供税率为3%的增值税专用发票，税费率为：3%； ',SHOW_BORDER,'L',0,1,'','',true);
        $this->MultiCell(DEFAULT_PAGE_WIDTH,DEFAULT_LINE_HEIGHT,'C.乙方结算时提供税率为6%的增值税专用发票，税费率为：0%；',SHOW_BORDER,'L',0,1,'','',true);
        $this->setCellPaddings(1, 1, 1, 1);
        $this->AddPage();
        $this->Line(58, 110, 140, 110, $style);   // 开户名称
        $this->Line(58, 117, 140, 117, $style);   // 开户银行
        $this->Line(58, 123, 140, 123, $style);   // 银行账号

        $this->setBkg();

        $this->MultiCell(DEFAULT_PAGE_WIDTH,DEFAULT_LINE_HEIGHT,'5.5 结算方式。按月结算，双方每月结束后以电子邮件形式进行结算确认，甲方应于每月',SHOW_BORDER,'L',0,1,'','',true);
        $this->MultiCell(DEFAULT_PAGE_WIDTH,DEFAULT_LINE_HEIGHT,'10日前向乙方提交结算账单供乙方核对，计算自上月1日0时0分至上月30日（大月为31',SHOW_BORDER,'L',0,1,'','',true);
        $this->MultiCell(DEFAULT_PAGE_WIDTH,DEFAULT_LINE_HEIGHT,'日）23点59分之间合作游戏收入及乙方应得分成。经双方确认后，乙方应按第5.4条约定',SHOW_BORDER,'L',0,1,'','',true);
        $this->MultiCell(DEFAULT_PAGE_WIDTH,DEFAULT_LINE_HEIGHT,'开具等额发票，甲方在当月15日23：59前收到发票的，于当月25到30日将乙方应得收入',SHOW_BORDER,'L',0,1,'','',true);
        $this->MultiCell(DEFAULT_PAGE_WIDTH,DEFAULT_LINE_HEIGHT,'汇至乙方的指定银行账号，后寄到的发票顺延到次月25到30日打款。以上结算如遇节假日',SHOW_BORDER,'L',0,1,'','',true);
        $this->MultiCell(DEFAULT_PAGE_WIDTH,DEFAULT_LINE_HEIGHT,'则顺延。',SHOW_BORDER,'L',0,1,'','',true);
        $this->MultiCell(DEFAULT_PAGE_WIDTH,DEFAULT_LINE_HEIGHT,'5.6 双方结算的最低额度为人民币1000元/月。即乙方当月的分成不足1000元，结算金额',SHOW_BORDER,'L',0,1,'','',true);
        $this->MultiCell(DEFAULT_PAGE_WIDTH,DEFAULT_LINE_HEIGHT,'则自动累积至次月，以此类推。',SHOW_BORDER,'L',0,1,'','',true);
        $this->MultiCell(DEFAULT_PAGE_WIDTH,DEFAULT_LINE_HEIGHT,'5.7 甲乙双方应按中国法律之规定，各自缴纳与自身经营有关之任何税款。',SHOW_BORDER,'L',0,1,'','',true);
        $this->MultiCell(DEFAULT_PAGE_WIDTH,DEFAULT_LINE_HEIGHT,'5.8 乙方指定账户资料如下：',SHOW_BORDER,'L',0,1,'','',true);
        $this->setCellPaddings(8, 1, 1, 1);
        $this->MultiCell(DEFAULT_PAGE_WIDTH,DEFAULT_LINE_HEIGHT,'开户名称：'.$account_name,SHOW_BORDER,'L',0,1,'','',true);
        $this->MultiCell(DEFAULT_PAGE_WIDTH,DEFAULT_LINE_HEIGHT,'开户银行：'.$account_bank,SHOW_BORDER,'L',0,1,'','',true);
        $this->MultiCell(DEFAULT_PAGE_WIDTH,DEFAULT_LINE_HEIGHT,'银行账号：'.$account_key,SHOW_BORDER,'L',0,1,'','',true);
        $this->setCellPaddings(1, 1, 1, 1);
        $this->MultiCell(DEFAULT_PAGE_WIDTH,DEFAULT_LINE_HEIGHT,'5.9 乙方账户变更的，应提前 15个工作日书面通知甲方。因乙方账户变更而未提前书面通',SHOW_BORDER,'L',0,1,'','',true);
        $this->MultiCell(DEFAULT_PAGE_WIDTH,DEFAULT_LINE_HEIGHT,'知、以及延迟开发票等造成甲方延迟付款的，甲方不承担违约责任，由此产生的损失由乙方',SHOW_BORDER,'L',0,1,'','',true);
        $this->MultiCell(DEFAULT_PAGE_WIDTH,DEFAULT_LINE_HEIGHT,'自行承担。',SHOW_BORDER,'L',0,1,'','',true);
        $this->MultiCell(DEFAULT_PAGE_WIDTH,DEFAULT_LINE_HEIGHT,'5.10 任何一方对分成收益提出异议，由双方协商解决。',SHOW_BORDER,'L',0,1,'','',true);
        $this->Ln(8);

        // 第六段标题
        $this->SetFont(DEFAULT_FONT, 'B', DEFAULT_TITLE_SIZE);
        $this->setCellPaddings(1, 1, 1, 1);
        $this->MultiCell(DEFAULT_PAGE_WIDTH,DEFAULT_LINE_HEIGHT,'6.违约与终止',SHOW_BORDER,'L',0,1,'','',true);

        // 第六段
        $this->SetFont(DEFAULT_FONT, '', DEFAULT_BODY_SIZE);
        $this->MultiCell(DEFAULT_PAGE_WIDTH,DEFAULT_LINE_HEIGHT,'6.1 任何一方不履行或不完全履行约定义务，应承担相应违约责任，并赔偿因此给对方造成',SHOW_BORDER,'L',0,1,'','',true);
        $this->MultiCell(DEFAULT_PAGE_WIDTH,DEFAULT_LINE_HEIGHT,'的损失。',SHOW_BORDER,'L',0,1,'','',true);
        $this->MultiCell(DEFAULT_PAGE_WIDTH,DEFAULT_LINE_HEIGHT,'6.2 违约方应当在非违约方以书面形式告知违约事实后的十个工作日内采取补救措施，逾期',SHOW_BORDER,'L',0,1,'','',true);
        $this->MultiCell(DEFAULT_PAGE_WIDTH,DEFAULT_LINE_HEIGHT,'不作补救的，非违约方有权终止协议并要求违约方赔偿其因此遭受的损失。本协议另有约定',SHOW_BORDER,'L',0,1,'','',true);
        $this->MultiCell(DEFAULT_PAGE_WIDTH,DEFAULT_LINE_HEIGHT,'的除外。',SHOW_BORDER,'L',0,1,'','',true);
        $this->Ln(8);

        // 第七段标题
        $this->SetFont(DEFAULT_FONT, 'B', DEFAULT_TITLE_SIZE);
        $this->setCellPaddings(1, 1, 1, 1);
        $this->MultiCell(DEFAULT_PAGE_WIDTH,DEFAULT_LINE_HEIGHT,'7.其他',SHOW_BORDER,'L',0,1,'','',true);

        // 第七段
        $this->SetFont(DEFAULT_FONT, '', DEFAULT_BODY_SIZE);
        $this->MultiCell(DEFAULT_PAGE_WIDTH,DEFAULT_LINE_HEIGHT,'7.1 因本协议解释及执行产生的争议，任何一方均可将争议提交甲方所在地人民法院解决。',SHOW_BORDER,'L',0,1,'','',true);
        $this->MultiCell(DEFAULT_PAGE_WIDTH,DEFAULT_LINE_HEIGHT,'7.2 本协议自双方签字或盖章之日起生效。一式贰份，双方各执一份。',SHOW_BORDER,'L',0,1,'','',true);
        $this->MultiCell(DEFAULT_PAGE_WIDTH,DEFAULT_LINE_HEIGHT,'（以下无正文）',SHOW_BORDER,'L',0,1,'','',true);
        $this->Ln(8);

        // 盖章签字处
        $this->SetFont(DEFAULT_FONT, 'B', 11);
        $this->setCellPaddings(1, 1, 1, 1);
        $this->Cell(DEFAULT_PAGE_WIDTH/2,5,'甲方 (盖章)',1,0,'L',0,'',0,0,'C','C');
        $this->Cell(DEFAULT_PAGE_WIDTH/2,5,'乙方 (盖章)',1,1,'L',0,'',0,0,'C','C');
        $this->setCellPaddings(1, 3, 1, 1);
        $this->Cell(DEFAULT_PAGE_WIDTH/2,25,'授权代表签字：刘立荣',1,0,'L',0,'',0,0,'T','T');
        $this->Cell(DEFAULT_PAGE_WIDTH/2,25,'授权代表签字：',1,1,'L',0,'',0,0,'T','T');
        $this->Image(dirname(__FILE__).'/ht.png', 53, 220, 50, 50, 'PNG', '', '', false, 300, '', false, false, false, false, false, false);

        $this->lastPage();
    }

}



// ---------------------------------------------------------
//// 新建一个PDF文档
//$pdf = new ContractPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
//$pdf->init(
//    'GN20150320-01',
//    '2015',
//    '03',
//    '20',
//    '陈宇',
//    'noprom@163.com',
//    '苏州蜗牛数字科技股份有限公司',
//    '苏州市工业园区中新大道西 171 号',
//    '吴建',
//    'noprom@163.com',
//    '太极熊猫',
//    'com.snailgame.panda.am',
//    '5:5',
//    'B',
//    '吴建',
//    '中国工商银行苏州分行',
//    '622202xxxxxxxxx'
//);
////Close and output PDF document
//$pdf->Output('金立合同.pdf', 'I');
