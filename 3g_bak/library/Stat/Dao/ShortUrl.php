<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class Stat_Dao_ShortUrl extends Common_Dao_Base {
    protected $_name    = "stat_short_url";
    protected $_primary = "id";
    protected $_adapter = 'stat';
}