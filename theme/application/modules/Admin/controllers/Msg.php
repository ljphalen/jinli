<?php

  if ( ! defined('BASE_PATH')) exit('Access Denied!');

  /**
   *
   * Enter description here ...
   * @author rainkid
   *
   */
  class MsgController extends Admin_BaseController {

      public $actions = array(
              'listUrl' => '/Admin/Msg/index' ,
              'addUrl' => '/Admin/Msg/add' ,
              'addPostUrl' => '/Admin/Msg/add_post' ,
              'editUrl' => '/Admin/Msg/edit' ,
              'editPostUrl' => '/Admin/Msg/edit_post' ,
              'deleteUrl' => '/Admin/Msg/delete' ,
              'pushUrl' => '/Admin/Msg/push' ,
      );
      public $perpage = 20;

      /**
       *
       * Enter description here ...
       */
      public function indexAction() {
          $page = intval($this -> getInput('page'));
          $perpage = $this -> perpage;
          list($total , $msgs) = Theme_Service_Msg::getList($page , $perpage);

          $this -> assign('msgs' , $msgs);
          $this -> assign('total' , $total);
          $this -> assign('pager' , Common::getPages($total , $page , $perpage , $this -> actions['listUrl'] . '/?'));
      }

      /**
       *
       * Enter description here ...
       */
      public function editAction() {
          $id = $this -> getInput('id');
          $info = Theme_Service_Msg::getMsg(intval($id));
          $this -> assign('info' , $info);
      }

      /**
       *
       * Enter description here ...
       */
      public function addAction() {

      }

      /**
       *
       * Enter description here ...
       */
      public function add_postAction() {
          list($total , ) = Theme_Service_Msg::getList(1 , 1);
          if ($total) $this -> output(-1 , '当前已有消息内容');
          $info = $this -> getPost(array('title' , 'content'));
          $info = $this -> _cookData($info);
          $result = Theme_Service_Msg::addMsg($info);
          if ( ! $result) $this -> output(-1 , '操作失败');
          $this -> output(0 , '操作成功');
      }

      /**
       *
       * Enter description here ...
       */
      public function edit_postAction() {
          $info = $this -> getPost(array('id' , 'title' , 'content'));
          $info = $this -> _cookData($info);
          $ret = Theme_Service_Msg::updateMsg($info , intval($info['id']));
          if ( ! $ret) $this -> output(-1 , '操作失败');
          $this -> output(0 , '操作成功.');
      }

      /**
       *
       * Enter description here ...
       */
      private function _cookData($info) {
          if ( ! $info['title']) $this -> output(-1 , '标题不能为空.');
          if ( ! $info['content']) $this -> output(-1 , '内容不能为空.');
          return $info;
      }

      /**
       *
       * Enter description here ...
       */
      public function deleteAction() {
          $id = $this -> getInput('id');
          $info = Theme_Service_Msg::getMsg($id);
          if ($info && $info['id'] == 0) $this -> output(-1 , '无法删除');

          $result = Theme_Service_Msg::deleteMsg($id);
          if ( ! $result) $this -> output(-1 , '操作失败');
          $this -> output(0 , '操作成功');
      }

      /**
       * pub msg
       */
      public function pushAction() {
          $id = $this -> getInput('id');

          $ret = json_decode($ret , true);

          $info = Theme_Service_Msg::getMsg(intval($id));
          if ( ! $info || $info['id'] == 0) $this -> showMsg(-1 , 'push消息不存在.');
          //if($info['status'] == 1)  $this->showMsg(-1, '该消息正在发送中.');
          //@
          $queue = Common::getQueue();
          $queue -> noRepeatPush('push_msg' , $info['id']);
          $this -> showMsg(-1 , '发送中...');
      }

  }
