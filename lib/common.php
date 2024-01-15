<?php
/**
 * 获取db数据
 * @param string $name
 * @param array $cond
 * @param array $orderby
 * @param int $page
 * @param int $pagesize
 * @return array
 */
function db($name, $cond = [], $orderby = [], $page = 0, $pagesize = 0){
	$name = DB.$name.'.php';
	if(is_file($name)){
		$data = include $name;
		if($cond !== false) $data = arrWhere($data, $cond, $orderby, $page, $pagesize);
		return $data;
	}else{
		return [];
	}
}
/**
 * 保存db数据
 * @param string $name
 * @param array $data
 * @return bool
 */
function dbSave($name,$data){
	return save(DB.$name.'.php',$data);
}
/**
 * 删除db数据
 * @param string $name
 * @param array $cond
 * @param bool $index 是否重新索引
 * @return bool
 */
function dbDelete($name,$cond=[],$index=false){
	$path = DB.$name.'.php';
	if(is_file($path)){
		if($cond){
			$arr = db($name);
			if(gettype($arr) !== 'array') return false;
			$sel = arrWhere($arr,$cond);
			foreach ($sel as $k => $v) {
				unset($arr[$k]);
			}
			if($index)$arr = array_merge($arr);
			return dbSave($name,$arr);
		}
		return delFile(dbPath($name));
	}
	return false;
}
/**
 * 更新db数据
 * @param string $name
 * @param array $cond
 * @param array|bool $data
 * @return bool
 */
function dbUpdate($name,$cond,$data=false){
	$path = DB.$name.'.php';
	if(is_file($path)){
		$arr1 = db($name);
		if(gettype($arr1) !== 'array') $arr1 = [];
		if($data === false){
			foreach ($cond as $k => $v) {
				$arr1[$k] = $v;
			}
		}else{
			if(gettype($data) !== 'array') return false;
			$arr2 = arrWhere($arr1,$cond);
			foreach ($arr2 as $k => $v) {
				foreach ($data as $k1 => $v1) {
					$arr1[$k][$k1] = $v1;
				}
			}
		}
		return dbSave($name,$arr1);
	}
	return false;
}
/**
 * 插入db数据
 * @param string $name
 * @param array $data
 * @param bool $index 是否重新索引
 * @return bool
 */
function dbInsert($name,$data,$index=true){
	$path = DB.$name.'.php';
	if(is_file($path)){
		$arr = db($name);
		if(is_array($arr)){
			if($index){
				$arr[] = $data;
			}else{
				foreach ($data as $k => $v) {
					$arr[$k] = $v;
				}
			}
			return dbSave($name,$arr);
		}else{
			if(is_string($data)){
				return dbSave($name,$arr.$data);
			}
		}
	}
	return false;
}
/**
 * 获取db数据路径
 * @param string $name
 * @return string
 */
function dbPath($name){
	return DB.$name.'.php';
}
/**
 * 数据库版本同步
 */
function dbSync($list){
	//递归遍历所有字段
	$forField = function($arr,$list)use(&$forField){
		if(!is_array($arr)) return;
		foreach($list as $name => $value){
			if(isset($arr[$name])){
				if(gettype($arr[$name]) !== gettype($value)) $arr[$name] = $value;
				if($value && is_array($value)) $arr[$name] = $forField($arr[$name],$value);
			}else{
				$arr[$name] = $value;
			}
		}
		return $arr;
	};
	$updateDb = function($arr,$list)use(&$forField){
		if($list['type'] == 'define-array' || $list['type'] == 'key-array'){
			foreach($arr as $k => $v){
				$arr[$k] = $forField($v,$list['value']);
			}
		}
		if($list['type'] == 'int-array'){
			foreach($list['value'] as $v){
				if(!in_array($v,$arr)) $arr[]=$v;
			}
		}
		if($list['type'] == 'array') $arr = $forField($arr,$list['value']);
		return $arr;
	};
	foreach ($list as $k => $v) {
		if(substr($k,0,1) == '#') continue;
		$dir = substr($k,0,1) == '@' ? 1 : 0;
		if($dir){
			$k = substr($k,1);
			if(is_dir(DB.$k)){
				if($v['type'] != 'files'){
					$filesList = glob(DB.$k.'/*', GLOB_NOSORT);
					foreach($filesList as $path){
						$data = include $path;
						if($data){
							$data = $updateDb($data,$v);
							save($path,$data);
						}
					}
				}
			}
		}else{ 
			$data = db($k);
			$data = $updateDb($data,$v);
			dbSave($k,$data);
		}
	}
	$conf = db('conf');
	$conf['db']['version'] = $list['#version'];
	dbSave('conf',$conf);
}
/**
 * 获取token
 * @param bool $sign 是否重置签名
 * @return string
 */
