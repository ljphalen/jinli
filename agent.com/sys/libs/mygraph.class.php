<?php

require_once 'plugin/jpgraph/jpgraph.php';
require_once 'plugin/jpgraph/jpgraph_pie.php';
require_once 'plugin/jpgraph/jpgraph_pie3d.php';
require_once 'plugin/jpgraph/jpgraph_bar.php';
require_once 'plugin/jpgraph/jpgraph_line.php';

class mygraph {

    private $graph;
    public $shaow = false;
    public $setMargin_left = 60;
    public $setMargin_right = 30;
    public $setMargin_up = 50;
    public $setMargin_down = 10;
    public $title = 'xxx';
    public $shape;
    public $color = array("orange", "blue", "red", "yellow", "green", "pink", "navy");
    public $color_line = array("red", "orange", "green", "blue", "orange", "blue", "red", "yellow"); //线图的颜色
    //圆形参数
    public $pie_size = 0.4; //圆形的大小
    public $center_H = 0.5; //圆心的横坐标
    public $center_V = 0.85; //圆心的纵坐标
    public $button_line = false; //显示圆形图低下对每种物品的描述
    //线性图参数
    //纵轴是否以百分号显示刻度尺
    public $persend = false;
    //横坐标刻度尺上的文字的显示角度
    public $angle = 0;
    //柱状图参数
    public $bar_color = 'blue';
    public $mark_bar_V = "%.1f%%"; //柱状图刻度格式;
    public $mark_bar_H = "%.1f%%"; //柱状图刻度格式;
    public $mark_line_V = '%3d.0%%'; //线状图刻度格式
    public $mark_line_H = '%3d.0%%'; //线状图刻度格式
    //横纵轴名称
    public $xaxis = '时间';  //x轴名称
    public $yaxis = '人数';
    //说明小图标的位置
    public $legend_H = 0.88; //横坐标
    public $legend_V = 0.04; //纵坐标
    public $description = array('First', 'Second', 'Third', 'Fourth', 'Fifth', 'Sixth', 'Seventh'); //各个模块之间的描述

    /**
     *
     * @param type $shape
     * @param type $height
     * @param type $width
     *  pie：园图
     *  bar:单，多柱状图
     *  bar_c:/重叠柱状图
     *  line:/单，多线图
     *  barline:柱状图和线状图一起
     */

    function __construct($shape, $height = 400, $width = 400) {
        $this->shape = $shape;
        switch ($shape) {
            case 'pie':
                $this->graph = new PieGraph($height, $width);
                break;
            default:
                $this->graph = new Graph($width, $height, 'auto');
                break;
        }

        $this->graph->SetScale("textlin");
        $this->graph->img->SetMargin($this->setMargin_left, $this->setMargin_right, $this->setMargin_up, $this->setMargin_down); //左，右，上，下
        if ($this->shaow) {
            $this->graph->SetShadow();
        }
        $this->graph->yaxis->title->SetFont(FF_SIMSUN, FS_BOLD);
        $this->graph->xaxis->title->SetFont(FF_SIMSUN, FS_BOLD);
        $this->graph->title->SetFont(FF_SIMSUN, FS_BOLD);
        $this->graph->xaxis->SetFont(FF_SIMSUN, FS_BOLD);
        $this->graph->legend->SetFont(FF_SIMSUN, FS_BOLD);
    }

    public function setMargin($left, $right, $up, $down) {
        $this->setMargin_left = $left;
        $this->setMargin_right = $right;
        $this->setMargin_up = $up;
        $this->setMargin_down = $down;
    }

    /**
     * 画图函数
     * @param type $data
     * @param type $data2
     * 描述：
     *  pie：园图
     *  bar:单，多柱状图
     *  bar_c:/重叠柱状图
     *  line:/单，多线图
     *  barline:柱状图和线状图一起
     */
    public function draw($data, $data2 = array()) {
        switch ($this->shape) {
            case 'pie':
                $this->pie($data);
                $this->graph->Stroke();
                break;
            case 'bar'://单，多柱状图
                $this->bar_multy($data);
                $this->graph->Stroke();

                break;
            case 'bar_c'://重叠柱状图
                $this->bar_combine($data);
                $this->graph->StrokeCSIM();

                break;
            case 'line'://单，多线图
                $this->line($data);
                $this->graph->Stroke();
                break;
            case 'barline':
                $this->bar_line($data, $data2);
                $this->graph->Stroke();
                break;
        }
    }

