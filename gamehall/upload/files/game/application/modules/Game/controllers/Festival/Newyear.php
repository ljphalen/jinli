<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * 2015新春
 * @author fanch
 *
 */
class Festival_NewyearController extends Game_BaseController{
	
	public function indexAction(){
		$intersrc = $this->getInput('intersrc');//来源
		$source = $this->getSource();
		$webRoot = Common::getWebRoot();
		$gameId = 117;
		$game = Resource_Service_GameData::getGameAllInfo($gameId);
		$gameData = array(
				'title'=>'游戏大厅',
				'url' => "http://game.gionee.com/client/index/detail?id=$gameId&t_bi=$source&intersrc=".($intersrc ? $intersrc:'xc1'),
				'gameid' => $gameId ,
				'downurl' => Common::monTjurl($webRoot .'/game/Festival_Newyear/tj', "{$game['link']}?gameid=117&t_bi=$source", $intersrc ? $intersrc:'xc1'),
				'packagename' => $game['package'],
				'filesize' => $game['size'],
				'sdkinfo' => 'Android1.6',
    			'resolution' => '240*320-1080*1920'
		);
		$prizeUrl = "http://game.gionee.com/client/prize/index?t_bi=$source&intersrc=" . ($intersrc ? $intersrc : 'xc1');
		$hdUrl = "http://game.gionee.com/game/news/detail/?id=2533&t_bi=$source&intersrc=" . ($intersrc ? $intersrc : 'xc2');
		$bbsUrl = "http://bbs.amigo.cn/forum.php?mod=forumdisplay&fid=150&fromapp=game";

		$this->assign('infoPage', implode(',', $gameData));
		$this->assign('prizeUrl', $prizeUrl);
		$this->assign('hdUrl', $hdUrl);
		$this->assign('bbsUrl', $bbsUrl);
		$this->assign('source', $source);
	}
	
	public function tjAction(){
	 $url = html_entity_decode(html_entity_decode($this->getInput('_url')));
	 $this->redirect($url);
	}
	
	/**
	 * 点击量
	 */
	public function tjjAction(){
		exit;
	}
	
}