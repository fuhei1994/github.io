<?php exit('404');?>
{include header}
<!-- hook.admin_tpl_header -->
<div class="title">主题列表 ‧ {$tpl.count}个 ‧ <a href="javascript:SX.import('tpl')" class="red">导入主题</a> ‧ <a href="{url admin/compile}" data-pjax="false" class="red">重新编译模板</a><!-- hook.admin_tpl_menu --></div>
<!-- hook.admin_tpl_list_top -->
<ul class="tpl">
{foreach $tpl.list}
	<li>
		<div class="tpl-icon"><img src="{$item.icon}"/></div>
		<div class="tpl-info">
			<div class="tpl-name">{$item.name}<span class="tpl-version">v{$item.version}</span></div>
			<div class="tpl-operate">
				{if $conf.tpl == $item.id}
				<span class="tpl-use">使用中</span>
				{if $item.setting}<a href="{url admin/tpl/setting/$item.id}" class="tpl-btn bg-blue">设置</a>{/if}
				<!-- hook.admin_tpl_install_operate -->
				{else}
				<a class="tpl-btn" href="{url admin/tpl/install/$item.id}" data-pjax="false">使用</a><a href="{url admin/tpl/delete/$item.id}" data-pjax="false" class="tpl-btn bg-red" onclick="return SX.confirm(this,'确实要删除吗？删除不可恢复！')">删除</a>
				<!-- hook.admin_tpl_notInstall_operate -->
				{/if}
				<a href="{url admin/tpl/download/$item.id}" data-pjax="false" class="tpl-btn bg-orange">导出</a>
				<!-- hook.admin_tpl_operate -->
			</div>
		</div>
	</li>
{/foreach}
</ul>
<!-- hook.admin_tpl_footer -->
{include footer}