    private function pie($mydata = array()) {
        $keys = array_keys($mydata);

        $data = array_values($mydata);

        for ($i = 0; $i < count($mydata); $i++) {
            $labels[] = $data[$i]."\n(%.1f%%)";
        }

        if ($this->title) {
            $this->graph->title->Set($this->myDecode($this->title));
        }
        $p1 = new PiePlot($data);
        if ($this->button_line) {
            $p1->SetLegends($this->myDecode($keys));
        }
        $p1->SetSize($this->pie_size);
        $p1->SetLabels($labels);
        $this->graph->legend->Pos($this->center_H, $this->center_V, "center", "left");
        $this->graph->Add($p1);
    }

    /**
     * 画柱状图
     * @param type $mydata
     */
    private function bar($mydata) {

        $keys = array_keys($mydata);

        $data = array_values($mydata);

        $b2plot = new BarPlot($data);
        $b2plot->SetFillColor($this->bar_color);

        for ($i = 0; $i < count($mydata); $i++) {
            $targ[] = "bar_clsmex2.php#" . $i;
            $alts[] = $this->mark_bar_H;
        }

        $b2plot->SetCSIMTargets($targ, $alts);
        $abplot = new AccBarPlot(array($b2plot));
        $abplot->value->Show();
        $this->graph->Add($abplot);
        if ($this->title) {
            $this->graph->title->Set($this->myDecode($this->title));
        }
        if ($this->xaxis) {
            $this->graph->xaxis->title->Set($this->myDecode($this->xaxis));
        }
        if ($this->yaxis) {
            $this->graph->yaxis->title->Set($this->myDecode($this->yaxis));
        }
        $this->graph->xaxis->SetTickLabels($keys);
        $this->graph->yaxis->SetLabelFormat($this->mark_bar_V);
        $this->graph->img->SetMargin(60, 30, 50, 30); //左，右，上，下
        //$this->graph->xaxis->SetLabelAngle(15);
    }

    /**
     * 画线图，可以画单线和多线图
     * @param type $mydata
     *
     * 调用方法：
     * $draw->draw(array('Java'=>array(1,2,3,4,5),'Android'=>array(1,2,3,4,5),'Symbian'=>array(1,2,3,4,5),'IOS'=>array(1,2,3,4,5),'PC'=>array(1,2,3,4,5)));
     */
    private function line($mydata = array()) {
        if (empty($mydata))
            exit("没有画图参数");

        $mycount = 0;
        foreach ($mydata as $myval) {
            $mycount = $mycount > count($myval) ? $mycount : count($myval);
        }
        // $keys = array_keys($mydata);
        //$description = $this->myDecode($description);
        //$this->graph->SetTheme($theme_class);++++++++
        $this->graph->img->SetAntiAliasing(false);
        //$this->graph->title->Set($this->myDecode($title));
        $this->graph->SetBox(false);
        $this->graph->img->SetAntiAliasing();
        $this->graph->yaxis->HideZeroLabel();
        $this->graph->yaxis->HideLine(false);
        $this->graph->yaxis->HideTicks(false, false);
        $this->graph->xgrid->Show();
        $this->graph->xgrid->SetLineStyle("solid");
        $this->graph->xgrid->SetColor('#E3E3E3');

        $keys = array_keys($mydata);
        $values = array_values($mydata);
        foreach ($values as $key => $var) {
            $p = new LinePlot($var);
            if (isset($this->color_line[$key])) {
                $p->SetColor($this->color_line[$key]);
            }
            $p->SetLegend($this->myDecode($keys[$key]));
            $this->graph->Add($p);
        }

        if ($this->title) {
            $this->graph->title->Set($this->myDecode($this->title));
        }

        if ($this->xaxis) {
            $this->graph->xaxis->title->Set($this->myDecode($this->xaxis));
        }
        if ($this->yaxis) {
            $this->graph->yaxis->title->Set($this->myDecode($this->yaxis));
        }
        $this->graph->legend->SetFrameWeight(1);
        $this->graph->legend->Pos($this->legend_H, $this->legend_V, "center", "left");
        $this->graph->xaxis->SetTickLabels($this->description);
        if ($this->persend) {
            $this->graph->yaxis->SetLabelFormat($this->mark_line_V);
        }

        $this->graph->xaxis->SetLabelAngle($this->angle);
    }

