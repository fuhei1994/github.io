<?php exit('404');?>
<!DOCTYPE html>
<html lang="zh-Hans">
<head>
	<!-- hook.head_header -->
	<meta http-equiv="Content-Type" content="text/html" charset="UTF-8"/>
	<title>{$conf.title}</title>
    <meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no"/>
    <meta name="keywords" content="{$conf.key}"/>
    <meta name="description" content="{$conf.desc}"/>
	<!-- hook.meta -->
    <link rel="shortcut icon" href="{#LIB_STYLE}logo.png"/>
    <link rel="stylesheet" href="{#LIB_STYLE}fk.css"/>
	<link rel="stylesheet" href="{#TPL_STYLE}main.css"/>
	<!-- hook.css -->
	<script src="{#LIB_STYLE}common.js"></script>
	<!-- hook.script -->
	<!-- hook.head_footer -->
</head>
<body>
	<!-- hook.body_header -->
	<div class="header">
		<div class="header-title"><a href="{#HOME}">{$conf.name}</a></div>
		<div class="header-menu">
			{foreach $navbarList}
			<a href="{$item.url}" target="{if $item.target}_blank{/if}">{$item.name}</a><span class="_drop"></span>
			{/foreach}
			{if LOGIN}
				<a href="{url admin/article/create}">写文章</a>
				{if $page == 'page'}
					<span class="_drop"></span><a href="{url admin/article/editor/$id}">编辑</a>
					<span class="_drop"></span><a href="javascript:SX.delArticle('{$id}')">删除</a>
				{/if}
				<span class="_line"></span><a href="{url admin/index}">后台</a><span class="_drop"></span><a href="{url admin/logout}">退出</a>
			{else}
				<a href="{url admin/login}">登录</a>
			{/if}
		</div>
	</div>
	<div class="main">