function getToken($sign=false){
	if($sign) $_SESSION['tokenSign'] = randStr(8);
	return md5($_SERVER['REMOTE_ADDR'].
	(isset($_SERVER['HTTP_CLIENT_IP'])?$_SERVER['HTTP_CLIENT_IP']:'').
	(isset($_SERVER['HTTP_X_FORWARDED_FOR'])?$_SERVER['HTTP_X_FORWARDED_FOR']:'').
	(isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:'').
	(isset($_SERVER['HTTP_SEC_CH_UA_MOBILE'])?$_SERVER['HTTP_SEC_CH_UA_MOBILE']:'').
	(isset($_SERVER['HTTP_SEC_CH_UA'])?$_SERVER['HTTP_SEC_CH_UA']:'')).$_SESSION['tokenSign'];
}
/**
 * 检查是否已登录
 */
function checkLogin(){
	if(!LOGIN) jump('login');
}
/**
 * 删除前后端模板缓存
 */
function delCompile(){
	delFile(LIB.'admin/compile');
	delFile(TPLPATH.'compile');
}
/**
 * 删除正文里的文件
 * @param string $content 正文
 */
function delContentFiles($content){
	if($content){
		preg_match_all("/\[(img|file)\s+([^\s\]]+).*?\]/i", $content, $files);
		if (!empty($files)) {				
			foreach ($files[2] as $v) {if(is_file(ROOT.$v))unlink(ROOT.$v);}
		}
	}
}
/**
 * 文件上传
 * @param string $name
 * @param string|function $html
 */
function upload($arr=[]){
	global $hook;
	include LIB.'upload.class.php';
	$arr['inputName'] = isset($arr['inputName']) ? $arr['inputName'] : 'file';
	$arr['path'] = isset($arr['path']) ? $arr['path'] : 'db/upload/';
	$arr['nameType'] = isset($arr['nameType']) ? $arr['nameType'] : 'time';
	$arr['name'] = isset($arr['name']) ? $arr['name'] : false;
	$arr['size'] = isset($arr['size']) ? $arr['size'] : 100;
	$arr['ext'] = isset($arr['ext']) ? $arr['ext'] : false;
	$arr['absolutePath'] = isset($arr['absolutePath']) ? $arr['absolutePath'] : true;
	$up = new Upload($arr['inputName'],$arr['path'],$arr['name']);
	$up->absolutePath = $arr['absolutePath'];
	$up->setMaxSize($arr['size']);
	$up->setNameType($arr['nameType']);
	if($arr['ext']) $up->setAllowExt($arr['ext']);
	$up->multiFile();
	$error = $up->getErrorMsg();
	if($error) return ['error'=>1,'data'=>is_array($error) ? $error[0] : $error];
	$fileList = $up->getUploadFiles();
	foreach($hook['model_upload'] as $fn) $fn();
	return ['error'=>0,'data'=>$fileList];
}
/**
 * 设置扩展接口
 * @param string $name
 * @param string|function $html
 */
function hook($name,$html){
    global $hook;
    $hook[$name] = isset($hook[$name]) ? $hook[$name] : '';
    if($name === 'css' || $name === 'admin_css'){
        $hook[$name][] = function()use($html){echo '<link rel="stylesheet" href="'.$html.'"/>';};
    }elseif($name === 'script' || $name === 'admin_script'){
    	$html = function()use($html){echo '<script src="'.$html.'"></script>';};
       $hook[$name][] = eval($html);;
    }else{
    	if(gettype($html) == 'object'){
    		$hook[$name][] = $html;
    	}else{
    		$hook[$name][] = function()use($html){echo $html;};
    	}
    }
}
/**
 * 获取或保存扩展中的配置
 * ini('demo','a',1) //设置a=1
 * ini('demo',['b'=>2,'c'=>3]) //设置b=2,c=3
 * ini('demo',['d'=>4,'e'=>5],true) //清空之前的所有数据，然后设置d=4,e=5,
 * ini('demo') //获取demo中的所有数据
 * ini('demo','a') //获取demo中的a数据
 * @param string $name 扩展名称
 * @param string|array $key 获取或保存的key
 * @param mix $value 需保存的数据
 * @return mix
 */
