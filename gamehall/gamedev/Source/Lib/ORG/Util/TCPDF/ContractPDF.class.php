<?php
//============================================================+
// File name   : ContractPDF.class.php
// Begin       : 2015-04-05
// Last Update : 2015-04-05
//
// Description : 生成一个合同的PDF
//
// Author: TyeeNoprom (https://github.com/noprom)
//
//============================================================+

// 项目框架引入
require_once(dirname(__FILE__).'/tcpdf.php');

class ContractPDF extends TCPDF{
//
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


    public function init($number,$year,$month,$day,$a_contact,$a_email,$b_name,$b_address,
                         $b_contact,$b_email,$app_name,$package,$share_ratio,$shui_type,
                         $account_name,$account_bank,$account_key){


        // 设置文档信息
        $title = '金立游戏开发者平台合作协议';
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
        $this->Line(168, 41, 195, 41, $style);  // 协议编号
        $this->Line(168, 47, 195, 47, $style);  // 签约时间

        $this->Line(26, 54, 75, 54, $style);    // 甲方
        $this->Line(32, 58, 120, 58, $style);   // 甲方联系地址
        $this->Line(29, 63, 75, 63, $style);    // 甲方联系人
        $this->Line(26, 67, 75, 67, $style);    // 甲方联系人邮箱

        $this->Line(26, 76, 90, 76, $style);    // 乙方
        $this->Line(32, 81, 120, 81, $style);   // 乙方联系地址
        $this->Line(29, 85, 75, 85, $style);    // 乙方联系人
        $this->Line(26, 90, 75, 90, $style);    // 乙方联系人邮箱

        $this->Line(112, 113, 200, 113, $style);    // 游戏名称以及包名


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
        $this->MultiCell(180,5,'协议编号:  '.$number,0,'R',0,1,'','',true);
        $this->MultiCell(180,5,'签约时间:  '.$year.'年'.$month.'月'.$day.'日',0,'R',0,1,'','',true);

        // 设置甲方信息
        $this->SetFont($defaultFont, '', 10);
        $this->MultiCell(180,5,'甲 方：深圳市金立通信设备有限公司
联系地址：深圳市福田区深南大道 7008 号阳光高尔夫大厦 15 楼
联系人： '.$a_contact.'
邮箱： '.$a_email,0,'L',0,1,'','',true);
        $this->Ln(3);

// 设置乙方信息
        $this->SetFont($defaultFont, '', 10);
        $this->MultiCell(180,5,'乙 方：  '.$b_name.'
联系地址： '.$b_address.'
联系人： '.$b_contact.'
邮箱： '.$b_email,0,'L',0,1,'','',true);
        $this->Ln(3);

// 设置正文
        $this->SetFont($defaultFont, '', 10);
        $this->setCellPaddings(15, 1, 1, 1);
        $this->MultiCell(180,5,'经甲乙双方充分协商,达成如下合同条款,以兹双方共同信守。',0,'L',0,1,'','',true);
        $this->setCellPaddings(1, 1, 1, 1);

// 第一段标题
        $this->SetFont($defaultFont, 'B', 11);
        $this->setCellPaddings(1, 1, 1, 1);
        $this->MultiCell(180,5,'1.定义',0,'L',0,1,'','',true);
        $this->SetFont($defaultFont, 'B', 10);

// 第一段
        $this->MultiCell(180,5,'1.1 合作产品：指乙方拥有自主知识产权的 Android 简体中文版 '.$app_name.'(包名：'.$package.')
及其增值服务。
1.2 用户：指通过甲方平台下载合作产品成功运行合作产品的用户。
1.3 甲方平台：指甲方自身或关联公司或甲方指定的第三方拥有的推广平台。
',0,'L',0,1,'','',true);

// 第二段标题
        $this->SetFont($defaultFont, 'B', 11);
        $this->setCellPaddings(1, 1, 1, 1);
        $this->MultiCell(180,5,'2. 合作内容',0,'L',0,1,'','',true);
        $this->SetFont($defaultFont, 'B', 10);

// 第二段
        $this->MultiCell(180,5,'2.1 乙方向甲方提供合作产品所必要的信息,甲方利用甲方平台为合作产品进行推广服务。
2.2 就用户在合作产品上进行消费所产生的收益,甲乙双方按本协议第五条约定分成。
2.3 合同期限自合同签订之日起壹年。
',0,'L',0,1,'','',true);

// 第三段标题
        $this->SetFont($defaultFont, 'B', 11);
        $this->setCellPaddings(1, 1, 1, 1);
        $this->MultiCell(180,5,'3.  乙方 权利义务',0,'L',0,1,'','',true);
        $this->SetFont($defaultFont, 'B', 10);

// 第三段
        $this->MultiCell(180,5,'3.1 乙方保证合作产品不侵犯第三方的合法权益, 不违反公序良俗, 不含黄色、 色情、 宗教、政治等禁止性内容。
如有违反此项约定的,由乙方承担全部责任,且乙方保证甲方免于遭受损失。
3.2 乙方保证乙方产品在甲方平台的正常浏览及下载,并合作产品正常运行和维护,对因合作产品产生的投诉、纠纷等相关问题自行承担责任。
3.3 乙方更新合作产品版本或停止服务的,需提前15日通知甲方。
3.4 乙方确保合作游戏产品以及依托合作游戏平台所发布的信息内容不得含有暗扣费、乱扣费等违规情形;否则,甲方有权立即终止本协议,且乙方须赔偿甲方所遭受的一切损失。
3.5 乙方保证,终端用户点击运行合作手机游戏产品时,未经终端用户确认的,不得运行,不得收取相关费用;否则,甲方有权立即终止本协议, 且乙方须赔偿甲方所遭受的一切损失。
3.6 乙方保证,在用户所持终端弹出信息窗口的,应当以显著的方式向用户提供关闭或者退出窗口的功能标识。
',0,'L',0,1,'','',true);

// 第四段标题
        $this->SetFont($defaultFont, 'B', 11);
        $this->setCellPaddings(1, 1, 1, 1);
        $this->MultiCell(180,5,'4. 甲方 权利义务',0,'L',0,1,'','',true);
        $this->SetFont($defaultFont, 'B', 10);

// 第四段
        $this->MultiCell(180,5,'4.1 甲方保证所获得的乙方所有资料,仅限用于合作专区的合作项目,决不用于其他目的(双方协商书面同意的除外),
否则乙方有权要求甲方赔偿由此引起的一切直接经济损失。
4.2 如任何第三方在甲方平台发布仿冒合作产品的, 甲方应在接到乙方书面通知后 2个工作日内下架。
4.3 甲方在推广合作产品过程中,不得进行不正当竞争行为,包括但不限于以下类型：
4.3.1 利用协议提供的合作游戏推广权利谋取单方面的不正当利益;
4.3.2 采取带有淫秽、色情、赌博、暴力、迷信及危害国家安全等违法或不健康内同的文字、图片、音频、视频等诱使广告方式引导用户;
4.3.3 其他有悖于商业规范和法律规范的行为。
4.4 乙方授权甲方在对合作产品推广的过程中使用乙方公司名称、商号、商标及合作产品图片、影像、视频等的相关权利。甲方行使上述权利时不得损害乙方合法权益,在本协议终止之日起20个工作日内撤下所有此类合作产品相关的内容。
',0,'L',0,1,'','',true);

        // 新增一页的下划线
        $this->AddPage();
        $this->Line(56, 43, 70, 43, $style);   // 分成比例
        $this->Line(60, 61, 72, 61, $style);   // 税费率

        $this->Line(32, 114, 80, 114, $style);   // 开户名称
        $this->Line(32, 118, 80, 118, $style);   // 开户银行
        $this->Line(32, 123, 80, 123, $style);   // 银行账号
        $this->Line(60, 61, 72, 61, $style);   // 税费率

// 第五段标题
        $this->SetFont($defaultFont, 'B', 11);
        $this->setCellPaddings(1, 1, 1, 1);
        $this->MultiCell(180,5,'5. 收入分配与结算',0,'L',0,1,'','',true);
        $this->SetFont($defaultFont, 'B', 10);

// 第五段
        $this->MultiCell(180,5,'5.1 乙方分成收益=用户消费金额*(1-渠道费率)*(1-税费率)*乙方分成比例
5.2 甲方与乙方分成比例为 '.$share_ratio.' .
5.3 渠道费率：在用户充值A币或A券的时候产生的渠道费率统一为：3%。
A 币或 A 券：用于购买 Amigo 平台上的各种虚拟产品和增值服务,用户先用人民币购买 A 币或 A 券,然后用 A 币或 A 券购买道具,A 币或 A 券：人民币=1：1.
5.4 甲乙双方同意选择以下第 '.$shui_type.' 种方式计算税费率。
A. 乙方结算时提供的为普通发票,税费率为：6%;
B. 乙方结算时提供税率为 3%的增值税专用发票,税费率为：3%;
C. 乙方结算时提供税率为 6%的增值税专用发票,税费率为：0%;
5.5  结算方式。按月结算,双方每月结束后以电子邮件形式进行结算确认,甲方应于每月10日前向乙方提交结算账单供乙方核对,计算自上月1日0时0分至上月30日(大月为31 日)23 点 59 分之间合作游戏收入及乙方应得分成。经双方确认后,乙方应按第 5.4 条约定开具等额发票,甲方在当月 15 日 23：59 前收到发票的,于当月 25 到30 日将乙方应得收入汇至乙方的指定银行账号,后寄到的发票顺延到次月25到30日打款。
以上结算如遇节假日则顺延。
5.6 双方结算的最低额度为人民币 1000 元/月。即乙方当月的分成不足1000元,结算金额则自动累积至次月,以此类推。
5.7 甲乙双方应按中国法律之规定,各自缴纳与自身经营有关之任何税款。
5.8 乙方指定账户资料如下：
开户名称： '.$account_name.'
开户银行：'.$account_bank.'
银行账号：'.$account_key.'
5.9 乙方账户变更的,应提前 15 个工作日书面通知甲方。因乙方账户变更而未提前书面通知、以及延迟开发票等造成甲方延迟付款的,甲方不承担违约责任,由此产生的损失由乙方自行承担。
5.10 任何一方对分成收益提出异议,由双方协商解决。
',0,'L',0,1,'','',true);

// 第六段标题
        $this->SetFont($defaultFont, 'B', 11);
        $this->setCellPaddings(1, 1, 1, 1);
        $this->MultiCell(180,5,'6. 违约与终止',0,'L',0,1,'','',true);
        $this->SetFont($defaultFont, 'B', 10);

// 第六段
        $this->MultiCell(180,5,'6.1 任何一方不履行或不完全履行约定义务,应承担相应违约责任,并赔偿因此给对方造成的损失。
6.2 违约方应当在非违约方以书面形式告知违约事实后的十个工作日内采取补救措施,逾期不作补救的,非违约方有权
终止协议并要求违约方赔偿其因此遭受的损失。本协议另有约定的除外。
',0,'L',0,1,'','',true);

// 第七段标题
        $this->SetFont($defaultFont, 'B', 11);
        $this->setCellPaddings(1, 1, 1, 1);
        $this->MultiCell(180,5,'7. 其他',0,'L',0,1,'','',true);
        $this->SetFont($defaultFont, 'B', 10);

// 第七段
        $this->MultiCell(180,5,'7.1 因本协议解释及执行产生的争议,任何一方均可将争议提交甲方所在地人民法院解决。
7.2 本协议自双方签字或盖章之日起生效。一式贰份,双方各执一份。
(以下无正文)
',0,'L',0,1,'','',true);
        $this->Ln(5);

// 盖章签字处
        $this->SetFont($defaultFont, 'B', 11);
        $this->setCellPaddings(1, 1, 1, 1);
        $this->Cell(90,5,'甲方 (盖章)',1,0,'L',0,'',0,0,'C','C');
        $this->Cell(90,5,'乙方 (盖章)',1,1,'L',0,'',0,0,'C','C');
        $this->setCellPaddings(1, 3, 1, 1);
        $this->Cell(90,25,'授权代表签字：刘立荣',1,0,'L',0,'',0,0,'T','T');
        $this->Cell(90,25,'授权代表签字：',1,1,'L',0,'',0,0,'T','T');
        $this->Image(dirname(__FILE__).'/ht.png', 50, 160, 50, 50, 'PNG', '', '', false, 300, '', false, false, 1, false, false, false);
        // move pointer to last page
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
//$pdf->Output('example_005.pdf', 'I');
