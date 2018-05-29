<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class ContactController extends Game_BaseController {

    /**
     * 
     */
    public function indexAction() {
    }
    
    public function clientAction() {
        $game = array();
        if(ENV == 'product'){
            $game = Resource_Service_GameData::getGameAllInfo(117);
        } else {
            $game = Resource_Service_GameData::getGameAllInfo(66);
        }
        $this->assign('game', $game);
    }
}