function ini($name,$key=null,$value=null){
	global $ini;
	if(!isset($ini[$name])) $ini[$name] = [];
	if(gettype($key) == 'array'){
		if($value === true) $ini[$name] = [];
		foreach($key as $k => $v){
			$ini[$name][$k] = $v;
		}
		return dbSave('ini',$ini);
	}
	if($key === false){
		unset($ini[$name]);
		return dbSave('ini',$ini);
	}
	if($value !== null){
		$ini[$name][$key] = $value;
		return dbSave('ini',$ini);
	}
	return $key !== null ? (isset($ini[$name][$key]) ? $ini[$name][$key] : false) : $ini[$name];
}
/**
 * 网站提示
 * @param string $text
 * @param string $url
 */
function prompt($text='',$url=false){
	$_SESSION['prompt']['text']=$text;
	$_SESSION['prompt']['url']=$url;
	jump(get(0) == 'admin' ? 'admin/prompt' : 'prompt');
}
/**
 * 检查更新
 * @return string|bool
 */
function checkUpdate(){
	$v = curl(API_HOST.'getVersion');
	if($v){
		$v = type($v,'array');
		if(isset($v['data'])){
			if(V !== $v['data']) return $v['data'];
		}
	}
	return false;
}
/**
 * 根据文件列表压缩文件
 * @param string $path
 * @param string $pathName
 * @return string
 */
function sxs($list,$pathName=''){
	global $util;
	$str = "# 溯雪资源文件\n";
	foreach ($list as $v) {
		$rv = ROOT.$v;
		if(is_file($rv)){
			$content = str_replace(['@',"\r\n","\n"],['_@_','_@rn@_','_@n@_'],file_get_contents($rv));
			$str .= '[file '.$v.']'.$content."\n";
		}else{
			$str .= '[dir '.$v."]\n";
			$str .= sx($rv,true,false);
		}
	}
	$str = trim($str,"\n");
	$util->createFile($pathName,$str);
	return $str;
}
/**
 * 根据目录压缩文件
 * @param string $path
 * @param string $completePath
 * @param string $pathName
 * @return string
 */
function sx($path,$completePath=false,$pathName=''){
	global $util;
	$head = "# 溯雪资源文件\n";
	if($pathName !== false){
		$confPath = $path.'/conf.php';
		if(is_file($confPath)){
			$conf = include $confPath;
			if(is_array($conf)){
				if(isset($conf['id'])) $head .= "# Id: {$conf['id']}\n";
				if(isset($conf['type'])) $head .= "# Type: {$conf['type']}\n";
				if(isset($conf['name'])) $head .= "# Name: {$conf['name']}\n";
				if(isset($conf['intro'])) $head .= "# Intro: {$conf['intro']}\n";
				if(isset($conf['home'])) $head .= "# Home: {$conf['home']}\n";
				if(isset($conf['author'])) $head .= "# Author: {$conf['author']}\n";
				if(isset($conf['contact'])) $head .= "# Contact: {$conf['contact']}\n";
				if(isset($conf['version'])) $head .= "# Version: {$conf['version']}\n";
				if(isset($conf['limit'])) $head .= "# Limit: {$conf['limit']}\n";
				$head .= "\n";
			}
		}
	}
	$nPath = $path.'/';
	$run = function($path)use($nPath,$completePath,&$run){
		$str = '';
		$list = glob($path.'/*', GLOB_NOSORT);
		foreach ($list as $v) {
			$vPath = substr($v,strlen($completePath?ROOT:$nPath));
			if(is_file($v)){
				$content = str_replace(['@',"\r\n","\r","\n"],['_@_','_@rn@_','_@r@_','_@n@_'],file_get_contents($v));
				$str .= '[file '.$vPath.']'.$content."\n";
			}else{
				$info = pathinfo($v);
				if($info['basename'] !== 'compile'){
					$str .= '[dir '.$vPath."]\n";
					$str .= $run($v);
				}
			}
		}
		return $str;
	};
	$str = $run($path);
	if($pathName !== false) $str = $head.'SX.'.base64_encode(trim($str,"\n"));
	// if($pathName !== false) $str = $head.trim($str,"\n");
	if($pathName){
		$util->createFile($pathName,$str);
	}
	return $str;
}
/**
 * 解压文件
 * @param string $file
 * @param string $path
 * @return string
 */
