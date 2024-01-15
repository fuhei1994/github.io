<?php exit('404');?>
{include header}
<form action="{url admin/article/create}" method="post" class="article-form article-create">
	<!-- hook.admin_article_create_top -->
	<div class="form-row article-create-title">
		<div class="form-col"><label>标题</label><input type="text" name="title" placeholder="文章标题" value=""/></div>
		<!-- hook.admin_article_create_title -->
	</div>
	<!-- hook.admin_article_create_info_top -->
	<div class="form-row article-create-info">
		<div class="form-col">
			<label>分类</label>
			<select name="cid" title="分类">
				{foreach $categoryList}
				<option value="{$item.id}">{$item.name}</option>
				{/foreach}
			</select>
		</div>
		<div class="form-col"><label>标签</label><input type="text" name="tag" placeholder="多个标签用空格隔开" value=""/></div>
		<div class="form-col"><label>URL</label><input type="text" name="id" placeholder="URL名称，不填以时间命名" value=""/></div>
		<!-- hook.admin_article_create_info -->
	</div>
	<!-- hook.admin_article_create_attr_top -->
	<div class="form-row article-create-attr">
		<div class="form-col">
			<label>属性</label>
			<label><input name="top" type="checkbox" value="1"/>置顶</label>
			<label><input name="private" type="checkbox" value="1"/>私密</label>
			<label><input name="comment" type="checkbox" checked value="1"/>评论</label>
			<!-- hook.admin_article_create_attr_col -->
		</div>
		<!-- hook.admin_article_create_attr -->
	</div>
	<!-- hook.admin_article_create_intro_top -->
	<div class="form-row article-create-intro">
		<label>描述</label>
		<textarea name="intro" rows="2" placeholder="描述，200字符以内"></textarea>
	</div>
	<!-- hook.admin_article_create_content_top -->
	<div class="form-row article-create-content">
		<label>内容</label>
		<textarea name="content" id="content" placeholder="第一行为#标题"></textarea>
	</div>
	<!-- hook.admin_article_create_bottom -->
	<div class="form-col">
		<input type="submit" class="btn bg-blue" value="保存"/>
	</div>
	<!-- hook.editor -->
</form>
{include footer}