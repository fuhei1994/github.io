<?php
//官网：https://xueluo.cn
//作者：雪落
//时间：2022-01-10
header('Content-Type:text/html;charset=utf-8');
date_default_timezone_set('PRC');
$_SERVER['_memory_usage'] = memory_get_usage();
$timeStart = microtime(true);
session_start();
//主页
define('HOME',substr($_SERVER['SCRIPT_NAME'],0,-9));
//根目录
$root = str_replace('\\','/',$_SERVER['DOCUMENT_ROOT']);
$root = (substr($root,-1)==='/'?substr($root,0,-1):$root).HOME;
define('ROOT',$root);
//数据目录
define('DB',ROOT.'db/');
//类库目录
define('LIB',ROOT.'lib/');
//扩展目录
define('EXT',ROOT.'ext/');
//保存登陆状态
define('LOGIN', isset($_SESSION['login'])?$_SESSION['login']:0);
//官网API接口地址
define('API_HOST','https://xueluo.cn/api/');
//溯雪版本
define('V','1.1.8');

//引入库
include LIB.'function.php';
include LIB.'common.php';
include LIB.'fk.class.php';
include LIB.'tpl.class.php';
include LIB.'file.util.class.php';
//时间戳
$time = time();
//文件操作类
$util = new fileUtil();
//系统配置
$conf = db('conf');
//用户配置
$ini = db('ini');
//0:线上模式（无错）1:调试模式（无错+日志）2:开发模式（报错+日志）
define('DEBUG', $conf['debug']);
//伪静态url
define('URL',HOME.($conf['rewrite']?'':'?'));
function_exists('ini_set') AND ini_set('display_errors', DEBUG ? '1' : '0');
error_reporting(DEBUG ? E_ALL : 0);
DEBUG AND set_error_handler('errorHandle', -1);

//缓存信息
$_SESSION['ip'] = isset($_SESSION['ip']) ? $_SESSION['ip'] : ip();
$_SESSION['checkIP'] = isset($_SESSION['checkIP']) ? $_SESSION['checkIP'] : 0;
$_SESSION['views'] = isset($_SESSION['views']) ? $_SESSION['views'] : [];
$_SESSION['commentCount'] = isset($_SESSION['commentCount']) ? $_SESSION['commentCount'] : 0;
$_SESSION['loginCount'] = isset($_SESSION['loginCount']) ? $_SESSION['loginCount'] : 0;
$_SESSION['vcode'] = isset($_SESSION['vcode']) ? $_SESSION['vcode'] : 0;
$_SESSION['token'] = isset($_SESSION['token']) ? $_SESSION['token'] : [];
$_SESSION['tokenSign'] = isset($_SESSION['tokenSign']) ? $_SESSION['tokenSign'] : 0;
$_SESSION['tokenTime'] = isset($_SESSION['tokenTime']) ? $_SESSION['tokenTime'] : $time;
$_SESSION['includeTheme'] = 0;

//鉴权
if(LOGIN){
	if($time-$_SESSION['tokenTime'] > 1800 || !isset($_SESSION['token'][$_COOKIE['token']]) || substr($_COOKIE['token'],0,10) !== substr(getToken(),0,10)){
		session_destroy();
		jump();
	}else{
		$_SESSION['token'] = array_filter($_SESSION['token'],function($v){return $v > time()-30;});
		$_SESSION['tokenTime'] = $time;
		$token = getToken(true);
		$_SESSION['token'][$token]=$time;
		setcookie('token',$token,0,'/');
	}
}

