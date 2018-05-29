<?php
class SyncModel extends RelationModel
{
	protected $trueTableName = 'apks';
	
	//接口URL
	CONST API_URL_GAME_CATE = "http://game.gionee.com/api/game/attribute?sid=1";
	CONST API_URL_FEE_TYPE = "http://game.gionee.com/api/game/attribute?sid=3";
	CONST API_URL_RESO = "http://game.gionee.com/api/game/attribute?sid=4";
	CONST API_URL_LABEL = "http://game.gionee.com/api/game/attribute?sid=8";
	CONST API_URL_LABEL_CHILD = "http://game.gionee.com/api/game/label?lid=";
}