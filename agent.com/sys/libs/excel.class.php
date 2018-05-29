<?php
/**
* Simple REAL ExcelXLSFile Generator - Format of created files: Excel 97-2003
* Get a real XLS file format instead of xml, csv or any other 'fake' excel format.
* Tested with Office 2003, Office 2007, Office 2010, OpenOffice 3x.
* Only two cell types supported at the moment - String and numeric.
*
* Thanks goes out to http://www.appservnetwork.com/modules.php?name=News&file=article&sid=8
* where i found the pack() solution.
*
* Feel free to share this class to everyone.
* Released under GNU GPL License - http://www.gnu.org/copyleft/gpl.html
*
* @author Roland Eigelsreiter
* @version 1.1
* @required PHP 5.x, Nothing else
* @changelog
*   1.1 = Added file encoding
*   1.0 beta = Initial Release
*/

class excel{

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
    public static function arrayToFile(array &$array, $filename="excel.xls", $download=TRUE, $encoding = "GBK"){
        $instance = new self;
        $instance->BOF();
        $rowNr = 0;
        foreach($array as $row){
            $cellNr = 0;
            foreach($row as $value){
                /*$enc = mb_detect_encoding($value ,"ASCII,JIS,UTF-8,EUC-JP,SJIS, ISO-8859-1");
                if($enc != $encoding){
                    $value = mb_convert_encoding($value, $encoding, $enc);
                }*/
				$value=iconv('UTF-8','GBK',$value);
                $instance->Write($rowNr, $cellNr, $value);
                $cellNr++;
            }
            $rowNr++;
        }
        $instance->EOF();
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
        $this->data .= pack("sssss", 0x203, 14, $row, $col, 0x0).pack("d", $value);
    }
}
