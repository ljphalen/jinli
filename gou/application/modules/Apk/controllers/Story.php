<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class StoryController extends Apk_BaseController {


	public function detailAction() {
        
        $id = intval($this->getInput('id'));
        
        $data = Gou_Service_Story::get($id);
        Gou_Service_Story::updateTJ($data['id']);

        $author = $author_temp = array('nickname'=>'购物大厅网友', 'avatar'=>'');
        if($data['author_id']){
            $author_temp = Gou_Service_UserAuthor::get($data['author_id']);
        }elseif($data['uid']){
            $author_temp = User_Service_Uid::getByUid($data['uid']);
            $author_temp['nickname'] = $author_temp['nickname']?$author_temp['nickname']:'购物大厅网友';
        }
        $author = array_merge($author, $author_temp);

        $staticroot = Common::getAttachPath();
        $data['author']=$author['nickname'];
        $data['avatar']=$staticroot.$author['avatar'];

        $this->assign('title', html_entity_decode($data['title']));
		$this->assign('data', $data);
	}

	public function itemAction() {

        $id = intval($this->getInput('id'));

        $data = Gou_Service_Story::get($id);
        Gou_Service_Story::updateTJ($data['id']);

        $author = $author_temp = array('nickname'=>'购物大厅网友', 'avatar'=>'');
        if($data['author_id']){
            $author_temp = Gou_Service_UserAuthor::get($data['author_id']);
        }elseif($data['uid']){
            $author_temp = User_Service_Uid::getByUid($data['uid']);
            $author_temp['nickname'] = $author_temp['nickname']?$author_temp['nickname']:'购物大厅网友';
        }
        $author = array_merge($author, $author_temp);

        $staticroot = Common::getAttachPath();
        $data['author']=$author['nickname'];
        $data['avatar']=$staticroot.$author['avatar'];

        $this->assign('title', html_entity_decode($data['title']));
		$this->assign('data', $data);
	}
}
