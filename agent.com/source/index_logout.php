<?php
session_start();
unset($_SESSION['userinfo']);
jump_url('退出成功！','/index.php?ac=login');