//请求方式：POST、GET
$method = $_SERVER['REQUEST_METHOD'];
//是否为ajax请求
$ajax = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower(trim($_SERVER['HTTP_X_REQUESTED_WITH'])) == 'xmlhttprequest');
//当前地址
$url = $_SERVER['REQUEST_URI'];
//当前主题模板绝对路径
define('TPLPATH',ROOT.'tpl/'.$conf['tpl'].'/');
//当前主题模板相对路径
define('TPL',HOME.'tpl/'.$conf['tpl'].'/');
//当前主题样式路径
define('TPL_STYLE',HOME.'tpl/'.$conf['tpl'].'/style/');
//公共样式路径
define('LIB_STYLE',HOME.'lib/style/');
//公共图片路径
define('LIB_IMG',HOME.'lib/img/');
//当前网址
$host = (isHttps() ? 'https' : 'http').'://'.$_SERVER['SERVER_NAME'].HOME;
//前端页面
$page = get(0,'str','index');
//后端页面
$adminPage = get(1,'str','index');
if($page == 'admin' && $adminPage != 'login'){
	checkLogin();
}
//hook钩子名称预设
//前端
$hook=[];
//后端
foreach ([
	//全局公共
	'common','editor',
	//后端视图层
	'admin_head_header','admin_meta','admin_css','admin_script','admin_head_footer','admin_body_header','admin_sidebar_top','admin_sidebar_menu_top','admin_sidebar_menu_1','admin_sidebar_menu_2','admin_sidebar_menu_3','admin_sidebar_menu_bottom','admin_sidebar_bottom','admin_header_menu_left','admin_header_menu_middle','admin_header_menu_right','admin_content_top','admin_footer','admin_body_footer','admin_body_footer','admin_body_footer','admin_body_footer',
	//首页模板
	'admin_index_header','admin_index_info_top','admin_index_info_bottom','admin_index_system','admin_index_system_top','admin_index_system_bottom','admin_index_news','admin_index_server','admin_index_server_top','admin_index_server_bottom','admin_index_footer',
	//文章管理
	'admin_article_header','admin_article_menu_header','admin_article_menu_left_top','admin_article_menu_left_bottom','admin_article_menu_right_top','admin_article_menu_right_bottom','admin_article_menu_footer','admin_article_list_th_1','admin_article_list_th_2','admin_article_list_th_3','admin_article_list_td_1','admin_article_list_td_2','admin_article_list_td_3','admin_article_list_operate','admin_article_bottom','admin_article_sidebar_top','admin_article_sidebar_bottom','admin_article_footer',
	//文章添加
	'admin_article_create_top','admin_article_create_title','admin_article_create_info_top','admin_article_create_info','admin_article_create_attr_top','admin_article_create_attr_col','admin_article_create_attr','admin_article_create_intro_top','admin_article_create_content_top','admin_article_create_bottom',
	//文章编辑
	'admin_article_editor_top','admin_article_editor_title','admin_article_editor_info_top','admin_article_editor_info','admin_article_editor_attr_top','admin_article_editor_attr_col','admin_article_editor_attr','admin_article_editor_intro_top','admin_article_editor_content_top','admin_article_editor_bottom',
	//导航设置
	'admin_navbar_header','admin_navbar_col_top','admin_navbar_col_form','admin_navbar_col_operate','admin_navbar_col_bottom','admin_navbar_del_js','admin_navbar_col_top_js','admin_navbar_col_form_js','admin_navbar_col_operate_js','admin_navbar_col_bottom_js','admin_navbar_add_js','admin_navbar_submit_success_js','admin_navbar_footer',
	//分类管理
	'admin_category_header','admin_category_col_top','admin_category_col_form','admin_category_col_operate','admin_category_col_bottom','admin_category_del_js','admin_category_col_top_js','admin_category_col_form_js','admin_category_col_operate_js','admin_category_col_bottom_js','admin_category_add_js','admin_category_submit_js','admin_category_submit_success_js','admin_category_footer',
	//友情链接
	'admin_link_header','admin_link_col_top','admin_link_col_form','admin_link_col_operate','admin_link_col_bottom','admin_link_del_js','admin_link_col_top_js','admin_link_col_form_js','admin_link_col_operate_js','admin_link_col_bottom_js','admin_link_add_js','admin_link_submit_success_js','admin_link_footer',
	//设置
	'admin_setting_header','admin_setting_top','admin_setting_form_1','admin_setting_form_2','admin_setting_form_3','admin_setting_form_4','admin_setting_form_5','admin_setting_bottom','admin_setting_footer',
	//主题
	'admin_tpl_header','admin_tpl_menu','admin_tpl_list_top','admin_tpl_install_operate','admin_tpl_notInstall_operate','admin_tpl_operate','admin_tpl_footer',
	//扩展
	'admin_ext_header','admin_ext_menu','admin_ext_list_top','admin_ext_install_operate_top','admin_ext_install_operate_bottom','admin_ext_notInstall_operate_top','admin_ext_notInstall_operate_bottom','admin_ext_footer',
	//错误
	'admin_error_header','admin_error_menu','admin_error_list_top','admin_error_footer',
	//登录
	'admin_login_header','admin_login_form','admin_login_form_bottom','admin_login_footer',
	//后端业务层
	'admin_model_common','admin_model_login_success','admin_model_login_fail','admin_model_navbar','admin_model_category','admin_model_link','admin_model_article_category','admin_model_article_tag','admin_model_article_delete','admin_model_article_move_tag','admin_model_article_move_category','admin_model_article_create','admin_model_article_create_success','admin_model_article_create_fail','admin_model_article_editor','admin_model_article_editor_success','admin_model_article_editor_fail','admin_model_article','admin_model_setting','admin_model_tpl','admin_model_tpl_install','admin_model_tpl_delete','admin_model_tpl_download','admin_model_ext','admin_model_ext_install','admin_model_ext_uninstall','admin_model_ext_delete','admin_model_ext_download','admin_model_error','admin_model_default_page',
	//前端视图层
	'head_header','meta','css','script','head_footer','body_header','body_footer',
	//前端业务层
	'model_index','model_category','model_tag','model_search','model_comment_delete','model_comment_delete_success','model_comment_delete_fail','model_comment_add','model_comment_add_success','model_comment_add_fail','model_upload','model_import','model_prompt','model_default_page','model_default_page_filter',
] as $extTag) {
	$hook[$extTag] = [];
}
//模板编译
$tpl = new Tpl([
	'path' => '/tpl/',
	'name' => $conf['tpl'],
	'compile' => $conf['compile'],
]);
//获取配置信息
if($ajax){
	if(post('getConf','int')){
		ajax(0,['HOME'=>HOME,'URL'=>URL]);
	}
}
//检测安装
if(!$conf['install']){
	!is_writeable(DB) && exit('db文件夹无写入权限，请检查！');
	!extension_loaded('curl') && exit('请开启curl扩展！');
	!extension_loaded('gd') && exit('请开启gd扩展！');
	if($page == 'install'){
		if($method == 'POST'){
			$conf['title'] = post('title','str',$conf['title']);
			$conf['name'] = post('name','str',$conf['name']);
			$conf['intro'] = post('intro','str',$conf['intro']);
			$conf['password'] = md5((string)post('password','str',$conf['password']));
			$conf['install'] = post('install','bool',$conf['install']);
			if($conf['password']){
				$tpl->compile();
				dbSave('conf',$conf);
			}
			jump();
		}
	}
	include LIB.'admin/install.php';
	exit;
}
//ip黑名单检测
$ip = $_SESSION['ip'];
if(!$_SESSION['checkIP'] && $conf['blacklist']){
	$_SESSION['checkIP'] = 1;
	$blacklist = explode(' ',$conf['blacklist']);
	$error = '你的IP被系统拉入黑名单，如需解除，请联系管理员！';
	foreach($blacklist as $_ip){
		if(strpos($_ip,'/') !== false){
			preg_match($_ip,$ip) AND exit($error);
		}else{
			$ip == $_ip AND exit($error);
		}
	}
}
//更新浏览量
if(!isset($_SESSION['isVisit'])){
	$_SESSION['isVisit'] = 1;
	dbUpdate('conf',['views'=>++$conf['views']]);
}

