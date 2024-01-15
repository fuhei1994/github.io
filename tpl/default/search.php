<?php exit('404');?>
{include header}
<div class="content">
	<div class="tip">一共找到<span>{$article.count}</span>篇文章</div>
	{if $article.list}
	<div class="title">列表
		<form action="{url search}" method="post">
			<input type="text" name="name" placeholder="文章搜索">
			<input type="submit" class="btn" value="搜索">
		</form>
	</div>
	<div class="list-title">
		<span>标题 (▲=置顶 ▣=私密 ❖=普通)</span>
		<span>时间</span>
		<span>分类</span>
		<span>浏览</span>
		<span>评论</span>
	</div>
	<ul class="list">
	{foreach $article.list}
		{{
			$icon = $item.top ? '▲' : '❖';
			$icon = $item.private ? '▣' : $icon;
			$class = $item.top ? 'top' : 'common';
			$class = $item.private ? $class.' private' : $class;
			$time = dates($item.time);
		}}
		<li class="{$class}">
			<a href="{$item.url}">{$icon} {$item.title}</a>
			<span>{$time}</span>
			<span>{$item.category.name}</span>
			<span>{$item.views}</span>
			<span>{$item.comments}</span>
		</li>
	{/foreach}
	</ul>
	<div class="paging">{$article.paging}</div>
	{else}
	暂无数据
	{/if}
</div>
{include sidebar}
{include footer}