function unsx($file, $path=ROOT){
	global $util;
	$content = strlen($file)<200 && is_file($file) ? file_get_contents($file) : $file;
	$content = explode("\n",$content);
	$arr = [];
	foreach ($content as $v) {
		$v = preg_replace('/^#.*$/','',trim($v));
		if($v) $arr[] = $v;
	}
	if(count($arr) === 1 && substr($arr[0],0,3) === 'SX.') $arr = explode("\n",base64_decode(substr($arr[0],3)));
	foreach ($arr as $v) {
		preg_match('/^\[(dir|file)\s(.*?)\](.+?)$/', $v, $m);
		if($m){
			$f = str_replace('\\','/',$m[2]);
			if(substr($f,0,1) === '/' || stristr($f,'..') !== false) continue;
			if($m[1] == 'dir'){
				$util->createDir($path.$f);
			}elseif($m[1] == 'file'){
				$value = str_replace(['_@rn@_','_@r@_','_@n@_','_@_'],["\r\n","\r","\n",'@'],$m[3]);
				$util->createFile($path.$f,$value);
			}
		}
	}
}
/**
 * 获取模板列表
 * @return array
 */
function getTpl() {
	global $conf;
	$list = glob(ROOT.'tpl/*', GLOB_ONLYDIR|GLOB_NOSORT);
	$tpl = [];
	$tpl['list'] = [];
	$arr1 = $arr2 = [];
	$tpl['count'] = 0;
	if($list){
		foreach ($list as $name) {
			$name = substr($name, strrpos($name, '/') + 1);
			$tplPath = ROOT.'tpl/'.$name.'/conf.php';
			if(is_file($tplPath)){
				$tplConf = include $tplPath;
				if(isset($tplConf['name']) && isset($tplConf['intro'])){
					$tplConf['id'] = $name;
					$tplConf['icon'] = HOME.'tpl/'.$name.'/icon.png';
					$tplConf['setting'] = is_file(ROOT.'tpl/'.$name.'/setting.php') ? true : false;
					if($conf['tpl'] == $name){
						$arr1[$name] = $tplConf;
					}else{
						$arr2[$name] = $tplConf;
					}
				}
			}
		}
		$tpl['list'] = $arr1+$arr2;
		$tpl['count'] = count($tpl['list']);
	}
	return $tpl;
}
/**
 * 获取扩展列表
 * @return array
 */
function getExt() {
	global $conf;
	$list = glob(ROOT.'ext/*', GLOB_ONLYDIR|GLOB_NOSORT);
	$ext = [];
	$ext['list'] = [];
	$ext['installList'] = $ext['notInstallList'] = [];
	$ext['installCount'] = $ext['count'] = 0;
	$installExt = $conf['ext'];
	if($list){
		foreach ($list as $name) {
			$name = substr($name, strrpos($name, '/') + 1);
			$extPath = EXT.$name.'/conf.php';
			if(is_file($extPath)){
				$extConf = include $extPath;
				if(isset($extConf['name']) && isset($extConf['intro'])){
					$extConf['id'] = $name;
					$extConf['icon'] = HOME.'ext/'.$name.'/icon.png';
					$extConf['setting'] = is_file(EXT.$name.'/setting.php') ? true : false;
					if(isset($installExt[$name])){
						$ext['installList'][$name] = $extConf;
					}else{
						$ext['notInstallList'][$name] = $extConf;
					}
				}
			}
		}
		$ext['installCount'] = count($ext['installList']);
		$ext['list'] = $ext['installList']+$ext['notInstallList'];
		$ext['count'] = count($ext['list']);
	}
	return $ext;
}
/**
 * 文件删除
 */
