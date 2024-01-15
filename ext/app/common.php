<?php
if($page == 'admin'){
	//引入样式文件
	hook('admin_css',HOME.'ext/app/style/main.css');
	//添加菜单
	hook('admin_sidebar_menu_3','<a href="'.URL.'admin/app">应用中心</a>');
}
?>