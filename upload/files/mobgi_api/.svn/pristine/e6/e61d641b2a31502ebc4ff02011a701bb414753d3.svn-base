<?php
/**
 * Example Database connection settings and DB relationship mapping
 * $dbmap[Table A]['has_one'][Table B] = array('foreign_key'=> Table B's column that links to Table A );
 * $dbmap[Table B]['belongs_to'][Table A] = array('foreign_key'=> Table A's column where Table B links to );
 

//Food relationship
$dbmap['Food']['belongs_to']['FoodType'] = array('foreign_key'=>'id');
$dbmap['Food']['has_many']['Article'] = array('foreign_key'=>'food_id');
$dbmap['Food']['has_one']['Recipe'] = array('foreign_key'=>'food_id');
$dbmap['Food']['has_many']['Ingredient'] = array('foreign_key'=>'food_id', 'through'=>'food_has_ingredient');

//Food Type
$dbmap['FoodType']['has_many']['Food'] = array('foreign_key'=>'food_type_id');

//Article
$dbmap['Article']['belongs_to']['Food'] = array('foreign_key'=>'id');

//Recipe
$dbmap['Recipe']['belongs_to']['Food'] = array('foreign_key'=>'id');

//Ingredient
$dbmap['Ingredient']['has_many']['Food'] = array('foreign_key'=>'ingredient_id', 'through'=>'food_has_ingredient');
 
*/ 

//$dbconfig[ Environment or connection name] = array(Host, Database, User, Password, DB Driver, Make Persistent Connection?);
/**
 * Database settings are case sensitive.
 * To set collation and charset of the db connection, use the key 'collate' and 'charset'
 * array('localhost', 'database', 'stephen', '1234', 'mysql', true, 'collate'=>'utf8_unicode_ci', 'charset'=>'utf8'); 
 */

/* $dbconfig['dev'] = array('localhost', 'database', 'stephen', '1234', 'mysql', true);
 * $dbconfig['prod'] = array('localhost', 'database', 'stephen', '1234', 'mysql', true);
 */ 

//$dbconfig['prod'] = array('210.14.152.112:23306', 'mobgi', 'eric', 'XqfX29pXso', 'mysql', false,'charset'=>'utf8');
//$dbconfig['admin'] = array('210.14.152.112:23306', 'mobgi_backend', 'eric', 'XqfX29pXso', 'mysql', false,'charset'=>'utf8');
$dbconfig['prod'] = array('192.168.0.14', 'mobgi_api', 'stephen', '123456', 'mysql', false,'charset'=>'utf8');
$dbconfig['dev'] = array('192.168.0.14', 'mobgi_api', 'stephen', '123456', 'mysql', false,'charset'=>'utf8');
$dbconfig['admin'] = array('192.168.0.14', 'mobgi_backend', 'stephen', '123456', 'mysql', false,'charset'=>'utf8');
$dbconfig['statis'] = array('192.168.0.14', 'mobgi_stat', 'stephen', '123456', 'mysql', false,'charset'=>'utf8');
$dbconfig['user_retention'] = array('113.107.201.168:65321', 'ids_report', 'mogi', 'bWOE6mWk2u0fE', 'mysql', false,'charset'=>'utf8');
$dbconfig['www'] = array('192.168.0.14', 'mobgi_www', 'stephen', '123456', 'mysql', false,'charset'=>'utf8');
$dbconfig['cheat'] = array('192.168.0.14', 'mobgi_cheat' , 'stephen', '123456', 'mysql', false, 'charset'=>'utf8');
$dbconfig['ads'] = array('192.168.0.14', 'mobgi_ads' , 'stephen', '123456', 'mysql', false, 'charset'=>'utf8');
$dbconfig['rtb'] = array('192.168.0.14', 'mobgi_rtb' , 'stephen', '123456', 'mysql', false, 'charset'=>'utf8');
$dbconfig['implantable'] = array('192.168.0.14', 'implantable_advert', 'stephen', '123456', 'mysql', false,'charset'=>'utf8');
?>
