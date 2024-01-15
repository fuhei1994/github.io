<?php exit('404');?>
<div class="sidebar">
	<div class="title">分类</div>
	<ul class="sidebar-tag">
		{foreach $categoryList}
		<li><a href="{$item.url}">{$item.name}({$item.count})</a></li>
		{/foreach}
	</ul>
	<div class="title">标签</div>
	<ul class="sidebar-tag">
		{foreach $tagList}
		<li><a href="{$item.url}">{$item.name}({$item.count})</a></li>
		{/foreach}
	</ul>
</div>