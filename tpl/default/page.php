<?php exit('404');?>
{include header}
<div class="content">
	<div class="article-title">{$article.title}</div>
	<div class="article-content">{$article.content}</div>
	{if $article.comment}
	<div class="title">评论留言</div>
	<form action="{url comment}" id="comment" method="post" class="comment">
		<input type="hidden" name="page" value="{$id}"/>
		<input type="hidden" name="pid"/>
		<textarea name="content" placeholder="填写联系方式和留言内容"></textarea>
		{if $conf.vcode.open}
		<img src="{url vcode}" onclick="this.src='{url vcode}'" class="vcode-img" title="点击更换验证码" alt="验证码"/>
		<input type="text" class="vcode-input" name="vcode" placeholder="验证码"/>
		{/if}
		<input type="submit" class="btn" value="提交"/><span class="comment-replys"></span>
	</form>
	<div class="tip">一共<span>{$comment.count}</span>条留言</div>
	{$comment.html}
	<div class="paging">{$comment.paging}</div>
	{/if}
</div>
{include sidebar}
{include footer}