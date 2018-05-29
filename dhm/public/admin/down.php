<?php
$docs_root = dirname(dirname(__DIR__));
$file = $docs_root."/docs/gou.zip";
$x = is_file($file);
header("Content-type: application/octet-stream");
header('Content-Disposition: attachment; filename="' . basename($file) . '"');
header("Content-Length: " . filesize($file));
readfile($file);