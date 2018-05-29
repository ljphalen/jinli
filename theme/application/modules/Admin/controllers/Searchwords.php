<?php

/*
 * 热词搜索;
 *
 */

if (!defined('BASE_PATH')) exit('Access Denied!');

class SearchwordsController extends Admin_BaseController {

    public function indexAction() {

        $res = Theme_Service_Searchwords::getlistWords($where);

        $this->assign("words", $res);
        $this->assign("meunOn", "zt_searchwords");
    }

    public function addWordsAction() {
        $words = $this->getPost("name");
        $where = "1 order by sort DESC limit 1";
        $sort = Theme_Service_Searchwords::getlistWords($where)[0]['sort'];
        $data = array("words" => $words, "writetime" => time(), "sort" => $sort + 1);
        $res = Theme_Service_Searchwords::addWords($data);
        if ($res) echo json_encode(array("opt" => 'insert'));
        exit;
    }

    public function editWordsAction() {
        $words = $this->getPost("name");
        // $sort = $this->getPost("sort");
        $id = $this->getPost("id");
        $res = Theme_Service_Searchwords::editWords($id, $words);
        echo $res;
        exit;
    }

    public function editSortAction() {
        $sort = $this->getPost("sort");
        $id = $this->getPost("id");
        $res = Theme_Service_Searchwords::editsort($id, $sort);
        echo $res;
        exit;
    }

    public function delWordsAction() {
        $id = $this->getPost("id");
        $res = Theme_Service_Searchwords::delWords($id);
        echo $res;
        exit;
    }

}
