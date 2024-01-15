<?php
/**
 * 函数集
 * 创建：2019-11-05
 * 更新：2022-11-13
 */
/**
 * 错误处理
 * @param string $errno
 * @param string $errstr
 * @param string $errfile
 * @param string $errline
 * @return bool
 */
function errorHandle($errno, $errstr, $errfile, $errline) {
	$subject = '错误['.$errno.']: '.$errstr.', 文件: '.$errfile.', 行: '.$errline;
	$error = db('error');
	$error = $error && is_array($error) ? $error : [];
	$error[] = [
		'ip' => ip(),
		'url' => getUrl(),
		'time' => time(),
		'content' => $subject
	];
	dbSave('error',$error);
	if(DEBUG === 1) return true;
	$message = [];
	$arr = debug_backtrace();
	array_shift($arr);
	foreach($arr as $v) {
		$args = '';
		if(!empty($v['args']) && is_array($v['args'])){
			foreach ($v['args'] as $v2){
				$args .= ($args ? ' , ' : '').(is_array($v2) ? 'array('.count($v2).')' : (is_object($v2) ? 'object' : $v2));
			}
		}
		!isset($v['file']) AND $v['file'] = '';
		!isset($v['line']) AND $v['line'] = '';
		$message [] = '文件: '.$v['file'].', 行: '.$v['line'].', '.$v['function'].'('.$args.')';
	}
	echo '<div class="notice"><b>'.$subject.'</b><p>'.implode("<br>\r\n", $message).'</p></div>';
	return true;
}
/**
 * 类型获取与转换
 * 说明：如果$data为数组类型，将转换所有数组值为$type
 * @param mixed $data 数据
 * @param string $type 转换类型，为空返回数据类型，有效值为：str|string|int|int|float|object|array|bool|json|stripTags|trim|urlencode|urldecode
 */
