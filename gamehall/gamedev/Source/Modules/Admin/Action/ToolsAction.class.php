<?php
class ToolsAction extends SystemAction
{
	function rebuildCache()
	{
		$this->deleteDir(DATA_HOME.'/Runtime/');
		$this->success('操作成功');
	}
	
	function deleteDir($dir) {
		if (is_dir ( $dir )) {
			if (rmdir ( $r )) return true;
			if ($dp = opendir ( $dir )) {
				while ( ($file = readdir ( $dp )) != false ) {
					$r = sprintf ( "%s/%s", $dir, $file );
					if (is_dir ( $r )) {
						if ($file != '.' && $file != '..')
							if (! rmdir ( $r ))
								self::deleteDir ( $r );
					} else {
						unlink ( $r );
					}
				}
				closedir ( $dp );
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
}