<?php
$mysqli = new mysqli ( '127.0.0.1', 'root', '123456', 'game' );
/* check connection */
if ($mysqli->connect_errno) {
	echo $callback.'({"result": "Database connect error!!"})';
	return;
}
$tables = $mysqli->query("show tables", '1');
var_dump($tables->fetch_array());
var_dump($tables);

/*  $db = new mysqli;
$db->connect('127.0.0.1', 'root', '123456', 'game');
$db->query("show tables", '1');
swoole_event_add(swoole_get_mysqli_sock($db), function($__db_sock) {
	global $db;
	var_dump($__db_sock);
	$res = $db->reap_async_query();
	var_dump($res->fetch_all(MYSQLI_ASSOC));
	$db->query("show tables", MYSQLI_ASYNC);
	sleep(1);
});
echo "Finish\n"; */