function delFile($file) {
	global $util;
	return $util->delete($file);
}
/**
 * 获取内存使用情况
 */
function getMemory(){
	return (int)((memory_get_usage() - $_SERVER['_memory_usage']) / 1024);
}
/**
 * 获取运行时间
 */
function getRunTime(){
	global $timeStart;
	return (float)substr(microtime(true) - $timeStart,0,6);
}
/**
 * 获取文章列表
 * @param array $cond 条件
 * @param array $orderby 排序
 * @param bool|string $url 分页
 * @param int $page 当前页
 * @param int $pagesize 每页条数
 * @return array
 */
function getArticle($cond = [], $orderby = [], $url = false, $page = 0, $pagesize = 0){
	global $articleList,$categoryList;
	$selList = arrWhere($articleList,$cond, $orderby);
	$arr1 = $arr2 = [];
	foreach ($selList as $k => $v) {
		$v['url'] = URL.$k;
		if($v['tag']){
			$list = [];
			$html = '';
			foreach($v['tag'] as $t){
				$a = '<a href="'.URL.'tag/'.$t.'">'.$t.'</a>';
				$html .= $a;
				$list[] = [
					'name' => $t,
					'url' => URL.'tag/'.$t,
					'html' => $a
				];
			}
			$v['tag'] = [
				'name' => implode(' ',$v['tag']),
				'html' => $html,
				'list' => $list
			];
		}else{
			$v['tag'] = [
				'name' => '',
				'html' => '',
				'list' => []
			];
		}
		if(isset($categoryList[$v['cid']])){
			$v['category'] = $categoryList[$v['cid']];
		}else{
			$v['category'] = [
				'id' => '',
				'name' => '',
				'intro' => '',
				'count' => 0,
				'url' => ''
			];
		}
		if($v['top']){
			$arr1[$k] = $v;
		}else{
			$arr2[$k] = $v;
		}
	}
	$selList = $arr1+$arr2;
	$arr = [];
	$arr['count'] = count($selList);
	$arr['list'] = $pagesize ? arrPages($selList,$page, $pagesize) : $selList;
	if($url) $arr['paging'] = pages(URL.$url, $arr['count'], $page, $pagesize);
	return $arr;
}
/**
 * 重新格式化分类信息
 */
function categoryInit(){
	$conf = db('conf');
	$articleList = db('article');
	foreach($conf['category'] as $k => $v){
		$conf['category'][$k]['count'] = 0;
	}
	foreach($articleList as $v){
		if(isset($conf['category'][$v['cid']])){
			$conf['category'][$v['cid']]['count'] += 1;
		}
	}
	dbUpdate('conf',['category'=>$conf['category']]);
}
/**
 * 获取分类列表
 * @param bool|string $name
 * @return array
 */
function getCategory(){
	global $conf;
	$arr = [];
	foreach ($conf['category'] as $k=> $v) {
		$arr[$k]=[
			'id' => $k,
			'name' => $v['name'],
			'intro' => $v['intro'],
			'count' => $v['count'],
			'url' => URL.'category/'.$k
		];
	}
	return $arr;
}
/**
 * 重新格式化标签信息
 */
function tagInit(){
	$articleList = db('article');
	$arr = [];
	foreach ($articleList as $v) {
		if($v['tag']){
			foreach ($v['tag'] as $vs) {
				$arr[$vs] = isset($arr[$vs]) ? ++$arr[$vs] : 1;
			}
		}
	}
	dbUpdate('conf',['tag'=>$arr]);
}
/**
 * 获取标签列表
 * @return array
 */
function getTag(){
	global $conf;
	$arr = [];
	foreach ($conf['tag'] as $k=> $v) {
		$arr[$k]=[
			'id' => $k,
			'name' => $k,
			'count' => $v,
			'url' => URL.'tag/'.$k
		];
	}
	return $arr;
}
/**
 * 获取访客列表
 * @param bool|string $url
 * @param int $page
 * @param int $pagesize
 * @return array
 */
