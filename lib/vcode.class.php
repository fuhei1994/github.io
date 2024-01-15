<?php
/**
 * 验证码类
 * 创建：2022-10-15
 * 更新：2022-10-15
 */
class vcode {
	private $width;   //宽
	private $height;  //高
	private $num;	  //数量
	private $code;    //验证码
	private $img;     //图像的资源
	private $border;  //边框
	private $bgcolor; //背景颜色
	function __construct($width=80, $height=25, $num=4, $border=false, $bgcolor=false) {
		$this->width = $width;
		$this->height = $height;
		$this->num = $num;
		$this->border = $border;
		$this->bgcolor = $bgcolor;
		$this->code = $this->createCode(); //调用自己的方法
	}
	//获取字符的验证码
	function getCode() {
		return $this->code;
	}
	//输出图像
	function outImg() {
		$this->createBack();
		$this->outString();
		$this->setDisturbColor();
		$this->printImg();
	}
	//创建背景
	private function createBack() {
		//创建资源
		$this->img = imagecreatetruecolor($this->width, $this->height);
		//设置随机背景颜色
		if($this->bgcolor){
			$bg = explode(',',$this->bgcolor);
			$bgcolor =  imagecolorallocate($this->img, $bg[0], $bg[1], $bg[2]); 
		}else{
			$bgcolor =  imagecolorallocate($this->img, rand(225, 255), rand(225, 255), rand(225, 255)); 
		}
		//设置背景填充
		imagefill($this->img, 0, 0, $bgcolor);
		//画边框
		if($this->border){
			$bordercolor =  imagecolorallocate($this->img, 0, 0, 0);
			imagerectangle($this->img, 0, 0, $this->width-1, $this->height-1, $bordercolor);
		}
	}
	//画字
	private function outString() {
		for($i=0; $i<$this->num; $i++) {
			$color= imagecolorallocate($this->img, rand(0, 128), rand(0, 128), rand(0, 128));
			$x = 3+($this->width/$this->num)*$i;
			$y = rand(0, 8);
			imagechar($this->img, 5, $x, $y, $this->code[$i], $color);
		}
	}
	//设置干扰元素
	private function setDisturbColor() {
		for($i=0; $i<30; $i++) { //点
			$color= imagecolorallocate($this->img, rand(0, 255), rand(0, 255), rand(0, 255)); 
			imagesetpixel($this->img, rand(1, $this->width-2), rand(1, $this->height-2), $color);
		}
		for($i=0; $i<6; $i++) { //线
			$color= imagecolorallocate($this->img, rand(0, 255), rand(0, 128), rand(0, 255)); 
			imagearc($this->img,rand(-10, $this->width+10), rand(-10, $this->height+10), rand(30, 300), rand(30, 300), 55,44, $color);
		}
	}
	//输出图像
	private function printImg() {
		if (imagetypes() & IMG_GIF) {
			header('Content-type: image/gif');
			imagegif($this->img);
		} elseif (function_exists('imagejpeg')) {
			header('Content-type: image/jpeg');
			imagegif($this->img);
		} elseif (imagetypes() & IMG_PNG) {
			header('Content-type: image/png');
			imagegif($this->img);
		} else {
			die('此PHP服务器不支持图像');
		}
	}
	//生成验证码字符串
	private function createCode() {
		$codes = '3456789abcdefghijkmnpqrstuvwxyABCDEFGHIJKLMNPQRSTUVWXY';
		$code = '';
		for($i=0; $i < $this->num; $i++) {
			$code .=$codes[rand(0, strlen($codes)-1)];	
		}
		return $code;
	}
	//用于自动销毁图像资源
	function __destruct() {
		imagedestroy($this->img);
	}
}
?>