//文章列表
$articleList = db('article',[],['time'=>1]);
//标签列表
$tagList = getTag();
//分类列表
$categoryList = getCategory();
//导航链接
$navbarList = $conf['navbar'];

// foreach($navbarList as $k => $v){
// 	if($v['url'] == '/index'){
// 		array_splice($navbarList,$k,1);
// 	}
// }
// print_r($navbarList);

//友情链接
$linkList = $conf['link'];
//加载已安装的扩展
$extList = $conf['ext'];
foreach ($extList as $k => $v) {
	$commonPath = EXT.$k.'/common.php';
	if(is_file($commonPath)) include $commonPath;
}
foreach($hook['common'] as $fn) $fn();
$settingPage = [];
foreach ($extList as $k => $v) {
	$mainPath = EXT.$k.'/main.php';
	$settingPage[$k] = ($page == 'admin' && $adminPage == 'ext' && get(2) == 'setting' && get(3) == $k) ? 'admin/ext/setting/'.$k : false;
	if(is_file($mainPath)) include $mainPath;
}
//加载模板中的公共文件
$commonPath = TPLPATH.'common.php';
if(is_file($commonPath)) include $commonPath;
$mainPath = TPLPATH.'main.php';
if(is_file($mainPath)) include $mainPath;
//页面路由
switch($page){

	//验证码
	case 'vcode':
		include LIB.'vcode.class.php';
		$code = new vcode($conf['vcode']['width'],$conf['vcode']['height'],$conf['vcode']['length']);
		$_SESSION['vcode'] = $code->getCode();
		echo $code->outimg();
		exit;

	//首页
	case 'index':
		$pageNum = get(1,'int',1);
		$pageSize = $conf['article']['paging'];
		$article = getArticle(LOGIN?[]:['private'=>0],['time'=>1],'index/{page}',$pageNum,$pageSize);
		foreach($hook['model_index'] as $fn) $fn();
		include $tpl->view('index');
		break;

	//分类
	case 'category':
		$cid = get(1,'str');
		if(!$cid || empty($categoryList[$cid])) jump('index');
		$pageNum = get(2,'int',1);
		$pageSize = $conf['article']['paging'];
		$category = $categoryList[$cid];
		$article = getArticle(LOGIN?['cid'=>$cid]:['cid'=>$cid,'private'=>0],['time'=>1,'top'=>1],'category/'.$cid.'/{page}',$pageNum,$pageSize);
		foreach($hook['model_category'] as $fn) $fn();
		include $tpl->view(is_file(TPLPATH.'category.php')?'category':'index');
		break;

	//标签
	case 'tag':
		$tag = get(1,'urldecode');
		$cond = ['tag'=>['IN'=>$tag]];
		if(!LOGIN)$cond['private'] = 0;
		$pageNum = get(2,'int',1);
		$pageSize = $conf['article']['paging'];
		$article = getArticle($cond,[],'tag/'.$tag.'/{page}',$pageNum,$pageSize);
		foreach($hook['model_tag'] as $fn) $fn();
		include $tpl->view(is_file(TPLPATH.'tag.php')?'tag':'index');
		break;

	//搜索页
	case 'search':
		$searchName = post('name','urldecode');
		$searchName && jump('search&name='.$searchName);
		$searchName = strip_tags(get('name','urldecode'));
		$cond = ['title'=>['LIKE'=>$searchName]];
		if(!LOGIN) $cond['private'] = 0;
		$pageNum = get(1,'int',1);
		$pageSize = $conf['article']['paging'];
		$article = getArticle($cond,['time'=>1,'top'=>1],'search/{page}&name='.$searchName,$pageNum,$pageSize);
		foreach($hook['model_search'] as $fn) $fn();
		include $tpl->view(is_file(TPLPATH.'search.php')?'search':'index');
		break;

	//评论
	case 'comment':
		$action = get(1,'str');

		//删除留言
		if($action == 'delete'){
			checkLogin();
			$articleId = get(2,'str');
			$commentId = get(3,'int');
			if(!$articleId || !$commentId){
				foreach($hook['model_comment_delete_fail'] as $fn) $fn();
				!$articleId && prompt('文章ID不存在');
				!$commentId && prompt('评论ID不存在');
			}
			foreach($hook['model_comment_delete'] as $fn) $fn();
			delComment('comment/'.$articleId,$commentId);
			commentsInit($articleId);
			foreach($hook['model_comment_delete_success'] as $fn) $fn();
			jump($articleId);
		}

		//添加留言
		if($method == 'POST'){
			$vcode = post('vcode','str');
			if($conf['vcode']['open'] && (!$vcode || strtolower($_SESSION['vcode']) !== strtolower((string)$vcode))){
				prompt('验证码不正确');
			}
			if($_SESSION['commentCount'] > $conf['comment']['restrict']){
				prompt('每日评论次数不能超过'.$conf['comment']['restrict'].'次哦！');
			}
			$pid = post('pid','int',0);
			$content = trim((string)post('content','stripTags'));
			if(mb_strlen($content) > 2000){
				prompt('留言字数不能大于2000');
			}
			$articleId = post('page','str');
			$path = 'comment/'.$articleId;
			if(!isset($articleList[$articleId]) || !$articleList[$articleId]['comment']) prompt('非法操作');
			$comment = db($path);
			$id = $comment ? $comment[count($comment)-1]['id'] + 1 : 1;
			if(!empty($content)){
				$post = ['id' => $id,'pid' => $pid,'admin' => LOGIN,'content' => $content,'ip' => $ip,'time' => $time];
				$selComment = arrWhere($comment,['ip'=>$ip,'content'=>$content,'time'=>['>'=>strtotime(date('Y-m-d',time()))]]);
				if(count($selComment) > 2) prompt('非法操作！');
				foreach($hook['model_comment_add'] as $fn) $fn();
				$comment[] = $post;
				dbSave($path,$comment);
				$conf['comment']['count'] += 1;
				dbSave('conf',$conf);
				$_SESSION['commentCount'] += 1;
				unset($_SESSION['vcode']);
				commentsInit($articleId);
				foreach($hook['model_comment_add_success'] as $fn) $fn();
				jump($articleId.'#comment-'.$id);
			}
			foreach($hook['model_comment_add_fail'] as $fn) $fn();
			prompt('留言内容不能为空');
		}
		break;

	//上传
	case 'upload':
		checkLogin();
		$parm = [];
		if(post('name'))$parm['name'] = post('name');
		if(post('path'))$parm['path'] = post('path');
		$arr = upload($parm);
		ajax($arr['error'],$arr['data']);
		break;

	//导入
	case 'import':
		checkLogin();
		if(isset($_FILES['file']) && $_FILES['file']){
			if(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION) == 'sx'){
				$itype = get(1,'str','system');
				$sx = file_get_contents($_FILES['file']['tmp_name']);
				//id
				!preg_match('/\s*#\s*Id:\s*(.+)/', $sx, $id) && ajax(1,'应用缺少Id');
				!preg_match('/^[a-zA-Z]+[a-zA-Z0-9_-]+$/',$id[1]) && ajax(1,'应用Id格式不正确');
				!check($id[1],'length',1,30) && ajax(1,'应用Id长度限制1-30');
				//type
				!preg_match('/\s*#\s*Type:\s*(.+)/', $sx, $type) && ajax(1,'应用缺少Type');
				if($type[1] !== 'tpl' && $type[1] !== 'ext' && $type[1] !== 'system') ajax(1,'应用Type不正确');
				if(preg_match('/\s*#\s*Limit:\s*(.+)/', $sx, $limit)){
					$v1 = str_replace('.','',$limit[1]);
					$v2 = str_replace('.','',V);
					if((int)$v1 > (int)$v2) ajax(1,'你的溯雪版本太低，无法安装使用，需要升级到v'.$limit[1].'及以上');
				}
				foreach($hook['model_import'] as $fn) $fn();
				if($itype == 'tpl'){
					unsx($sx,ROOT.'tpl/'.$id[1].'/');
				}elseif($itype == 'ext'){
					unsx($sx,EXT.$id[1].'/');
				}else{
					unsx($sx);
				}
				unlink($_FILES['file']['tmp_name']);
				ajax(0,'导入成功');
			}
		}
		ajax(1,'导入失败');
		break;

	//提示
	case 'prompt':
		if(empty($_SESSION['prompt'])) jump();
		$prompt = $_SESSION['prompt']['text'];
		$url = $_SESSION['prompt']['url'];
		foreach($hook['model_prompt'] as $fn) $fn();
		include $tpl->view($page);
		unset($_SESSION['prompt']);
		break;
	
	//登录
	case 'login':
		if(is_file(TPLPATH.'login.php')){
			include $tpl->view('login');
		}else{
			jump('admin/login');
		}
		break;

	//后台
	case 'admin':
		$adminPage = get(1,'string','index');

		//模板编译
		$adminTpl = new Tpl([
			'path' => '/lib/',
			'name' => 'admin',
			'compile' => $conf['compile'],
		]);

		foreach($hook['admin_model_common'] as $fn) $fn();

		//登录
		if($adminPage == 'login'){
			$conf['title'] = $conf['title'].'-登录';
			if($method == 'POST'){
			    if($_SESSION['loginCount'] > 9) prompt('密码错误次数太多，请好好想想哦！');
				$password = md5((string)post('password','trim'));
				if(!empty($password)){
					if($password === (string)$conf['password']){
						$_SESSION['login'] = 1;
						$_SESSION['tokenTime'] = $time;
						$token = getToken(true);
						$_SESSION['token'][$token]=time();
						setcookie('token',$token,0,'/');
						foreach($hook['admin_model_login_success'] as $fn) $fn();
					}else{
					    $_SESSION['loginCount'] += 1;
						foreach($hook['admin_model_login_fail'] as $fn) $fn();
						prompt('密码错误');
					}
				}else{
					prompt('密码不能为空');
				}
				jump();
			}
			include $adminTpl->view('login');
			exit;
		}

		//提示
		if($adminPage == 'prompt'){
			if(empty($_SESSION['prompt'])) jump('admin');
			$prompt = $_SESSION['prompt']['text'];
			$url = $_SESSION['prompt']['url'];
			include $adminTpl->view('prompt');
			unset($_SESSION['prompt']);
			exit;
		}

		//后台路由
		switch($adminPage){

			//登出
			case 'logout':
				session_destroy();
				jump();

			//查看phpinfo
			case 'phpinfo':
				print_r(phpinfo());
				exit;
			
			//查看配置
			case 'config':
				echo '<!DOCTYPE html><html lang="zh-Hans"><head><meta http-equiv="Content-Type" content="text/html" charset="UTF-8"/><title>server</title></head><body style="font-size:13px;display:flex;"><pre style="flex:0 0 50%;width:50%;white-space:pre-wrap;padding:10px;word-break:break-all;    box-sizing:border-box;">'."\nconfig:\n";
				print_r($conf);
				echo '</pre><pre style="flex:0 0 50%;width:50%;white-space:pre-wrap;padding:10px;word-break:break-all;box-sizing:border-box;">'."\nserver:\n";
				print_r($_SERVER);
				echo '</pre></body></html>';
				exit;

			//检查更新
			case 'checkUpdate':
				ajax(0,checkUpdate());
			
			//数据库版本更新
			case 'dbUpdate':
				$data = curl(API_HOST.'getDb');
				if($data){
					dbSync(type($data,'array'));
					$conf = db('conf');
					delCompile();
					ajax(0,'更新成功');
				}
				ajax(1,'更新失败');

			//核心文件更新
			case 'update':
				$data = curl(API_HOST.'getSxlog');
				if($data){
					delFile(ROOT.'update.php');
					unsx($data);
					$list = db('list');
					if(!$conf['db'] || $conf['db']['version'] != $list['#version']) dbSync($list);
					delCompile();
					if(is_file(ROOT.'update.php')){
						include ROOT.'update.php';
						delFile(ROOT.'update.php');
					}
					ajax(0,'更新成功啦！记得清理浏览器缓存刷新一下哦！');
				}
				ajax(1,'更新失败');

			//首页
			case 'index':
				$_SESSION['updateAlert'] = isset($_SESSION['updateAlert']) ? 0 : 1;
				include $adminTpl->view('index');
				break;
			
			//导航设置
			case 'navbar':
				if($method == 'POST'){
					$name = post('name');
					$url = post('url');
					$target = post('target','array',[]);
					$post = [];
					if($name){
						foreach($name as $k => $v){
							!$v && ajax(1,'名称不能为空');
							!$url[$k] && ajax(1,'链接不能为空');
							$post[] = [
								'name' => $v,
								'url' => $url[$k],
								'target' => in_array((string)$k,$target) ? 1 : 0,
								'child' => [],
							];
						}

					}
					foreach($hook['admin_model_navbar'] as $fn) $fn();
					dbUpdate('conf',['navbar'=>$post]);
					ajax(0,'保存成功');
				}
				include $adminTpl->view('navbar');
				break;

			//分类管理
			case 'category':
				if($method == 'POST'){
					$id = post('id');
					$newId = post('newId');
					$name = post('name');
					$intro = post('intro');
					$delId = post('delId');
					$post = [];
					$isModify = 0;
					if($newId){
						$modifyId = [];
						foreach($newId as $k => $v){
							!$name[$k] && ajax(1,'名称不能为空');
							!$v && ajax(1,'别称不能为空');
							// 判断分类是否已修改
							if($id[$k] && $v !== $id[$k]){
								$modifyId[$id[$k]] = $v;
							}
							$category = [
								'id' => $v,
								'name' => $name[$k],
								'intro' => $intro[$k],
								'count' => 0,
							];
							$post[$v] = $category;
						}
						if($modifyId){
							foreach($articleList as $k => $v){
								if(isset($modifyId[$v['cid']])){
									$isModify = 1;
									$articleList[$k]['cid'] = $modifyId[$v['cid']];
								}
							}
						}
					}
					if($delId){
						foreach($articleList as $k => $v){
							if(in_array($v['cid'],(array)$delId)){
								$isModify = 1;
								$articleList[$k]['cid'] = '';
							}
						}
					}
					if($isModify) dbSave('article',$articleList);
					foreach($hook['admin_model_category'] as $fn) $fn();
					dbUpdate('conf',['category'=>$post]);
					categoryInit();
					ajax(0,'保存成功');
				}
				include $adminTpl->view('category');
				break;
			
			//友情链接
			case 'link':
				if($method == 'POST'){
					$name = post('name');
					$url = post('url');
					$target = post('target','array',[]);
					$post = [];
					if($name){
						foreach($name as $k => $v){
							!$v && ajax(1,'名称不能为空');
							!$url[$k] && ajax(1,'链接不能为空');
							$post[] = [
								'name' => $v,
								'url' => $url[$k],
								'target' => in_array((string)$k,$target) ? 1 : 0
							];
						}
					}
					foreach($hook['admin_model_link'] as $fn) $fn();
					dbUpdate('conf',['link'=>$post]);
					ajax(0,'保存成功');
				}
				include $adminTpl->view('link');
				break;

			//文章管理
			case 'article':
				$type = get(2,'str');

				//分类
				if($type == 'category'){
					$cid = get(3,'urldecode');
					$pageNum = get(4,'int',1);
					$pageSize = $conf['article']['paging'];
					$article = getArticle(['cid'=>$cid],[],'admin/article/category/'.$cid.'/{page}',$pageNum,$pageSize);
					foreach($hook['admin_model_article_category'] as $fn) $fn();
					include $adminTpl->view('article');
				}
				
				//标签
				if($type == 'tag'){
					$tag = get(3,'urldecode');
					$pageNum = get(4,'int',1);
					$pageSize = $conf['article']['paging'];
					$article = getArticle(['tag'=>['IN'=>$tag]],[],'admin/article/tag/'.$tag.'/{page}',$pageNum,$pageSize);
					foreach($hook['admin_model_article_tag'] as $fn) $fn();
					include $adminTpl->view('article');
				}

				//批量删除
				elseif($type == 'delete'){
					$id = post('id');
					if(!$id) {
						$id = get(3,'string');
						$id = $id ? [$id] : 0;
					}
					if($id){
						foreach ($id as $v) {
							if(isset($articleList[$v])){
								delContentFiles(db('article/'.$v,false));
								unset($articleList[$v]);
								$conf['article']['count'] = count($articleList);
								delFile(dbPath('article/'.$v));
								delFile(dbPath('comment/'.$v));
							}
						}
						foreach($hook['admin_model_article_delete'] as $fn) $fn();
						dbSave('conf',$conf);
						dbSave('article',$articleList);
						tagInit();
						categoryInit();
						ajax(0,'删除成功');
					}
					ajax(1,'删除失败');
				}

				//移动分类和标签
				elseif($type == 'move'){
					$id = post('id');
					$com = get(3,'str');
					if($com == 'tag'){
						$tag = post('tag','str');
						if($id && $tag){
							foreach ($id as $v) {
								if(isset($articleList[$v])){
									$articleList[$v]['tag'] = [$tag];
								}
							}
							foreach($hook['admin_model_article_move_tag'] as $fn) $fn();
							dbSave('article',$articleList);
							tagInit();
							ajax(0,'操作成功');
						}
					}elseif($com == 'category'){
						$cid = post('cid','str');
						if($id && $cid){
							foreach ($id as $v) {
								if(isset($articleList[$v])){
									$articleList[$v]['cid'] = $cid;
								}
							}
							foreach($hook['admin_model_article_move_category'] as $fn) $fn();
							dbSave('article',$articleList);
							categoryInit();
							ajax(0,'操作成功');
						}
					}
					ajax(1,'操作失败');
				}

				//创建
				elseif($type == 'create'){
					if($method == 'POST'){
						$title = trim((string)post('title','stripTags'));
						$content = trim((string)post('content'));
						$id = trim((string)post('id'));
						$cid = post('cid','trim');
						//是否为自定义url
						$id = empty($id)?'T'.$time:$id;
						if(isset($articleList[$id])) prompt('已存在该URL名称');
						$tag = post('tag','trim');
						$tag = $tag ? preg_split('/\s+/', (string)$tag) : [];
						$path = dbPath('article/'.$id);
						if(!empty($content)){
							$intro = post('intro','string');
							if(!$intro){
								$fk = new fk($content);
								$html = preg_replace('/<[^>]+>/i','',$fk->html);
								$html = preg_replace('/[\r\n]+/','',$html);
								$intro =  mb_substr($html,0,$conf['brief'],'utf-8');
							}
							//获取第一张图片
							preg_match('/(\[img (.*?\.(jpg|jpeg|png|gif|bmp|tif)).*?\])/i', $content, $img);
							$img = $img ? $img[2] : false;
							$top = post('top','int',0);
							$private = post('private','int',0);
							$comment = post('comment','int',0);
							if(save($path,$content)){
								$post = [
									'id'=>$id,
									'cid'=>$cid,
									'title'=>$title,
									'intro'=>$intro,
									'img'=>$img,
									'time'=>$time,
									'top'=>$top,
									'private'=>$private,
									'views'=>0,
									'comment'=>$comment,
									'comments'=>0,
									'tag'=>$tag
								];
								foreach($hook['admin_model_article_create'] as $fn) $fn();
								$articleList[$id] = $post;
								$conf['article']['count'] = count($articleList);
								dbSave('conf',$conf);
								if(!dbSave('article',$articleList)){
									delFile($path);
								}
								tagInit();
								categoryInit();
								foreach($hook['admin_model_article_create_success'] as $fn) $fn();
							}else{
								foreach($hook['admin_model_article_create_fail'] as $fn) $fn();
								prompt('保存失败');
							}
						}else{
							foreach($hook['admin_model_article_create_fail'] as $fn) $fn();
							prompt('内容不能为空');
						}
						jump('admin/article');
					}
					include $adminTpl->view('article.create');
				}

				//编辑
				elseif($type == 'editor'){
					$id = get(3,'str');
					$article = getArticle(['id'=>$id]);
					if($article['list']){
						$article = $article['list'][$id];
					}else{
						prompt('没有该数据');
					}
					$article['content'] = db('article/'.$id,false);
					if($method == 'POST'){
						$title = trim((string)post('title','stripTags'));
						$content = trim((string)post('content'));
						$path = dbPath('article/'.$id);
						if(!empty($content)){
							$intro = post('intro','string');
							if(!$intro){
								$fk = new fk($content);
								$html = preg_replace('/<[^>]+>/i','',$fk->html);
								$html = preg_replace('/[\r\n]+/','',$html);
								$intro =  mb_substr($html,0,$conf['brief'],'utf-8');
							}

							//获取第一张图片
							$img = false;
							if(strpos($content,'[!img]') === false){
								preg_match('/img (src\s*=\s*[\'|"]+)?(.*?\.(jpg|jpeg|png|gif|bmp|tif))/i', $content, $img);
								$img = $img ? $img[2] : false;
							}

							//判断编辑的文档是不是为上个文档的url，不是的话，删除旧有的数据，建立新数据
							$name = post('id','trim');
							$newId = empty($name)?'T'.$time:$name;
							$cid = post('cid','trim');

							//从内容中提取时间
							preg_match('/\[\s*时间\s*\]\s*([\d\-\:\s]+)/i', $content, $match);
							$time = $article['time'];
							$top = post('top','int',0);
							$private = post('private','int',0);
							$comment = post('comment','int',0);
							$views = $articleList[$id]['views'];
							$comments =  $articleList[$id]['comments'];
							$tag = post('tag','trim');
							$tag = $tag ? preg_split('/\s+/', (string)$tag) : [];
							if($newId != $id){
								delFile($path);
								unset($articleList[$id]);
								$path = dbPath('article/'.$newId);
								$util->rename(DB.'comment/'.$id.'.php',$newId.'.php');
							}
							if(save($path,$content)){
								$post = [
									'id'=>$newId,
									'cid'=>$cid,
									'title'=>$title,
									'intro'=>$intro,
									'img'=>$img,
									'time'=>$time,
									'top'=>$top,
									'private'=>$private,
									'views'=>$views,
									'comment'=>$comment,
									'comments'=>$comments,
									'tag'=>$tag
								];
								foreach($hook['admin_model_article_editor'] as $fn) $fn();
								$articleList[$newId] = $post;
								if(!dbSave('article',$articleList)){
									delFile($path);
									prompt('编辑失败');
								}
								tagInit();
								categoryInit();
								foreach($hook['admin_model_article_editor_success'] as $fn) $fn();
							}else{
								foreach($hook['admin_model_article_editor_fail'] as $fn) $fn();
								prompt('编辑失败');
							}
						}else{
							foreach($hook['admin_model_article_editor_fail'] as $fn) $fn();
							prompt('内容不能为空');
						}
						jump('admin/article');
					}
					include $adminTpl->view('article.editor');
				}else{
					$pageNum = get(2,'int',1);
					$pageSize = $conf['article']['paging'];
					$article = getArticle(LOGIN?[]:['private'=>0],['time'=>1,'top'=>1],'admin/article/{page}',$pageNum,$pageSize);
					foreach($hook['admin_model_article'] as $fn) $fn();
					include $adminTpl->view('article');
				}
				break;
			
			//基础设置
			case 'setting':
				$tplList = getTpl();
				if($method == 'POST'){
					$conf['title'] = post('title','str','');
					$conf['name'] = post('name','str','');
					$conf['intro'] = post('intro','str','');
					$conf['mood'] = post('mood','str','');
					$conf['key'] = post('key','str','');
					$conf['desc'] = post('desc','str','');
					$conf['brief'] = post('brief','int',0);
					$password = post('password','str');
					$conf['password'] = strlen((string)$password) ? md5((string)$password) : $conf['password'];
					$compile = post('compile','bool');
					if($compile !== $conf['compile']){
						$conf['compile'] = $compile;
						delCompile();
					}
					$conf['debug'] = post('debug','int',$conf['debug']);
					$rewrite = post('rewrite','bool');
					if($rewrite !== $conf['rewrite']){
						$conf['rewrite'] = $rewrite;
						delCompile();
					}
					$conf['comment']['restrict'] = post('commentRestrict','int',$conf['comment']['restrict']);
					$conf['comment']['paging'] = post('commentPaging','int',$conf['comment']['paging']);
					$conf['article']['paging'] = post('articlePaging','int',$conf['article']['paging']);
					$conf['vcode']['open'] = post('vcodeOpen','bool',false);
					$conf['vcode']['width'] = post('vcodeWidth','int',80);
					$conf['vcode']['height'] = post('vcodeHeight','int',32);
					$conf['vcode']['length'] = post('vcodeLength','int',4);
					$conf['icp'] = post('icp','str','');
					$conf['prn'] = post('prn','str','');
					$conf['views'] = post('views','int',0);
					$conf['blacklist'] = post('blacklist','str','');
					$conf['js'] = post('js','str','');
					foreach($hook['admin_model_setting'] as $fn) $fn();
					dbSave('conf',$conf);
					header('Location:'.HOME.($conf['rewrite']?'':'?').'admin/setting');
				}
				include $adminTpl->view('setting');
				break;

			//编译模板
			case 'compile':
				delCompile();
				jump('admin/tpl');

			//主题管理
			case 'tpl':
				$tpl = getTpl();
				$tplPage = get(2,'str');
				$tplId = get(3,'str');
				foreach($hook['admin_model_tpl'] as $fn) $fn();
				if($tplId && isset($tpl['list'][$tplId])){
					if($tplPage == 'install'){
						$conf = db('conf');
						$conf['tpl'] = $tplId;
						foreach($hook['admin_model_tpl_install'] as $fn) $fn();
						dbSave('conf',$conf);
					}
					elseif($tplPage == 'delete'){
						$util->delete(ROOT.'tpl/'.$tplId);
						foreach($hook['admin_model_tpl_delete'] as $fn) $fn();
					}
					elseif($tplPage == 'download'){
						foreach($hook['admin_model_tpl_download'] as $fn) $fn();
						$sx = sx(ROOT.'tpl/'.$tplId);
						Header('Content-type: application/octet-stream');
						Header('Accept-Ranges: bytes');
						header('Content-Disposition: attachment; filename='.$tplId.'.sx');
						echo $sx;
						exit;
					}
					jump('admin/tpl');
				}
				include $adminTpl->view('tpl');
				break;

			//扩展管理
			case 'ext':
				$ext = getExt();
				$extPage = get(2,'str');
				$extId = get(3,'str');
				foreach($hook['admin_model_ext'] as $fn) $fn();
				if($extId && isset($ext['list'][$extId])){
					$extConfPath = EXT.$extId.'/conf.php';
					$extConf = include $extConfPath;
					if($extPage == 'install'){
						if(!isset($conf['ext'][$extId])){
							$conf['ext'][$extId] = [];
							foreach($hook['admin_model_ext_install'] as $fn) $fn();
							dbSave('conf',$conf);
							$installPath = EXT.$extId.'/install.php';
							if(is_file($installPath)) include $installPath;
						}
					}
					elseif($extPage == 'uninstall'){
						if(isset($conf['ext'][$extId])){
							unset($conf['ext'][$extId]);
							foreach($hook['admin_model_ext_uninstall'] as $fn) $fn();
							dbSave('conf',$conf);
							$uninstallPath = EXT.$extId.'/uninstall.php';
							if(is_file($uninstallPath)) include $uninstallPath;
						}
					}
					elseif($extPage == 'delete'){
						foreach($hook['admin_model_ext_delete'] as $fn) $fn();
						$util->delete(EXT.$extId);
					}
					elseif($extPage == 'download'){
						foreach($hook['admin_model_ext_download'] as $fn) $fn();
						$sx = sx(EXT.$extId);
						Header('Content-type: application/octet-stream');
						Header('Accept-Ranges: bytes');
						header('Content-Disposition: attachment; filename='.$extId.'.sx');
						echo $sx;
						exit;
					}
					elseif($extPage == 'setting'){
						if($extId && $ext['list'][$extId]['setting']){
							$settingTpl = new Tpl([
								'path' => '/ext/',
								'name' => $extId
							]);
							include $settingTpl->view('setting');
							exit;
						}
					}

					jump('admin/ext');
				}
				include $adminTpl->view('ext');
				break;

			//错误日志
			case 'error':
				if(get(2) == 'delete'){
					dbSave('error',[]);
					jump('admin/error');
				}
				$pageNum = get(2,'int',1);
				$pageSize = 30;
				$error = getError([],['time'=>1],'admin/error/{page}', $pageNum, $pageSize);
				foreach($hook['admin_model_error'] as $fn) $fn();
				include $adminTpl->view('error');
				break;

			//其它
			default:
				foreach($hook['admin_model_default_page'] as $fn) $fn();
				!$_SESSION['includeTheme'] && prompt('没有该数据');
		}
		break;

	//其它
	default:
		foreach($hook['model_default_page'] as $fn) $fn();
		$id = get(0,'string');
		if(isset($articleList[$id]) && $data = db('article/'.$id,false)){
			if($articleList[$id]['private'] && !LOGIN){
				prompt('游客无法访问私密文章，请登录！');
			}
		}else{
			$_SESSION['includeTheme'] ? exit : prompt('没有该数据');
		}
		$page = 'page';
		$article = $articleList[$id];
		$article['id'] = $id;
		$article['tag'] = $articleList[$id]['tag'];

		//网站描述和标题
		$conf['desc'] = $article['intro'];
		$conf['title'] = $conf['title'].'-'.$articleList[$id]['title'];
		$fk = new fk($data);
		$article['content'] = $fk->html;

		//留言板
		$pageNum = get(1,'int',1);
		$pageSize = $conf['comment']['paging'];
		$comment = getComment($id,[],[],$id.'/{page}',$pageNum,$pageSize);

		//更新浏览量
		if(!in_array($id,$_SESSION['views'])){
			$_SESSION['views'][]=$id;
			$articleList[$id]['views'] += 1;
			dbSave('article',$articleList);
		}
		foreach($hook['model_default_page_filter'] as $fn) $fn();
		include $tpl->view('page');
}
?>