function getVisit($url = false, $page = 0, $pagesize = 0){
	$visit = array_reverse(db('visit'));
	$todayStart = strtotime(date('Y-m-d'),time());
	$todayEnd = $todayStart + 86400;
	$todayCount=0;
	foreach ($visit as $v) {
		if($v>$todayStart && $v<$todayEnd){
			$todayCount++;
		}
	}
	$arr = [];
	$arr['today'] = $todayCount;
	$arr['count'] = count($visit);
	$arr['list'] = arrPages($visit,$page, $pagesize);
	if($url) $arr['paging'] = pages(URL.$url, $arr['count'], $page, $pagesize);
	return $arr;
}
/**
 * 获取错误列表
 * @param array $cond
 * @param array $orderby
 * @param bool|string $url
 * @param int $page
 * @param int $pagesize
 * @return array
 */
function getError($cond = [], $orderby = [], $url = false, $page = 0, $pagesize = 0){
	$error = db('error',$cond,$orderby);
	$arr = [];
	$arr['count'] = count($error);
	$arr['list'] = arrPages($error,$page, $pagesize);
	if($url) $arr['paging'] = pages(URL.$url, $arr['count'], $page, $pagesize);
	return $arr;
}
/**
 * 重新格式化文章中的评论数
 * @param string $id
 */
function commentsInit($id){
	global $articleList;
	if(isset($articleList[$id])){
		$articleList[$id]['comments'] = count(db('comment/'.$id));
		dbSave('article',$articleList);
	}
}
/**
 * 获取评论
 * @param string $id
 * @param array $cond
 * @param array $orderby
 * @param bool|string $url
 * @param int $page
 * @param int $pagesize
 * @return array
 */
function getComment($id, $cond = [], $orderby = [], $url = false, $page = 0, $pagesize = 0){
	$comment = db('comment/'.$id);
	$comment = arrWhere($comment,$cond, $orderby);
	$arr = [];
	$arr['count'] = count($comment);
	$comment = array_reverse(arrTree($comment));
	$arr['list'] = arrPages($comment,$page, $pagesize);
	$arr['html'] = getCommentHtml($arr['list']);
	if($url) $arr['paging'] = pages(URL.$url, count($comment), $page, $pagesize);
	return $arr;
}
/**
 * 获取留言列表html
 * @param array $list
 * @return string
 */
function getCommentHtml($list){
	if(!$list) return '';
	$html = '<ul class="comment">';
	foreach ($list as $v) {
		$html .= '<li id="comment-'.$v['id'].'"><div class="comment-box'.($v['admin'] ? ' comment-admin' : '').'"><div class="comment-title"><span class="comment-ip ip-'.$v['id'].'">'.(isset($v['admin']) && $v['admin'] ? '作者':(isset($v['username']) ? $v['username'] : $v['ip'])).'</span><span class="comment-time">'.dates($v['time']).'</span><span class="comment-reply" onclick="SX.reply('.$v['id'].');">回复</span>'.(LOGIN ? '<a href="'.URL.'comment/delete/'.get(0).'/'.$v['id'].'" data-pjax="false" onclick="return SX.confirm(this,\'确实要删除吗？删除不可恢复！\')">删除</a>' : '').'</div><div class="comment-content"><p>'.implode('</p><p>', explode("\r\n",$v['content'])).'</p></div>';
		if($v['child']){
			$html .= getCommentHtml($v['child']);
		}
		$html .='</div></li>';
	}
	$html .= '</ul>';
	return $html;
}
/**
 * 删除留言
 * @param string $path
 * @param bool|int $id
 */
function delComment($path,$id=false){
	global $util;
	$dbPath = DB.$path.'.php';
	if(is_file($dbPath)){
		if($id !== false){
			$arr = db($path,['pid'=>$id]);
			foreach ($arr as $value) {
				delComment($path,$value['id']);
			}
			$conf = db('conf');
			$conf['comment']['count'] -= 1;
			dbDelete($path,['id'=>$id],true);
			dbSave('conf',$conf);
			//如果不存在留言，直接删除整个文件
			if(!db($path)) $util->delete($dbPath);
		}else{
			$util->delete($dbPath);
		}
	}
}
?>