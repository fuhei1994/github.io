<?php
/**
 * 操纵文件类
 * 例子：
 * fileUtil::createDir('a/1/2/3');             建立文件夹，建一个a/1/2/3文件夹
 * fileUtil::createFile('b/1/2/3');            建立文件，在b/1/2/文件夹下面建一个3文件
 * fileUtil::createFile('b/1/2/3.exe');        建立文件，在b/1/2/文件夹下面建一个3.exe文件
 * fileUtil::copy('b/1/2/3.exe','b/b/3.exe');  复制文件或整个目录，建立一个b/b文件夹，并把b/1/2文件夹中的3.exe文件复制进去
 * fileUtil::cut('a','b/c');                   剪贴文件夹，建立一个b/c/a文件夹,并把a文件夹下的所有文件剪贴到b/c/a文件夹中
 * fileUtil::cut('a/','b/c');                  剪贴文件夹，建立一个b/c文件夹,并把a文件夹下的所有文件剪贴到b/c文件夹中
 * fileUtil::delete('d');                      删除文件或该目录下的所有文件
 * fileUtil::rename('b/1/2/3.exe','4.exe');    重命名
 */
class fileUtil {
	/**
	 * 新建文件夹
	 * @param string $path
	 * @return bool
	 */
	public function createDir($path) {
		$path = mb_convert_encoding($path,'UTF-8');
		return is_dir($path) ? true : mkdir($path,0777,true);
	}

	/**
	 * 新建文件
	 * @param string $path 
	 * @param bool $overwrite 是否覆盖原文件
	 * @return bool
	 */
	public function createFile($path, $content='', $overwrite = true) {
		$path = mb_convert_encoding($path,'UTF-8');
		//该路径是否存在，不存在则创建
		if(strpos($path,'/') !== false){
			$dir = substr($path,0,strripos($path,'/'));
			if(!is_dir($dir)) mkdir($dir,0777,true);
		}
		return $overwrite || !is_file($path) ? file_put_contents($path, $content) : false;
	}

	/**
	 * 复制
	 * @param string $source 结尾如果为/,复制该文件下的所有文件
	 * @param string $dest
	 * @param bool $overwrite 是否覆盖原文件
	 * @return bool
	 */
	public function copy($source, $dest, $overwrite = true) {
		//该路径是否存在，不存在则创建
		if(substr($dest,-1) === '/' && !is_dir($dest)) mkdir($dest,0777,true);

		//该文件是否存在，存在的话直接复制
		if(is_file($source)){
			if(substr($dest,-1) === '/'){
				$name = substr($source,strripos($source,'/'));
				$dest .= $name;
			}
			if($overwrite || !is_file($dest)) return copy($source, $dest);
		}
		//如果源地址为文件夹，通过递归来实现复制该文件夹下的所有文件夹
		if(is_dir($source)){
			if(substr($source,-1) !== '/'){
				$name = substr($source,strripos($source,'/'));
				$dest .= $name;
			}
			if($source == $dest) return true;
			function callFunc($source, $dest, $overwrite){
				$dir = opendir($source);
				if(!is_dir($dest)) mkdir($dest,0777,true);
				while(false !== ( $file = readdir($dir)) ) {
					$s = $source . '/' . $file;
					$d = $dest . '/' . $file;
					if (( $file != '.' ) && ( $file != '..' )) {
						if( is_dir($s) ) {
							callFunc($s,$d, $overwrite);
						}else{
							if($overwrite || !is_file($d)) copy($s,$d);
						}
					}
				}
				closedir($dir);
			}callFunc($source, $dest, $overwrite);
		}
	}

	/**
	 * 剪贴
	 * @param string $source 结尾如果为/,剪切该文件下的所有文件
	 * @param string $dest
	 * @param bool $overwrite 是否覆盖原文件
	 * @return bool
	 */
	public function cut($source, $dest, $overwrite = true) {
		if(is_file($source)){
			$name = substr($source,strripos($source,'/'));
			$dest .= $name;
		}
		if($source == $dest) return true;
		fileUtil::copy($source, $dest, $overwrite);
		fileUtil::delete($source);
	}

	/**
	 * 删除
	 * @param string $path 结尾如果为/,删除该文件下的所有文件
	 * @return bool
	 */
	public function delete($path) {
		if(is_dir($path)){
			//先删除目录下的文件：
			$dh=opendir($path);
			while ($file=readdir($dh)) {
				if($file!='.' && $file!='..') {
					$fullpath=$path.'/'.$file;
					if(is_dir($fullpath)) {
						fileUtil::delete($fullpath);
					} else {
						unlink($fullpath);
					}
				}
			}
			closedir($dh);
			//删除当前文件夹：
			return substr($path,-1) !== '/' ? rmdir($path) : true;
		}elseif(is_file($path)){
			return unlink($path);
		}
		return false;
	}

	/**
	 * 重命名
	 * @param string $path
	 * @param string $name
	 * @param bool $overwrite 是否覆盖原文件
	 * @return bool
	 */
	public function rename($path,$name,$overwrite = true) {
		if(strpos($path,'/') !== false){
			$dir = substr($path,0,strripos($path,'/'));
			$name = $dir.'/'.$name;
		}
		if(is_dir($name)) return false;
		return $overwrite || !is_file($name) ? rename($path,$name) : false;
	}
}
?>