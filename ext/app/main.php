<?php
// 应用中心
if($page == 'admin' && $adminPage == 'app'){

	//登录限制
	if(!LOGIN) jump('admin/login');

	//模板编译
	$appTpl = new Tpl([
		'path' => '/ext/',
		'name' => 'app',
		'compile' => $conf['compile'],
	]);

	//页面路由
	$appPage = get(2,'str','tpl');
	$appType = get(3,'str','index');

	//用户key
	$keyPath = EXT.'app/key.php';
	if(!is_file($keyPath)) save($keyPath,['host'=>'','key'=>'']);
	$userKey = include $keyPath;

	//验证host
	if($_SERVER['SERVER_NAME'] !== $userKey['host']){
		$userKey = ['host'=>$_SERVER['SERVER_NAME'],'key'=>''];
		save($keyPath,$userKey);
	}

	/**
	 * 获取官方应用中心数据
	 * @param  string $url
	 * @param  bool|array|string $params
	 * @return string
	 */
	function getApi($url,$params=[]){
		global $conf,$userKey;
		$tpl = getTpl();
		$ext = getExt();
		$res = curl('https://xueluo.cn/api/'.$url,array_merge($userKey,[
			'system'=>[
				'version'=>V,
				'dbVersion'=>$conf['db']['version']
			],
			'url'=>URL,
			'tpl'=>$tpl['list'],
			'ext'=>$ext['list']
		],$params));
		$arr = type($res,'array');
		if(type($arr) == 'array' && $arr['error']){
			if($arr['error'] === 4001){
				jump('admin/app/login');
			}
			exit($res);
		}
		return $res;
	}

	//登录
	if($appPage == 'login'){
		if($method == 'POST'){
			$form = post('form','int',1);
			$username = post('username','str');
			$password = post('password','str');
			$res = getApi('login',['username'=>$username,'password'=>$password,'form'=>$form]);
			$arr = type($res,'array');
			if($arr && !$arr['error']){
				$userKey['key'] = $arr['data'];
				save($keyPath,$userKey);
				ajax(0,'登录成功');
			}
			exit($res);
		}
		$html = getApi('login');
		include $appTpl->view('app');
	}
	//退出
	elseif($appPage == 'logout'){
		$userKey['key'] = '';
		save($keyPath,$userKey);
		jump('admin/app/user');
	}
	//主题列表
	if($appPage == 'tpl'){
		$pageNum = get(3,'int',1);
		$pageSize = 30;
		$html = getApi('tpl',['page'=>$pageNum,'size'=>$pageSize]);
		include $appTpl->view('app');
	}
	//扩展列表
	elseif($appPage == 'ext'){
		$pageNum = get(3,'int',1);
		$pageSize = 30;
		$html = getApi('ext',['page'=>$pageNum,'size'=>$pageSize]);
		include $appTpl->view('app');
	}
	//应用详情
	elseif($appPage == 'view'){
		$type = get(3,'string');
		$id = get(4,'string');
		$html = getApi('view',['type'=>$type,'id'=>$id]);
		include $appTpl->view('app');
	}
	//应用评论
	elseif($appPage == 'comment'){
		$type = post('type','string');
		$id = post('id','string');
		$pid = post('pid','int',0);
		$content = post('content','string');
		$res = getApi('comment',['type'=>$type,'id'=>$id,'pid'=>$pid,'content'=>$content]);
		exit($res);
	}
	//应用评论删除
	elseif($appPage == 'deleteComment'){
		$type = post('type','string');
		$name = post('name','string');
		$id = post('id','int');
		$res = getApi('deleteComment',['type'=>$type,'name'=>$name,'id'=>$id]);
		exit($res);
	}
	//用户中心
	elseif($appPage == 'user'){
		$param = [];
		//账号设置
		if($appType == 'settingUpdate'){
			if($method == 'POST'){
				$param['password'] = post('password','str','');
				$param['question'] = post('question','str','');
				$param['answer'] = post('answer','str','');
				$param['contact'] = post('contact','str','');
				$param['mail'] = post('mail','str','');
				$param['home'] = post('home','str','');
				$param['content'] = post('content','str','');
				$res = getApi('user/'.$appType,$param);
				exit($res);
			}
		}
		$param['tab'] = get(3,'string','tpl');
		$html = getApi('user/'.$appType,$param);
		include $appTpl->view('app');
	}
	//申请成为开发者
	elseif($appPage == 'apply'){
		if($method == 'POST'){
			$res = getApi('apply-developer');
			exit($res);
		}
		$html = getApi('apply');
		include $appTpl->view('app');
	}
	//开发者中心
	elseif($appPage == 'developer'){
		$tab = get(3,'string','tpl');
		$html = getApi('developer',['tab'=>$tab]);
		include $appTpl->view('app');
	}
	//发布新应用
	elseif($appPage == 'publish'){
		$tab = get(3,'str','upload');
		if($tab === 'upload'){
			if($method == 'POST'){
				if(isset($_FILES['file'])){
					$file = $_FILES['file'];
					$sx = file_get_contents($file['tmp_name']);
					if($sx){
						//清空sx文件夹
						$util->delete(EXT.'app/tmp');
						$util->createDir(EXT.'app/tmp');
						unsx($sx,EXT.'app/tmp/');
						unlink($file['tmp_name']);
						!is_file(EXT.'app/tmp/conf.php') && ajax(1,'应用缺少配置文件');
						!is_file(EXT.'app/tmp/icon.png') && ajax(1,'应用缺少主图');
						ajax(0,'上传成功');
					}
				}
				ajax(1,'上传失败');
			}
		}elseif($tab === 'submit'){
			$confPath = EXT.'app/tmp/conf.php';
			$iconPath = EXT.'app/tmp/icon.png';
			//判断缓存中有没有数据，针对刷新当前页面
			if(!is_file($confPath)) jump('admin/app/publish');
			$view = include $confPath;
			$publishPath = EXT.'app/publish/'.$view['type'].'/'.$view['id'];
			//删除旧数据
			$util->delete($publishPath);
			//创建新数据
			$util->cut(EXT.'app/tmp/',$publishPath);
			$html = getApi('publish-view',['tab'=>$tab,'view'=>$view]);
			include $appTpl->view('app');
			exit;
		}
		$html = getApi('publish',['tab'=>$tab]);
		include $appTpl->view('app');
	}
	//发布新应用
	elseif($appPage == 'publish-submit'){
		if($method == 'POST'){
			$type = post('type','str');
			$id = post('id','str');
			$newId = post('newId','str');
			$publishPath = EXT.'app/publish/'.$type.'/'.$newId;
			$id !== $newId && !is_dir($publishPath) && $util->rename(EXT.'app/publish/'.$type.'/'.$id,$newId);
			$confPath = $publishPath.'/conf.php';
			$iconPath = $publishPath.'/icon.png';
			$view = include $confPath;
		
			$name = post('name','str');
			$intro = post('intro','str');
			$home = post('home','str');
			$version = post('version','str');
			$content = post('content','str');

			!check($name,'length',1,50) && ajax(1,'名称字数限制1-50');
			!check($intro,'length',1,350) && ajax(1,'简介字数限制1-350');
			!check($home,'length',1,200) && ajax(1,'主页字数限制1-200');
			!check($version,'length',1,6) && ajax(1,'版本字数限制1-6');
			!check($content,'length',1,100000) && ajax(1,'内容字数限制1-10万字');

			$info = [];
			$info['id'] = $newId;
			$info['type'] = $type;
			$info['author'] = $view['author'];
			$info['name'] = $name;
			$info['intro'] = $intro;
			$info['home'] = $home;
			$info['version'] = $version;
			$info['content'] = $content;

			if(save($confPath,$info)){
				$sx = sx($publishPath);
				$icon = file_get_contents($iconPath);
				$res = getApi('publish-upload',['sx'=>$sx,'icon'=>$icon,'conf'=>$info]);
				$arr = type($res,'array');
				if($arr && !$arr['error']){
					$util->delete($publishPath);
				}
				exit($res);
			}
			ajax(1,'发布失败，请检查');
		}

	}
	//应用编辑
	elseif($appPage == 'editor'){
		$type = get(3,'str');
		$id = get(4,'str');
		if($method == 'POST'){
			$name = post('name','str');
			$version = post('version','str');
			$home = post('home','str');
			$intro = post('intro','str');
			$content = post('content','str');
			$res = getApi('editorForm',[
				'type'=>$type,
				'id'=>$id,
				'name'=>$name,
				'version'=>$version,
				'home'=>$home,
				'intro'=>$intro,
				'content'=>$content,
			]);
			exit($res);
		}
		$html = getApi('editor',['type'=>$type,'id'=>$id]);
		include $appTpl->view('app');
	}
	//应用上架 下架 删除
	elseif($appPage == 'put' || $appPage == 'pull' || $appPage == 'del'){
		$type = get(3,'str');
		$id = get(4,'str');
		$res = getApi($appPage,['type'=>$type,'id'=>$id]);
		exit($res);
	}
	//应用主图上传
	elseif($appPage == 'upload'){
		$type = get(3,'str');
		$id = get(4,'str');
		if($method == 'POST'){
			if($type != 'tpl' && $type != 'ext') ajax(1,'type不正确');
			if(isset($_FILES['file'])){
				$file = $_FILES['file'];
				if($file['type'] !== 'image/png') ajax(1,'只能上传png格式的主图哦');
				$img = file_get_contents($file['tmp_name']);
				$util->createFile(EXT.'app/publish/'.$type.'/'.$id.'/icon.png',$img);
				unlink($file['tmp_name']);
				ajax(0,'上传成功');
			}
		}
		ajax(1,'上传失败');
	}
	//应用主图上传
	elseif($appPage == 'upload-publish'){
		$type = get(3,'str');
		$id = get(4,'str');
		if($method == 'POST'){
			if($type != 'tpl' && $type != 'ext') ajax(1,'type不正确');
			if(isset($_FILES['file'])){
				$file = $_FILES['file'];
				if($file['type'] !== 'image/png') ajax(1,'只能上传png格式的主图哦');
				$img = file_get_contents($file['tmp_name']);
				unlink($file['tmp_name']);
				$res = getApi('upload',['type'=>$type,'id'=>$id,'img'=>$img]);
				exit($res);
			}
		}
		ajax(1,'上传失败');
	}
	//系统
	elseif($appPage == 'system'){
		$html = getApi('system');
		include $appTpl->view('app');
	}
	//安装
	elseif($appPage == 'install'){
		$id = get(4,'str');
		if($appType == 'tpl'){
			$res = getApi('download/tpl/'.$id);
			$arr = type($res,'array');
			if(type($arr) == 'array' && $arr['error']){
				ajax('1',$arr['data']);
			}
			unsx($res,ROOT.'tpl/'.$id.'/');
			$tpl->compile();
			ajax('0','安装成功');
		}
		elseif($appType == 'ext'){
			$res = getApi('download/ext/'.$id);
			$arr = type($res,'array');
			if(type($arr) == 'array' && $arr['error']){
				ajax(1,'安装失败');
			}
			unsx($res,ROOT.'ext/'.$id.'/');
			ajax('0','安装成功');
		}
	}
	exit;
}
?>