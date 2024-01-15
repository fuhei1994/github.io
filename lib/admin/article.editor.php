<?php exit('404');?>
{include header}
<form action="{url admin/article/editor/$article.id}" method="post" class="article-form article-editor">
	<!-- hook.admin_article_editor_top -->
	<div class="form-row article-editor-title">
		<div class="form-col"><label>标题</label><input type="text" name="title" placeholder="文章标题" value="{$article.title}"/></div>
		<!-- hook.admin_article_editor_title -->
	</div>
	<!-- hook.admin_article_editor_info_top -->
	<div class="form-row article-editor-info">
		<div class="form-col">
			<label>分类</label>
			<select name="cid" title="分类">
				{foreach $categoryList}
				<option value="{$item.id}" {if $item.id === $article.cid}selected="selected"{/if}>{$item.name}</option>
				{/foreach}
			</select>
		</div>
		<div class="form-col"><label>标签</label><input type="text" name="tag" placeholder="多个标签用空格隔开" value="{$article.tag.name}"/></div>
		<div class="form-col"><label>URL</label><input type="text" name="id" placeholder="URL名称，不填以时间命名" value="{$article.id}"/></div>
		<!-- hook.admin_article_editor_info -->
	</div>
	<!-- hook.admin_article_editor_attr_top -->
	<div class="form-row article-create-attr">
		<div class="form-col">
			<label>属性</label>
			<label><input name="top" type="checkbox" {if $article.top}checked{/if} value="1"/>置顶</label>
			<label><input name="private" type="checkbox" {if $article.private}checked{/if} value="1"/>私密</label>
			<label><input name="comment" type="checkbox" {if $article.comment}checked{/if} value="1"/>评论</label>
			<!-- hook.admin_article_editor_attr_col -->
		</div>
		<!-- hook.admin_article_editor_attr -->
	</div>
	<!-- hook.admin_article_editor_intro_top -->
	<div class="form-row article-editor-intro">
		<label>描述</label>
		<textarea name="intro" rows="2" placeholder="描述，200字符以内">{$article.intro}</textarea>
	</div>
	<!-- hook.admin_article_editor_content_top -->
	<div class="form-row article-editor-content">
		<label>内容</label>
		<textarea name="content" id="content" placeholder="第一行为#标题">{#htmlentities($article.content)}</textarea>
	</div>
	<!-- hook.admin_article_editor_bottom -->
	<div class="form-col">
		<input type="submit" class="btn bg-blue" value="保存"/>
	</div>
	<!-- hook.editor -->
</form>
{include footer}