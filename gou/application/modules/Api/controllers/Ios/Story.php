<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * @author tiansh
 *
 */
class Ios_StoryController extends Api_BaseController {
	
	public $perpage = 10;
	public $actions = array(
				'indexUrl'=>'/api/story/index',
				'detailUrl'=>'/story/detail',
	          'detailUrlComment'=>'/story/item',
				'tjUrl'=>'/index/tj'
	);
	public $cacheKey = 'Ios_Story_index';
	public $versionName = 'Story_Version';
    
	
	/**
	 * 列表
	 */
    public function indexAction()
    {
        $version = intval($this->getInput('data_version'));
        $uid = strval($this->getInput('uid'));
        $server_version = Gou_Service_Config::getValue($this->versionName);
        if ($version >= $server_version) {
            if(Common::getIosClientVersion() > 110) {
                $this->emptyOutput(0, '');
            }else{
                $this->emptyOutput(-1, '');
            }
        }

        $page = intval($this->getInput('page'));
        if ($page < 1) $page = 1;

        $authors = Gou_Service_UserAuthor::getAuthors();
        $authors = Common::resetKey($authors, 'id');

        //shops
        $condition['start_time'] = array('<=',time());
        $condition['status'] = 1;

        list($total, $data) = Gou_Service_Story::getList($page, $this->perpage, $condition,  array('recommend'=>'DESC','sort'=>'DESC','start_time'=>'DESC', 'id'=>'DESC'));
        if($total==0){
            $this->output(0, '', array('list' => array(), 'hasnext' => false, 'curpage' => 1,'version'=>$server_version));
        }
        $fid =array_keys(Common::resetKey($data,'id'));
        $fav = User_Service_Favorite::getStoryListByUid($uid,$fid);


        $webroot = Common::getWebRoot();
        $staticroot = Common::getAttachPath();

        $result = array();
        foreach ($data as $k=>$v) {
            $action = $this->actions['detailUrl'];
            if(Common::getIosClientVersion() > 111) {
                $action = $this->actions['detailUrlComment'];
            }
              $result[$k]['id'] = $v['id'];
              $result[$k]['title'] = html_entity_decode($v['title']);
              if($v['img']) $result[$k]['img'] = sprintf('%s%s', $staticroot, $v['img']);
              $result[$k]['summary'] = html_entity_decode($v['summary']);
              $result[$k]['praise'] = Common::parise($v['praise']);
              $result[$k]['favorite'] = Common::parise($v['favorite']);
              $result[$k]['like'] = Common::parise(sprintf('%s',$v['favorite']+$v['praise']));
              $result[$k]['comment'] = Common::parise($v['comment']);
              $result[$k]['recommend'] = $v['recommend'] ? true : false;
              $result[$k]['is_favorite'] = false;
              $result[$k]['author'] = $authors[$v['author_id']]['nickname'];
              $result[$k]['avatar'] = sprintf('%s%s', $staticroot, $authors[$v['author_id']]['avatar']);
              $result[$k]['url'] = $webroot . $action . '?id=' . $v['id'] . '&uid=' . $uid;
              $result[$k]['start_time'] = Gou_Service_Story::fmtTime($v['start_time']);
                if (!empty($fav[$v['id']])) {
                    $result[$k]['fav_id'] = $fav[$v['id']]['id'];
                    $result[$k]['is_favorite'] = true;
                  }
        }

        $pager = ($page - 1) * $this->perpage;
        $hasnext = (ceil((int)$total / $this->perpage) - ($page)) > 0 ? true : false;
        $this->output(0, '', array('list' => $result, 'hasnext' => $hasnext, 'curpage' => $page,'version'=>$server_version));
    }

    
    /**
     * 点赞
     */
    public function praiseAction(){
        $id=intval($this->getInput('id'));

        $type=-1;
        if($this->getInput('type'))$type=1;
        $ret=Gou_Service_Story::praise($id,$type);
        if($ret){
            Gou_Service_Config::setValue($this->versionName, Common::getTime());
            $this->output(0,'success',array('type'=>$type));
        }
        $this->output(1,'failure',array());
    }

    
    /**
     * 添加收藏 
     */
    public function addfavAction(){

        $data['item_id'] = intval($this->getInput('id'));
        $data['uid'] = strval($this->getInput('uid'));
        $data['type'] = 1;
        $data['channel_id'] = 1;

        $story = Gou_Service_Story::get($data['item_id']);
        if(!$story) $this->output(1,'记录不已删除或不存在，无法收藏',array('item_id'=>$data['item_id']));

        //add user
//        if($data['uid']) {
//            $user = User_Service_Uid::getBy(array('uid'=>$data['uid']));
//            if(!$user) User_Service_Uid::addUser(array('uid'=>$data['uid']));
//        }
        

        $item = User_Service_Favorite::getOneByParams($data['uid'],$data['item_id'],$data['type']);
        if($item) $this->output(1,'您已收藏，请勿重复收藏',array('id'=>$item['id']));
        
        $ret = User_Service_Favorite::addFavorite($data);
        if($ret){
            Gou_Service_Config::setValue($this->versionName, Common::getTime());
            Gou_Service_Story::updateFavorite($story['id']);
            
            $this->output(0,'收藏成功',array('fav_id'=>$ret));
        }
        $this->output(1,'收藏出错',$data);
    }
}