    /**
     * 多个柱状对比图
     * @param type $mydata
     * @param type $mark
     * 调用方法：
     * $draw->draw(array('Java'=>array(1,2,3,4,5),'Android'=>array(1,2,3,4,5),'Symbian'=>array(1,2,3,4,5),'IOS'=>array(1,2,3,4,5),'PC'=>array(1,2,3,4,5)));
     */
    private function bar_multy($mydata = array()) {
        // Create the graph. These two calls are always required
        if (empty($mydata))
            exit("没有画图参数");
        //$description = $this->myDecode($description);
        $tmp_keys = array();
        foreach ($mydata as $mkey => $mvar) {
            $tmp_keys = array_keys($mvar);
            for ($i = 0; $i < count($mydata[$mkey]); $i++) {
                $targ[] = "bar_clsmex2.php#" . $i;
                $alts[] = $this->mark_bar_H;
            }
            break;
        }
        $keys = array_keys($mydata);
        $values = array_values($mydata);

        //var_dump($values);exit;
        // Create the bar plots
        //$i=0;
        foreach ($values as $key => $var) {
            $b1plot[$key] = new BarPlot(array_values($var));
            if (isset($this->color[$key])) {
                $b1plot[$key]->SetFillColor($this->color[$key]);
            }
            $b1plot[$key]->SetCSIMTargets($targ, $alts);
            $b1plot[$key]->SetLegend($this->myDecode($keys[$key]));
        }


        $gbarplot = new GroupBarPlot($b1plot);
        $this->graph->Add($gbarplot);
        if ($this->title) {
            $this->graph->title->Set($this->myDecode($this->title));
        }
        if ($this->xaxis) {
            $this->graph->xaxis->title->Set($this->myDecode($this->xaxis));
        }
        if ($this->yaxis) {
            $this->graph->yaxis->title->Set($this->myDecode($this->yaxis));
        }
        if ($this->persend) {
            $this->graph->yaxis->SetLabelFormat($this->mark_bar_V);
        }
        if ($this->description) {
            $this->graph->xaxis->SetTickLabels($this->myDecode($tmp_keys));
        }
        $this->graph->legend->Pos($this->legend_H, $this->legend_V, "center", "left");
        // Send back the HTML page which will call this script again
        // to retrieve the image.
    }

    /*
     * 重叠柱状图
     */

    private function bar_combine($mydata = array()) {

        if (empty($mydata))
            exit("没有画图参数");
        foreach ($mydata as $mkey => $mvar) {
            for ($i = 0; $i < count($mydata[$mkey]); $i++) {
                $targ[] = "bar_clsmex2.php#" . $i;
                $alts[] = "val=%d";
            }
        }

        // Create the bar plots
        $keys = array_keys($mydata);
        $values = array_values($mydata);
        // $i = 0;
        foreach ($values as $key => $var) {
            $b1plot[$key] = new BarPlot($var);
            if (isset($this->color[$key])) {
                $b1plot[$key]->SetFillColor($this->color[$key]);
            }
            $b1plot[$key]->SetCSIMTargets($targ, $alts);
            $b1plot[$key]->SetLegend($this->myDecode($keys[$key]));
        }
        // Create the grouped bar plot
        //$abplot = new AccBarPlot(array($b1plot,$b2plot));
        $abplot = new AccBarPlot($b1plot);

        $abplot->SetShadow();
        $abplot->value->Show();
        // ...and add it to the graPH
        $this->graph->Add($abplot);
        if ($this->title) {
            $this->graph->title->Set($this->myDecode($this->title));
        }
        if ($this->xaxis) {
            $this->graph->xaxis->title->Set($this->myDecode($this->xaxis));
        }
        if ($this->yaxis) {
            $this->graph->yaxis->title->Set($this->myDecode($this->yaxis));
        }
        if ($this->description) {
            $this->graph->xaxis->SetTickLabels($this->myDecode($this->description));
        }
        $this->graph->legend->Pos($this->legend_H, $this->legend_V, "center", "left");
        // Send back the HTML page which will call this script again
        // to retrieve the image.
    }

