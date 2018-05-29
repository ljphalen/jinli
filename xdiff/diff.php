<?php 
ini_set('memory_limit', '700M');

if (xdiff_file_bdiff("old.apk", "new.apk", "data/apk.patch")) {
	echo "bdiff success.\n";
} else {
	echo "bdiff faild.\n";
}

/*if (xdiff_file_bpatch("old.apk", "data/apk.patch", "data/a.new.apk")) {
	echo "bpatch success.\n";
} else {
	echo "bpatch faild.\n";
}*/
?>
