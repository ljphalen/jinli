<?php

class Util_DownFile_Test{

    /**
    * The data string
    *
    * @var string
    */
    private $data = NULL;

    /**
    * Create a XLS file from a array
    *
    * @param array $array
    *   Required format:
    *       array(
    *           0 => array("FirstName", "LastName", "..."),
    *           1 => array("Captain", "Jack", "Sparrow"),
    *           2 => ....
    *       )
    * @param string $filename The filename to store or download
    *   If not download this is the path to the file
    * @param bool $download Flag if open a download dialog or save to disk
    * @param string $encoding Change the file encoding to UTF-8 if you want it
    */
    public static function arrayToFile(array &$array, $filename="excel.xls", $download=TRUE, $encoding = "UTF-8"){
        $instance = new self;
        $instance->BOF();
        $rowNr = 0;
        foreach($array as $row){
            $cellNr = 0;
            foreach($row as $value){
               /*  $enc = mb_detect_encoding($value ,"ASCII,JIS,UTF-8,EUC-JP,SJIS, ISO-8859-1");
                if($enc != $encoding){
                    $value = mb_convert_encoding($value, $encoding, $enc);
                } */
				//$value=mb_convert_encoding($value,'gb2312','UTF-8');
            	$value=iconv('UTF-8','GBK',$value);
                $instance->Write($rowNr, $cellNr, $value);
                $cellNr++;
            }
            $rowNr++;
        }
        $instance->EOF();
       /*   echo $instance->data;
        exit;  */
        if($download === false){
            file_put_contents($filename, $instance->data);
        }else{
        	
	         header("Content-Type: application/force-download");
            header("Content-Type: application/octet-stream");
            header("Content-Type: application/download");
            header("Content-Disposition: attachment; filename=\"$filename\" ");
            header("Content-Transfer-Encoding: binary ");
            header("Content-Length: ".strlen($instance->data));
            echo $instance->data;
            
        	//header('Content-Encoding: none');
        	//header("content-Type:text/html; charset=gbk");
        	//header('Content-Transfer-Encoding: binary');
        	//header('Content-Type: application/octet-stream' );
        	//header('Content-Length: ' . strlen($instance->data));
        	//header('Content-Disposition: attachment; filename=' . $filename);
        	
        	/* header('Content-Encoding: none');
            header("Content-Type: application/force-download");
            header("Content-Type: application/octet-stream");
            header("Content-Type: application/download");
            header("Content-Disposition: attachment; filename=\"$filename\" ");
            header("Content-Transfer-Encoding: binary ");
            header("Content-Length: ".strlen($instance->data)); */
            echo $instance->data;
        }
    }

    /**
    * Append begin of file string
    */
    private function BOF() {
        $this->data .= pack("ssssss", 0x809, 0x8, 0x0, 0x10, 0x0, 0x0);
    }

    /**
    * Append end of file string
    */
    private function EOF() {
        $this->data .= pack("ss", 0x0A, 0x00);
    }

    /**
    * Append a cell to data - Automaticly check for number or string cell
    *
    * @param int $row
    * @param int $col
    * @param string $value
    */
    private function Write($row, $col, $value) {
        if(is_numeric($value)){
            $this->WriteNumber($row, $col, $value);
            return;
        }
        $length = strlen($value);
        $this->data .= pack("ssssss", 0x204, 8 + $length, $row, $col, 0x0, $length).$value;
    }

    /**
    * Append a number cell
    *
    * @param int $row
    * @param int $col
    * @param string $value
    */
    private function WriteNumber($row, $col, $value) {
        $this->data .= pack("sssss", 0x203, 14, $row, $col, 0x0).pack("A", $value);
    }
}
$content = array(0=>array('标题1','标题2','标题3','标题4','标题5'),
		         1=>array('test1','test1','test1','test1','test1'),
		         2=>array('test2','test3','test4','tes5','test6'),
		         3=>array('test1','test1','test1','test1','test1'),
		        );
Util_DownFile_Test::arrayToFile($content,'test.xls');