function type($data,$type=false){
	if(!isset($data) || $data===false || (!$data && $data!=='0' && $data!==0 && $data!==[] && $data!==''))return false;
	if(!$type)return gettype($data);
	switch($type){
		case 'str':
			return (is_array($data) || is_object($data))?var_export($data,true):(string)$data;
		case 'stripTags':
			return strip_tags((string)$data);
		case 'urlencode':
			return (string)urlencode($data);
		case 'urldecode':
			return (string)urldecode($data);
		case 'trim':
			return trim((string)$data);
		case 'int':
			return intval($data);
		case 'float':
			return floatval($data);
		case 'object':
			if(is_object($data))return $data;
			if(is_array($data)){
				return json_decode(json_encode($data,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
			}elseif(is_string($data)){
				return json_decode($data);
			}
			return (object)$data;
		case 'array':
			if(is_array($data))return $data;
			if(is_object($data)){
				return json_decode(json_encode($data,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES),true);
			}elseif(is_string($data)){
				return json_decode($data,true);
			}
			return (array)$data;
		case 'bool':
			$data=trim($data);
			if($data=='true' || $data=='1'){
				return true;
			}elseif($data=='false' || $data=='null' || $data=='0'){
				return false;
			}
			return boolval($data);
		case 'json':
			return is_array($data)||is_object($data)?json_encode($data,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_NUMERIC_CHECK|JSON_PRETTY_PRINT|JSON_PRESERVE_ZERO_FRACTION):false;
	}
	return $data;
}
/**
 * 获取URL上参数和$_GET参数
 * 例子：http://xxx.xxx/index/page.html?a=1&b=2 
 * print_r(get()) 返回以下数组
 * array(
 *    0 => index,
 *    1 => page,
 *    a => 1,
 *    b => 2,
 * )
 * @param string|int $key 数据的KEY，为空将获取所有参数
 * @param string $type 数据转换类型，为空默认为字符串，有效值参考type方法
 * @param mixed $def 获取的get不存在的话，返回该设定数值
 */
function get($key=false,$type=false,$def=false){
	$url = urldecode(str_replace('/?', '/', '/'.substr($_SERVER['REQUEST_URI'],strlen(URL))));
	$url = substr($url,-1) === '=' ? substr($url,0,-1) : $url;
	$parse = parse_url($url);
	if(strpos($url,'?') === false){
		$pos = strpos($url,'&');
		if($pos === false){
			$parse = parse_url($url);
		}else{
			$parse['path'] = substr($url,0,$pos);
			$parse['query']=substr($url,$pos+1);
		}
	}
	$f = isset($parse['path'])?$parse['path']:'';
	$g = isset($parse['query'])?$parse['query']:'';
	$g = $g?$g:[];
	$p = $a = [];
	$p = substr($f,stripos($f, '/')+1);
	$p = $p?explode('/',trim($p,'/')):[];
	if($g){
		$g = explode('&',$g);
		foreach ($g as $k => $v) {
			$b = explode('=',$v);
			if(isset($b[1])) $a[$b[0]] = $b[1];
		}
	}
	$_GET=$a;
	$v = array_merge($p,$a);
	if($key===false)return $v;
	if(!isset($v[$key]))return $def!==false?$def:false;
	if(!$type)return $v[$key];
	return type($v[$key],$type);
}
/**
 * 获取$_POST参数并安全转换类型
 * 例如：post('title','str') 获取的POST下title的值并安全转为字符串类型
 * @param string|int $key 数据的KEY，为空将获取所有参数
 * @param string $type 数据转换类型，为空默认为字符串，有效值参考type方法
 * @param mixed $def 默认返回值
 */
function post($key=false,$type=false,$def=false){
	if($key===false)return $_POST;
	if(!isset($_POST[$key]))return $def!==false?$def:false;
	if(!$type)return $_POST[$key];
	return type($_POST[$key],$type);
}
/**
 * 获取数组的某个元素
 * @param array $arr
 * @param string $key
 * @param mixed $def默认返回值
 * @return mixed
 */
function arr($arr,$key,$def){
	return is_array($arr) && isset($arr[$key]) ? $arr[$key] : $def;
}
/**
 * 数据覆盖保存
 * @param string $path 文件路径，不存在则创建
 * @param string|array $data 要保存的数据
 * @return string|bool
 */
function save($path,$data){
	global $util;
	if(is_array($data)){
		return $util->createFile($path,"<?php\nreturn ".var_export($data, true).";\n?>");
	}elseif(is_string ($data)){
		return $util->createFile($path,"<?php\nreturn '".str_replace('\'','\\\'',$data)."';\n?>");
	}else{
		return $util->createFile($path,"<?php\nreturn ".$data.";\n?>");
	}
}
/**
 * 打印消息
 * @param bool $error 是否报错
 * @param mixed $data 打印消息
 */
function ajax($error, $data=null) {
	header('Content-type:application/json');
    exit($data!==null?type(['error'=>$error, 'data'=>$data],'json'):$error);
}
/**
 * 正则验证
 * @param string $str 字符串
 * @param string $type 类型
 * @param int $min 最小值
 * @param int $max 最大值
 */
function check($str,$type,$min=1,$max=''){
	if($str===false) return false;
	if($type == 'str' && preg_match("/^[a-zA-Z0-9]{".$min.",".$max."}$/",$str)) return true; //字母数字
	elseif($type == 'num' && preg_match("/^[0-9]{".$min.",".$max."}$/",$str)) return true; //纯数字
	elseif($type == 'en' && preg_match("/^[a-zA-Z]{".$min.",".$max."}$/",$str)) return true; //纯字母
	elseif($type == 'zh' && preg_match("/^[\x7f-\xff]+$/",$str)) return true; //中文
	elseif($type == 'pc' && preg_match("/^[0-9]{4,6}$/",$str)) return true; //邮编
	elseif($type == 'name' && preg_match("/^[\x80-\xffa-zA-Z0-9]{".$min.",".$max."}$/", $str)) return true; //中英文数字
	elseif($type == 'ip') return (bool)ip2long($str); //ip
	elseif($type == 'date' && $str==date('Y-m-d',strtotime($str))) return true; //日期
	elseif($type == 'url' && preg_match('/(https?|ftps?):\/\/([\w\d\-_]+[\.\w\d\-_]+)[:\d+]?([\/]?[\w\/\.\?=&;%@#\+,]+)/i', $str)) return true; //网址
	elseif($type == 'mail' && preg_match("/^([a-z0-9_]|\\-|\\.)+@(([a-z0-9_]|\\-)+\\.){1,2}[a-z]{2,4}$/i",$str)) return true; //邮箱
	elseif($type == 'mp' && preg_match('/^(1[3-9])\d{9}$/',$str)) return true; //手机号
	elseif($type == 'tel' && preg_match("/^([0-9]{3}|0[0-9]{3})-[0-9]{7,8}$/",$str)) return true; //座机号
	elseif($type == 'idCard' && preg_match("/^([0-9]{15}|[0-9]{17}[0-9a-z])$/i",$str)) return true; //身份证号
	elseif($type == 'length'){ //字符串长度
		$len = count(str2arr($str));
		if($len>=$min && $len<=$max)return true;
	}
	return false;
}
/**
 * 数组转树结构
 * @param  array $arr
 * @return array
 */
function arrTree($arr,$id=0,$level=0){
	if(empty($arr)) return [];
	$list =[];
	foreach ($arr as $v){
		if ($v['pid'] == $id){
			$v['level']=$level;
			$v['child'] = arrTree($arr,$v['id'],$level+1);
			$list[] = $v;
		}
	}
	return $list?$list:[];
}
/**
 * 中英文字符串打散为数组
 * @param string $str 字符串
 * @return array
 */
function str2arr($str){
	preg_match_all('/./u', $str, $m);
	return $m[0];
}
/**
 * 判断是否为移动端访问
 * @return bool
 */
function isMobile(){
	$agent = strtolower($_SERVER['HTTP_USER_AGENT']);
	if(strpos($agent, 'mac os')==true || strpos($agent, 'iphone')==true || strpos($agent, 'android')==true || strpos($agent, 'ipad')==true ){
		return true;
	}
	return false;
}
/**
 * 判断当前协议是否为HTTPS
 * @return bool
 */
function isHttps() {
	if (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off') {
		return true;
	} elseif (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
		return true;
	} elseif (!empty($_SERVER['HTTP_FRONT_END_HTTPS']) && strtolower($_SERVER['HTTP_FRONT_END_HTTPS']) !== 'off') {
		return true;
	}
	return false;
}
/**
 * 获取完整的URL地址
 * @return string
 */
function getUrl() {
  return (isHttps() ? 'https' : 'http').'://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
}
/**
 * 页面跳转
 * @param string $url 页面地址
 */
function jump($url=false){
	$url = $url ? URL.$url : URL;
	header('Location:'.$url);
	exit;
}
/**
 * 分页
 * 例如：pages('user/list/{page}.html', 100, 10, 5);
 * @param string $url 分页链接 {page}为页码
 * @param int $totalnum 数据总数
 * @param int $page 当前页
 * @param int $pagesize 每页显示多少条数据
 * @return html
 */
function pages($url, $totalnum, $page, $pagesize = 20) {
	$totalpage = ceil($totalnum / $pagesize);
	if($totalpage < 2) return '';
	$page = min($totalpage, $page);
	$shownum = 2;
	$start = max(1, $page - $shownum);
	$end = min($totalpage, $page + $shownum);
	$right = $page + $shownum - $totalpage;
	$right > 0 && $start = max(1, $start -= $right);
	$left = $page - $shownum;
	$left < 0 && $end = min($totalpage, $end -= $left);
	$s = '<a href="'.($page == 1?'javascript:;':str_replace('{page}', $page-1, $url)).'" class="paging-prev'.($page == 1?' paging-disabled':'').'">上一页</a>';
	if($start > 1)$s .= '<a href="'.str_replace('{page}', 1, $url).'" class="paging-link">1</a>';
	if($start > 2)$s .= '<span class="paging-ell">…</span>';
	for($i=$start; $i<=$end; $i++) {
		$s .= '<a href="'.str_replace('{page}', $i, $url).'" class="paging-'.($i == $page?'active':'link').'">'.$i.'</a>';
	}
	if($totalpage - $end > 1)$s .='<span class="paging-ell">…</span>';
	if($end != $totalpage) $s .= '<a href="'.str_replace('{page}', $totalpage, $url).'" class="paging-link">'.$totalpage.'</a>';
	$s .= '<a href="'.($page == $totalpage?'javascript:;':str_replace('{page}', $page+1, $url)).'" class="paging-next'.($page == $totalpage?' paging-disabled':'').'">下一页</a>';
	return $s;
}
/**
 * 格式化时间
 * 说明：返回格式为 2018-11-02 19:01:13
 * @param  int $timestamp 时间戳
 * @return string
 */
function dates($timestamp) {
	return date('Y-m-d H:i:s',$timestamp?$timestamp:time());
}
/**
 * 友好显示时间
 * 说明：返回格式为 2天前
 * @param  int $timestamp 时间戳
 * @return string
 */
function humanDate($timestamp) {
	$seconds = time() - $timestamp;
	if($seconds > 31536000) {
		return date('Y', $timestamp).'年前';
	} elseif($seconds > 2592000) {
		return floor($seconds / 2592000).'月前';
	} elseif($seconds > 86400) {
		return floor($seconds / 86400).'天前';
	} elseif($seconds > 3600) {
		return floor($seconds / 3600).'小时前';
	} elseif($seconds > 60) {
		return floor($seconds / 60).'分钟前';
	}
	return $seconds.'秒前';
}
/**
 * 友好显示字节大小
 * 说明：返回格式为 4.21M
 * @param  int $size 字节大小
 * @return string
 */
function humanSize($size) {
	if($size > 1073741824) {
		return number_format($size / 1073741824, 2, '.', '').'G';
	} elseif($size > 1048576) {
		return number_format($size / 1048576, 2, '.', '').'M';
	} elseif($size > 1024) {
		return number_format($size / 1024, 2, '.', '').'K';
	}
	return $size.'B';
}
/**
 * 下载文件
 * 说明：返回格式为 4.21M
 * @param string $path 文件路径
 */
function downFile($path){
    if(is_file($path)){
        $path = realpath($path);
        $info = pathinfo($path);
        Header('Content-type: application/octet-stream');
        Header('Accept-Ranges: bytes');
        Header('Content-Length: '.filesize($path));
        header('Content-Disposition: attachment; filename='.$info['basename']);
        echo file_get_contents($path);
        readfile($path);
    }
    exit;
}
/**
 * 随机字符串
 * @param string $len 字符长度
 */
function randStr($len){
	if(!$len) return;
	$str = '';
	while ($len--) {
		$a = mt_rand(0,1); //随机字母还是数字
		$b = $a?chr(rand(65,90)):mt_rand(0,9);
		$str .= $b;
	}
	return $str;
}
/**
 * 获取用户IP
 * @return string
 */
function ip(){
	$ip = empty($_SERVER['REMOTE_ADDR']) ? 0 : $_SERVER['REMOTE_ADDR'];
    return (bool)ip2long($ip) ? $ip : '未知IP';
}
/**
 * 增强数组-多字段排序
 * @param array $arr
 * @param mixed $key 1:一维数组降序，0:一维数组升序
 * @param bool $desc
 * @return array
 */
function arrSort($arr, $key, $desc = true){
	$list = [];
	if(is_array($key)){
		foreach ($key as $k => $v) {
			$arr = arrSort($arr,$k,$v);
		}
		return $arr;
	}else{
		if($key === 1){
			arsort($arr);
			return $arr;
		}elseif($key === 0){
			asort($arr);
			return $arr;
		}else{
			$sort = [];
			foreach ($arr as $k => $v) {
				$sort[$k] = $v[$key];
			}
			$desc ? arsort($sort) : asort($sort);
			foreach ($sort as $k => $v) {
				$list[$k] = $arr[$k];
			}
		}
	}
	return $list;
}
/**
 * 对数组进行查找，排序，筛选，支持多种条件排序
 * @param array $arr
 * @param array $cond
 * @param array $orderby
 * @param int $page
 * @param int $pagesize
 * @return array
 */
function arrWhere($arr, $cond = [], $orderby = [], $page = 0, $pagesize = 0) {
	$resultarr = [];
	if(empty($arr)) return $arr;
	// 根据条件，筛选结果
	if($cond) {
		foreach($arr as $key=>$val) {
			$ok = TRUE;
			foreach($cond as $k=>$v) {
				if(!isset($val[$k])) {
					$ok = FALSE; break;
				}
				if(!is_array($v)) {
					if($val[$k] != $v) {
						$ok = FALSE; break;
					}
				} else {
					foreach($v as $k3=>$v3) {
						if(
							($k3 == '>' && $val[$k] <= $v3) || 
							($k3 == '<' && $val[$k] >= $v3) ||
							($k3 == '>=' && $val[$k] < $v3) ||
							($k3 == '<=' && $val[$k] > $v3) ||
							($k3 == '==' && $val[$k] != $v3) ||
							($k3 == 'LIKE' && stripos($val[$k], $v3) === FALSE) ||
							($k3 == '!LIKE' && stripos($val[$k], $v3) !== FALSE) ||
							($k3 == 'IN' && !in_array($v3, $val[$k])) ||
							($k3 == '!IN' && in_array($v3, $val[$k]))
						)  {
							$ok = FALSE; break 2;
						}
					}
				}
			}
			if($ok) $resultarr[$key] = $val;
		}
	} else {
		$resultarr = $arr;
	}
	if($orderby) {
		$resultarr = arrSort($resultarr, $orderby);
	}
	if($page){
		$start = ($page - 1) * $pagesize;
		$resultarr = arrSlice($resultarr, $start, $pagesize);
	}
	return $resultarr;
}
/**
 * 数组切割
 * @param array $arr
 * @param int $start
 * @param int $length
 * @return array
 */
function arrSlice($arr, $start, $length = 0) {
	if(isset($arr[0])) return array_slice($arr, $start, $length);
	$key = array_slice(array_keys($arr), $start, $length);
	$list = [];
	foreach($key as $k) {
		$list[$k] = $arr[$k];
	}
	return $list;
}

/**
 * 返回数组分页后的数据
 * @param array $data
 * @param int $page
 * @param int $pagesize
 * @return array
 */
function arrPages($data,$page,$pagesize=20){
	if(!$data || !$page || !$page) return [];
	$count = count($data);
	$start = ($page-1)*$pagesize;
	if($start>$count) return [];
	$arr = [];
	$i = 0;
	foreach ($data as $k=> $v) {
		$i++;
		if($i>$start){
			if($pagesize--){
				$arr[$k] = $v;
			}else{
				return $arr;
			}
		}
	}
	return $arr;
}
/**
 * 发起请求
 * @param string $url
 * @param string $params
 * @param string $method
 * @return string
 */
function curl($url, $params = [], $method = 'POST', $cookie = ''){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
    curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    if(substr($url,0,5) === 'https'){
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); // 对认证证书来源的检查
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); // 从证书中检查SSL加密算法是否存在
    }
    if ($method == 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        curl_setopt($ch, CURLOPT_URL, $url);
    } else {
        if ($params) {
            if (is_array($params)) {
                $params = http_build_query($params);
            }
            curl_setopt($ch, CURLOPT_URL, $url . '?' . $params);
        } else {
            curl_setopt($ch, CURLOPT_URL, $url);
        }
    }
	$header = ['Content-type: application/x-www-form-urlencoded', 'X-Requested-With: XMLHttpRequest'];
	if($cookie) $header[] = "Cookie: $cookie";
	curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    $response = curl_exec($ch);
    if (!$response) return false;
    curl_close($ch);
    return $response;
}
?>