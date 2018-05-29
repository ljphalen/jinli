<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class XsController extends Api_BaseController {

    public function searchAction() {
        $file = "/Users/sam/xunsearch/sdk/php/app/demo.ini";
        $xs = new XS_Main($file);

        $doc = new XS_Document(array(  //  创建 XSDocument
            "pid" => 123,  //  主键字段，必须指定
            "subject" => " 测试文档标题", "message" => " 测试文档内容",
            "chrono" => time()
        ));

        $xs->getIndex()->add($doc);

        $search = $xs->search;
        $docs = $search->setQuery("测试")->search();
        print_r($docs);
//
//
//        $docs = $xs->search->search('Hello');
//        print_r($docs);
    }
}