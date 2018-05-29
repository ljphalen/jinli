<?php
if (!defined('SROOT')) {
	echo '<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">';
	echo '<html><head>';
	echo '<title>404 Not Found</title>';
	echo '</head><body>';
	echo '<h1>Not Found</h1>';
	echo '<p>The requested URL '. $_SERVER['REQUEST_URI'] .' was not found on this server.</p>';
	echo '<hr>';
	echo '<address>Apache/2.2.17 (Ubuntu) Server at '. $_SERVER['HTTP_HOST'] .' Port '. $_SERVER['SERVER_PORT'] .'</address>';
	echo '</body></html>';
	exit();
}
?>