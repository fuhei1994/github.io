<?php exit('404');?>
{include header}
<!-- hook.admin_article_header -->
<div class="article">
	<div class="article-list">
		<div class="title">文章列表</div>
		<!-- hook.admin_article_menu_header -->
		<div class="article-menu">
			<div class="article-menu-left">
				<!-- hook.admin_article_menu_left_top -->
				<div class="form-col">
					<div class="btn bg-red" onclick="del()">批量删除</div>
				</div>
				<div class="form-col">
					<select name="cid" title="分类">
						{foreach $categoryList}
						<option value="{$item.id}">{$item.name}</option>
						{/foreach}
					</select>
					<div class="btn bg-blue" onclick="move('category')">移动到该分类</div>
				</div>
				<div class="form-col">
					<select name="tag" style="margin-left:10px;">
						{foreach $tagList}
						<option value="{$item.id}">{$item.name}</option>
						{/foreach}
					</select>
					<div class="btn bg-blue" onclick="move('tag')">移动到该标签</div>
				</div>
				<!-- hook.admin_article_menu_left_bottom -->
			</div>
			<div class="article-menu-right">
				<!-- hook.admin_article_menu_right_top -->
				<a href="{url admin/article/create}" class="btn">创建文章</a>
				<!-- hook.admin_article_menu_right_bottom -->
			</div>
		</div>
		<!-- hook.admin_article_menu_footer -->
		{if $article.list}
		<div class="table table-hover nowrap">
			<table>
				<thead>
					<tr>
						<th>选择</th>
						<th style="width:100%;">标题</th>
						<!-- hook.admin_article_list_th_1 -->
						<th>分类</th>
						<th>标签</th>
						<th>浏览</th>
						<th>评论</th>
						<!-- hook.admin_article_list_th_2 -->
						<th>时间</th>
						<!-- hook.admin_article_list_th_3 -->
						<th>操作</th>
					</tr>
				</thead>
				<tbody>
					{foreach $article.list}
					{{
						$item.icon = $item.top ? '▲' : ($item.private ? '▣' : '❖');
						$item.time = dates($item.time);
					}}
					<tr>
						<td><input type="checkbox" name="article" value="{$item.id}"/></td>
						<td><a href="{$item.url}" target="_blank">{$item.icon} {$item.title}</a></td>
						<!-- hook.admin_article_list_td_1 -->
						<td>{$item.category.name}</td>
						<td>{$item.tag.name}</td>
						<td>{$item.views}</td>
						<td>{$item.comments}</td>
						<!-- hook.admin_article_list_td_2 -->
						<td>{$item.time}</td>
						<!-- hook.admin_article_list_td_3 -->
						<td>
							<a href="{url admin/article/editor/$item.id}">编辑</a>
							<!-- hook.admin_article_list_operate -->
						</td>
					</tr>
					{/foreach}
				</tbody>
			</table>
		</div>
		<div class="paging">{$article.paging}</div>
		{else}
		暂无数据
		{/if}
		<!-- hook.admin_article_bottom -->
	</div>
	<div class="article-tab">
		<!-- hook.admin_article_sidebar_top -->
		<div class="title">分类</div>
		<ul class="tag-list">
			{foreach $categoryList}
			<li><a href="{url admin/article/category/$item.id}">{$item.name}({$item.count})</a></li>
			{/foreach}
		</ul>
		<div class="title">标签</div>
		<ul class="tag-list">
			<li><a href="{url admin/article}">全部({$conf.article.count})</a></li>
			{foreach $tagList}
			<li><a href="{url admin/article/tag/$item.id}">{$item.name}({$item.count})</a></li>
			{/foreach}
		</ul>
		<!-- hook.admin_article_sidebar_bottom -->
	</div>
</div>
<script>
	SX('tbody tr').click(i=>{SX('[name=article]')[i].click()});
	SX('[name=article]').click(()=>{SX.sp()});
	//批量删除
	function del(){
		var sel = [];
		SX('[name=article]').each(function(){
			if(this.checked){
				sel.push(this.value);
			}
		})
		if(!sel.length) return SX.pop('请选择文章');
		SX.alert({
			content:'确实要删除吗？删除不可恢复！',
			yes(){
				SX.ajax('{url admin/article/delete}',{'id[]':sel}).then(res=>{
					SX.pop(res.data);
					!res.error && SX.pjax.render();
				})
			}
		})
	}
	//移动
	function move(type){
		var sel = [];
		SX('[name=article]').each(function(){
			if(this.checked){
				sel.push(this.value);
			}
		})
		if(!sel.length) return SX.pop('请选择文章');
		var data = {'id[]':sel};
		if(type == 'category'){ //分类
			data.cid = SX('[name=cid]')[0].value;
		}else if(type == 'tag'){ //标签
			data.cid = SX('[name=tag]')[0].value;
		}
		SX.ajax('{url admin/article/move/}'+type,data).then(res=>{
			SX.pop(res.data);
			!res.error && SX.pjax.render();
		})
	}
</script>
<!-- hook.admin_article_footer -->
{include footer}