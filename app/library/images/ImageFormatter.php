<?php 
namespace ImLike\Images;

use \finfo as finfo;

class ImageFormatter{

	private $filepath;
	private $isJpeg;
	private $fullRatio;
	private $ratioOk;

	public function __construct($filepath){
		$this->filepath = $filepath;
		$this->isJpeg = $this->isJpeg();
		if(!$this->isJpeg){
			throw new NotJpegException();
		}
	}

	public function isJpeg(){
		$finfo = new finfo(FILEINFO_MIME);
		$fileInfoString = $finfo->file($this->filepath);
		$isJpeg = false;
		if($fileInfoString !== null){
			$fileInfoArray = explode("; ", $fileInfoString);
			$isJpeg = ($fileInfoArray[0] === "image/jpeg") ? true : false;
		}
		return $isJpeg;
	}

	public function checkRatio(){
		$size = getimagesize($this->filepath);

		$this->fullRatio = $size[0] / $size[1];
		$this->ratioOk = (substr($this->fullRatio, 0, 4) === "0.56") ? true : false;
		return $this->ratioOk;
	}

	public function getImage($type){
		if($type == "post"){
			if($this->checkRatio()){ // ratio ok, just create new image file to strip the tagz;
				$image = imagecreatefromjpeg($this->filepath);

				if(imagejpeg($image, $this->filepath, 90)) return $this->filepath;
				else return "error while creating image";
			}else{
				$image = imagecreatefromjpeg($this->filepath);
				$size = getimagesize($this->filepath);
				$imageResized = imagecreatetruecolor(480, 800);
				if(imagecopyresized($imageResized, $image, 0, 0, 0, 0, 480, 800, $size[0], $size[1]) && imagejpeg($imageResized, $this->filepath, 90)) return $this->filepath;
				else return "error while creating image";
			}
		}else if ($type == "profileImage"){
			$image = imagecreatefromjpeg($this->filepath);
			$size = getimagesize($this->filepath);
			$imageResized = imagecreatetruecolor(300, 300);
			if(imagecopyresized($imageResized, $image, 0, 0, 0, 0, 300, 300, $size[0], $size[1]) && imagejpeg($imageResized, $this->filepath, 90)) return $this->filepath;
			else return "error while creating image";
		}
	}

}