<?php

class ImageRead {
	public $imgsrc;
	public $imgdata;
	public $imgform;
	public $fileSize;
	public function getdir($source) {
		$this->imgsrc = $source;
	}
	public function img2data() {
		$this->_imgfrom ( $this->imgsrc );
		$this->fileSize = filesize ( $this->imgsrc );
		$handle = fopen ( $this->imgsrc, 'rb' );
		return $this->imgdata = fread ( $handle, $this->fileSize );
	}
	public function data2img() {
		header ( "content-type:{$this->imgform}" );
		echo $this->imgdata;
	
		//echo $this->imgform;
		//imagecreatefromstring($this->imgdata);
	}
	public function _imgfrom($imgsrc) {
		$info = getimagesize ( $imgsrc );
		//var_dump($info);
		return $this->imgform = $info ['mime'];
	}
}

?>