    /*
     * 柱状图和线图混合图
     * @unfinished
     */

    private function bar_line($bar_data = array(), $line_data = array()) {

        if (!empty($bar_data)) {
            foreach ($bar_data as $mkey => $mvar) {
                for ($i = 0; $i < count($bar_data[$mkey]); $i++) {
                    $targ[] = "bar_clsmex2.php#" . $i;
                    $alts[] = "val=" . $this->mark_bar_H;
                }
            }

            // Create the linear error plot

            $keys = array_keys($bar_data);
            $values = array_values($bar_data);
            foreach ($values as $key => $var) {
                $b1plot[$key] = new BarPlot($var);
                if (isset($this->color[$key])) {
                    $b1plot[$key]->SetFillColor($this->color[$key]);
                }
                $b1plot[$key]->SetCSIMTargets($targ, $alts);
                $b1plot[$key]->SetLegend($this->myDecode($keys[$key]));
            }
            $gbarplot = new GroupBarPlot($b1plot);
            $this->graph->Add($gbarplot);
        }

        if (!empty($line_data)) {
            $this->graph->SetYScale(0, 'int');
            $this->graph->ynaxis[0]->SetColor('black');
            //$this->graph->ynaxis[0]->title->Set($this->myDecode($xaxis));
            $this->graph->ynaxis[0]->title->SetFont(FF_SIMSUN, FS_BOLD, 10);
            //$this->graph->xaxis->SetTickLabels($description);
            $this->graph->ynaxis[0]->title->SetColor('black');
            $this->graph->ynaxis[0]->SetLabelFormat($this->mark_line_V);
            $keys = array_keys($line_data);
            $values = array_values($line_data);
            foreach ($values as $k => $v) {
                $l1plot[$k] = new LinePlot($v);
                if (isset($this->color_line[$k])) {
                    $l1plot[$k]->SetColor($this->color_line[$k]);
                }
                $l1plot[$k]->SetWeight(5);
                //$l1plot->SetLegend("Prediction");
                $l1plot[$k]->SetLegend($this->myDecode($keys[$k]));
                $l1plot[$k]->mark->SetType(MARK_DIAMOND);
                $l1plot[$k]->mark->SetWidth(8);
                $l1plot[$k]->mark->SetFillColor($this->color_line[$k]);
                $l1plot[$k]->SetCSIMTargets($targ, $alts);
                //Center the line plot in the center of the bars
                $l1plot[$k]->SetBarCenter();
                $this->graph->AddY(0, $l1plot[$k]);
            }
        }
        if ($this->title) {
            $this->graph->title->Set($this->myDecode($this->title));
        }
        if ($this->xaxis) {
            $this->graph->xaxis->title->Set($this->myDecode($this->xaxis));
        }
        if ($this->yaxis) {
            $this->graph->yaxis->title->Set($this->myDecode($this->yaxis));
        }
        if ($this->description) {
            $this->graph->xaxis->SetTickLabels($this->myDecode($this->description));
        }
    }

    /**
     * 将utf8转变为GBK
     * @param type $str
     * @return type
     */
    private function myDecode($str) {
        if (!is_array($str)) {
            return iconv("UTF-8", "GBK", $str);
        } else {
            $tmp = array();
            foreach ($str as $key => $var) {
                $tmp[] = iconv("UTF-8", "GBK", $var);
            }
            //var_dump($tmp);
            return $tmp;
        }
    }

}

//$draw = new mygraph('line');
//$draw->draw(array('我' => array(1, 2, 3, 4, 5, 6), 'Android' => array(1, 2, 3, 4, 5), 'Symbian' => array(1, 2, 3, 4, 5), 'IOS' => array(1, 2, 3, 4, 5), 'PC' => array(1, 2, 3, 4, 5)));