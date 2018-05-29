<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 *
 * 文件操作类
 * @author hu.liaoh
 */
Class Util_System{
    /**
     *
     * 解压文件
     * @param string $package
     * @param string $dir
     */
    function unzipFile( $package , $dir ){
        @ini_set('memory_limit', '256M');
        if( class_exists('ZipArchive')){
            return $this->_unzipFileZiparchive( $package, $dir );
        }
        return $this->_unzipFilePclzip( $package, $dir );
    }

    /**
     *
     * 压缩文件
     * @param String $package 压缩文件路径
     * @param String $packageName 压缩包名称，如:test,压缩为zip压缩包
     * @param String $localname 压缩包根目录文件名
     */
    function zipFile( $package , $packageName, $localname ){
        if( class_exists('ZipArchive') ){
            return $this->_zipFileZiparchive( $package, $packageName.'.zip' , $localname);
        }
        return $this->_zipFilePclzip( $package, $packageName.'.zip' , $localname);
    }

    /**
     *
     * ZipArchive 解压压缩包
     * @param string $package
     * @param string $dir
     */
    function _unzipFileZiparchive( $package, $dir){
        $archive = new ZipArchive();
        $zopen = $archive->open($package, ZipArchive::CHECKCONS);
        if( !$zopen) exit('ZipArchive open failed');
        $list = $archive->extractTo($dir);
        $archive->close();
        return $list;
    }

    /**
     *
     * Ziparchive形式压缩文件
     * @param String $package 压缩文件路径
     * @param String $packageName 压缩包名称，如：test.zip
     * @param String $localname 压缩要根文件夹名称
     */
    function _zipFileZiparchive( $package, $packageName, $localname){
        $archive = new ZipArchive();
        $packageName = $packageName;
        $zopen = $archive->open( $packageName, ZipArchive::CREATE );
        if( !$zopen ) exit('ZipArchive open failed');
        $list = $this->_archiveAddDir( $archive, $package, $localname,$packageName );
        if(!$list) exit('文件压缩失败');
        $archive->close();
        return $list;
    }

    /**
     *
     * ZipArchive添加目录及目录下所有文件
     * @param Object $archive ZipArchive 对象引用
     * @param String $path 文件目录路径
     * @param String $localname 压缩包根目录文件夹名
     * @param String $packageName 压缩包名称
     */
    function _archiveAddDir( $archive, $path, $localname, $packageName) {
        if(!$archive->addEmptyDir($localname)) return false;
        $nodes = glob($path . '/*');
        foreach ($nodes as $node) {
            // see: http://bugs.php.net/bug.php?id=40494
            // and: http://pecl.php.net/bugs/bug.php?id=9443
            if($archive->numFiles % 200 == 0){
                $archive->close();
                if(!$archive->open($packageName, ZipArchive::CREATE)) return false;
            }
            $partes = pathinfo($node);
            if (is_dir($node)) {
                $this->_archiveAddDir($archive, $path."/".$partes["basename"], $localname."/".$partes["basename"], $packageName);
            } else if (is_file($node))  {
                if(!$archive->addFile(str_replace("\\","/",$node), $localname . '/' .$partes['basename'])) return false;
            }
        }
        return true;
    }

    /**
     *
     * PclZip 解压压缩包
     * @param string $package
     * @param string $dir
     */
    function _unzipFilePclzip( $package, $dir ){
        require_once( 'PclZip.php' );
        $archive = new PclZip($package);
        $list = $archive->extract(PCLZIP_OPT_PATH, $dir);
        if (!$list) exit($archive->errorInfo(true));
        return $list;
    }

    /**
     *
     * Pclzip形式压缩文件
     * @param String $package 压缩文件路径
     * @param String $packageName 压缩包名称，如：test.zip
     * @param String $localname 压缩要根文件夹名称
     */
    function _zipFilePclzip( $package, $packageName , $localname){
        require_once( 'PclZip.php' );
        $archive = new PclZip($packageName);
        $remove = $dir = $package;
        if (substr($dir, 1, 1) == ':') $remove = substr($dir, 2);
        $list = $archive->create($dir, PCLZIP_OPT_REMOVE_PATH, $remove, PCLZIP_OPT_ADD_PATH, $localname);
        if ($list == 0) {
            exit("Error : ".$archive->errorInfo(true));
        }
    }

    /**
     *
     * 创建目录，支持递归创建目录
     * @param String $dirName 要创建的目录
     * @param int $mode 目录权限
     */
    function smkdir($dirName, $mode = 0777) {
        $dirs = explode('/', str_replace('\\', '/', $dirName));
        $dir='';
        foreach ($dirs as $part) {
            $dir.=$part.'/';
            if (!is_dir($dir) && strlen($dir)>0){
                if(!@mkdir($dir, $mode)) return false;
                if(!@chmod($dir, $mode)) return false;
            }
        }
        return true;
    }

    /**
     *
     * 创建文件，如果目录不存在，会自动创建目录
     * @param String $filename 文件名称
     * @param Int $mode 文件权限
     */
    function touchFile($filename, $mode = 0777){
        if( !file_exists(dirname($filename)) ) $this->smkdir(dirname($filename));
        if( !touch( $filename) ) return false;
        if( !chmod($filename, $mode) ) return false;
        return true;
    }

    /**
     *
     * 删除目录，支持递归删除多级目录
     * @param String $dir 目录
     */
    function srmdir( $dir ){
        if (!file_exists($dir)) return true;
        if (!is_dir($dir) || is_link($dir)) return unlink($dir);
        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') continue;
            if (!$this->srmdir($dir . "/" . $item)) {
                chmod($dir . "/" . $item, 0777);
                if (!$this->srmdir($dir . "/" . $item)) return false;
            };
        }
        return rmdir($dir);
    }

    /**
     *
     * 拷被文件
     * @param String $src 源目录
     * @param String $dst 目标k目录
     */
    function scopy($src,$dst) {
        $dir = opendir($src);
        if(!$this->smkdir($dst)) return false;
        while(false !== ( $file = readdir($dir)) ) {
            if (( $file != '.' ) && ( $file != '..' )) {
                if ( is_dir($src . '/' . $file) ) {
                    $this->smkdir($dst . '/' . $file);
                    $this->scopy($src . '/' . $file,$dst . '/' . $file);
                }
                else {
                    if(!copy($src . '/' . $file,$dst . '/' . $file)) return false;
                }
            }
        }
        closedir($dir);
        return true;
    }

    /**
     *
     * 获取md5_file文件数组
     * @param String $dir 目录
     * @param String $ext 检查文件后缀名
     * @param Bool $sub 是否检查子目录
     */
    function safefile(&$md5_a, $dir, $ext='', $sub=1){
        $exts = '/(' . $ext . ')$/i';
        $fp = opendir( $dir );
        while($filename = readdir( $fp )){
            $path = $dir.$filename;
            if($filename != '.' && $filename != '..' && (preg_match($exts, $filename ) || $sub && is_dir( $path ))){
                if($sub && is_dir($path)){
                    $this->safefile($md5_a, $path.'/',$ext, $sub);
                } else{
                    $md5_a[$path] = md5_file($path);
                }
            }
        }
        closedir($fp);
    }

    /**
     * 
     * 根据md5文件检查文件
     * @param Array $check 输出
     * @param String $keyword 
     * @param String $dir
     * @param String $sub
     */
    function checkfile( &$check, $keyword, $dir, $sub ){
        $fp = opendir( $dir );
        while($filename = readdir( $fp )){
            $path = $dir . $filename;
            if( $filename != '.' && $filename != '..' ){
                if( is_dir( $path ) ){
                    $sub && $this->checkfile( $check, $keyword, $path . '/', $sub );
                } elseif( preg_match( '/(\.php|\.php3|\.htm|\.js)$/i', $filename ) && filesize( $path ) < 1048576 ){
                    $a = strtolower( readover( $path ) );
                    if( strpos( $a, $keyword ) !== false ){
                        $check[$path] = 1;
                    }
                }
            }
        }
        closedir($fp);
    }
}